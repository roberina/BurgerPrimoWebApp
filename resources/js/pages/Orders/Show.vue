<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white pt-16 lg:pt-20">
    <Navbar />

    <main class="max-w-3xl mx-auto px-6 py-12">

      <!-- ───── KOHALE TOIMETATUD ───── -->
      <template v-if="order.status === 'completed'">
        <div class="flex flex-col items-center justify-center py-20 text-center gap-6">
          <!-- Animatsioon -->
          <div class="relative">
            <div class="text-9xl" style="filter: drop-shadow(0 0 32px rgba(34,197,94,0.5))">🎉</div>
            <div class="absolute -top-3 -right-3 text-4xl animate-bounce" style="animation-delay:0.2s">🍔</div>
            <div class="absolute -bottom-3 -left-3 text-3xl animate-bounce" style="animation-delay:0.5s">⭐</div>
          </div>
          <div class="space-y-3 mt-4">
            <h1 class="text-4xl font-black text-white">{{ t('order.show.enjoy') }}</h1>
            <p class="text-green-400 font-bold text-xl">{{ t('order.show.delivered') }}</p>
            <p class="text-gray-500 text-sm max-w-xs mx-auto">
              Tellimus <span class="font-mono text-[#D2691E] font-bold">{{ order.order_number }}</span>
              {{ t('order.show.delivered.sub') }}
            </p>
          </div>
          <div class="flex gap-3 mt-4 flex-wrap justify-center">
            <a href="/menu"
               class="px-6 py-3 rounded-2xl font-bold text-sm transition"
               style="background: linear-gradient(135deg, #D2691E, #B8571A); color: white;">
              {{ t('order.show.reorder') }}
            </a>
            <a href="/orders"
               class="px-6 py-3 rounded-2xl font-bold text-sm bg-white/8 hover:bg-white/12 border border-white/10 text-gray-300 transition">
              {{ t('order.show.my_orders') }}
            </a>
          </div>
        </div>
      </template>

      <!-- ───── KOHALETOIMETAMINE: Bolt-laadne vaade ───── -->
      <template v-else-if="order.status === 'delivering'">

        <!-- Suur kaart -->
        <div class="mb-4 rounded-2xl overflow-hidden shadow-2xl">
          <DeliveryMap
            :courier-lat="order.courier_lat ?? null"
            :courier-lng="order.courier_lng ?? null"
            :courier-updated-at="order.courier_updated_at ?? null"
            :delivery-lat="order.delivery_lat ?? null"
            :delivery-lng="order.delivery_lng ?? null"
            :delivery-address="order.delivery_address ?? null"
            height="420px"
          />
        </div>

        <!-- Kuller on teel info -->
        <div class="mb-6 bg-[#121212] border border-cyan-900/40 rounded-2xl overflow-hidden">
          <div class="bg-cyan-950/40 px-6 py-4 flex items-center gap-4 border-b border-cyan-900/30">
            <div class="w-12 h-12 rounded-full bg-cyan-500/15 flex items-center justify-center text-2xl shrink-0">🛵</div>
            <div class="flex-1">
              <p class="font-bold text-lg text-white">{{ t('order.show.courier.title') }}</p>
              <p class="text-sm text-cyan-400">Tellimus nr
                <span class="font-mono font-bold">{{ order.order_number }}</span>
                {{ t('order.show.courier.sub') }}
              </p>
            </div>
            <div class="text-right shrink-0">
              <span :class="getStatusClass(order.status)">{{ getStatusLabel(order.status) }}</span>
              <div v-if="deliveryEta" class="mt-1">
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">ETA</p>
                <p class="text-xl font-bold text-cyan-400 leading-tight">{{ deliveryEta }}</p>
              </div>
            </div>
          </div>
          <div class="px-6 py-4 flex items-center gap-3">
            <span class="text-xl">🏠</span>
            <div>
              <p class="text-xs text-gray-500 mb-0.5">{{ t('order.show.dest') }}</p>
              <p class="text-sm text-gray-200 font-medium">{{ order.delivery_address ?? t('order.show.dest.unknown') }}</p>
            </div>
          </div>
        </div>

        <!-- Tellimuse detailid (kompaktne) -->
        <div class="bg-[#121212] rounded-2xl border border-[#1a1a1a] p-5 space-y-4 mb-6">
          <div class="space-y-2">
            <p class="text-xs text-gray-500 uppercase tracking-widest">{{ t('order.show.products') }}</p>
            <div v-for="item in order.items" :key="item.id"
                 class="flex items-center justify-between bg-[#0d0d0d] rounded-xl px-4 py-3">
              <div>
                <p class="font-semibold text-sm">{{ item.burger_name }}</p>
                <p class="text-xs text-gray-500">{{ item.quantity }}} {{ t('order.show.qty') }}</p>
              </div>
              <p class="font-bold text-[#D2691E] text-sm">{{ Number(item.price * item.quantity).toFixed(2) }}€</p>
            </div>
          </div>
          <div class="flex items-center justify-between pt-3 border-t border-[#1a1a1a]">
            <span class="text-gray-400 text-sm">{{ t('order.show.total') }}</span>
            <span class="text-2xl font-bold text-[#D2691E]">{{ Number(order.total_amount).toFixed(2) }}€</span>
          </div>
        </div>

        <!-- Auto-refresh teade -->
        <p class="text-center text-xs text-gray-600 mb-4">
          {{ t('order.show.refresh') }}
        </p>

      </template>

      <!-- ───── TAVALINE VAADE (kõik muud staatused) ───── -->
      <template v-else-if="order.status !== 'completed'">

      <!-- Success / Status Banner -->
      <div class="mb-8 rounded-2xl p-8 text-center"
           :class="order.status === 'completed'
             ? 'bg-green-900/20 border border-green-800/50'
             : 'bg-green-900/20 border border-green-800/50'">
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-3xl font-bold mb-2">{{ t('order.show.submitted') }}</h1>
        <p class="text-gray-400">{{ t('order.show.number') }}
          <span class="font-mono font-bold text-[#D2691E]">{{ order.order_number }}</span>
        </p>
      </div>

      <!-- Order Card -->
      <div class="bg-[#121212] rounded-2xl overflow-hidden border border-[#1a1a1a]">

        <!-- Status Bar -->
        <div class="bg-[#0d0d0d] px-6 py-4 flex items-center justify-between border-b border-[#1a1a1a]">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('order.show.submitted.label') }}</p>
            <p class="text-sm text-gray-300 font-medium">{{ formatDate(order.created_at) }}</p>
          </div>
        </div>

        <div class="p-6 space-y-6">

          <!-- Status progress indicator -->
          <div class="flex items-center gap-2">
            <div
              v-for="(step, i) in statusSteps"
              :key="step.key"
              class="flex items-center gap-2 flex-1"
            >
              <div class="flex flex-col items-center flex-1">
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                  :class="getStepClass(step.key)"
                >
                  <svg v-if="isStepDone(step.key)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                  </svg>
                  <span v-else>{{ i + 1 }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1 text-center">{{ step.label }}</p>
              </div>
              <div
                v-if="i < statusSteps.length - 1"
                class="h-0.5 flex-1 mb-4 transition-all"
                :class="isStepDone(step.key) ? 'bg-[#D2691E]' : 'bg-[#1a1a1a]'"
              ></div>
            </div>
          </div>

          <!-- Items -->
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">{{ t('order.show.products') }}</p>
            <div class="space-y-3">
              <div
                v-for="item in order.items"
                :key="item.id"
                class="bg-[#0d0d0d] rounded-xl px-4 py-3"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <p class="font-semibold">{{ item.burger_name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ item.quantity }}} {{ t('order.show.qty') }}</p>
                    <div v-if="item.ingredients?.length" class="mt-2 space-y-0.5">
                      <p v-for="(ingredient, index) in item.ingredients" :key="index" class="text-xs text-gray-500">
                        {{ ingredient.quantity }}x {{ ingredient.name }}
                      </p>
                    </div>
                  </div>
                  <p class="font-bold text-[#D2691E]">{{ Number(item.price * item.quantity).toFixed(2) }}€</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Customer Notes -->
          <div v-if="order.customer_notes" class="bg-[#0d0d0d] rounded-xl px-4 py-3">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('order.show.notes') }}</p>
            <p class="text-sm text-gray-300">{{ order.customer_notes }}</p>
          </div>

          <!-- Total -->
          <div class="flex items-center justify-between pt-4 border-t border-[#1a1a1a]">
            <span class="text-gray-400 font-medium">{{ t('order.show.total') }}</span>
            <span class="text-3xl font-bold text-[#D2691E]">{{ Number(order.total_amount).toFixed(2) }}€</span>
          </div>
        </div>
      </div>

      <!-- Auto refresh notice -->
      <p class="text-center text-xs text-gray-600 mt-4">
        {{ t('order.show.auto_refresh') }}
      </p>

      <!-- Sihtkoha määramine (kui pole veel seatud) -->
      <div v-if="canSetLocation" class="mt-6 bg-[#121212] rounded-2xl border border-[#1a1a1a] p-5">
        <p class="text-sm font-semibold text-white mb-1">{{ t('order.show.set_dest') }}</p>
        <p class="text-xs text-gray-500 mb-4">{{ t('order.show.set_dest_sub') }}</p>
        <AddressPickerMap
          :model-lat="pickLat"
          :model-lng="pickLng"
          :model-address="pickAddress"
          @update:model-lat="pickLat = $event"
          @update:model-lng="pickLng = $event"
          @update:model-address="pickAddress = $event"
        />
        <button
          @click="saveDeliveryLocation"
          :disabled="!pickAddress || savingLocation"
          class="mt-4 w-full py-3 rounded-xl font-semibold text-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
          style="background-color: #D2691E; color: white;"
        >
          {{ savingLocation ? t('order.show.saving') : t('order.show.save_dest') }}
        </button>
      </div>

      <!-- Seatud sihtkoht (näita kui on olemas) -->
      <div v-else-if="order.delivery_address" class="mt-4 bg-[#121212] rounded-2xl border border-[#1a1a1a] px-5 py-4 flex items-start gap-3">
        <span class="text-xl mt-0.5">🏠</span>
        <div>
          <p class="text-xs text-gray-500 uppercase tracking-widest mb-0.5">{{ t('order.show.dest') }}</p>
          <p class="text-sm text-gray-200">{{ order.delivery_address }}</p>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-6 flex gap-4">
        <Link
          href="/menu"
          class="flex-1 text-white px-6 py-3.5 rounded-xl font-semibold transition hover:opacity-90 text-center"
          style="background-color: #D2691E"
        >
          {{ t('order.show.order_more') }}
        </Link>
        <Link
          href="/orders"
          class="flex-1 bg-[#121212] hover:bg-[#1a1a1a] text-white px-6 py-3.5 rounded-xl font-semibold transition text-center border border-[#1a1a1a]"
        >
          {{ t('order.show.all_orders') }}
        </Link>
      </div>

      </template><!-- /v-else -->

    </main>

    <!-- Kulleri kohalejõudmise teatis -->
    <Transition name="arrived">
      <div v-if="showArrivedBanner"
           class="fixed inset-0 flex items-center justify-center"
           style="z-index: 9999; background: rgba(0,0,0,0.88);"
           @click="dismissArrivedBanner">
        <div class="text-center rounded-3xl border border-green-500/30 p-10"
             style="background: linear-gradient(135deg, #061206, #0a1f0a); max-width: 360px; width: 90%; box-shadow: 0 0 80px rgba(34,197,94,0.25);"
             @click.stop>
          <div class="text-8xl mb-2" style="filter: drop-shadow(0 0 40px rgba(34,197,94,0.8))">🛵</div>
          <div class="text-5xl mb-4">🍔</div>
          <h2 class="text-3xl font-black text-white mb-2">{{ t('order.show.arrived.title') }}</h2>
          <p class="text-green-400 font-semibold text-lg mb-1">{{ t('order.show.arrived.sub') }}</p>
          <p class="text-gray-500 text-sm mb-8">
            Tellimus <span class="font-mono text-[#D2691E] font-bold">{{ order.order_number }}</span>
          </p>
          <button
            @click="dismissArrivedBanner"
            class="w-full py-3 rounded-2xl font-bold text-white text-base transition hover:opacity-90"
            style="background: linear-gradient(135deg, #16a34a, #15803d); box-shadow: 0 4px 20px rgba(22,163,74,0.4);">
            {{ t('order.show.arrived.ok') }}
          </button>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import DeliveryMap from '@/components/DeliveryMap.vue';
import AddressPickerMap from '@/components/AddressPickerMap.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t, locale } = useI18n();

const haversineKm = (lat1: number, lng1: number, lat2: number, lng2: number): number => {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLng = (lng2 - lng1) * Math.PI / 180;
  const a = Math.sin(dLat / 2) ** 2
    + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(a));
};
import Navbar from '@/components/Navbar.vue';

interface Ingredient {
  name: string;
  quantity: number;
}

interface OrderItem {
  id: number;
  burger_name: string;
  price: number;
  quantity: number;
  ingredients: Ingredient[];
}

interface Order {
  id: number;
  order_number: string;
  total_amount: number;
  status: string;
  created_at: string;
  customer_notes: string | null;
  items: OrderItem[];
  courier_lat?: number | null;
  courier_lng?: number | null;
  courier_updated_at?: string | null;
  delivery_lat?: number | null;
  delivery_lng?: number | null;
  delivery_address?: string | null;
  delivery_method?: string | null;
}

interface Props {
  order: Order;
}

const props = defineProps<Props>();

// Courier arrival notification
const showArrivedBanner = ref(false);

const dismissArrivedBanner = () => {
  showArrivedBanner.value = false;
};

watch(() => props.order.status, (newStatus, oldStatus) => {
  if (oldStatus === 'delivering' && newStatus === 'completed') {
    showArrivedBanner.value = true;
  }
});

// Delivery address picker (shown when address not yet set)
const pickLat = ref<number | null>(null);
const pickLng = ref<number | null>(null);
const pickAddress = ref('');
const savingLocation = ref(false);

const deliveryEta = computed(() => {
  const { courier_lat, courier_lng, delivery_lat, delivery_lng } = props.order;
  if (!courier_lat || !courier_lng || !delivery_lat || !delivery_lng) return null;
  const dist = haversineKm(courier_lat, courier_lng, delivery_lat, delivery_lng);
  const mins = Math.max(1, Math.ceil(dist * 1.35 / 25 * 60));
  if (mins < 60) return `~${mins} min`;
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return m > 0 ? `~${h}h ${m}min` : `~${h}h`;
});

const canSetLocation = computed(() =>
  !props.order.delivery_address &&
  ['pending', 'confirmed', 'preparing', 'ready', 'delivering'].includes(props.order.status)
);

const saveDeliveryLocation = () => {
  if (!pickLat.value || !pickLng.value || !pickAddress.value) return;
  savingLocation.value = true;
  router.patch(`/orders/${props.order.id}/delivery-location`, {
    delivery_lat: pickLat.value,
    delivery_lng: pickLng.value,
    delivery_address: pickAddress.value,
  }, {
    onFinish: () => { savingLocation.value = false; },
  });
};

// Status progress steps — computed so labels react to locale changes
// The "delivering" step is only shown for delivery orders
const isDeliveryOrder = computed(() => props.order.delivery_method === 'delivery');

const statusSteps = computed(() => {
  const steps = [
    { key: 'pending',    label: t('order.step.pending') },
    { key: 'confirmed',  label: t('order.step.confirmed') },
    { key: 'preparing',  label: t('order.step.preparing') },
    { key: 'ready',      label: t('order.step.ready') },
    { key: 'completed',  label: t('order.step.completed') },
  ];
  if (isDeliveryOrder.value) {
    steps.splice(4, 0, { key: 'delivering', label: t('order.step.delivering') });
  }
  return steps;
});

const statusOrder = ['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled', 'rejected'];

const isStepDone = (stepKey: string): boolean => {
  const currentIndex = statusOrder.indexOf(props.order.status);
  const stepIndex = statusOrder.indexOf(stepKey);
  return stepIndex <= currentIndex;
};

const getStepClass = (stepKey: string): string => {
  if (props.order.status === stepKey) return 'bg-[#D2691E] text-white';
  if (isStepDone(stepKey)) return 'bg-[#D2691E]/30 text-[#D2691E]';
  return 'bg-[#1a1a1a] text-gray-600';
};

// Auto-refresh while order is active
const isActive = computed(() =>
  ['pending', 'confirmed', 'preparing', 'ready', 'delivering'].includes(props.order.status)
);

let refreshInterval: ReturnType<typeof setInterval> | null = null;

watch(isActive, (active) => {
  if (!active && refreshInterval) {
    clearInterval(refreshInterval);
    refreshInterval = null;
  }
});

onMounted(() => {
  if (isActive.value) {
    refreshInterval = setInterval(() => {
      router.reload({ only: ['order'] });
    }, 3000);
  }
});

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});

const getStatusClass = (status: string): string => {
  const classes: Record<string, string> = {
    pending:    'px-3 py-1 rounded-full bg-yellow-900/30 text-yellow-400 text-xs font-semibold',
    confirmed:  'px-3 py-1 rounded-full bg-blue-900/30 text-blue-400 text-xs font-semibold',
    preparing:  'px-3 py-1 rounded-full bg-purple-900/30 text-purple-400 text-xs font-semibold',
    ready:      'px-3 py-1 rounded-full bg-[#D2691E]/20 text-[#D2691E] text-xs font-semibold',
    delivering: 'px-3 py-1 rounded-full bg-cyan-900/30 text-cyan-400 text-xs font-semibold',
    completed:  'px-3 py-1 rounded-full bg-green-900/30 text-green-400 text-xs font-semibold',
    cancelled:  'px-3 py-1 rounded-full bg-gray-800 text-gray-400 text-xs font-semibold',
    rejected:   'px-3 py-1 rounded-full bg-red-900/30 text-red-400 text-xs font-semibold',
  };
  return classes[status] || classes.pending;
};

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    pending:    t('order.status.pending'),
    confirmed:  t('order.status.confirmed'),
    preparing:  t('order.status.preparing'),
    ready:      t('order.status.ready'),
    delivering: t('order.show.delivering'),
    completed:  t('order.status.completed'),
    cancelled:  t('order.status.cancelled'),
    rejected:   t('order.status.rejected'),
  };
  return labels[status] || status;
};

const formatDate = (date: string): string => {
  return new Date(date).toLocaleString(locale.value === 'et' ? 'et-EE' : 'en-GB', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit',
  });
};
</script>

<style scoped>
.arrived-enter-active { transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.arrived-leave-active  { transition: all 0.25s ease; }
.arrived-enter-from    { opacity: 0; transform: scale(0.7); }
.arrived-leave-to      { opacity: 0; transform: scale(0.9); }
</style>