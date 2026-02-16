<?php

namespace App\Http\Controllers;

use App\Models\CustomBurger;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(): Response
    {
        $cart = session()->get('cart', []);
        
        // Load full details for cart items (both burgers and menu items)
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $item) {
            if (isset($item['burger_id'])) {
                // Custom burger from builder
                $burger = CustomBurger::with('ingredients')->find($item['burger_id']);
                if ($burger) {
                    $cartItems[] = [
                        'type' => 'custom_burger',
                        'id' => $item['burger_id'],
                        'burger' => [
                            'id' => $burger->id,
                            'name' => $burger->name,
                            'description' => $burger->description,
                            'total_price' => (float) $burger->total_price,
                            'is_favorite' => $burger->is_favorite,
                            'ingredients' => $burger->ingredients,
                        ],
                        'quantity' => $item['quantity'],
                        'subtotal' => (float) ($burger->total_price * $item['quantity']),
                    ];
                    $total += (float) ($burger->total_price * $item['quantity']);
                }
            } elseif (isset($item['menu_item_id'])) {
                // Regular menu item
                $cartItems[] = [
                    'type' => 'menu_item',
                    'id' => $item['id'],
                    'menu_item_id' => $item['menu_item_id'],
                    'name' => $item['name'],
                    'base_price' => (float) $item['base_price'],
                    'size' => $item['size'],
                    'drinks' => $item['drinks'] ?? [],
                    'sauces' => $item['sauces'] ?? [],
                    'fries' => $item['fries'] ?? null,
                    'needs_utensils' => $item['needs_utensils'] ?? false,
                    'special_instructions' => $item['special_instructions'] ?? '',
                    'total_price' => (float) $item['total_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => (float) ($item['total_price'] * $item['quantity']),
                ];
                $total += (float) ($item['total_price'] * $item['quantity']);
            }
        }
        
        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Add custom burger to cart (from burger builder)
     */
    public function add(Request $request)
{
    $validated = $request->validate([
        'burger_id' => 'required|exists:custom_burgers,id',
        'quantity' => 'required|integer|min:1|max:10',
    ]);

    $burger = CustomBurger::with('ingredients')->findOrFail($validated['burger_id']);

    // Check if burger belongs to current user
    if ($burger->user_id !== auth()->id()) {
        return redirect()->back()->with('error', 'See burger ei kuulu sulle!');
    }

    // Check if all ingredients are available
    foreach ($burger->ingredients as $ingredient) {
        if (!$ingredient->is_available) {
            return redirect()->back()->with('error', 'Mõned burgeri koostisosad ei ole enam saadaval!');
        }
    }

    $cart = session()->get('cart', []);

    $cartId = 'burger_' . $burger->id . '_' . time();

    $cart[$cartId] = [
        'type' => 'custom_burger',
        'burger_id' => $burger->id,
        'quantity' => $validated['quantity'],
        'subtotal' => $burger->total_price * $validated['quantity'],
    ];

    session()->put('cart', $cart);

    return redirect()->route('cart.index')->with('success', 'Burger lisatud ostukorvi!');
}

    /**
     * Add menu item to cart (from menu page)
     */
    public function addMenuItem(Request $request)
    {
        $validated = $request->validate([
            'menu_item_id' => 'required|integer',
            'name' => 'required|string',
            'base_price' => 'required|numeric',
            'size' => 'required|string',
            'drinks' => 'nullable|array',
            'sauces' => 'nullable|array',
            'fries' => 'nullable|array',
            'needs_utensils' => 'boolean',
            'special_instructions' => 'nullable|string',
            'total_price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        // Generate unique ID for this cart item
        $cartItemId = uniqid('menu_', true);

        // Add menu item to cart
        $cart[] = [
            'id' => $cartItemId,
            'menu_item_id' => $validated['menu_item_id'],
            'name' => $validated['name'],
            'base_price' => $validated['base_price'],
            'size' => $validated['size'],
            'drinks' => $validated['drinks'] ?? [],
            'sauces' => $validated['sauces'] ?? [],
            'fries' => $validated['fries'] ?? null,
            'needs_utensils' => $validated['needs_utensils'] ?? false,
            'special_instructions' => $validated['special_instructions'] ?? '',
            'total_price' => $validated['total_price'],
            'quantity' => $validated['quantity'],
        ];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Toode lisatud korvi!');
    }

    /**
     * Add new burger directly from builder
     */
    public function addNew(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|integer|min:1|max:10',
        ]);

        // Create the burger first
        $burger = CustomBurger::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => null,
            'total_price' => 0,
            'is_favorite' => false,
        ]);

        // Attach ingredients
        foreach ($validated['ingredients'] as $ingredientData) {
            $burger->ingredients()->attach($ingredientData['id'], [
                'quantity' => $ingredientData['quantity'],
            ]);
        }

        // Calculate price
        $totalPrice = $burger->calculateTotalPrice();
        $burger->update(['total_price' => $totalPrice]);

        // Add to cart
        $cart = session()->get('cart', []);
        $cart[] = [
            'burger_id' => $burger->id,
            'quantity' => 1,
        ];
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Burger added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = session()->get('cart', []);
        
        foreach ($cart as &$item) {
            // Check if it's a custom burger
            if (isset($item['burger_id']) && $item['burger_id'] == $itemId) {
                $item['quantity'] = $validated['quantity'];
                break;
            }
            // Check if it's a menu item
            if (isset($item['id']) && $item['id'] == $itemId) {
                $item['quantity'] = $validated['quantity'];
                break;
            }
        }
        
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        $cart = session()->get('cart', []);
        
        $cart = array_filter($cart, function($item) use ($itemId) {
            // Don't remove if it's a custom burger with matching ID
            if (isset($item['burger_id']) && $item['burger_id'] == $itemId) {
                return false;
            }
            // Don't remove if it's a menu item with matching ID
            if (isset($item['id']) && $item['id'] == $itemId) {
                return false;
            }
            return true;
        });
        
        session()->put('cart', array_values($cart));
        
        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->back()->with('success', 'Cart cleared!');
    }

    /**
     * Checkout and create order
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $validated = $request->validate([
            'customer_notes' => 'nullable|string|max:500',
            'delivery_method' => 'required|in:dine_in,takeaway',  // ADD THIS

        ]);

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => 0,
            'status' => 'pending',
            'customer_notes' => $validated['customer_notes'] ?? null,
            'delivery_method' => $validated['delivery_method'],  // ADD THIS

        ]);

        $totalAmount = 0;

        // Add items from cart
        foreach ($cart as $cartItem) {
            if (isset($cartItem['burger_id'])) {
                // Custom burger
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
                // Menu item - format ingredients properly for display
                $price = $cartItem['total_price'];
                $quantity = $cartItem['quantity'];
                $itemTotal = $price * $quantity;
                $totalAmount += $itemTotal;

                // Format ingredients array for display
                $ingredientsData = [];
                
                // Add size
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

                // Add drinks
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

                // Add sauces
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

                // Add fries
                if (!empty($cartItem['fries'])) {
                    $ingredientsData[] = [
                        'name' => $cartItem['fries']['name'] ?? 'Friikartul',
                        'category' => 'fries',
                        'quantity' => 1,
                        'price' => isset($cartItem['fries']['price']) ? (float) $cartItem['fries']['price'] : 0,
                    ];
                }

                // Add utensils note
                if (!empty($cartItem['needs_utensils'])) {
                    $ingredientsData[] = [
                        'name' => 'Söögiriistad: Jah',
                        'category' => 'utensils',
                        'quantity' => 1,
                        'price' => 0,
                    ];
                }

                // Add special instructions if any
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

        $order->update(['total_amount' => $totalAmount]);

        // Clear cart
        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);
    }
}