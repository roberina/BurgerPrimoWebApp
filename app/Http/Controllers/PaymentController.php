<?php

namespace App\Http\Controllers;

use App\Models\CustomBurger;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    /**
     * Show checkout page with Stripe integration
     */
    public function checkout(Request $request): Response
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Teie ostukorv on tühi!');
        }

        $deliveryMethod = $request->get('delivery_method', 'takeaway');

        // Calculate total from cart
        $cartItems = [];
        $subtotal = 0;
        
        foreach ($cart as $item) {
            if (isset($item['burger_id'])) {
                $burger = CustomBurger::with('ingredients')->find($item['burger_id']);
                if ($burger) {
                    $cartItems[] = [
                        'type' => 'custom_burger',
                        'name' => $burger->name,
                        'quantity' => $item['quantity'],
                        'price' => (float) $burger->total_price,
                    ];
                    $subtotal += (float) ($burger->total_price * $item['quantity']);
                }
            } elseif (isset($item['menu_item_id'])) {
                $cartItems[] = [
                    'type' => 'menu_item',
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => (float) $item['total_price'],
                ];
                $subtotal += (float) ($item['total_price'] * $item['quantity']);
            }
        }

        // Add packaging fee for takeaway (€0.20 per item)
        $packagingFee = 0;
        if ($deliveryMethod === 'takeaway') {
            $itemCount = count($cart);
            $packagingFee = $itemCount * 0.20;
        }

        $total = $subtotal + $packagingFee;

        return Inertia::render('Payment/Checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'packagingFee' => $packagingFee,
            'total' => $total,
            'deliveryMethod' => $deliveryMethod,
            'stripePublicKey' => config('services.stripe.public_key'),
        ]);
    }

    /**
     * Create payment intent for Stripe
     */
    public function createIntent(Request $request)
    {
        $validated = $request->validate([
            'delivery_method' => 'required|in:dine_in,takeaway,delivery',
            'customer_notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            if (isset($item['burger_id'])) {
                $burger = CustomBurger::find($item['burger_id']);
                if ($burger) {
                    $total += (float) ($burger->total_price * $item['quantity']);
                }
            } elseif (isset($item['menu_item_id'])) {
                $total += (float) ($item['total_price'] * $item['quantity']);
            }
        }

        // Add packaging fee for takeaway
        if ($validated['delivery_method'] === 'takeaway') {
            $itemCount = count($cart);
            $packagingFee = $itemCount * 0.20;
            $total += $packagingFee;
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int) ($total * 100), // Convert to cents
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'user_id' => auth()->id(),
                    'delivery_method' => $validated['delivery_method'],
                    'customer_notes' => $validated['customer_notes'] ?? '',
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process successful payment and create order
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'delivery_method' => 'required|in:dine_in,takeaway,delivery',
            'customer_notes' => 'nullable|string|max:500',
            'delivery_lat'     => 'required_if:delivery_method,delivery|nullable|numeric|between:57.85,58.75',
            'delivery_lng'     => 'required_if:delivery_method,delivery|nullable|numeric|between:21.50,23.20',
            'delivery_address' => 'required_if:delivery_method,delivery|nullable|string|max:500',
        ]);

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
            
            // Retrieve payment intent to verify
            $paymentIntent = \Stripe\PaymentIntent::retrieve($validated['payment_intent_id']);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment not successful'], 400);
            }

            $cart = session()->get('cart', []);
            
            if (empty($cart)) {
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $paymentIntent->amount / 100, // Convert from cents
                'status' => 'pending',
                'customer_notes' => $validated['customer_notes'] ?? null,
                'delivery_method' => $validated['delivery_method'],
                'payment_intent_id' => $validated['payment_intent_id'],
                'payment_status' => 'succeeded',
                'payment_method' => 'card',
                'paid_at' => now(),
                'delivery_lat' => $validated['delivery_lat'] ?? null,
                'delivery_lng' => $validated['delivery_lng'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
            ]);

            $totalAmount = 0;

            // Add items from cart
            foreach ($cart as $cartItem) {
                if (isset($cartItem['burger_id'])) {
                    $burger = CustomBurger::with('ingredients')->find($cartItem['burger_id']);
                    
                    if (!$burger || $burger->user_id !== auth()->id()) {
                        continue;
                    }

                    $price = $burger->total_price;
                    $quantity = $cartItem['quantity'];
                    $itemTotal = $price * $quantity;
                    $totalAmount += $itemTotal;

                    $ingredientsData = $burger->ingredients->map(function ($ingredient) {
                        return [
                            'name' => $ingredient->name,
                            'category' => $ingredient->category,
                            'quantity' => $ingredient->pivot->quantity,
                            'price' => $ingredient->price,
                        ];
                    })->toArray();

                    $order->items()->create([
                        'custom_burger_id' => $burger->id,
                        'burger_name' => $burger->name,
                        'ingredients' => $ingredientsData,
                        'price' => $price,
                        'quantity' => $quantity,
                    ]);
                } elseif (isset($cartItem['menu_item_id'])) {
                    $price = $cartItem['total_price'];
                    $quantity = $cartItem['quantity'];
                    $itemTotal = $price * $quantity;
                    $totalAmount += $itemTotal;

                    // Format ingredients array for display
                    $ingredientsData = [];
                    
                    if (!empty($cartItem['size'])) {
                        $sizeNames = [
                            'small' => 'Väike',
                            'medium' => 'Keskmine',
                            'large' => 'Suur',
                        ];
                        $sizeName = $sizeNames[$cartItem['size']] ?? $cartItem['size'];
                        $ingredientsData[] = [
                            'name' => 'Suurus: ' . $sizeName,
                            'category' => 'size',
                            'quantity' => 1,
                            'price' => 0,
                        ];
                    }

                    if (!empty($cartItem['drinks'])) {
                        foreach ($cartItem['drinks'] as $drink) {
                            $ingredientsData[] = [
                                'name' => $drink['name'] ?? 'Jook',
                                'category' => 'drink',
                                'quantity' => 1,
                                'price' => isset($drink['price']) ? (float) $drink['price'] : 0,
                            ];
                        }
                    }

                    if (!empty($cartItem['sauces'])) {
                        foreach ($cartItem['sauces'] as $sauce) {
                            $ingredientsData[] = [
                                'name' => $sauce['name'] ?? 'Kaste',
                                'category' => 'sauce',
                                'quantity' => 1,
                                'price' => isset($sauce['price']) ? (float) $sauce['price'] : 0,
                            ];
                        }
                    }

                    if (!empty($cartItem['fries'])) {
                        $ingredientsData[] = [
                            'name' => $cartItem['fries']['name'] ?? 'Friikartul',
                            'category' => 'fries',
                            'quantity' => 1,
                            'price' => isset($cartItem['fries']['price']) ? (float) $cartItem['fries']['price'] : 0,
                        ];
                    }

                    if (!empty($cartItem['needs_utensils'])) {
                        $ingredientsData[] = [
                            'name' => 'Söögiriistad: Jah',
                            'category' => 'utensils',
                            'quantity' => 1,
                            'price' => 0,
                        ];
                    }

                    if (!empty($cartItem['special_instructions'])) {
                        $ingredientsData[] = [
                            'name' => 'Märkused: ' . $cartItem['special_instructions'],
                            'category' => 'notes',
                            'quantity' => 1,
                            'price' => 0,
                        ];
                    }

                    $order->items()->create([
                        'menu_item_id' => $cartItem['menu_item_id'],
                        'burger_name' => $cartItem['name'],
                        'ingredients' => $ingredientsData,
                        'price' => $price,
                        'quantity' => $quantity,
                    ]);
                }
            }

            // Add packaging fee as an order item if takeaway
            if ($validated['delivery_method'] === 'takeaway') {
                $itemCount = count($cart);
                $packagingFee = $itemCount * 0.20;
                
                $order->items()->create([
                    'burger_name' => 'Pakendamise tasu',
                    'ingredients' => [[
                        'name' => 'Pakendid ja nõud kaasavõtuks',
                        'category' => 'packaging',
                        'quantity' => $itemCount,
                        'price' => 0.20,
                    ]],
                    'price' => $packagingFee,
                    'quantity' => 1,
                ]);
            }

            // Clear cart
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}