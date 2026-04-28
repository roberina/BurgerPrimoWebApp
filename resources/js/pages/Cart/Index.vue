<template>
  <div class="page-bg" aria-hidden="true">
    <div class="page-bg-img" />
    <div class="page-bg-overlay" />
  </div>

  <div class="page-root text-white pt-16 lg:pt-20">
    <Navbar />

    <!-- Confirm Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="modal.show = false">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" />
          <div class="relative glass rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-transparent via-[#D2691E] to-transparent" />
            <div class="p-6">
              <div class="flex items-center gap-4 mb-4">
                <div :class="modal.type === 'danger' ? 'bg-red-500/15 text-red-400' : 'bg-[#D2691E]/15 text-[#D2691E]'" class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                  <svg v-if="modal.type === 'danger'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-white">{{ modal.title }}</h3>
                  <p class="text-sm text-gray-400 mt-0.5">{{ modal.message }}</p>
                </div>
              </div>
              <div class="flex gap-3 mt-6">
                <button @click="modal.show = false" class="flex-1 py-3 rounded-xl border border-white/10 text-gray-300 hover:bg-white/5 transition-colors font-semibold text-sm">{{ t('cart.modal.cancel') }}</button>
                <button @click="modal.onConfirm(); modal.show = false" :class="modal.type === 'danger' ? 'bg-red-600 hover:bg-red-500' : 'bg-[#D2691E] hover:bg-[#B8571A]'" class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition-all shadow-lg">{{ modal.confirmLabel }}</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-10 pb-24">
      <div class="mb-8">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-semibold uppercase tracking-widest mb-4">{{ t('cart.badge') }}</div>
        <h1 class="text-4xl font-bold mb-2">{{ t('cart.title') }}</h1>
        <p class="text-gray-400">{{ t('cart.sub') }}</p>
      </div>

      <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Items -->
        <div class="lg:col-span-2 space-y-4">
          <div v-for="item in cartItems" :key="item.type === 'custom_burger' ? `burger-${item.id}` : `menu-${item.id}`" class="glass p-6 hover:border-[#D2691E]/25 transition-colors duration-200">

            <!-- Custom Burger -->
            <template v-if="item.type === 'custom_burger'">
              <div class="flex gap-4">
                <div class="flex-1">
                  <span class="inline-block text-xs bg-yellow-500/15 text-yellow-400 px-2.5 py-1 rounded-full font-bold mb-3">{{ t('cart.custom.badge') }}</span>
                  <h3 class="text-lg font-bold mb-2">{{ item.burger.name }}</h3>
                  <div class="text-sm text-gray-500 space-y-0.5">
                    <div v-for="ingredient in item.burger.ingredients" :key="ingredient.id" class="flex items-center gap-1.5">
                      <span class="w-1 h-1 rounded-full bg-[#D2691E]/60 shrink-0" />
                      {{ ingredient.name }}
                      <span class="text-gray-600">× {{ ingredient.pivot.quantity }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-3 mt-4">
                    <button @click="updateQuantity(item.id, item.quantity - 1)" :disabled="item.quantity <= 1" class="w-8 h-8 rounded-lg glass-card flex items-center justify-center hover:bg-[#D2691E]/20 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-lg">−</button>
                    <span class="text-base font-bold w-8 text-center">{{ item.quantity }}</span>
                    <button @click="updateQuantity(item.id, item.quantity + 1)" class="w-8 h-8 rounded-lg glass-card flex items-center justify-center hover:bg-[#D2691E]/20 transition-colors text-lg">+</button>
                  </div>
                </div>
                <div class="flex flex-col items-end justify-between">
                  <button @click="confirmRemoveItem(item.id)" class="text-red-400 hover:text-red-300 p-2 hover:bg-red-500/10 rounded-lg transition-colors" :title="t('cart.remove')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  </button>
                  <div class="text-right">
                    <div class="text-2xl font-bold text-[#D2691E]">€{{ Number(item.subtotal).toFixed(2) }}</div>
                    <div class="text-xs text-gray-600 mt-0.5">€{{ Number(item.burger.total_price).toFixed(2) }} × {{ item.quantity }}</div>
                  </div>
                </div>
              </div>
            </template>

            <!-- Menu Item -->
            <template v-else-if="item.type === 'menu_item'">
              <div class="flex gap-4">
                <div class="flex-1">
                  <span class="inline-block text-xs bg-[#D2691E]/15 text-[#D2691E] px-2.5 py-1 rounded-full font-bold mb-3">{{ t('cart.menu.badge') }}</span>
                  <h3 class="text-lg font-bold mb-2">{{ item.name }}</h3>
                  <div class="text-sm text-gray-500 space-y-1">
                    <div v-if="item.size" class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-[#D2691E]/60 shrink-0" />{{ getSizeName(item.size) }}</div>
                    <div v-for="(drink, idx) in item.drinks" :key="`d-${idx}`" class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-[#D2691E]/60 shrink-0" />{{ drink.name }}</div>
                    <div v-for="(sauce, idx) in item.sauces" :key="`s-${idx}`" class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-[#D2691E]/60 shrink-0" />{{ sauce.name }}</div>
                    <div v-if="item.fries" class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-[#D2691E]/60 shrink-0" />{{ item.fries.name }}</div>
                    <div v-if="item.needs_utensils" class="flex items-center gap-1.5 text-gray-400"><span class="w-1 h-1 rounded-full bg-gray-600 shrink-0" />{{ t('cart.utensils') }}</div>
                    <div v-if="item.special_instructions" class="text-gray-400 italic mt-1">„{{ item.special_instructions }}"</div>
                  </div>
                  <div class="flex items-center gap-3 mt-4">
                    <button @click="updateQuantity(item.id, item.quantity - 1)" :disabled="item.quantity <= 1" class="w-8 h-8 rounded-lg glass-card flex items-center justify-center hover:bg-[#D2691E]/20 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-lg">−</button>
                    <span class="text-base font-bold w-8 text-center">{{ item.quantity }}</span>
                    <button @click="updateQuantity(item.id, item.quantity + 1)" class="w-8 h-8 rounded-lg glass-card flex items-center justify-center hover:bg-[#D2691E]/20 transition-colors text-lg">+</button>
                  </div>
                </div>
                <div class="flex flex-col items-end justify-between">
                  <button @click="confirmRemoveItem(item.id)" class="text-red-400 hover:text-red-300 p-2 hover:bg-red-500/10 rounded-lg transition-colors" :title="t('cart.remove')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  </button>
                  <div class="text-right">
                    <div class="text-2xl font-bold text-[#D2691E]">€{{ Number(item.subtotal).toFixed(2) }}</div>
                    <div class="text-xs text-gray-600 mt-0.5">€{{ Number(item.total_price).toFixed(2) }} × {{ item.quantity }}</div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Summary -->
        <div class="lg:col-span-1">
          <div class="glass p-6 sticky top-24">
            <h2 class="text-xl font-bold mb-6">{{ t('cart.summary') }}</h2>

            <div class="space-y-3 mb-6">
              <div class="flex justify-between text-sm text-gray-400">
                <span>{{ t('cart.items') }}</span>
                <span>{{ cartItems.length }}</span>
              </div>
              <div class="flex justify-between text-sm text-gray-400">
                <span>{{ t('cart.subtotal') }}</span>
                <span>€{{ Number(total).toFixed(2) }}</span>
              </div>
              <div v-if="deliveryMethod === 'takeaway'" class="flex justify-between text-sm text-[#D2691E]">
                <span>{{ t('cart.packaging') }}</span>
                <span>€{{ packagingFee.toFixed(2) }}</span>
              </div>
            </div>

            <div class="border-t border-white/6 pt-4 mb-6">
              <div class="flex justify-between items-baseline">
                <span class="font-bold">{{ t('cart.total') }}</span>
                <span class="text-3xl font-bold text-[#D2691E]">€{{ finalTotal.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Delivery type -->
            <div class="mb-6">
              <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-3">{{ t('cart.type') }}</p>

              <!-- Courier availability indicator -->
              <div v-if="deliveryStatus" class="flex items-center gap-2 mb-3 px-3 py-2 rounded-xl text-xs font-semibold"
                   :class="deliveryStatus.available
                     ? 'bg-green-950/50 border border-green-800/40 text-green-400'
                     : 'bg-white/4 border border-white/8 text-gray-500'">
                <span class="w-2 h-2 rounded-full flex-shrink-0"
                      :class="deliveryStatus.available ? 'bg-green-400 animate-pulse' : 'bg-gray-600'"></span>
                <span v-if="deliveryStatus.available">
                  {{ deliveryStatus.couriers }} kuller{{ deliveryStatus.couriers === 1 ? '' : 'it' }} {{ t('cart.couriers.available') }} · ~{{ deliveryStatus.eta }}
                </span>
                <span v-else>{{ t('cart.delivery.unavailable') }}</span>
              </div>

              <div class="grid grid-cols-3 gap-3">
                <button type="button" @click="deliveryMethod = 'dine_in'" :class="deliveryMethod === 'dine_in' ? 'border-[#D2691E] bg-[#D2691E]/10 text-white' : 'border-white/8 text-gray-500 hover:border-white/20 hover:text-gray-300'" class="py-4 rounded-xl border-2 flex flex-col items-center gap-2 transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                  <span class="text-xs font-semibold">{{ t('cart.dine_in') }}</span>
                </button>
                <button type="button" @click="deliveryMethod = 'takeaway'" :class="deliveryMethod === 'takeaway' ? 'border-[#D2691E] bg-[#D2691E]/10 text-white' : 'border-white/8 text-gray-500 hover:border-white/20 hover:text-gray-300'" class="py-4 rounded-xl border-2 flex flex-col items-center gap-2 transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                  <span class="text-xs font-semibold">{{ t('cart.takeaway') }}</span>
                </button>
                <button type="button" @click="deliveryMethod = 'delivery'" :class="deliveryMethod === 'delivery' ? 'border-[#D2691E] bg-[#D2691E]/10 text-white' : 'border-white/8 text-gray-500 hover:border-white/20 hover:text-gray-300'" class="py-4 rounded-xl border-2 flex flex-col items-center gap-2 transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 22V12h6v10" /></svg>
                  <span class="text-xs font-semibold">{{ t('cart.delivery') }}</span>
                </button>
              </div>
            </div>

            <Transition name="fade">
              <div v-if="showDeliveryWarning" class="mb-4 flex items-center gap-3 bg-[#D2691E]/10 border border-[#D2691E]/30 rounded-xl px-4 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#D2691E] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span class="text-sm text-[#D2691E] font-semibold">{{ t('cart.warning') }}</span>
              </div>
            </Transition>

            <button @click="checkout" class="btn-magnetic w-full py-4 rounded-xl bg-[#D2691E] hover:bg-[#B8571A] text-white font-bold text-sm transition-all shadow-lg shadow-[#D2691E]/20">
              {{ t('cart.checkout') }}
            </button>
            <button @click="confirmClearCart" class="w-full mt-3 text-red-400 hover:text-red-300 py-2.5 rounded-xl hover:bg-red-500/8 transition-colors text-sm font-medium">
              {{ t('cart.clear') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-24">
        <p class="text-6xl mb-6">🛒</p>
        <h2 class="text-3xl font-bold mb-3">{{ t('cart.empty.title') }}</h2>
        <p class="text-gray-400 mb-8">{{ t('cart.empty.sub') }}</p>
        <div class="flex gap-4 justify-center flex-wrap">
          <Link href="/menu" class="btn-magnetic inline-flex items-center gap-2 px-8 py-4 bg-[#D2691E] hover:bg-[#B8571A] text-white rounded-2xl font-bold text-sm transition-all">{{ t('cart.empty.menu') }}</Link>
          <Link href="/burger-builder" class="btn-magnetic inline-flex items-center gap-2 px-8 py-4 glass border-[#D2691E]/40 text-white rounded-2xl font-bold text-sm transition-all hover:border-[#D2691E]">{{ t('cart.empty.builder') }}</Link>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

interface CartItem {
  type: 'custom_burger' | 'menu_item'
  id: string | number
  quantity: number
  subtotal: number
  burger?: any
  name?: string
  size?: string
  drinks?: Array<{ id: number; name: string; price: number }>
  sauces?: Array<{ id: number; name: string; price: number }>
  fries?: { id: string; name: string; price: number } | null
  needs_utensils?: boolean
  special_instructions?: string
  total_price?: number
}

const props = defineProps<{ cartItems: CartItem[]; total: number }>()

const page = usePage()
const deliveryStatus = computed(() => (page.props as any).deliveryStatus as { available: boolean; couriers: number; eta: string | null } | null)

const deliveryMethod    = ref<'dine_in' | 'takeaway' | 'delivery' | null>(null)
const showDeliveryWarning = ref(false)

const modal = ref({
  show: false,
  type: 'danger' as 'danger' | 'warning',
  title: '',
  message: '',
  confirmLabel: t('cart.modal.confirm'),
  onConfirm: () => {},
})

const openModal = (options: Omit<typeof modal.value, 'show'>) => {
  modal.value = { show: true, ...options }
}

const packagingFee = computed(() => deliveryMethod.value === 'takeaway' ? props.cartItems.length * 0.20 : 0)
const finalTotal   = computed(() => props.total + packagingFee.value)

const getSizeName = (size?: string) => ({
  small:  t('cart.size.small'),
  medium: t('cart.size.medium'),
  large:  t('cart.size.large'),
}[size ?? ''] ?? size ?? '')

const updateQuantity = (itemId: string | number, newQuantity: number) => {
  if (newQuantity < 1) return
  router.post(`/cart/${itemId}/update`, { quantity: newQuantity }, { preserveScroll: true })
}

const confirmRemoveItem = (itemId: string | number) => {
  openModal({
    type: 'danger',
    title: t('cart.modal.remove.title'),
    message: t('cart.modal.remove.msg'),
    confirmLabel: t('cart.modal.remove.btn'),
    onConfirm: () => router.delete(`/cart/${itemId}`, { preserveScroll: true }),
  })
}

const confirmClearCart = () => {
  openModal({
    type: 'danger',
    title: t('cart.modal.clear.title'),
    message: t('cart.modal.clear.msg'),
    confirmLabel: t('cart.modal.clear.btn'),
    onConfirm: () => router.post('/cart/clear', {}, { preserveScroll: true }),
  })
}

const checkout = () => {
  if (!deliveryMethod.value) {
    showDeliveryWarning.value = true
    setTimeout(() => { showDeliveryWarning.value = false }, 4000)
    return
  }
  router.visit('/payment/checkout', { method: 'get', data: { delivery_method: deliveryMethod.value } })
}
</script>

<style scoped>
.page-bg { position: fixed; inset: 0; z-index: 0; }
.page-bg-img { width: 100%; height: 100%; background-image: url('/img/main25.jpg'); background-size: cover; background-position: center; filter: brightness(0.28) saturate(0.65); }
.page-bg-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.35) 40%, rgba(0,0,0,0.50) 100%); }
.page-root { position: relative; z-index: 1; min-height: 100vh; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>