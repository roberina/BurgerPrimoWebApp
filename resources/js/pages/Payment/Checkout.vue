<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <!-- Navbar -->
    <Navbar :cartCount="cartItems.length" />

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">{{ t('checkout.title') }}</h1>
        <p class="text-gray-400">{{ t('checkout.sub') }}</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-[#121212] rounded-2xl p-6">
          <h2 class="text-2xl font-bold mb-6">{{ t('checkout.summary') }}</h2>
          
          <div class="space-y-4 mb-6">
            <div v-for="(item, index) in cartItems" :key="index" class="flex justify-between items-start">
              <div class="flex-1">
                <p class="font-semibold">{{ item.name }}</p>
                <p class="text-sm text-gray-400">{{ t('checkout.qty') }} {{ item.quantity }}</p>
              </div>
              <p class="font-bold text-[#D2691E]">€{{ (item.price * item.quantity).toFixed(2) }}</p>
            </div>
          </div>

          <!-- Show subtotal and packaging fee -->
          <div class="space-y-2 mb-4">
            <div class="flex justify-between text-gray-400">
              <span>{{ t('checkout.subtotal') }}</span>
              <span>€{{ Number(subtotal).toFixed(2) }}</span>
            </div>
            <div v-if="packagingFee > 0" class="flex justify-between text-[#D2691E]">
              <span>{{ t('checkout.packaging') }}</span>
              <span>€{{ Number(packagingFee).toFixed(2) }}</span>
            </div>
          </div>

          <hr class="border-[#0B0B0B] my-6" />

          <div class="flex justify-between items-center text-2xl font-bold">
            <span>{{ t('checkout.total') }}</span>
            <span class="text-[#D2691E]">€{{ Number(total).toFixed(2) }}</span>
          </div>

          <!-- Delivery Method Display -->
          <div class="mt-6 p-4 bg-[#0B0B0B] rounded-xl">
            <p class="text-sm text-gray-400 mb-2">{{ t('checkout.type') }}</p>
            <div class="flex items-center gap-2">
              <svg v-if="deliveryMethod === 'dine_in'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#D2691E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#D2691E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              <span class="font-semibold">{{ deliveryMethod === 'dine_in' ? t('checkout.dine_in') : t('checkout.takeaway') }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-[#121212] rounded-2xl p-6">
          <h2 class="text-2xl font-bold mb-6">{{ t('checkout.payment') }}</h2>

          <!-- Delivery Address Picker (takeaway / home delivery) — KOHUSTUSLIK -->
          <div v-if="deliveryMethod !== 'dine_in'" class="mb-6" ref="addressSection">
            <label class="text-sm mb-3 flex items-center gap-2"
                   :class="addressError ? 'text-red-400' : 'text-gray-400'">
              🏠 <span>{{ t('checkout.address') }}</span>
              <span class="text-xs font-normal" :class="addressError ? 'text-red-400' : 'text-gray-600'">
                {{ addressError ? t('checkout.address.req') : t('checkout.address.region') }}
              </span>
            </label>
            <div :class="addressError ? 'ring-2 ring-red-500/60 rounded-xl' : ''">
              <AddressPickerMap
                :model-lat="deliveryLat"
                :model-lng="deliveryLng"
                :model-address="deliveryAddress"
                @update:model-lat="deliveryLat = $event; addressError = false"
                @update:model-lng="deliveryLng = $event"
                @update:model-address="deliveryAddress = $event"
              />
            </div>
            <p v-if="addressError" class="mt-2 text-sm text-red-400 flex items-center gap-1.5">
              ⚠ {{ t('checkout.address.err') }}
            </p>
          </div>

          <!-- Customer Notes -->
          <div class="mb-6">
            <label for="customer_notes" class="text-sm text-gray-400 mb-2 block">{{ t('checkout.notes') }}</label>
            <textarea
              id="customer_notes"
              v-model="customerNotes"
              rows="3"
              class="w-full bg-[#0B0B0B] border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#D2691E] transition-colors"
              :placeholder="t('checkout.notes.ph')"
            ></textarea>
          </div>

          <!-- Stripe Card Element -->
          <div class="mb-6">
            <label class="text-sm text-gray-400 mb-2 block">{{ t('checkout.card') }}</label>
            <div 
              id="card-element" 
              class="bg-[#0B0B0B] border border-gray-700 rounded-xl px-4 py-4"
            ></div>
            <div v-if="cardError" class="text-red-400 text-sm mt-2">{{ cardError }}</div>
          </div>

          <!-- Submit Button -->
          <button
            @click="handleSubmit"
            :disabled="processing"
            class="w-full bg-gradient-to-r from-[#D2691E] to-[#B8571A] hover:from-[#E07A2E] hover:to-[#D2691E] text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-[#D2691E]/50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!processing">{{ t('checkout.pay') }} €{{ Number(total).toFixed(2) }}</span>
            <span v-else>{{ t('checkout.processing') }}</span>
          </button>

          <p class="text-xs text-gray-500 text-center mt-4">
            {{ t('checkout.secure') }}
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { useToast } from '@/composables/useToast';
import { useI18n } from '@/composables/useI18n';
const { t } = useI18n();

const { success, error: showError } = useToast();
import { router } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import AddressPickerMap from '@/components/AddressPickerMap.vue';

interface CartItem {
  type: string;
  name: string;
  quantity: number;
  price: number;
}

interface Props {
  cartItems: CartItem[];
  subtotal: number;
  packagingFee: number;
  total: number;
  deliveryMethod: 'dine_in' | 'takeaway';
  stripePublicKey: string;
}

const props = defineProps<Props>();

const deliveryMethod = ref<'dine_in' | 'takeaway'>(props.deliveryMethod);
const customerNotes = ref('');
const processing = ref(false);
const cardError = ref('');

const deliveryLat = ref<number | null>(null);
const deliveryLng = ref<number | null>(null);
const deliveryAddress = ref('');
const addressError = ref(false);
const addressSection = ref<HTMLElement | null>(null);

let stripe: any = null;
let cardElement: any = null;

onMounted(async () => {
  // Load Stripe.js
  const stripeScript = document.createElement('script');
  stripeScript.src = 'https://js.stripe.com/v3/';
  stripeScript.async = true;
  document.head.appendChild(stripeScript);

  stripeScript.onload = () => {
    // @ts-ignore
    stripe = Stripe(props.stripePublicKey);
    const elements = stripe.elements();
    
    cardElement = elements.create('card', {
      style: {
        base: {
          color: '#ffffff',
          fontFamily: 'system-ui, sans-serif',
          fontSize: '16px',
          '::placeholder': {
            color: '#6b7280',
          },
        },
        invalid: {
          color: '#f87171',
        },
      },
    });
    
    cardElement.mount('#card-element');
    
    cardElement.on('change', (event: any) => {
      cardError.value = event.error ? event.error.message : '';
    });
  };
});

const handleSubmit = async () => {
  // Valideeri tarneaadress
  if (deliveryMethod.value !== 'dine_in' && !deliveryAddress.value) {
    addressError.value = true;
    await nextTick();
    addressSection.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  if (!stripe || !cardElement) {
    cardError.value = 'Stripe ei ole veel laaditud. Palun proovige uuesti.';
    return;
  }

  processing.value = true;
  cardError.value = '';

  try {
    // Create payment intent
    const intentResponse = await fetch('/payment/create-intent', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        delivery_method: deliveryMethod.value,
        customer_notes: customerNotes.value,
        delivery_lat: deliveryLat.value,
        delivery_lng: deliveryLng.value,
        delivery_address: deliveryAddress.value || null,
      }),
    });

    const intentData = await intentResponse.json();

    if (!intentResponse.ok) {
      throw new Error(intentData.error || 'Maksesoovi loomine ebaõnnestus');
    }

    // Confirm payment with Stripe
    const { error, paymentIntent } = await stripe.confirmCardPayment(intentData.clientSecret, {
      payment_method: {
        card: cardElement,
      },
    });

    if (error) {
      throw new Error(error.message);
    }

    if (paymentIntent.status === 'succeeded') {
      // Process payment on backend
      const processResponse = await fetch('/payment/process', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          payment_intent_id: paymentIntent.id,
          delivery_method: deliveryMethod.value,
          customer_notes: customerNotes.value,
          delivery_lat: deliveryLat.value,
          delivery_lng: deliveryLng.value,
          delivery_address: deliveryAddress.value || null,
        }),
      });

      const processData = await processResponse.json();

      if (!processResponse.ok) {
        throw new Error(processData.error || 'Tellimuse töötlemine ebaõnnestus');
      }

      // Show success toast and redirect
      success(`Makse õnnestus! Tellimus #${processData.order_number} esitatud ✓`);
      router.visit(`/orders/${processData.order_id}`);
    }
  } catch (err: any) {
    cardError.value = err.message || 'Makse ebaõnnestus. Palun proovige uuesti.';
    showError(cardError.value);
  } finally {
    processing.value = false;
  }
};
</script>