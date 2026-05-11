<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Check, X, Trash2, Star } from 'lucide-vue-next'

const { success, error } = useToast()

const modal = reactive({ show: false, title: '', message: '', confirmLabel: 'Kinnita', onConfirm: () => {} })
const openModal = (opts: Omit<typeof modal, 'show'>) => { Object.assign(modal, { show: true, ...opts }) }

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

const approve = (id: number) =>
  router.post(`/admin/reviews/${id}/approve`, {}, {
    preserveScroll: true,
    onSuccess: () => success('Arvustus kinnitatud'),
  })

const reject = (id: number) =>
  router.post(`/admin/reviews/${id}/reject`, {}, {
    preserveScroll: true,
    onSuccess: () => success('Arvustus tagasi lükatud'),
  })

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
    <template #header>
      <div>
        <h1 class="text-sm font-semibold text-zinc-100">Arvustused</h1>
        <p class="text-xs text-zinc-500">Halda klientide arvustusi</p>
      </div>
    </template>

    <!-- Confirm modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-150"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
      >
        <div
          v-if="modal.show"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          @click.self="modal.show = false"
        >
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">{{ modal.title }}</h3>
              <p class="text-xs text-zinc-400">{{ modal.message }}</p>
              <div class="flex gap-2 mt-5">
                <button
                  @click="modal.show = false"
                  class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors"
                >
                  Tühista
                </button>
                <button
                  @click="modal.onConfirm(); modal.show = false"
                  class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors"
                >
                  {{ modal.confirmLabel }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Tabs -->
    <div class="flex gap-1 mb-5">
      <button
        v-for="tab in ['pending', 'approved', 'rejected']"
        :key="tab"
        @click="activeTab = tab as any"
        :class="[
          'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-colors',
          activeTab === tab
            ? 'bg-orange-500/15 text-orange-400'
            : 'bg-[#18181b] border border-[#27272a] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a]',
        ]"
      >
        {{ tab === 'pending' ? 'Ootel' : tab === 'approved' ? 'Kinnitatud' : 'Tagasi lükatud' }}
        <span
          :class="[
            'inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-semibold',
            activeTab === tab ? 'bg-orange-500/20 text-orange-300' : 'bg-[#27272a] text-zinc-600',
          ]"
        >
          {{ tab === 'pending' ? pending.length : tab === 'approved' ? approved.length : rejected.length }}
        </span>
      </button>
    </div>

    <!-- Reviews list -->
    <div class="space-y-3">
      <template
        v-for="review in (activeTab === 'pending' ? pending : activeTab === 'approved' ? approved : rejected)"
        :key="review.id"
      >
        <div class="bg-[#18181b] border border-[#27272a] rounded-lg p-4">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-orange-500/10 flex items-center justify-center flex-shrink-0">
                  <span class="text-orange-400 text-xs font-bold">{{ review.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-zinc-100">{{ review.name }}</p>
                  <p class="text-xs text-zinc-600">{{ formatDate(review.created_at) }}</p>
                </div>
                <div class="flex gap-0.5 ml-1">
                  <Star
                    v-for="j in 5"
                    :key="j"
                    :size="12"
                    :class="j <= review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-zinc-700'"
                  />
                </div>
              </div>
              <p class="text-sm text-zinc-300 leading-relaxed">{{ review.content }}</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-1.5 flex-shrink-0">
              <button
                v-if="activeTab !== 'approved'"
                @click="approve(review.id)"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-500/10 text-green-400 hover:bg-green-500/20 rounded-md text-xs font-medium transition-colors"
              >
                <Check :size="11" />
                Kinnita
              </button>
              <button
                v-if="activeTab !== 'rejected'"
                @click="reject(review.id)"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 rounded-md text-xs font-medium transition-colors"
              >
                <X :size="11" />
                Lükka tagasi
              </button>
              <button
                @click="destroy(review.id)"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-md text-xs font-medium transition-colors"
              >
                <Trash2 :size="11" />
                Kustuta
              </button>
            </div>
          </div>
        </div>
      </template>

      <!-- Empty state -->
      <div
        v-if="(activeTab === 'pending' ? pending : activeTab === 'approved' ? approved : rejected).length === 0"
        class="flex flex-col items-center py-12 bg-[#18181b] border border-[#27272a] rounded-lg"
      >
        <Star :size="28" class="text-zinc-700 mb-3" />
        <p class="text-sm text-zinc-500">Arvustusi pole</p>
      </div>
    </div>
  </AdminLayout>
</template>
