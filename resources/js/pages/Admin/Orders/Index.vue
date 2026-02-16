<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
  Filter,
  RefreshCw
} from 'lucide-vue-next';

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
}

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface Props {
  orders: {
    data: Order[];
    links: PaginationLink[];
  };
  stats?: any;
  filters?: any;
}

const props = defineProps<Props>();

const orderStatuses = reactive<Record<number, string>>({});
const activeFilter = ref<string>('all');
const audioContext = ref<AudioContext | null>(null);
const previousOrderCount = ref<number>(props.orders.data.filter(o => o.status === 'pending').length);
const refreshInterval = ref<number | null>(null);
const isRefreshing = ref<boolean>(false);

// Initialize order statuses
props.orders.data.forEach(order => {
  orderStatuses[order.id] = order.status;
});

// Filter orders based on active filter
const filteredOrders = computed(() => {
  if (activeFilter.value === 'all') {
    return props.orders.data;
  }
  
  if (activeFilter.value === 'active') {
    return props.orders.data.filter(o => 
      ['pending', 'confirmed', 'preparing', 'ready'].includes(o.status)
    );
  }
  
  return props.orders.data.filter(o => o.status === activeFilter.value);
});

// Computed stats
const pendingOrders = computed(() => 
  props.orders.data.filter(o => o.status === 'pending')
);

const confirmedOrders = computed(() =>
  props.orders.data.filter(o => o.status === 'confirmed')
);

const preparingOrders = computed(() => 
  props.orders.data.filter(o => o.status === 'preparing')
);

const readyOrders = computed(() => 
  props.orders.data.filter(o => o.status === 'ready')
);

// Play notification sound
const playNotificationSound = () => {
  try {
    // Create AudioContext if not exists
    if (!audioContext.value) {
      audioContext.value = new (window.AudioContext || (window as any).webkitAudioContext)();
    }

    const ctx = audioContext.value;
    const oscillator = ctx.createOscillator();
    const gainNode = ctx.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(ctx.destination);

    // Create a pleasant notification sound
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, ctx.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);

    oscillator.start(ctx.currentTime);
    oscillator.stop(ctx.currentTime + 0.5);
    
    // Second beep
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
  } catch (error) {
    console.error('Error playing notification sound:', error);
  }
};

// Auto-refresh orders
const refreshOrders = () => {
  isRefreshing.value = true;
  
  router.reload({
    only: ['orders'],
    onSuccess: (page: any) => {
      const newPendingCount = page.props.orders.data.filter((o: Order) => o.status === 'pending').length;
      
      // Check if there are new pending orders
      if (newPendingCount > previousOrderCount.value) {
        playNotificationSound();
        
        // Show browser notification if permitted
        if ('Notification' in window && Notification.permission === 'granted') {
          new Notification('Uus tellimus! 🍔', {
            body: `${newPendingCount} uut tellimust ootab kinnitamist`,
            icon: '/favicon.svg',
            tag: 'new-order',
          });
        }
      }
      
      previousOrderCount.value = newPendingCount;
      isRefreshing.value = false;
    },
    onError: () => {
      isRefreshing.value = false;
    },
  });
};

// Request notification permission
const requestNotificationPermission = () => {
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
};

// Start auto-refresh on mount
onMounted(() => {
  requestNotificationPermission();
  
  // Refresh every 10 seconds
  refreshInterval.value = window.setInterval(() => {
    refreshOrders();
  }, 10000);
});

// Clear interval on unmount
onUnmounted(() => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value);
  }
  if (audioContext.value) {
    audioContext.value.close();
  }
});

const confirmOrder = (orderId: number) => {
  router.post(`/admin/orders/${orderId}/confirm`, {}, {
    preserveScroll: true,
  });
};

const updateOrderStatus = (orderId: number, newStatus: string) => {
  orderStatuses[orderId] = newStatus;
  router.post(`/admin/orders/${orderId}/status`, {
    status: newStatus,
  } as any, {
    preserveScroll: true,
  });
};

const rejectOrder = (orderId: number) => {
  const reason = prompt('Tagasilükkamise põhjus (valikuline):');
  if (reason !== null) {
    router.post(`/admin/orders/${orderId}/reject` as any, {
      admin_notes: reason || 'Tellimus lükati tagasi',
    }, {
      preserveScroll: true,
    });
  }
};

const getTimeSince = (date: string): string => {
  const now = new Date();
  const orderDate = new Date(date);
  const diffMs = now.getTime() - orderDate.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  
  if (diffMins < 1) return 'Praegu';
  if (diffMins < 60) return `${diffMins}min tagasi`;
  const diffHours = Math.floor(diffMins / 60);
  return `${diffHours}h tagasi`;
};

const formatTime = (date: string): string => {
  return new Date(date).toLocaleString('et-EE', {
    hour: '2-digit',
    minute: '2-digit',
  });
};

const setFilter = (filter: string) => {
  activeFilter.value = filter;
};
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-xl lg:text-2xl font-bold">Tellimused</h2>
          <p class="text-sm text-gray-400 mt-1">Reaalajas tellimuste haldamine</p>
        </div>
      </div>
    </template>

    <!-- Auto-refresh Indicator -->
    <div class="mb-4 flex items-center justify-between bg-[#111111] border border-gray-800 rounded-lg px-4 py-3">
      <div class="flex items-center gap-3">
        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
        <span class="text-sm text-gray-400">Automaatne uuendamine (iga 10 sek)</span>
      </div>
      <button
        @click="refreshOrders"
        :disabled="isRefreshing"
        class="text-sm text-orange-500 hover:text-orange-400 font-semibold transition flex items-center gap-2 disabled:opacity-50"
      >
        <RefreshCw :size="16" :class="{ 'animate-spin': isRefreshing }" />
        {{ isRefreshing ? 'Uuendamine...' : 'Uuenda kohe' }}
      </button>
    </div>

    <!-- Quick Stats & Filters -->
    <div class="bg-[#111111] rounded-xl border border-gray-800 p-6 mb-6">
      <div class="flex items-center gap-2 mb-4">
        <Filter :size="18" class="text-gray-400" />
        <h3 class="font-semibold text-white">Filtreeri tellimusi</h3>
      </div>

      <!-- Filter Buttons -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- All Orders -->
        <button
          @click="setFilter('all')"
          :class="[
            'p-4 rounded-lg border-2 transition-all text-left',
            activeFilter === 'all' 
              ? 'border-orange-600 bg-orange-600/10' 
              : 'border-gray-800 bg-[#0a0a0a] hover:border-gray-700'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <Bell :size="20" :class="activeFilter === 'all' ? 'text-orange-500' : 'text-gray-400'" />
            <span :class="['text-2xl font-bold', activeFilter === 'all' ? 'text-orange-500' : 'text-white']">
              {{ orders.data.length }}
            </span>
          </div>
          <p :class="['text-sm font-semibold', activeFilter === 'all' ? 'text-orange-500' : 'text-gray-400']">
            Kõik tellimused
          </p>
        </button>

        <!-- Active Orders -->
        <button
          @click="setFilter('active')"
          :class="[
            'p-4 rounded-lg border-2 transition-all text-left',
            activeFilter === 'active' 
              ? 'border-orange-600 bg-orange-600/10' 
              : 'border-gray-800 bg-[#0a0a0a] hover:border-gray-700'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <ChefHat :size="20" :class="activeFilter === 'active' ? 'text-orange-500' : 'text-gray-400'" />
            <span :class="['text-2xl font-bold', activeFilter === 'active' ? 'text-orange-500' : 'text-white']">
              {{ pendingOrders.length + confirmedOrders.length + preparingOrders.length + readyOrders.length }}
            </span>
          </div>
          <p :class="['text-sm font-semibold', activeFilter === 'active' ? 'text-orange-500' : 'text-gray-400']">
            Aktiivsed
          </p>
        </button>

        <!-- Pending -->
        <button
          @click="setFilter('pending')"
          :class="[
            'p-4 rounded-lg border-2 transition-all text-left',
            activeFilter === 'pending' 
              ? 'border-orange-600 bg-orange-600/10' 
              : 'border-gray-800 bg-[#0a0a0a] hover:border-gray-700'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <Bell :size="20" :class="activeFilter === 'pending' ? 'text-orange-500' : 'text-gray-400'" />
            <span :class="['text-2xl font-bold', activeFilter === 'pending' ? 'text-orange-500' : 'text-white']">
              {{ pendingOrders.length }}
            </span>
          </div>
          <p :class="['text-sm font-semibold', activeFilter === 'pending' ? 'text-orange-500' : 'text-gray-400']">
            Uued
          </p>
        </button>

        <!-- Confirmed -->
        <button
          @click="setFilter('confirmed')"
          :class="[
            'p-4 rounded-lg border-2 transition-all text-left',
            activeFilter === 'confirmed' 
              ? 'border-orange-600 bg-orange-600/10' 
              : 'border-gray-800 bg-[#0a0a0a] hover:border-gray-700'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <Check :size="20" :class="activeFilter === 'confirmed' ? 'text-orange-500' : 'text-gray-400'" />
            <span :class="['text-2xl font-bold', activeFilter === 'confirmed' ? 'text-orange-500' : 'text-white']">
              {{ confirmedOrders.length }}
            </span>
          </div>
          <p :class="['text-sm font-semibold', activeFilter === 'confirmed' ? 'text-orange-500' : 'text-gray-400']">
            Kinnitatud
          </p>
        </button>

        <!-- Preparing -->
        <button
          @click="setFilter('preparing')"
          :class="[
            'p-4 rounded-lg border-2 transition-all text-left',
            activeFilter === 'preparing' 
              ? 'border-orange-600 bg-orange-600/10' 
              : 'border-gray-800 bg-[#0a0a0a] hover:border-gray-700'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <ChefHat :size="20" :class="activeFilter === 'preparing' ? 'text-orange-500' : 'text-gray-400'" />
            <span :class="['text-2xl font-bold', activeFilter === 'preparing' ? 'text-orange-500' : 'text-white']">
              {{ preparingOrders.length }}
            </span>
          </div>
          <p :class="['text-sm font-semibold', activeFilter === 'preparing' ? 'text-orange-500' : 'text-gray-400']">
            Valmistamisel
          </p>
        </button>

        <!-- Ready -->
        <button
          @click="setFilter('ready')"
          :class="[
            'p-4 rounded-lg border-2 transition-all text-left',
            activeFilter === 'ready' 
              ? 'border-green-600 bg-green-600/10' 
              : 'border-gray-800 bg-[#0a0a0a] hover:border-gray-700'
          ]"
        >
          <div class="flex items-center justify-between mb-2">
            <Check :size="20" :class="activeFilter === 'ready' ? 'text-green-500' : 'text-gray-400'" />
            <span :class="['text-2xl font-bold', activeFilter === 'ready' ? 'text-green-500' : 'text-white']">
              {{ readyOrders.length }}
            </span>
          </div>
          <p :class="['text-sm font-semibold', activeFilter === 'ready' ? 'text-green-500' : 'text-gray-400']">
            Valmis
          </p>
        </button>
      </div>
    </div>

    <!-- Active Filter Display -->
    <div v-if="activeFilter !== 'all'" class="mb-6 flex items-center justify-between bg-[#111111] border border-gray-800 rounded-lg px-4 py-3">
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-400">Näitan:</span>
        <span class="text-sm font-semibold text-orange-500">
          {{ activeFilter === 'active' ? 'Aktiivsed tellimused' :
             activeFilter === 'pending' ? 'Uued tellimused' :
             activeFilter === 'confirmed' ? 'Kinnitatud tellimused' :
             activeFilter === 'preparing' ? 'Valmistamisel' :
             activeFilter === 'ready' ? 'Valmis tellimused' : '' }}
        </span>
        <span class="text-sm text-gray-400">({{ filteredOrders.length }})</span>
      </div>
      <button
        @click="setFilter('all')"
        class="text-sm text-gray-400 hover:text-white transition"
      >
        Tühista filter
      </button>
    </div>

    <!-- Orders Grid -->
    <div v-if="filteredOrders.length > 0" class="space-y-6">
      <!-- PENDING ORDERS -->
      <div v-if="filteredOrders.some(o => o.status === 'pending')">
        <div class="flex items-center gap-3 mb-4">
          <div class="h-0.5 flex-1 bg-orange-600/20"></div>
          <h3 class="text-lg font-bold text-orange-500 flex items-center gap-2">
            <Bell :size="20" />
            UUED ({{ filteredOrders.filter(o => o.status === 'pending').length }})
          </h3>
          <div class="h-0.5 flex-1 bg-orange-600/20"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div
            v-for="order in filteredOrders.filter(o => o.status === 'pending')"
            :key="order.id"
            class="bg-[#111111] rounded-xl border-2 border-orange-600/30 overflow-hidden hover:border-orange-600/50 transition-colors"
          >
            <!-- Header -->
            <div class="bg-orange-600/5 px-6 py-4 border-b border-gray-800">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl font-bold text-orange-500">#{{ order.order_number }}</span>
                    <span class="px-3 py-1 rounded-full bg-orange-600 text-white text-xs font-bold uppercase">
                      Uus
                    </span>
                  </div>
                  <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                      <User :size="16" class="text-gray-400" />
                      <span class="text-white font-medium">{{ order.user.name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <Clock :size="16" class="text-gray-400" />
                      <span class="text-gray-300">{{ formatTime(order.created_at) }}</span>
                      <span class="text-orange-500 font-semibold">({{ getTimeSince(order.created_at) }})</span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-400 mb-1">KOKKU</p>
                  <p class="text-3xl font-bold text-orange-500">€{{ Number(order.total_amount).toFixed(2) }}</p>
                </div>
              </div>

              <!-- Delivery Method -->
              <div class="mt-3 flex items-center gap-2">
                <component :is="order.delivery_method === 'dine_in' ? Home : ShoppingBag" :size="18" class="text-gray-400" />
                <span class="text-sm font-medium text-gray-300">
                  {{ order.delivery_method === 'dine_in' ? 'Kohapeal' : 'Kaasa' }}
                </span>
              </div>
            </div>

            <!-- Items -->
            <div class="p-6">
              <p class="text-xs font-bold text-gray-400 uppercase mb-3 tracking-wider">Tooted:</p>
              <div class="space-y-4">
                <div
                  v-for="item in order.items"
                  :key="item.id"
                  class="bg-[#0a0a0a] rounded-lg p-4 border border-gray-800"
                >
                  <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                      <div class="flex items-center gap-2 mb-1">
                        <span class="text-2xl font-bold text-orange-500">{{ item.quantity }}x</span>
                        <h4 class="text-lg font-bold text-white">{{ item.burger_name }}</h4>
                      </div>
                    </div>
                    <p class="text-lg font-bold text-orange-500">€{{ Number(item.price * item.quantity).toFixed(2) }}</p>
                  </div>

                  <!-- Ingredients -->
                  <div class="bg-[#111111] rounded p-3 border border-gray-800">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Koostis:</p>
                    <div class="grid grid-cols-2 gap-2">
                      <div
                        v-for="(ing, idx) in item.ingredients"
                        :key="idx"
                        class="flex items-center gap-2"
                      >
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                        <span class="text-sm text-gray-200">
                          <span v-if="ing.quantity > 1" class="font-bold text-white">{{ ing.quantity }}x </span>
                          {{ ing.name }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Customer Notes -->
              <div v-if="order.customer_notes" class="mt-4 bg-orange-600/10 border border-orange-600/30 rounded-lg p-4">
                <div class="flex items-start gap-2">
                  <AlertCircle :size="18" class="text-orange-500 flex-shrink-0 mt-0.5" />
                  <div>
                    <p class="text-xs font-bold text-orange-500 uppercase mb-1">Märkused:</p>
                    <p class="text-sm text-white">"{{ order.customer_notes }}"</p>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-4 flex gap-2">
                <button
                  @click="confirmOrder(order.id)"
                  class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg text-base font-bold transition flex items-center justify-center gap-2"
                >
                  <Check :size="20" />
                  KINNITA
                </button>
                <button
                  @click="rejectOrder(order.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-lg text-base font-bold transition flex items-center justify-center gap-2"
                >
                  <X :size="20" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CONFIRMED & PREPARING ORDERS -->
      <div v-if="filteredOrders.some(o => ['confirmed', 'preparing'].includes(o.status))">
        <div class="flex items-center gap-3 mb-4">
          <div class="h-0.5 flex-1 bg-gray-700"></div>
          <h3 class="text-lg font-bold text-gray-300 flex items-center gap-2">
            <ChefHat :size="20" />
            VALMISTAMISEL ({{ filteredOrders.filter(o => ['confirmed', 'preparing'].includes(o.status)).length }})
          </h3>
          <div class="h-0.5 flex-1 bg-gray-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div
            v-for="order in filteredOrders.filter(o => ['confirmed', 'preparing'].includes(o.status))"
            :key="order.id"
            class="bg-[#111111] rounded-xl border border-gray-800 overflow-hidden hover:border-gray-700 transition-colors"
          >
            <!-- Header -->
            <div class="px-6 py-3 border-b border-gray-800 bg-[#0a0a0a]">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <span class="text-xl font-bold text-white">#{{ order.order_number }}</span>
                  <span class="text-sm text-gray-300">{{ order.user.name }}</span>
                  <component :is="order.delivery_method === 'dine_in' ? Home : ShoppingBag" :size="16" class="text-gray-400" />
                </div>
                <div class="flex items-center gap-3">
                  <span class="text-sm text-gray-400">{{ getTimeSince(order.created_at) }}</span>
                  <span class="text-xl font-bold text-white">€{{ Number(order.total_amount).toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <!-- Items -->
            <div class="p-4">
              <div class="space-y-2 mb-4">
                <div
                  v-for="item in order.items"
                  :key="item.id"
                  class="bg-[#0a0a0a] rounded p-3 border border-gray-800"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-white">
                      <span class="text-orange-500">{{ item.quantity }}x</span>
                      {{ item.burger_name }}
                    </span>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(ing, idx) in item.ingredients"
                      :key="idx"
                      class="text-xs bg-gray-800 px-2 py-1 rounded text-gray-300"
                    >
                      <span v-if="ing.quantity > 1" class="font-bold">{{ ing.quantity }}x </span>{{ ing.name }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Status Change -->
              <select
                v-model="orderStatuses[order.id]"
                @change="updateOrderStatus(order.id, orderStatuses[order.id])"
                class="w-full bg-[#0a0a0a] border border-gray-700 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:border-orange-600 transition"
              >
                <option value="confirmed">Kinnitatud</option>
                <option value="preparing">Valmistamisel</option>
                <option value="ready">Valmis</option>
                <option value="completed">Täidetud</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- READY ORDERS -->
      <div v-if="filteredOrders.some(o => o.status === 'ready')">
        <div class="flex items-center gap-3 mb-4">
          <div class="h-0.5 flex-1 bg-green-600/20"></div>
          <h3 class="text-lg font-bold text-green-500 flex items-center gap-2">
            <Check :size="20" />
            VALMIS ({{ filteredOrders.filter(o => o.status === 'ready').length }})
          </h3>
          <div class="h-0.5 flex-1 bg-green-600/20"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="order in filteredOrders.filter(o => o.status === 'ready')"
            :key="order.id"
            class="bg-[#111111] rounded-xl border-2 border-green-600/30 overflow-hidden hover:border-green-600/50 transition-colors"
          >
            <div class="bg-green-600/5 px-4 py-3 border-b border-gray-800">
              <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-green-500">#{{ order.order_number }}</span>
                <component :is="order.delivery_method === 'dine_in' ? Home : ShoppingBag" :size="18" class="text-gray-400" />
              </div>
              <p class="text-sm text-white mt-1">{{ order.user.name }}</p>
            </div>
            <div class="p-4">
              <p class="text-xs text-gray-400 mb-2">{{ order.items.length }} toode(t)</p>
              <p class="text-2xl font-bold text-green-500 mb-3">€{{ Number(order.total_amount).toFixed(2) }}</p>
              <button
                @click="updateOrderStatus(order.id, 'completed')"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition"
              >
                Märgi täidetuks
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Other Orders -->
      <div v-if="filteredOrders.some(o => !['pending', 'confirmed', 'preparing', 'ready'].includes(o.status))">
        <div class="flex items-center gap-3 mb-4">
          <div class="h-0.5 flex-1 bg-gray-800"></div>
          <h3 class="text-lg font-bold text-gray-400">MUUD</h3>
          <div class="h-0.5 flex-1 bg-gray-800"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div
            v-for="order in filteredOrders.filter(o => !['pending', 'confirmed', 'preparing', 'ready'].includes(o.status))"
            :key="order.id"
            class="bg-[#111111] rounded-lg border border-gray-800 p-4 opacity-60"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="font-bold text-gray-400">#{{ order.order_number }}</span>
              <span class="text-xs px-2 py-1 rounded bg-gray-800 text-gray-400">
                {{ order.status }}
              </span>
            </div>
            <p class="text-sm text-gray-500">{{ order.user.name }}</p>
            <p class="text-lg font-bold text-gray-400 mt-2">€{{ Number(order.total_amount).toFixed(2) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16 bg-[#111111] rounded-xl border border-gray-800">
      <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
        <ShoppingBag :size="32" class="text-gray-600" />
      </div>
      <p class="text-xl font-semibold text-gray-400 mb-2">
        {{ activeFilter === 'all' ? 'Tellimusi ei ole' : 'Selle filtriga tellimusi ei ole' }}
      </p>
      <p class="text-sm text-gray-500">
        {{ activeFilter === 'all' ? 'Uued tellimused kuvatakse siin' : 'Vali teine filter või tühista filtreerimine' }}
      </p>
      <button
        v-if="activeFilter !== 'all'"
        @click="setFilter('all')"
        class="mt-4 px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
      >
        Näita kõiki tellimusi
      </button>
    </div>

    <!-- Pagination -->
    <div v-if="orders.links.length > 3" class="mt-8 flex justify-center gap-2">
      <Link
        v-for="link in orders.links"
        :key="link.label"
        :href="link.url || '#'"
        :class="[
          'px-4 py-2 rounded-lg text-sm font-semibold transition',
          link.active
            ? 'bg-orange-600 text-white'
            : link.url
            ? 'bg-[#111111] text-gray-300 hover:bg-[#1a1a1a] border border-gray-800'
            : 'bg-[#111111] text-gray-600 cursor-not-allowed border border-gray-800'
        ]"
        v-html="link.label"
      />
    </div>
  </AdminLayout>
</template>