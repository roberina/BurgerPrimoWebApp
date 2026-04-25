<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white pt-16 lg:pt-20">
    <Navbar />

    <main class="max-w-xl mx-auto px-4 py-8">
      <h1 class="text-2xl font-black mb-6">Kulleri töölaud</h1>

      <!-- Online toggle -->
      <div class="rounded-2xl border p-5 mb-8 transition-all"
           :class="isOnline
             ? 'bg-green-950/40 border-green-700/40'
             : 'bg-[#111] border-white/8'">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-bold text-lg" :class="isOnline ? 'text-green-400' : 'text-gray-300'">
              {{ isOnline ? 'Oled tööl' : 'Oled offline' }}
            </p>
            <p class="text-sm mt-0.5" :class="isOnline ? 'text-green-600' : 'text-gray-600'">
              {{ isOnline ? 'Kliendid näevad sind saadavalolekuna' : 'Kliendid ei näe sind saadavalolekuna' }}
            </p>
          </div>
          <button
            @click="toggleOnline"
            :disabled="toggling"
            class="px-5 py-2.5 rounded-xl font-bold text-sm transition disabled:opacity-50"
            :class="isOnline
              ? 'bg-red-900/40 border border-red-800/50 text-red-400 hover:bg-red-900/70'
              : 'border border-green-700/50 text-green-400 hover:bg-green-900/30'"
            style="background: isOnline ? '' : 'linear-gradient(135deg, #14532d33, #16653433)'"
          >
            {{ toggling ? '...' : (isOnline ? 'Lõpeta tööpäev' : 'Alusta tööpäevaga') }}
          </button>
        </div>
      </div>

      <div v-if="activeOrders.length === 0 && completedOrders.length === 0"
           class="text-center py-16">
        <div class="text-6xl mb-4">🛵</div>
        <p class="text-gray-400 font-semibold">Hetkel pole sulle ühtegi tellimust määratud</p>
        <p class="text-gray-600 text-sm mt-1">Uued tellimused ilmuvad siia automaatselt</p>
      </div>

      <!-- Aktiivsed tellimused -->
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
              <span class="font-mono font-bold text-[#D2691E]">{{ order.order_number }}</span>
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

      <!-- Lõpetatud tellimused -->
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
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface OrderItem { burger_name: string; quantity: number; price: number; }
interface Order {
  id: number;
  order_number: string;
  status: string;
  total_amount: number;
  delivery_address: string | null;
  items: OrderItem[];
}

const props = defineProps<{ orders: Order[]; courierOnline: boolean }>();

const page = usePage();
const isOnline = ref(props.courierOnline);
const toggling = ref(false);

const activeOrders = computed(() => props.orders.filter(o => o.status === 'delivering'));
const completedOrders = computed(() => props.orders.filter(o => o.status === 'completed'));

const csrfToken = (): string =>
  (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';

const toggleOnline = async () => {
  toggling.value = true;
  try {
    const res = await fetch('/courier/toggle-online', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
    });
    const data = await res.json();
    isOnline.value = data.online;
  } catch { /* vaikne */ }
  toggling.value = false;
};

let refreshInterval: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
  refreshInterval = setInterval(() => {
    router.reload({ only: ['orders'] });
  }, 5000);
});

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});
</script>
