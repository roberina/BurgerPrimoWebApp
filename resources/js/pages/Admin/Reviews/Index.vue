<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

const { success, error } = useToast()
const modal = reactive({ show: false, title: '', message: '', confirmLabel: 'Kinnita', onConfirm: () => {} })
const openModal = (opts: Omit<typeof modal, 'show'>) => { Object.assign(modal, { show: true, ...opts }) }
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'

interface Review {
  id: number
  name: string
  content: string
  rating: number
  status: string
  created_at: string
}

defineProps<{
  pending: Review[]
  approved: Review[]
  rejected: Review[]
}>()

const activeTab = ref<'pending' | 'approved' | 'rejected'>('pending')

const approve = (id: number) => router.post(`/admin/reviews/${id}/approve`, {}, { preserveScroll: true, onSuccess: () => success('Arvustus kinnitatud ✓') })
const reject = (id: number) => router.post(`/admin/reviews/${id}/reject`, {}, { preserveScroll: true, onSuccess: () => success('Arvustus tagasi lükatud') })
const destroy = (id: number) => {
  openModal({
    title: 'Kustuta arvustus',
    message: 'Kas oled kindel, et soovid selle arvustuse kustutada?',
    confirmLabel: 'Kustuta',
    onConfirm: () => router.delete(`/admin/reviews/${id}`, {
      preserveScroll: true,
      onSuccess: () => success('Arvustus kustutatud'),
      onError: () => error('Kustutamine ebaõnnestus'),
    }),
  })
}

const formatDate = (d: string) => new Date(d).toLocaleDateString('et-EE')
</script>

<template>
  <Head title="Arvustused" />
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold text-white mb-6">Arvustused</h1>

      <!-- Tabs -->
      <div class="flex gap-2 mb-6">
        <button v-for="tab in ['pending', 'approved', 'rejected']" :key="tab"
          @click="activeTab = tab as any"
          :class="activeTab === tab ? 'bg-[#D2691E] text-white' : 'bg-[#121212] border border-[#1e1e1e] text-gray-400 hover:text-white hover:bg-[#1a1a1a]'"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
          {{ tab === 'pending' ? 'Ootel' : tab === 'approved' ? 'Kinnitatud' : 'Tagasi lükatud' }}
          <span class="ml-1 text-xs opacity-70">
            ({{ tab === 'pending' ? pending.length : tab === 'approved' ? approved.length : rejected.length }})
          </span>
        </button>
      </div>

      <!-- Reviews list -->
      <div class="space-y-4">
        <template v-for="review in (activeTab === 'pending' ? pending : activeTab === 'approved' ? approved : rejected)" :key="review.id">
          <div class="bg-[#1a1a1a] rounded-xl p-5 border border-[#2a2a2a]">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <div class="w-9 h-9 rounded-full bg-[#D2691E]/20 flex items-center justify-center">
                    <span class="text-[#D2691E] font-bold">{{ review.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="text-white font-semibold">{{ review.name }}</p>
                    <p class="text-gray-500 text-xs">{{ formatDate(review.created_at) }}</p>
                  </div>
                  <div class="flex gap-0.5 ml-2">
                    <span v-for="j in 5" :key="j" :class="j <= review.rating ? 'text-yellow-500' : 'text-gray-700'" class="text-sm">★</span>
                  </div>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed">{{ review.content }}</p>
              </div>

              <!-- Actions -->
              <div class="flex flex-col gap-2 flex-shrink-0">
                <button v-if="activeTab !== 'approved'" @click="approve(review.id)"
                  class="px-3 py-1.5 bg-green-600/20 text-green-400 hover:bg-green-600/40 rounded-lg text-xs font-medium transition-all">
                  ✓ Kinnita
                </button>
                <button v-if="activeTab !== 'rejected'" @click="reject(review.id)"
                  class="px-3 py-1.5 bg-yellow-600/20 text-yellow-400 hover:bg-yellow-600/40 rounded-lg text-xs font-medium transition-all">
                  ✗ Lükka tagasi
                </button>
                <button @click="destroy(review.id)"
                  class="px-3 py-1.5 bg-red-600/20 text-red-400 hover:bg-red-600/40 rounded-lg text-xs font-medium transition-all">
                  🗑 Kustuta
                </button>
              </div>
            </div>
          </div>
        
    <Teleport to="body">
      <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="modal.show = false">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" />
          <div class="relative bg-[#161616] border border-white/10 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="h-1 w-full bg-gradient-to-r from-[#D2691E] to-[#B8571A]" />
            <div class="p-6">
              <h3 class="text-lg font-bold text-white mb-1">{{ modal.title }}</h3>
              <p class="text-sm text-gray-400">{{ modal.message }}</p>
              <div class="flex gap-3 mt-6">
                <button @click="modal.show = false" class="flex-1 py-3 rounded-xl border border-white/10 text-gray-300 hover:bg-white/5 transition font-semibold text-sm">Tühista</button>
                <button @click="modal.onConfirm(); modal.show = false" class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm transition">{{ modal.confirmLabel }}</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

</template>

        <div v-if="(activeTab === 'pending' ? pending : activeTab === 'approved' ? approved : rejected).length === 0"
          class="text-center py-16 text-gray-600">
          <p class="text-4xl mb-3">📭</p>
          <p>Arvustusi pole</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>