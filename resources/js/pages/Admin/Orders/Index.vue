<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
  Clock,
  Check,
  X,
  Bell,
  Home,
  ShoppingBag,
  ChefHat,
  AlertCircle,
  User,
  RefreshCw,
  Bike,
  Copy,
} from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { success } = useToast();
const page = usePage();
const origin = typeof window !== 'undefined' ? window.location.origin : '';

const confirmModal = reactive({ show: false, title: '', message: '', confirmLabel: 'Kinnita', onConfirm: () => {} });
const rejectModal = reactive({ show: false, orderId: null as number | null, reason: '' });
const courierLinkModal = reactive({ show: false, link: '' });
const copiedLink = ref(false);

const openConfirmModal = (opts: Omit<typeof confirmModal, 'show'>) => {
  Object.assign(confirmModal, { show: true, ...opts });
};

const openRejectModal = (id: number) => {
  rejectModal.orderId = id;
  rejectModal.reason = '';
  rejectModal.show = true;
};

const submitRejectModal = () => {
  if (!rejectModal.orderId) return;
  router.post(`/admin/orders/${rejectModal.orderId}/refund` as any, {
    admin_notes: rejectModal.reason || 'Tellimus lükati tagasi',
  }, { preserveScroll: true });
  rejectModal.show = false;
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(courierLinkModal.link);
    copiedLink.value = true;
    setTimeout(() => (copiedLink.value = false), 2000);
  } catch {
    const el = document.createElement('textarea');
    el.value = courierLinkModal.link;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    copiedLink.value = true;
    setTimeout(() => (copiedLink.value = false), 2000);
  }
};

interface User {
  id: number;
  name: string;
  email: string;
}

interface Ingredient {
  name: string;
  quantity: number;
  price?: number;
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
  user: User;
  total_amount: number;
  status: string;
  created_at: string;
  customer_notes?: string | null;
  delivery_method?: string;
  items: OrderItem[];
  courier_token?: string | null;
  courier_lat?: number | null;
  courier_lng?: number | null;
  courier_updated_at?: string | null;
  courier_user?: { id: number; name: string; email: string } | null;
}

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface Props {
  orders: { data: Order[]; links: PaginationLink[] };
  stats?: any;
  filters?: any;
}

const props = defineProps<Props>();

const orderStatuses = reactive<Record<number, string>>({});
const activeFilter = ref<string>('all');
const audioContext = ref<AudioContext | null>(null);
const previousOrderCount = ref<number>(props.orders.data.filter(o => o.status === 'pending_confirmation').length);
const refreshInterval = ref<number | null>(null);
const isRefreshing = ref<boolean>(false);

props.orders.data.forEach(order => { orderStatuses[order.id] = order.status; });

const filteredOrders = computed(() => {
  if (activeFilter.value === 'all') return props.orders.data;
  if (activeFilter.value === 'active') {
    return props.orders.data.filter(o =>
      ['pending_confirmation', 'confirmed', 'preparing', 'ready', 'awaiting_courier', 'picked_up'].includes(o.status),
    );
  }
  return props.orders.data.filter(o => o.status === activeFilter.value);
});

const pendingOrders = computed(() => props.orders.data.filter(o => o.status === 'pending_confirmation'));
const confirmedOrders = computed(() => props.orders.data.filter(o => o.status === 'confirmed'));
const preparingOrders = computed(() => props.orders.data.filter(o => o.status === 'preparing'));
const readyOrders = computed(() => props.orders.data.filter(o => o.status === 'ready'));
const awaitingCourierOrders = computed(() => props.orders.data.filter(o => o.status === 'awaiting_courier'));
const pickedUpOrders = computed(() => props.orders.data.filter(o => o.status === 'picked_up'));

const playNotificationSound = () => {
  try {
    if (!audioContext.value) {
      audioContext.value = new (window.AudioContext || (window as any).webkitAudioContext)();
    }
    const ctx = audioContext.value;
    const oscillator = ctx.createOscillator();
    const gainNode = ctx.createGain();
    oscillator.connect(gainNode);
    gainNode.connect(ctx.destination);
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    gainNode.gain.setValueAtTime(0.3, ctx.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
    oscillator.start(ctx.currentTime);
    oscillator.stop(ctx.currentTime + 0.5);
    setTimeout(() => {
      const osc2 = ctx.createOscillator();
      const gain2 = ctx.createGain();
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.frequency.value = 1000;
      osc2.type = 'sine';
      gain2.gain.setValueAtTime(0.3, ctx.currentTime);
      gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
      osc2.start(ctx.currentTime);
      osc2.stop(ctx.currentTime + 0.5);
    }, 200);
  } catch (e) {
    console.error('Error playing notification sound:', e);
  }
};

const refreshOrders = () => {
  isRefreshing.value = true;
  router.reload({
    only: ['orders'],
    onSuccess: (p: any) => {
      const newPendingCount = p.props.orders.data.filter((o: Order) => o.status === 'pending_confirmation').length;
      if (newPendingCount > previousOrderCount.value) {
        playNotificationSound();
        if ('Notification' in window && Notification.permission === 'granted') {
          new Notification('Uus tellimus!', {
            body: `${newPendingCount} uut tellimust ootab kinnitamist`,
            icon: '/favicon.svg',
            tag: 'new-order',
          });
        }
      }
      previousOrderCount.value = newPendingCount;
      isRefreshing.value = false;
    },
    onError: () => { isRefreshing.value = false; },
  });
};

const requestNotificationPermission = () => {
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
};

watch(
  () => (page.props.flash as any)?.courier_link,
  (link) => {
    if (link) {
      courierLinkModal.link = link;
      courierLinkModal.show = true;
    }
  },
  { immediate: true },
);

onMounted(() => {
  requestNotificationPermission();
  refreshInterval.value = window.setInterval(() => { refreshOrders(); }, 10000);
});

onUnmounted(() => {
  if (refreshInterval.value) clearInterval(refreshInterval.value);
  if (audioContext.value) audioContext.value.close();
});

const confirmOrder = (orderId: number) => router.post(`/admin/orders/${orderId}/confirm`, {}, { preserveScroll: true });
const startPreparing = (orderId: number) => router.post(`/admin/orders/${orderId}/start-preparing`, {}, { preserveScroll: true });
const markReady = (orderId: number) => router.post(`/admin/orders/${orderId}/mark-ready`, {}, { preserveScroll: true });
const releaseToCouriers = (orderId: number) => router.post(`/admin/orders/${orderId}/release-to-couriers`, {}, { preserveScroll: true });
const recallFromCouriers = (orderId: number) => router.post(`/admin/orders/${orderId}/recall-from-couriers`, {}, { preserveScroll: true });

const getTimeSince = (date: string): string => {
  const diffMins = Math.floor((Date.now() - new Date(date).getTime()) / 60000);
  if (diffMins < 1) return 'Praegu';
  if (diffMins < 60) return `${diffMins}min tagasi`;
  return `${Math.floor(diffMins / 60)}h tagasi`;
};

const formatTime = (date: string): string =>
  new Date(date).toLocaleString('et-EE', { hour: '2-digit', minute: '2-digit' });

const setFilter = (filter: string) => { activeFilter.value = filter; };

const deliveryLabel = (method?: string) =>
  method === 'dine_in' ? 'Kohapeal' : method === 'delivery' ? 'Telli koju' : 'Kaasa';
const deliveryIcon = (method?: string) =>
  method === 'dine_in' ? Home : method === 'delivery' ? Bike : ShoppingBag;
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Tellimused</h1>
          <p class="text-xs text-zinc-500">Reaalajas tellimuste haldamine</p>
        </div>
      </div>
    </template>

    <!-- Confirm modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="confirmModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4" @click.self="confirmModal.show = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">{{ confirmModal.title }}</h3>
              <p class="text-xs text-zinc-400">{{ confirmModal.message }}</p>
              <div class="flex gap-2 mt-5">
                <button @click="confirmModal.show = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="confirmModal.onConfirm(); confirmModal.show = false" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">{{ confirmModal.confirmLabel }}</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Reject modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="rejectModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4" @click.self="rejectModal.show = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Tagasilükkamise põhjus</h3>
              <p class="text-xs text-zinc-400 mb-3">Valikuline — kuvatakse kliendile</p>
              <textarea
                v-model="rejectModal.reason"
                placeholder="nt. Toode on otsas..."
                rows="3"
                class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors resize-none"
              />
              <div class="flex gap-2 mt-4">
                <button @click="rejectModal.show = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="submitRejectModal" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Lükka tagasi</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Auto-refresh bar -->
    <div class="mb-4 flex items-center justify-between bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-2.5">
      <div class="flex items-center gap-2">
        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
        <span class="text-xs text-zinc-500">Automaatne uuendamine (iga 10 sek)</span>
      </div>
      <button
        @click="refreshOrders"
        :disabled="isRefreshing"
        class="text-xs text-orange-400 hover:text-orange-300 font-medium transition flex items-center gap-1.5 disabled:opacity-50"
      >
        <RefreshCw :size="13" :class="{ 'animate-spin': isRefreshing }" />
        {{ isRefreshing ? 'Uuendamine...' : 'Uuenda kohe' }}
      </button>
    </div>

    <!-- Filter stat cards -->
    <div class="bg-[#18181b] border border-[#27272a] rounded-lg p-4 mb-5">
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2">
        <!-- All -->
        <button
          @click="setFilter('all')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'all' ? 'border-orange-500/50 bg-orange-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <Bell :size="15" :class="activeFilter === 'all' ? 'text-orange-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'all' ? 'text-orange-400' : 'text-zinc-100']">{{ orders.data.length }}</span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'all' ? 'text-orange-400' : 'text-zinc-500']">Kõik</p>
        </button>

        <!-- Active -->
        <button
          @click="setFilter('active')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'active' ? 'border-orange-500/50 bg-orange-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <ChefHat :size="15" :class="activeFilter === 'active' ? 'text-orange-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'active' ? 'text-orange-400' : 'text-zinc-100']">
              {{ pendingOrders.length + confirmedOrders.length + preparingOrders.length + readyOrders.length }}
            </span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'active' ? 'text-orange-400' : 'text-zinc-500']">Aktiivsed</p>
        </button>

        <!-- Pending -->
        <button
          @click="setFilter('pending_confirmation')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'pending_confirmation' ? 'border-orange-500/50 bg-orange-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <Bell :size="15" :class="activeFilter === 'pending_confirmation' ? 'text-orange-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'pending_confirmation' ? 'text-orange-400' : 'text-zinc-100']">{{ pendingOrders.length }}</span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'pending_confirmation' ? 'text-orange-400' : 'text-zinc-500']">Uued</p>
        </button>

        <!-- Confirmed -->
        <button
          @click="setFilter('confirmed')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'confirmed' ? 'border-orange-500/50 bg-orange-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <Check :size="15" :class="activeFilter === 'confirmed' ? 'text-orange-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'confirmed' ? 'text-orange-400' : 'text-zinc-100']">{{ confirmedOrders.length }}</span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'confirmed' ? 'text-orange-400' : 'text-zinc-500']">Kinnitatud</p>
        </button>

        <!-- Preparing -->
        <button
          @click="setFilter('preparing')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'preparing' ? 'border-orange-500/50 bg-orange-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <ChefHat :size="15" :class="activeFilter === 'preparing' ? 'text-orange-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'preparing' ? 'text-orange-400' : 'text-zinc-100']">{{ preparingOrders.length }}</span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'preparing' ? 'text-orange-400' : 'text-zinc-500']">Valmistamisel</p>
        </button>

        <!-- Ready -->
        <button
          @click="setFilter('ready')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'ready' ? 'border-green-500/50 bg-green-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <Check :size="15" :class="activeFilter === 'ready' ? 'text-green-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'ready' ? 'text-green-400' : 'text-zinc-100']">{{ readyOrders.length }}</span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'ready' ? 'text-green-400' : 'text-zinc-500']">Valmis</p>
        </button>

        <!-- Picked up -->
        <button
          @click="setFilter('picked_up')"
          :class="['p-3 rounded-lg border-2 transition-all text-left', activeFilter === 'picked_up' ? 'border-cyan-500/50 bg-cyan-500/10' : 'border-[#27272a] bg-[#09090b] hover:border-[#3f3f46]']"
        >
          <div class="flex items-center justify-between mb-1.5">
            <Bike :size="15" :class="activeFilter === 'picked_up' ? 'text-cyan-400' : 'text-zinc-500'" />
            <span :class="['text-lg font-bold', activeFilter === 'picked_up' ? 'text-cyan-400' : 'text-zinc-100']">{{ pickedUpOrders.length }}</span>
          </div>
          <p :class="['text-xs font-medium', activeFilter === 'picked_up' ? 'text-cyan-400' : 'text-zinc-500']">Teel</p>
        </button>
      </div>
    </div>

    <!-- Active filter label -->
    <div v-if="activeFilter !== 'all'" class="mb-4 flex items-center justify-between bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-2.5">
      <div class="flex items-center gap-2">
        <span class="text-xs text-zinc-500">Näitan:</span>
        <span class="text-xs font-medium text-orange-400">
          {{ activeFilter === 'active' ? 'Aktiivsed' :
             activeFilter === 'pending_confirmation' ? 'Uued' :
             activeFilter === 'confirmed' ? 'Kinnitatud' :
             activeFilter === 'preparing' ? 'Valmistamisel' :
             activeFilter === 'ready' ? 'Valmis' :
             activeFilter === 'awaiting_courier' ? 'Ootab kullerit' :
             activeFilter === 'picked_up' ? 'Teel' : '' }}
        </span>
        <span class="text-xs text-zinc-600">({{ filteredOrders.length }})</span>
      </div>
      <button @click="setFilter('all')" class="text-xs text-zinc-400 hover:text-zinc-100 transition">Tühista filter</button>
    </div>

    <!-- Orders -->
    <div v-if="filteredOrders.length > 0" class="space-y-6">

      <!-- UUED -->
      <div v-if="filteredOrders.some(o => o.status === 'pending_confirmation')">
        <div class="flex items-center gap-3 mb-3">
          <div class="h-px flex-1 bg-orange-500/20"></div>
          <span class="text-xs font-semibold text-orange-400 flex items-center gap-1.5">
            <Bell :size="13" />
            UUED ({{ filteredOrders.filter(o => o.status === 'pending_confirmation').length }})
          </span>
          <div class="h-px flex-1 bg-orange-500/20"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div
            v-for="order in filteredOrders.filter(o => o.status === 'pending_confirmation')"
            :key="order.id"
            class="bg-[#18181b] rounded-lg border-2 border-orange-500/30 overflow-hidden hover:border-orange-500/50 transition-colors"
          >
            <div class="bg-orange-500/5 px-4 py-3 border-b border-[#27272a]">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-lg font-bold text-orange-400">#{{ order.order_number }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-orange-600 text-white text-[10px] font-bold uppercase">Uus</span>
                  </div>
                  <div class="flex items-center gap-3 text-xs">
                    <div class="flex items-center gap-1.5">
                      <User :size="12" class="text-zinc-500" />
                      <span class="text-zinc-200 font-medium">{{ order.user.name }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                      <Clock :size="12" class="text-zinc-500" />
                      <span class="text-zinc-400">{{ formatTime(order.created_at) }}</span>
                      <span class="text-orange-400 font-medium">({{ getTimeSince(order.created_at) }})</span>
                    </div>
                  </div>
                </div>
                <div class="text-right flex-shrink-0">
                  <p class="text-[10px] text-zinc-500 mb-0.5">KOKKU</p>
                  <p class="text-xl font-bold text-orange-400">€{{ Number(order.total_amount).toFixed(2) }}</p>
                </div>
              </div>
              <div class="mt-2 flex items-center gap-1.5">
                <component :is="deliveryIcon(order.delivery_method)" :size="13" class="text-zinc-500" />
                <span class="text-xs text-zinc-400">{{ deliveryLabel(order.delivery_method) }}</span>
              </div>
            </div>

            <div class="p-4">
              <p class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider mb-2">Tooted</p>
              <div class="space-y-3">
                <div v-for="item in order.items" :key="item.id" class="bg-[#09090b] rounded-md p-3 border border-[#27272a]">
                  <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
                      <span class="text-base font-bold text-orange-400">{{ item.quantity }}x</span>
                      <h4 class="text-sm font-semibold text-zinc-100">{{ item.burger_name }}</h4>
                    </div>
                    <p class="text-sm font-bold text-orange-400">€{{ Number(item.price * item.quantity).toFixed(2) }}</p>
                  </div>
                  <div class="bg-[#18181b] rounded p-2 border border-[#27272a]">
                    <p class="text-[10px] font-semibold text-zinc-600 uppercase mb-1.5">Koostis</p>
                    <div class="grid grid-cols-2 gap-1">
                      <div v-for="(ing, idx) in item.ingredients" :key="idx" class="flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-orange-500 flex-shrink-0"></span>
                        <span class="text-xs text-zinc-300">
                          <span v-if="ing.quantity > 1" class="font-semibold text-zinc-100">{{ ing.quantity }}x </span>
                          {{ ing.name }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="order.customer_notes" class="mt-3 bg-orange-500/10 border border-orange-500/20 rounded-md p-3">
                <div class="flex items-start gap-2">
                  <AlertCircle :size="14" class="text-orange-400 flex-shrink-0 mt-0.5" />
                  <div>
                    <p class="text-[10px] font-semibold text-orange-400 uppercase mb-1">Märkused</p>
                    <p class="text-xs text-zinc-200">"{{ order.customer_notes }}"</p>
                  </div>
                </div>
              </div>

              <div class="mt-3 flex gap-2">
                <button
                  @click="confirmOrder(order.id)"
                  class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-md text-sm font-semibold transition flex items-center justify-center gap-1.5"
                >
                  <Check :size="16" />
                  Kinnita
                </button>
                <button
                  @click="openRejectModal(order.id)"
                  class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-4 py-2.5 rounded-md text-sm font-semibold transition flex items-center justify-center"
                  title="Tagasilükka ja tagasta"
                >
                  <X :size="16" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- VALMISTAMISEL -->
      <div v-if="filteredOrders.some(o => ['confirmed', 'preparing'].includes(o.status))">
        <div class="flex items-center gap-3 mb-3">
          <div class="h-px flex-1 bg-[#27272a]"></div>
          <span class="text-xs font-semibold text-zinc-400 flex items-center gap-1.5">
            <ChefHat :size="13" />
            VALMISTAMISEL ({{ filteredOrders.filter(o => ['confirmed', 'preparing'].includes(o.status)).length }})
          </span>
          <div class="h-px flex-1 bg-[#27272a]"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div
            v-for="order in filteredOrders.filter(o => ['confirmed', 'preparing'].includes(o.status))"
            :key="order.id"
            class="bg-[#18181b] rounded-lg border border-[#27272a] overflow-hidden hover:border-[#3f3f46] transition-colors"
          >
            <div class="px-4 py-3 border-b border-[#27272a] bg-[#09090b]">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <span class="text-base font-bold text-zinc-100">#{{ order.order_number }}</span>
                  <span class="text-xs text-zinc-400">{{ order.user.name }}</span>
                  <component :is="deliveryIcon(order.delivery_method)" :size="13" class="text-zinc-600" />
                </div>
                <div class="flex items-center gap-3">
                  <span class="text-xs text-zinc-500">{{ getTimeSince(order.created_at) }}</span>
                  <span class="text-base font-bold text-zinc-100">€{{ Number(order.total_amount).toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <div class="p-4">
              <div class="space-y-2 mb-3">
                <div v-for="item in order.items" :key="item.id" class="bg-[#09090b] rounded-md p-3 border border-[#27272a]">
                  <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-medium text-zinc-100">
                      <span class="text-orange-400 font-bold">{{ item.quantity }}x</span>
                      {{ item.burger_name }}
                    </span>
                  </div>
                  <div class="flex flex-wrap gap-1.5">
                    <span v-for="(ing, idx) in item.ingredients" :key="idx" class="text-xs bg-[#27272a] px-2 py-0.5 rounded text-zinc-400">
                      <span v-if="ing.quantity > 1" class="font-semibold">{{ ing.quantity }}x </span>{{ ing.name }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex gap-2">
                <button
                  v-if="order.status === 'confirmed'"
                  @click="startPreparing(order.id)"
                  class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-md font-medium transition text-xs flex items-center justify-center gap-1"
                >
                  <ChefHat :size="13" /> Alusta valmistamist
                </button>
                <button
                  v-if="order.status === 'preparing'"
                  @click="markReady(order.id)"
                  class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md font-medium transition text-xs flex items-center justify-center gap-1"
                >
                  <Check :size="13" /> Märgi valmis
                </button>
                <button
                  @click="openRejectModal(order.id)"
                  class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-2 rounded-md text-xs font-medium transition flex items-center justify-center"
                  title="Tagasilükka ja tagasta"
                >
                  <X :size="13" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- VALMIS -->
      <div v-if="filteredOrders.some(o => o.status === 'ready')">
        <div class="flex items-center gap-3 mb-3">
          <div class="h-px flex-1 bg-green-500/20"></div>
          <span class="text-xs font-semibold text-green-400 flex items-center gap-1.5">
            <Check :size="13" />
            VALMIS ({{ filteredOrders.filter(o => o.status === 'ready').length }})
          </span>
          <div class="h-px flex-1 bg-green-500/20"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div
            v-for="order in filteredOrders.filter(o => o.status === 'ready')"
            :key="order.id"
            class="bg-[#18181b] rounded-lg border-2 border-green-500/30 overflow-hidden hover:border-green-500/50 transition-colors"
          >
            <div class="bg-green-500/5 px-4 py-3 border-b border-[#27272a]">
              <div class="flex items-center justify-between">
                <span class="text-base font-bold text-green-400">#{{ order.order_number }}</span>
                <component :is="deliveryIcon(order.delivery_method)" :size="15" class="text-zinc-500" />
              </div>
              <p class="text-sm text-zinc-200 mt-0.5">{{ order.user.name }}</p>
            </div>
            <div class="p-4">
              <p class="text-xs text-zinc-500 mb-1">{{ order.items.length }} toode(t)</p>
              <p class="text-xl font-bold text-green-400 mb-3">€{{ Number(order.total_amount).toFixed(2) }}</p>
              <div class="flex gap-2">
                <button
                  v-if="order.delivery_method === 'delivery'"
                  @click="releaseToCouriers(order.id)"
                  class="flex-1 bg-cyan-500/10 hover:bg-cyan-600 border border-cyan-500/30 hover:border-cyan-600 text-cyan-400 hover:text-white px-3 py-2 rounded-md font-medium transition text-xs flex items-center justify-center gap-1"
                >
                  <Bike :size="13" /> Saada kullerile
                </button>
                <button
                  v-else
                  @click="openRejectModal(order.id)"
                  class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-2 rounded-md text-xs font-medium transition flex items-center justify-center"
                  title="Tagasilükka ja tagasta"
                >
                  <X :size="13" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- OOTAB KULLERIT -->
      <div v-if="filteredOrders.some(o => o.status === 'awaiting_courier')">
        <div class="flex items-center gap-3 mb-3">
          <div class="h-px flex-1 bg-orange-400/20"></div>
          <span class="text-xs font-semibold text-orange-400 flex items-center gap-1.5">
            <Bike :size="13" />
            OOTAB KULLERIT ({{ filteredOrders.filter(o => o.status === 'awaiting_courier').length }})
          </span>
          <div class="h-px flex-1 bg-orange-400/20"></div>
        </div>

        <div class="space-y-3">
          <div
            v-for="order in filteredOrders.filter(o => o.status === 'awaiting_courier')"
            :key="order.id"
            class="bg-[#18181b] rounded-lg border-2 border-orange-400/20 overflow-hidden"
          >
            <div class="bg-orange-400/5 px-4 py-3 border-b border-[#27272a] flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-base font-bold text-orange-400">#{{ order.order_number }}</span>
                <span class="text-sm text-zinc-400">{{ order.user.name }}</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                  <div class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></div>
                  <span class="text-xs text-orange-400 font-medium">Ootab kulleri vastuvõttu</span>
                </div>
                <span class="text-base font-bold text-zinc-100">€{{ Number(order.total_amount).toFixed(2) }}</span>
              </div>
            </div>
            <div class="p-4">
              <div class="space-y-1 mb-3">
                <div v-for="item in order.items" :key="item.id" class="text-xs text-zinc-400">
                  <span class="text-orange-400 font-semibold">{{ item.quantity }}x</span> {{ item.burger_name }}
                </div>
              </div>
              <button
                @click="recallFromCouriers(order.id)"
                class="w-full bg-orange-600/20 hover:bg-orange-600 border border-orange-600/40 hover:border-orange-600 text-orange-400 hover:text-white px-4 py-2 rounded-md font-medium transition text-sm flex items-center justify-center gap-1.5"
              >
                <X :size="14" /> Tühista kulleri ootamine
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- TEEL -->
      <div v-if="filteredOrders.some(o => o.status === 'picked_up')">
        <div class="flex items-center gap-3 mb-3">
          <div class="h-px flex-1 bg-cyan-500/20"></div>
          <span class="text-xs font-semibold text-cyan-400 flex items-center gap-1.5">
            <Bike :size="13" />
            TEEL ({{ filteredOrders.filter(o => o.status === 'picked_up').length }})
          </span>
          <div class="h-px flex-1 bg-cyan-500/20"></div>
        </div>

        <div class="space-y-4">
          <div
            v-for="order in filteredOrders.filter(o => o.status === 'picked_up')"
            :key="order.id"
            class="bg-[#18181b] rounded-lg border-2 border-cyan-500/30 overflow-hidden"
          >
            <div class="bg-cyan-500/5 px-4 py-3 border-b border-[#27272a] flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-base font-bold text-cyan-400">#{{ order.order_number }}</span>
                <span class="text-sm text-zinc-400">{{ order.user.name }}</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs text-zinc-500">{{ getTimeSince(order.created_at) }}</span>
                <span class="text-base font-bold text-zinc-100">€{{ Number(order.total_amount).toFixed(2) }}</span>
              </div>
            </div>

            <div class="p-4 space-y-3">
              <div class="space-y-1.5">
                <p class="text-[10px] text-zinc-600 uppercase tracking-wider">Tooted</p>
                <div v-for="item in order.items" :key="item.id" class="bg-[#09090b] rounded-md p-3 border border-[#27272a] flex items-center justify-between">
                  <span class="text-sm font-medium">
                    <span class="text-cyan-400">{{ item.quantity }}x</span> {{ item.burger_name }}
                  </span>
                  <span class="text-cyan-400 font-bold text-sm">€{{ Number(item.price * item.quantity).toFixed(2) }}</span>
                </div>
              </div>

<div class="bg-[#09090b] rounded-md p-3 border border-[#27272a]">
                <p class="text-[10px] text-zinc-600 uppercase tracking-wider mb-1.5">Kuller</p>
                <div v-if="order.courier_user" class="flex items-center gap-2">
                  <div class="w-6 h-6 bg-cyan-500/10 rounded-full flex items-center justify-center">
                    <User :size="11" class="text-cyan-400" />
                  </div>
                  <span class="text-sm font-medium text-cyan-300">{{ order.courier_user.name }}</span>
                </div>
                <div v-else class="flex items-center gap-1.5">
                  <div class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></div>
                  <span class="text-xs text-orange-400">Kullerit ei ole veel</span>
                </div>
              </div>

              <div class="bg-[#09090b] rounded-md p-3 border border-[#27272a]">
                <div v-if="order.courier_lat" class="flex items-center gap-2">
                  <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                  <span class="text-xs text-green-400 font-medium">GPS aktiivne</span>
                </div>
                <div v-else class="flex items-center gap-2">
                  <div class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></div>
                  <span class="text-xs text-yellow-400">Ootab kulleri GPS signaali...</span>
                </div>
              </div>

              <p class="text-xs text-zinc-500 text-center">Kuller on teel — toimetamine kliendi kinnitab</p>
            </div>
          </div>
        </div>
      </div>

      <!-- MUUD -->
      <div v-if="filteredOrders.some(o => !['pending_confirmation', 'confirmed', 'preparing', 'ready', 'awaiting_courier', 'picked_up'].includes(o.status))">
        <div class="flex items-center gap-3 mb-3">
          <div class="h-px flex-1 bg-[#27272a]"></div>
          <span class="text-xs font-semibold text-zinc-500">MUUD</span>
          <div class="h-px flex-1 bg-[#27272a]"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div
            v-for="order in filteredOrders.filter(o => !['pending_confirmation', 'confirmed', 'preparing', 'ready', 'awaiting_courier', 'picked_up'].includes(o.status))"
            :key="order.id"
            class="bg-[#18181b] rounded-lg border border-[#27272a] p-4 opacity-50"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="font-bold text-zinc-400 text-sm">#{{ order.order_number }}</span>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#27272a] text-zinc-500">{{ order.status }}</span>
            </div>
            <p class="text-xs text-zinc-500">{{ order.user.name }}</p>
            <p class="text-base font-bold text-zinc-400 mt-2">€{{ Number(order.total_amount).toFixed(2) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="flex flex-col items-center py-16 bg-[#18181b] border border-[#27272a] rounded-lg">
      <div class="w-12 h-12 rounded-full bg-[#27272a] flex items-center justify-center mb-3">
        <ShoppingBag :size="20" class="text-zinc-600" />
      </div>
      <p class="text-sm font-medium text-zinc-500 mb-1">
        {{ activeFilter === 'all' ? 'Tellimusi ei ole' : 'Selle filtriga tellimusi ei ole' }}
      </p>
      <p class="text-xs text-zinc-700 mb-4">
        {{ activeFilter === 'all' ? 'Uued tellimused kuvatakse siin' : 'Vali teine filter või tühista filtreerimine' }}
      </p>
      <button v-if="activeFilter !== 'all'" @click="setFilter('all')" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md text-xs font-medium transition">
        Näita kõiki tellimusi
      </button>
    </div>

    <!-- Pagination -->
    <div v-if="orders.links.length > 3" class="mt-6 flex justify-center gap-1.5">
      <Link
        v-for="link in orders.links"
        :key="link.label"
        :href="link.url || '#'"
        :class="[
          'px-3 py-1.5 rounded-md text-xs font-medium transition',
          link.active ? 'bg-orange-600 text-white' :
          link.url ? 'bg-[#18181b] text-zinc-400 hover:bg-[#27272a] border border-[#27272a]' :
          'bg-[#18181b] text-zinc-600 cursor-not-allowed border border-[#27272a]',
        ]"
        v-html="link.label"
      />
    </div>
  </AdminLayout>
</template>
