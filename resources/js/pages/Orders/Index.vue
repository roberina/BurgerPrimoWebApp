<template>
    <Head>
    <meta name="description" content="Burger Primo pakub käsitsi valmistatud burgereid, krõbedaid snäkke ja maitsvaid jooke Kuressaares. Telli kohale või tule kohale!" head-key="description" />
    <meta name="robots" content="index, follow" head-key="robots" />
    <meta property="og:title" content="Burger Primo — Parimad burgerid Kuressaares" head-key="og:title" />
    <meta property="og:description" content="Burger Primo pakub käsitsi valmistatud burgereid, krõbedaid snäkke ja maitsvaid jooke Kuressaares. Telli kohale või tule kohale!" head-key="og:description" />
    <meta property="og:type" content="website" head-key="og:type" />
    <meta property="og:url" content="https://burgerprimo.ee" head-key="og:url" />
    <meta property="og:image" content="/img/Logo45.png" head-key="og:image" />
  </Head>
  <div class="min-h-screen bg-[#0B0B0B] text-white pt-16 lg:pt-20">
    <Navbar />

    <!-- Custom Confirm Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="modal.show = false">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" />
          <div class="relative bg-[#161616] border border-white/10 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="h-1 w-full bg-linear-to-r from-[#D2691E] to-[#B8571A]" />
            <div class="p-6">
              <div class="flex items-center gap-4 mb-4">
                <div :class="modal.type === 'danger' ? 'bg-red-500/15 text-red-400' : 'bg-[#D2691E]/15 text-[#D2691E]'" class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-white">{{ modal.title }}</h3>
                  <p class="text-sm text-gray-400 mt-0.5">{{ modal.message }}</p>
                </div>
              </div>
              <div class="flex gap-3 mt-6">
                <button @click="modal.show = false" class="flex-1 py-3 rounded-xl border border-white/10 text-gray-300 hover:bg-white/5 transition font-semibold text-sm">{{ t('orders.modal.cancel') }}</button>
                <button @click="modal.onConfirm(); modal.show = false" :class="modal.type === 'danger' ? 'bg-red-600 hover:bg-red-500' : 'bg-linear-to-r from-[#D2691E] to-[#B8571A]'" class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition-all shadow-lg">{{ modal.confirmLabel }}</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <main class="max-w-7xl mx-auto px-6 py-12">

      <!-- Kohaletoimetamise bänner — nähtav kui kuller on teel -->
      <Link v-if="deliveringOrder" :href="`/orders/${deliveringOrder.id}`"
        class="block mb-8 rounded-2xl overflow-hidden border border-cyan-700/50 hover:border-cyan-500 transition-all group">
        <div class="bg-linear-to-r from-cyan-950/80 to-[#0d1f2d] px-6 py-5 flex items-center gap-5">
          <div class="text-4xl animate-bounce">🛵</div>
          <div class="flex-1">
            <p class="font-bold text-lg text-white">{{ t('orders.courier.heading') }}</p>
            <p class="text-sm text-cyan-400">
              Tellimus <span class="font-mono font-bold">{{ deliveringOrder.order_number }}</span> {{ t('orders.courier.sub') }}
            </p>
          </div>
          <div class="bg-cyan-500 group-hover:bg-cyan-400 text-black font-bold px-5 py-2.5 rounded-xl text-sm transition shrink-0">
            {{ t('orders.courier.track') }}
          </div>
        </div>
        <div class="h-1 w-full bg-linear-to-r from-cyan-600 to-cyan-400"></div>
      </Link>
      <div class="mb-12 flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold mb-2">{{ t('orders.heading') }}</h1>
          <p class="text-gray-400">{{ t('orders.sub') }}</p>
        </div>
        <button v-if="selectedOrders.length > 0" @click="confirmDeleteSelected" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
          {{ t('orders.delete.selected') }} ({{ selectedOrders.length }})
        </button>
      </div>

      <div v-if="orders.length === 0" class="text-center py-24">
        <div class="text-7xl mb-6">📦</div>
        <h3 class="text-2xl font-bold mb-3">{{ t('orders.empty.heading') }}</h3>
        <p class="text-gray-400 mb-8">{{ t('orders.empty.sub') }}</p>
        <Link href="/menu" class="inline-block px-8 py-4 rounded-xl font-semibold transition hover:opacity-90 text-white uppercase tracking-wide" style="background-color: #D2691E">{{ t('orders.empty.cta') }}</Link>
      </div>

      <div v-else class="space-y-4">
        <div v-for="order in orders" :key="order.id" class="bg-[#121212] rounded-2xl overflow-hidden border border-[#1a1a1a] transition-all duration-200" :class="{ 'ring-2 ring-[#D2691E]': selectedOrders.includes(order.id) }">
          <div class="bg-[#0d0d0d] px-6 py-4 flex items-center justify-between border-b border-[#1a1a1a]">
            <div class="flex items-center gap-4">
              <label v-if="order.status === 'completed'" class="flex items-center cursor-pointer">
                <input type="checkbox" :checked="selectedOrders.includes(order.id)" @change="toggleOrderSelection(order.id)" class="w-5 h-5 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] cursor-pointer" />
              </label>
              <div>
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('orders.col.number') }}</p>
                <p class="font-mono text-lg font-bold text-[#D2691E]">{{ order.order_number }}</p>
              </div>
            </div>
            <div class="flex items-center gap-6">
              <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('orders.col.time') }}</p>
                <p class="text-sm font-medium text-gray-300">{{ formatDate(order.created_at) }}</p>
              </div>
              <span :class="getStatusClass(order.status)">{{ getStatusLabel(order.status) }}</span>
            </div>
          </div>

          <div class="p-6 relative">
            <button v-if="order.status === 'rejected'" @click="confirmDismiss(order.id)" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white flex items-center justify-center transition" :title="t('orders.dismiss')">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div v-if="order.status === 'rejected'" class="mb-4 bg-red-900/20 border border-red-800 rounded-xl px-4 py-3">
              <p class="text-sm text-red-400 font-medium">{{ t('orders.rejected.notice') }}</p>
            </div>
            <div class="flex items-start justify-between mb-5">
              <div class="flex-1">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">{{ t('orders.col.items') }}</p>
                <div class="space-y-2">
                  <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between bg-[#0d0d0d] rounded-xl px-4 py-3">
                    <div>
                      <p class="font-semibold text-sm">{{ item.burger_name }}</p>
                      <p class="text-xs text-gray-500 mt-0.5">{{ item.quantity }}x {{ t('orders.col.qty') }}</p>
                    </div>
                    <p class="font-bold text-[#D2691E] text-sm">{{ Number(item.price * item.quantity).toFixed(2) }}€</p>
                  </div>
                </div>
              </div>
              <div class="ml-8 text-right">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('orders.col.total') }}</p>
                <p class="text-3xl font-bold text-[#D2691E]">{{ Number(order.total_amount).toFixed(2) }}€</p>
              </div>
            </div>
            <div v-if="order.customer_notes" class="mb-4 bg-[#0d0d0d] rounded-xl px-4 py-3">
              <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ t('orders.notes.yours') }}</p>
              <p class="text-sm text-gray-300">{{ order.customer_notes }}</p>
            </div>
            <div v-if="order.admin_notes" class="mb-4 bg-blue-900/10 border border-blue-800/50 rounded-xl px-4 py-3">
              <p class="text-xs text-blue-400 uppercase tracking-widest mb-1">{{ t('orders.notes.restaurant') }}</p>
              <p class="text-sm text-gray-300">{{ order.admin_notes }}</p>
            </div>
            <div class="flex gap-3 mt-5">
              <Link :href="`/orders/${order.id}`" class="flex-1 bg-[#1a1a1a] hover:bg-[#222] text-white px-4 py-2.5 rounded-xl font-semibold transition text-center text-sm border border-[#2a2a2a] hover:border-[#D2691E]/30">{{ t('orders.btn.details') }}</Link>
              <button v-if="order.status === 'pending' || order.status === 'confirmed'" @click="confirmCancel(order.id)" class="flex-1 bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white px-4 py-2.5 rounded-xl font-semibold transition text-sm border border-red-800/50 hover:border-red-600">{{ t('orders.btn.cancel') }}</button>
              <Link v-if="order.status === 'completed'" href="/menu" class="flex-1 text-white px-4 py-2.5 rounded-xl font-semibold transition text-center text-sm hover:opacity-90" style="background-color: #D2691E">{{ t('orders.btn.reorder') }}</Link>
            </div>
          </div>
        </div>
      </div>

      <div v-if="orders.length > 0" class="mt-10 text-center">
        <Link href="/menu" class="inline-block px-10 py-4 rounded-xl font-semibold transition hover:opacity-90 text-white uppercase tracking-wide" style="background-color: #D2691E">{{ t('orders.cta') }}</Link>
      </div>
    </main>
  </div>
  <Footer />
</template>

<script setup lang="ts">
import { Link, router, Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();

interface OrderItem { id: number; burger_name: string; price: number; quantity: number; }
interface Order { id: number; order_number: string; total_amount: number; status: string; created_at: string; customer_notes: string | null; admin_notes: string | null; items: OrderItem[]; courier_lat?: number | null; courier_lng?: number | null; }
interface Props { orders: Order[]; }

const props = defineProps<Props>();
const { t } = useI18n();
const selectedOrders = ref<number[]>([]);

const modal = reactive({ show: false, type: 'danger' as 'danger'|'warning', title: '', message: '', confirmLabel: 'Kinnita', onConfirm: () => {} });
const openModal = (opts: Omit<typeof modal, 'show'>) => { Object.assign(modal, { show: true, ...opts }); };

const hasActiveOrders = computed(() => props.orders.some(o => ['pending','confirmed','preparing','ready','delivering'].includes(o.status)));
const deliveringOrder = computed(() => props.orders.find(o => o.status === 'delivering') ?? null);
let refreshInterval: ReturnType<typeof setInterval> | null = null;
onMounted(() => { if (hasActiveOrders.value) refreshInterval = setInterval(() => router.reload({ only: ['orders'] }), 15000); });
onUnmounted(() => { if (refreshInterval) clearInterval(refreshInterval); });

const toggleOrderSelection = (id: number) => {
  const i = selectedOrders.value.indexOf(id);
  if (i > -1) selectedOrders.value.splice(i, 1); else selectedOrders.value.push(id);
};

const confirmDeleteSelected = () => {
  const count = selectedOrders.value.length;
  openModal({ type: 'danger', title: 'Kustuta tellimused', message: `Kas oled kindel, et soovid kustutada ${count} tellimust?`, confirmLabel: 'Kustuta',
    onConfirm: () => {
      router.post('/orders/bulk-delete' as any, { order_ids: selectedOrders.value }, {
        preserveScroll: true,
        onSuccess: () => { success(`${count} tellimust kustutatud`); selectedOrders.value = []; },
        onError: () => error('Kustutamine ebaõnnestus'),
      });
    },
  });
};

const confirmCancel = (orderId: number) => {
  openModal({ type: 'danger', title: 'Tühista tellimus', message: 'Kas oled kindel, et soovid tellimuse tühistada?', confirmLabel: 'Tühista tellimus',
    onConfirm: () => {
      router.post(`/orders/${orderId}/cancel` as any, {}, {
        preserveScroll: true,
        onSuccess: () => success('Tellimus tühistatud'),
        onError: () => error('Tühistamine ebaõnnestus'),
      });
    },
  });
};

const confirmDismiss = (orderId: number) => {
  openModal({ type: 'warning', title: 'Eemalda tellimus', message: 'Kas soovid selle tellimuse nimekirjast eemaldada?', confirmLabel: 'Eemalda',
    onConfirm: () => {
      router.delete(`/orders/${orderId}` as any, {
        preserveScroll: true,
        onSuccess: () => success('Tellimus eemaldatud'),
        onError: () => error('Eemaldamine ebaõnnestus'),
      });
    },
  });
};

const getStatusClass = (s: string) => ({ pending:'px-3 py-1 rounded-full bg-yellow-900/30 text-yellow-400 text-xs font-semibold', confirmed:'px-3 py-1 rounded-full bg-blue-900/30 text-blue-400 text-xs font-semibold', preparing:'px-3 py-1 rounded-full bg-purple-900/30 text-purple-400 text-xs font-semibold', ready:'px-3 py-1 rounded-full bg-[#D2691E]/20 text-[#D2691E] text-xs font-semibold', completed:'px-3 py-1 rounded-full bg-green-900/30 text-green-400 text-xs font-semibold', cancelled:'px-3 py-1 rounded-full bg-gray-800 text-gray-400 text-xs font-semibold', rejected:'px-3 py-1 rounded-full bg-red-900/30 text-red-400 text-xs font-semibold' }[s] || 'px-3 py-1 rounded-full bg-gray-800 text-gray-400 text-xs font-semibold');
const getStatusLabel = (s: string) => ({ pending:t('order.status.pending'), confirmed:t('order.status.confirmed'), preparing:t('order.status.preparing'), ready:t('order.status.ready'), completed:t('order.status.completed'), cancelled:t('order.status.cancelled'), rejected:t('order.status.rejected') }[s] || s);
const { locale } = useI18n();
const formatDate = (d: string) => new Date(d).toLocaleString(locale.value === 'en' ? 'en-GB' : 'et-EE', { year:'numeric', month:'2-digit', day:'2-digit', hour:'2-digit', minute:'2-digit' });
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>