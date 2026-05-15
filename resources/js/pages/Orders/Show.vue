<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white pt-16 lg:pt-20">
    <Navbar />

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

      <!-- ───── KOHALE TOIMETATUD (delivery only) ───── -->
      <template v-if="order.status === 'delivered' && isDeliveryOrder">
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
              {{ t('order.show.order.prefix') }} <span class="font-mono text-[#D2691E] font-bold">{{ order.order_number }}</span>
              {{ t('order.show.delivered.sub') }}
            </p>
          </div>
          <div class="flex gap-3 mt-4 flex-wrap justify-center">
            <button
               @click="reorder"
               class="px-6 py-3 rounded-2xl font-bold text-sm transition cursor-pointer"
               style="background: linear-gradient(135deg, #D2691E, #B8571A); color: white;">
              {{ t('order.show.reorder') }}
            </button>
            <a href="/orders"
               class="px-6 py-3 rounded-2xl font-bold text-sm bg-white/8 hover:bg-white/12 border border-white/10 text-gray-300 transition">
              {{ t('order.show.my_orders') }}
            </a>
          </div>
        </div>
      </template>

      <!-- ───── KOHALETOIMETAMINE: Bolt-laadne vaade ───── -->
      <template v-else-if="order.status === 'picked_up'">

        <!-- Suur kaart -->
        <div class="mb-4 rounded-2xl overflow-hidden shadow-2xl" style="isolation: isolate;">
          <DeliveryMap
            :courier-lat="order.courier_lat ?? null"
            :courier-lng="order.courier_lng ?? null"
            :courier-updated-at="order.courier_updated_at ?? null"
            :delivery-lat="order.delivery_lat ?? null"
            :delivery-lng="order.delivery_lng ?? null"
            :delivery-address="order.delivery_address ?? null"
            :height="typeof window !== 'undefined' && window.innerWidth < 640 ? '240px' : '420px'"
          />
        </div>

        <!-- Kuller on teel info -->
        <div class="mb-6 bg-[#121212] border border-cyan-900/40 rounded-2xl overflow-hidden">
          <div class="bg-cyan-950/40 px-4 sm:px-6 py-4 flex items-center gap-3 sm:gap-4 border-b border-cyan-900/30">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-cyan-500/15 flex items-center justify-center text-xl sm:text-2xl shrink-0">🛵</div>
            <div class="flex-1 min-w-0">
              <p class="font-bold text-base sm:text-lg text-white">{{ t('order.show.courier.title') }}</p>
              <p class="text-xs sm:text-sm text-cyan-400 truncate">{{ t('order.show.order.nr') }}
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
          <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
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
                <p class="font-semibold text-sm">{{ ln(item.burger_name, item.cart_data?.burger_name_en) }}</p>
                <p class="text-xs text-gray-500">{{ item.quantity }} {{ t('order.show.qty') }}</p>
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

      <!-- ───── TAVALINE VAADE (kõik muud staatused + pickup completed) ───── -->
      <template v-else>

      <!-- Banner: cancelled -->
      <div v-if="isCancelled" class="mb-8 rounded-2xl p-5 sm:p-8 text-center bg-red-900/20 border border-red-800/50">
        <div class="text-6xl mb-4">❌</div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2 text-red-400">{{ t('order.show.cancelled') }}</h1>
        <p class="text-gray-400">{{ t('order.show.number') }}
          <span class="font-mono font-bold text-[#D2691E]">{{ order.order_number }}</span>
        </p>
      </div>
      <!-- Banner: active/pending orders -->
      <div v-else-if="order.status !== 'delivered'" class="mb-8 rounded-2xl p-5 sm:p-8 text-center bg-green-900/20 border border-green-800/50">
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ t('order.show.submitted') }}</h1>
        <p class="text-gray-400">{{ t('order.show.number') }}
          <span class="font-mono font-bold text-[#D2691E]">{{ order.order_number }}</span>
        </p>
      </div>
      <!-- Compact header for completed pickup orders -->
      <div v-else class="mb-6 flex items-center gap-3">
        <h1 class="text-xl font-bold text-white">{{ t('order.show.number') }} <span class="font-mono text-[#D2691E]">{{ order.order_number }}</span></h1>
        <span class="px-2.5 py-1 rounded-full bg-green-900/30 text-green-400 text-xs font-semibold">{{ t('order.step.completed') }}</span>
      </div>

      <!-- Order Card -->
      <div class="bg-[#121212] rounded-2xl overflow-hidden border border-[#1a1a1a]">

        <!-- Status Bar -->
        <div class="bg-[#0d0d0d] px-4 sm:px-6 py-4 flex items-center justify-between border-b border-[#1a1a1a]">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('order.show.submitted.label') }}</p>
            <p class="text-sm text-gray-300 font-medium">{{ formatDate(order.created_at) }}</p>
          </div>
        </div>

        <div class="p-4 sm:p-6 space-y-6">

          <!-- Status progress indicator -->
          <div class="flex items-center gap-1">
            <div
              v-for="(step, i) in statusSteps"
              :key="step.key"
              class="flex items-center gap-1 flex-1"
            >
              <div class="flex flex-col items-center flex-1">
                <div
                  class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                  :class="getStepClass(step.key)"
                >
                  <svg v-if="isStepDone(step.key)" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                  </svg>
                  <span v-else>{{ i + 1 }}</span>
                </div>
                <p class="hidden sm:block text-xs text-gray-500 mt-1 text-center leading-tight">{{ step.label }}</p>
              </div>
              <div
                v-if="i < statusSteps.length - 1"
                class="h-0.5 flex-1 sm:mb-4 transition-all"
                :class="isStepDone(step.key) && !isCancelled ? 'bg-[#D2691E]' : isCancelled && isStepDone(step.key) ? 'bg-[#D2691E]/30' : 'bg-[#1a1a1a]'"
              ></div>
            </div>
          </div>
          <!-- Mobile step label -->
          <p class="sm:hidden text-xs text-center text-[#D2691E] font-semibold mt-1">
            {{ statusSteps.find(s => s.key === order.status)?.label ?? statusSteps[statusSteps.length - 1]?.label }}
          </p>

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
                    <p class="font-semibold">{{ ln(item.burger_name, item.cart_data?.burger_name_en) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ item.quantity }} {{ t('order.show.qty') }}</p>
                    <div v-if="item.ingredients?.length" class="mt-2 space-y-0.5">
                      <p v-for="(ingredient, index) in item.ingredients" :key="index" class="text-xs text-gray-500">
                        {{ ingredient.quantity }}x {{ ln(ingredient.name, ingredient.name_en) }}
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

      <!-- Auto refresh notice — only while order is still active -->
      <p v-if="order.status !== 'delivered'" class="text-center text-xs text-gray-600 mt-4">
        {{ t('order.show.auto_refresh') }}
      </p>

      <!-- Actions -->
      <div class="mt-6 flex gap-4">
        <button
          @click="reorder"
          class="flex-1 text-white px-6 py-3.5 rounded-xl font-semibold transition hover:opacity-90 text-center cursor-pointer"
          style="background-color: #D2691E"
        >
          {{ t('order.show.order_more') }}
        </button>
        <Link
          href="/orders"
          class="flex-1 bg-[#121212] hover:bg-[#1a1a1a] text-white px-6 py-3.5 rounded-xl font-semibold transition text-center border border-[#1a1a1a]"
        >
          {{ t('order.show.all_orders') }}
        </Link>
      </div>

      </template>

    </main>

    <!-- Kulleri kohalejõudmise teatis -->
    <Transition name="arrived">
      <div v-if="showArrivedBanner"
           class="fixed inset-0 flex items-center justify-center cursor-pointer"
           style="z-index: 9999; background: rgba(0,0,0,0.88);"
           @click="dismissArrivedBanner">
        <div class="text-center rounded-3xl border border-green-500/30 p-10 cursor-pointer"
             style="background: linear-gradient(135deg, #061206, #0a1f0a); max-width: 360px; width: 90%; box-shadow: 0 0 80px rgba(34,197,94,0.25);"
             @click.stop>
          <div class="text-8xl mb-2" style="filter: drop-shadow(0 0 40px rgba(34,197,94,0.8))">🛵</div>
          <div class="text-5xl mb-4">🍔</div>
          <h2 class="text-3xl font-black text-white mb-2">{{ t('order.show.arrived.title') }}</h2>
          <p class="text-green-400 font-semibold text-lg mb-1">{{ t('order.show.arrived.sub') }}</p>
          <p class="text-gray-500 text-sm mb-8">
            {{ t('order.show.order.prefix') }} <span class="font-mono text-[#D2691E] font-bold cursor-pointer">{{ order.order_number }}</span>
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
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t, locale } = useI18n();
const ln = (et: string | undefined, en: string | null | undefined) => (locale.value === 'en' && en) ? en : (et ?? '');

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
  name_en?: string | null;
  quantity: number;
}

interface OrderItem {
  id: number;
  burger_name: string;
  price: number;
  quantity: number;
  ingredients: Ingredient[];
  cart_data?: { burger_name_en?: string } | null;
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
  cancelled_from_status?: string | null;
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
  if (oldStatus === 'picked_up' && newStatus === 'delivered') {
    showArrivedBanner.value = true;
  }
});

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


// Status progress steps — computed so labels react to locale changes
// The "delivering" step is only shown for delivery orders
const isDeliveryOrder = computed(() => props.order.delivery_method === 'delivery');

const statusSteps = computed(() => {
  const steps = [
    { key: 'pending_confirmation', label: t('order.step.pending') },
    { key: 'confirmed',            label: t('order.step.confirmed') },
    { key: 'preparing',            label: t('order.step.preparing') },
    { key: 'ready',                label: t('order.step.ready') },
    { key: 'delivered',            label: t('order.step.completed') },
  ];
  if (isDeliveryOrder.value) {
    steps.splice(4, 0, { key: 'picked_up', label: t('order.step.delivering') });
  }
  return steps;
});

const statusOrder = ['pending_confirmation', 'confirmed', 'preparing', 'ready', 'awaiting_courier', 'picked_up', 'delivered', 'cancelled', 'refunded'];

const isCancelled = computed(() =>
  props.order.status === 'cancelled' || props.order.status === 'refunded'
);

const isStepDone = (stepKey: string): boolean => {
  if (isCancelled.value) {
    const fromStatus = props.order.cancelled_from_status;
    if (!fromStatus) return false;
    const cancelledAtIndex = statusOrder.indexOf(fromStatus);
    const stepIndex = statusOrder.indexOf(stepKey);
    return stepIndex < cancelledAtIndex;
  }
  const currentIndex = statusOrder.indexOf(props.order.status);
  const stepIndex = statusOrder.indexOf(stepKey);
  return stepIndex <= currentIndex;
};

const getStepClass = (stepKey: string): string => {
  if (isCancelled.value) {
    const fromStatus = props.order.cancelled_from_status;
    if (stepKey === fromStatus) return 'bg-red-700 text-white';
    if (isStepDone(stepKey)) return 'bg-[#D2691E]/30 text-[#D2691E]';
    return 'bg-[#1a1a1a] text-gray-600';
  }
  if (props.order.status === stepKey) return 'bg-[#D2691E] text-white';
  if (isStepDone(stepKey)) return 'bg-[#D2691E]/30 text-[#D2691E]';
  return 'bg-[#1a1a1a] text-gray-600';
};

// Auto-refresh while order is active
const isActive = computed(() =>
  ['pending_confirmation', 'confirmed', 'preparing', 'ready', 'awaiting_courier', 'picked_up'].includes(props.order.status)
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
    pending_confirmation: 'px-3 py-1 rounded-full bg-yellow-900/30 text-yellow-400 text-xs font-semibold',
    confirmed:            'px-3 py-1 rounded-full bg-blue-900/30 text-blue-400 text-xs font-semibold',
    preparing:            'px-3 py-1 rounded-full bg-purple-900/30 text-purple-400 text-xs font-semibold',
    ready:                'px-3 py-1 rounded-full bg-[#D2691E]/20 text-[#D2691E] text-xs font-semibold',
    awaiting_courier:     'px-3 py-1 rounded-full bg-orange-900/30 text-orange-400 text-xs font-semibold',
    picked_up:            'px-3 py-1 rounded-full bg-cyan-900/30 text-cyan-400 text-xs font-semibold',
    delivered:            'px-3 py-1 rounded-full bg-green-900/30 text-green-400 text-xs font-semibold',
    cancelled:            'px-3 py-1 rounded-full bg-gray-800 text-gray-400 text-xs font-semibold',
    refunded:             'px-3 py-1 rounded-full bg-red-900/30 text-red-400 text-xs font-semibold',
  };
  return classes[status] || classes.pending_confirmation;
};

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    pending_confirmation: t('order.status.pending'),
    confirmed:            t('order.status.confirmed'),
    preparing:            t('order.status.preparing'),
    ready:                t('order.status.ready'),
    awaiting_courier:     t('order.status.awaiting_courier'),
    picked_up:            t('order.show.delivering'),
    delivered:            t('order.status.completed'),
    cancelled:            t('order.status.cancelled'),
    refunded:             t('order.status.rejected'),
  };
  return labels[status] || status;
};

const reorder = () => {
  router.post(`/orders/${props.order.id}/reorder` as any, {});
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