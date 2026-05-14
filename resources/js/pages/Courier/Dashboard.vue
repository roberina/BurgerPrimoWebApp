<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white pt-16 lg:pt-20">
    <Navbar />

    <main class="max-w-xl mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-black">Kulleri töölaud</h1>
        <div class="flex items-center gap-1.5 text-xs"
             :class="sseConnected ? 'text-green-500' : 'text-yellow-500'">
          <span class="w-2 h-2 rounded-full animate-pulse"
                :class="sseConnected ? 'bg-green-500' : 'bg-yellow-500'" />
          {{ sseConnected ? 'Ühendatud' : 'Ühendan...' }}
        </div>
      </div>

      <!-- Online toggle -->
      <div class="rounded-2xl border p-5 mb-8 transition-all"
           :class="isOnline ? 'bg-green-950/40 border-green-700/40' : 'bg-[#111] border-white/8'">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-bold text-lg" :class="isOnline ? 'text-green-400' : 'text-gray-300'">
              {{ isOnline ? 'Oled tööl' : 'Oled offline' }}
            </p>
            <p class="text-sm mt-0.5" :class="isOnline ? 'text-green-600' : 'text-gray-600'">
              {{ isOnline ? 'Saad uusi tellimusi' : 'Kliendid ei näe sind saadavalolekuna' }}
            </p>
          </div>
          <button
            @click="toggleOnline"
            :disabled="toggling"
            class="px-5 py-2.5 rounded-xl font-bold text-sm transition disabled:opacity-50"
            :class="isOnline
              ? 'bg-red-900/40 border border-red-800/50 text-red-400 hover:bg-red-900/70'
              : 'bg-green-950/40 border border-green-700/50 text-green-400 hover:bg-green-900/40'"
          >
            {{ toggling ? '...' : (isOnline ? 'Lõpeta tööpäev' : 'Alusta tööpäevaga') }}
          </button>
        </div>
      </div>

      <!-- Claimed-by-another toast -->
      <Teleport to="body">
        <Transition name="toast-slide">
          <div v-if="claimErrorMsg"
               class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-red-900/90 border border-red-700/60 text-red-200 text-sm font-semibold px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-2">
            <AlertTriangle :size="16" class="text-red-400 shrink-0" />
            {{ claimErrorMsg }}
          </div>
        </Transition>
      </Teleport>

      <!-- Empty state -->
      <div v-if="availableOrders.length === 0 && activeOrders.length === 0 && completedOrders.length === 0"
           class="text-center py-16">
        <Bike :size="48" class="text-zinc-700 mb-4 mx-auto" />
        <p class="text-gray-400 font-semibold">Hetkel pole saadaval ühtegi tellimust</p>
        <p class="text-gray-600 text-sm mt-1">Uued tellimused ilmuvad siia automaatselt</p>
      </div>

      <!-- Available orders -->
      <div v-if="availableOrders.length > 0" class="mb-8">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xs uppercase tracking-widest text-orange-400 font-bold">
            Saadaval ({{ availableOrders.length }})
          </h2>
          <!-- Sort controls -->
          <div class="flex gap-1">
            <button
              v-for="opt in sortOptions"
              :key="opt.value"
              @click="sortBy = opt.value"
              class="text-[10px] px-2.5 py-1 rounded-lg font-semibold transition"
              :class="sortBy === opt.value
                ? 'bg-orange-500/20 text-orange-400 border border-orange-500/30'
                : 'text-gray-600 border border-white/6 hover:border-white/15'"
            >
              {{ opt.label }}
            </button>
          </div>
        </div>

        <TransitionGroup name="order-list" tag="div" class="space-y-3">
          <div
            v-for="order in sortedAvailableOrders"
            :key="order.id"
            class="relative rounded-2xl p-4 border transition-all duration-300"
            :class="[
              newOrderIds.has(order.id)
                ? 'bg-orange-950/30 border-orange-500/50 shadow-orange-900/20 shadow-lg'
                : 'bg-[#111] border-orange-500/20 hover:border-orange-500/40'
            ]"
          >
            <!-- "NEW" badge -->
            <div v-if="newOrderIds.has(order.id)"
                 class="absolute -top-2 left-4 bg-orange-500 text-[#0B0B0B] text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">
              Uus
            </div>

            <div class="flex items-start justify-between mb-3">
              <div>
                <span class="font-mono font-bold text-orange-500 text-base">{{ order.order_number }}</span>
                <p class="text-xs text-gray-600 mt-0.5">{{ timeAgo(order.broadcasted_at) }}</p>
              </div>
              <span class="text-lg font-black text-white">{{ Number(order.total_amount).toFixed(2) }}€</span>
            </div>

            <!-- Items list -->
            <div class="space-y-0.5 mb-3">
              <div v-for="item in order.items" :key="item.burger_name"
                   class="flex items-center justify-between text-sm">
                <span class="text-gray-400">
                  <span class="text-gray-300 font-medium">{{ item.quantity }}×</span>
                  {{ item.burger_name }}
                </span>
                <span class="text-gray-500 text-xs">{{ (item.price * item.quantity).toFixed(2) }}€</span>
              </div>
            </div>

            <!-- Accept button -->
            <button
              @click="claimOrder(order.id)"
              :disabled="claiming === order.id"
              class="w-full py-2.5 rounded-xl font-bold text-sm transition disabled:opacity-60 flex items-center justify-center gap-2"
              style="background: linear-gradient(135deg, #C85A14, #D97020)"
            >
              <Loader2 v-if="claiming === order.id" :size="15" class="animate-spin" />
              <Zap v-else :size="15" />
              {{ claiming === order.id ? 'Võtan...' : 'Võta vastu' }}
            </button>
          </div>
        </TransitionGroup>
      </div>

      <!-- Active orders (picked_up by this courier) -->
      <div v-if="activeOrders.length > 0" class="mb-8">
        <h2 class="text-xs uppercase tracking-widest text-cyan-400 font-bold mb-3">Aktiivsed</h2>
        <div class="space-y-3">
          <Link
            v-for="order in activeOrders"
            :key="order.id"
            :href="`/courier/orders/${order.id}`"
            class="block bg-[#111] border border-white/8 rounded-2xl p-4 hover:border-cyan-500/40 transition"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="font-mono font-bold text-orange-500">{{ order.order_number }}</span>
              <span class="text-xs px-2 py-1 rounded-full bg-cyan-900/40 text-cyan-400 font-semibold">Teel</span>
            </div>
            <p class="text-sm text-gray-400 truncate">{{ order.delivery_address ?? 'Sihtkoht täpsustamata' }}</p>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-gray-600">{{ order.items.length }} ese(t)</span>
              <span class="text-sm font-bold text-white">{{ Number(order.total_amount).toFixed(2) }}€</span>
            </div>
          </Link>
        </div>
      </div>

      <!-- Completed orders -->
      <div v-if="completedOrders.length > 0">
        <h2 class="text-xs uppercase tracking-widest text-gray-600 font-bold mb-3">Lõpetatud</h2>
        <div class="space-y-3">
          <div
            v-for="order in completedOrders"
            :key="order.id"
            class="bg-[#0d0d0d] border border-white/5 rounded-2xl p-4"
          >
            <div class="flex items-center justify-between mb-1">
              <span class="font-mono font-bold text-gray-500">{{ order.order_number }}</span>
              <span class="text-xs px-2 py-1 rounded-full bg-green-900/30 text-green-500 font-semibold">Toimetatud</span>
            </div>
            <p class="text-sm text-gray-600 truncate">{{ order.delivery_address ?? '—' }}</p>
            <div class="flex justify-end mt-1">
              <span class="text-sm font-bold text-gray-500">{{ Number(order.total_amount).toFixed(2) }}€</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { Link, router } from '@inertiajs/vue3';
import { AlertTriangle, Bike, Loader2, Zap } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface OrderItem { burger_name: string; quantity: number; price: number; }
interface Order {
  id: number;
  order_number: string;
  status: string;
  total_amount: number;
  delivery_address?: string | null;
  broadcasted_at?: string | null;
  items: OrderItem[];
}

const props = defineProps<{ orders: Order[]; courierOnline: boolean }>();

const isOnline    = ref(props.courierOnline);
const toggling    = ref(false);
const sseConnected = ref(false);
const claiming    = ref<number | null>(null);
const claimErrorMsg = ref<string | null>(null);

const availableOrders = ref<Order[]>([]);
const newOrderIds     = ref(new Set<number>());

type SortKey = 'time' | 'value';
const sortBy = ref<SortKey>('time');
const sortOptions: { label: string; value: SortKey }[] = [
  { label: 'Uusim', value: 'time' },
  { label: 'Väärtus', value: 'value' },
];

const sortedAvailableOrders = computed(() => {
  const list = [...availableOrders.value];
  if (sortBy.value === 'value') {
    return list.sort((a, b) => b.total_amount - a.total_amount);
  }
  return list.sort((a, b) => {
    const ta = a.broadcasted_at ? new Date(a.broadcasted_at).getTime() : 0;
    const tb = b.broadcasted_at ? new Date(b.broadcasted_at).getTime() : 0;
    return tb - ta;
  });
});

const activeOrders    = computed(() => props.orders.filter(o => o.status === 'picked_up'));
const completedOrders = computed(() => props.orders.filter(o => o.status === 'delivered'));

const csrfToken = (): string =>
  (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';

function timeAgo(iso: string | null | undefined): string {
  if (!iso) return '';
  const diff = Math.floor((Date.now() - new Date(iso).getTime()) / 1000);
  if (diff < 60)  return `${diff}s tagasi`;
  if (diff < 3600) return `${Math.floor(diff / 60)}min tagasi`;
  return `${Math.floor(diff / 3600)}h tagasi`;
}

const toggleOnline = async () => {
  toggling.value = true;
  try {
    const res = await fetch('/courier/toggle-online', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
    });
    const data = await res.json();
    isOnline.value = data.online;
  } catch { /* silent */ }
  toggling.value = false;
};

async function claimOrder(orderId: number) {
  claiming.value = orderId;
  claimErrorMsg.value = null;
  try {
    const res = await fetch(`/courier/orders/${orderId}/accept`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken() },
    });
    if (res.status === 409) {
      const data = await res.json();
      claimErrorMsg.value = data.message ?? 'Tellimus on juba võetud';
      availableOrders.value = availableOrders.value.filter(o => o.id !== orderId);
      newOrderIds.value.delete(orderId);
      setTimeout(() => { claimErrorMsg.value = null; }, 3500);
    } else if (res.ok) {
      router.visit(`/courier/orders/${orderId}`);
    }
  } catch {
    claimErrorMsg.value = 'Ühenduse viga, proovi uuesti';
    setTimeout(() => { claimErrorMsg.value = null; }, 3500);
  }
  claiming.value = null;
}

// ── SSE ─────────────────────────────────────────────────────────────────────
let eventSource: EventSource | null = null;
let refreshInterval: ReturnType<typeof setInterval> | null = null;

function connectSSE() {
  eventSource?.close();
  eventSource = new EventSource('/courier/events');

  eventSource.onopen = () => { sseConnected.value = true; };

  eventSource.addEventListener('order:new', (e: MessageEvent) => {
    const order: Order = JSON.parse(e.data);
    if (!availableOrders.value.find(o => o.id === order.id)) {
      availableOrders.value.unshift(order);
      newOrderIds.value = new Set([...newOrderIds.value, order.id]);
      // Haptic feedback on mobile
      if ('vibrate' in navigator) navigator.vibrate([200, 100, 200]);
      // Clear "new" highlight after 4 s
      setTimeout(() => {
        newOrderIds.value = new Set([...newOrderIds.value].filter(id => id !== order.id));
      }, 4000);
    }
  });

  eventSource.addEventListener('order:removed', (e: MessageEvent) => {
    const { id }: { id: number } = JSON.parse(e.data);
    availableOrders.value = availableOrders.value.filter(o => o.id !== id);
    newOrderIds.value = new Set([...newOrderIds.value].filter(i => i !== id));
  });

  eventSource.onerror = () => {
    sseConnected.value = false;
    // EventSource auto-reconnects per spec; we just reflect the status.
  };
}

/** Fallback JSON poll — syncs available orders in case SSE misses an event. */
async function pollAvailableOrders() {
  try {
    const res  = await fetch('/courier/available-orders');
    const list: Order[] = await res.json();
    const incoming = new Set(list.map(o => o.id));

    // Remove orders no longer available
    availableOrders.value = availableOrders.value.filter(o => incoming.has(o.id));

    // Add any missing
    for (const order of list) {
      if (!availableOrders.value.find(o => o.id === order.id)) {
        availableOrders.value.unshift(order);
      }
    }
  } catch { /* silent */ }
}

onMounted(() => {
  connectSSE();
  // 15-second reconciliation poll as safety net
  refreshInterval = setInterval(() => {
    pollAvailableOrders();
    router.reload({ only: ['orders'] });
  }, 15000);
});

onUnmounted(() => {
  eventSource?.close();
  if (refreshInterval) clearInterval(refreshInterval);
});
</script>

<style scoped>
.order-list-enter-active { transition: all 0.35s ease; }
.order-list-leave-active { transition: all 0.25s ease; }
.order-list-enter-from   { opacity: 0; transform: translateY(-16px); }
.order-list-leave-to     { opacity: 0; transform: translateX(40px); }
.order-list-move         { transition: transform 0.3s ease; }

.toast-slide-enter-active { transition: all 0.3s ease; }
.toast-slide-leave-active { transition: all 0.2s ease; }
.toast-slide-enter-from   { opacity: 0; transform: translateX(-50%) translateY(-12px); }
.toast-slide-leave-to     { opacity: 0; transform: translateX(-50%) translateY(-12px); }
</style>
