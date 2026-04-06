<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { useToast } from '@/composables/useToast'
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, X, Check } from 'lucide-vue-next'

const { success, error } = useToast()

interface AddonItem {
  id: number
  type: 'size' | 'drink' | 'sauce' | 'fries'
  name: string
  price: number
  slug: string | null
  is_available: boolean
  sort_order: number
}

const props = defineProps<{
  grouped: Record<string, AddonItem[]>
  typeLabels: Record<string, string>
}>()

// Flash messages
const page = usePage()
const flash = computed(() => (page.props.flash as any) ?? {})

// Watch flash and show toasts
import { watch } from 'vue'
watch(() => flash.value, (f) => {
  if (f?.success) success(f.success)
  if (f?.error)   error(f.error)
}, { deep: true })

// Active tab
const activeType = ref<string>(Object.keys(props.grouped)[0] ?? 'size')

const typeIcons: Record<string, string> = {
  size:  '📏',
  drink: '🥤',
  sauce: '🧴',
  fries: '🍟',
}

// Modal state
const showModal = ref(false)
const editingId = ref<number | null>(null)

const emptyForm = () => ({
  type: activeType.value as AddonItem['type'],
  name: '',
  price: 0,
  slug: '',
  is_available: true,
  sort_order: 0,
})

const form = reactive(emptyForm())

const openCreate = () => {
  editingId.value = null
  Object.assign(form, emptyForm())
  form.type = activeType.value as AddonItem['type']
  showModal.value = true
}

const openEdit = (item: AddonItem) => {
  editingId.value = item.id
  Object.assign(form, { ...item, slug: item.slug ?? '' })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingId.value = null
}

const submit = () => {
  if (!form.name.trim()) {
    error('Nimi on kohustuslik')
    return
  }
  const data = { ...form, price: Number(form.price) || 0, sort_order: Number(form.sort_order) || 0 }

  if (editingId.value) {
    router.put(`/admin/addons/${editingId.value}`, data, {
      preserveScroll: true,
      onSuccess: closeModal,
    })
  } else {
    router.post('/admin/addons', data, {
      preserveScroll: true,
      onSuccess: closeModal,
    })
  }
}

// Modal confirm for delete
const deleteModal = reactive({ show: false, id: 0, name: '' })
const confirmDelete = (item: AddonItem) => {
  deleteModal.id = item.id
  deleteModal.name = item.name
  deleteModal.show = true
}
const doDelete = () => {
  router.delete(`/admin/addons/${deleteModal.id}`, { preserveScroll: true })
  deleteModal.show = false
}

const toggle = (item: AddonItem) => {
  router.post(`/admin/addons/${item.id}/toggle`, {}, { preserveScroll: true })
}

const formatPrice = (price: number) =>
  price === 0 ? 'Tasuta' : `+€${Number(price).toFixed(2)}`
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-xl lg:text-2xl font-bold">Lisandite haldus</h2>
          <p class="text-sm text-gray-400 mt-1">Halda menüü lisandeid: suurused, joogid, kastmed, friikartulid</p>
        </div>
        <button
          @click="openCreate"
          class="flex items-center gap-2 px-4 py-2 bg-[#D2691E] hover:bg-[#B8571A] text-white rounded-lg font-semibold transition text-sm"
        >
          <Plus :size="16" /> Lisa lisand
        </button>
      </div>
    </template>

    <!-- Type tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
      <button
        v-for="(label, type) in typeLabels"
        :key="type"
        @click="activeType = type"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all flex-shrink-0',
          activeType === type
            ? 'bg-[#D2691E] text-white shadow-lg shadow-[#D2691E]/20'
            : 'bg-[#121212] border border-[#1e1e1e] text-gray-400 hover:text-white hover:bg-[#1a1a1a]'
        ]"
      >
        <span>{{ typeIcons[type] }}</span>
        {{ label }}
        <span class="text-xs opacity-70">({{ grouped[type]?.length ?? 0 }})</span>
      </button>
    </div>

    <!-- Items list -->
    <div class="bg-[#121212] border border-[#1e1e1e] rounded-xl overflow-hidden">
      <!-- Table header -->
      <div class="grid grid-cols-12 px-5 py-3 bg-[#0d0d0d] border-b border-[#1e1e1e] text-xs font-semibold text-gray-500 uppercase tracking-wider">
        <div class="col-span-1">Järjek.</div>
        <div class="col-span-4">Nimi</div>
        <div class="col-span-2">Slug</div>
        <div class="col-span-2">Hind</div>
        <div class="col-span-2">Staatus</div>
        <div class="col-span-1 text-right">Tegevus</div>
      </div>

      <div v-if="grouped[activeType]?.length > 0" class="divide-y divide-[#1a1a1a]">
        <div
          v-for="item in grouped[activeType]"
          :key="item.id"
          class="grid grid-cols-12 items-center px-5 py-4 hover:bg-[#161616] transition-colors"
          :class="!item.is_available ? 'opacity-50' : ''"
        >
          <!-- Sort order -->
          <div class="col-span-1 text-gray-600 font-mono text-sm">#{{ item.sort_order }}</div>

          <!-- Name -->
          <div class="col-span-4">
            <span class="font-semibold text-white text-sm">{{ item.name }}</span>
          </div>

          <!-- Slug -->
          <div class="col-span-2">
            <code v-if="item.slug" class="text-xs text-[#D2691E] bg-[#0a0a0a] px-2 py-0.5 rounded border border-[#1e1e1e]">
              {{ item.slug }}
            </code>
            <span v-else class="text-gray-700 text-xs">—</span>
          </div>

          <!-- Price -->
          <div class="col-span-2">
            <span
              :class="item.price === 0 ? 'text-gray-500' : 'text-[#D2691E] font-bold'"
              class="text-sm"
            >{{ formatPrice(item.price) }}</span>
          </div>

          <!-- Status -->
          <div class="col-span-2">
            <span
              :class="item.is_available
                ? 'bg-green-500/15 text-green-400'
                : 'bg-gray-700/30 text-gray-500'"
              class="text-xs font-medium px-2.5 py-1 rounded-full"
            >
              {{ item.is_available ? '● Saadaval' : '○ Peidetud' }}
            </span>
          </div>

          <!-- Actions -->
          <div class="col-span-1 flex items-center justify-end gap-1">
            <button
              @click="toggle(item)"
              :title="item.is_available ? 'Peida' : 'Näita'"
              :class="item.is_available ? 'text-green-400 hover:bg-green-500/10' : 'text-gray-600 hover:bg-[#1e1e1e] hover:text-gray-400'"
              class="p-1.5 rounded-lg transition-all"
            >
              <ToggleRight v-if="item.is_available" :size="16" />
              <ToggleLeft v-else :size="16" />
            </button>
            <button
              @click="openEdit(item)"
              class="p-1.5 rounded-lg text-gray-500 hover:text-[#D2691E] hover:bg-[#D2691E]/10 transition-all"
            >
              <Pencil :size="14" />
            </button>
            <button
              @click="confirmDelete(item)"
              class="p-1.5 rounded-lg text-gray-600 hover:text-red-400 hover:bg-red-500/10 transition-all"
            >
              <Trash2 :size="14" />
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-16 text-gray-600">
        <p class="text-4xl mb-3">{{ typeIcons[activeType] }}</p>
        <p class="text-sm">Seda tüüpi lisandeid pole veel lisatud.</p>
        <button @click="openCreate" class="mt-4 px-4 py-2 bg-[#D2691E] text-white rounded-lg text-sm font-semibold hover:bg-[#B8571A] transition">
          Lisa esimene
        </button>
      </div>
    </div>

    <!-- Info card -->
    <div class="mt-6 bg-[#0d1520] border border-blue-500/20 rounded-xl p-4 text-sm text-blue-300">
      <p class="font-semibold mb-1">ℹ️ Kuidas lisandid töötavad</p>
      <p class="text-blue-400/70 leading-relaxed">
        Lisandid kuvatakse kliendile menüü toote lisamise modaalaknas. <strong>Suurused</strong> on valikuga (üks korraga),
        <strong>joogid</strong> ja <strong>kastmed</strong> on märkeruuduga (mitu korraga),
        <strong>friikartulid</strong> on raadionupuga (üks korraga). Slug <code class="text-blue-300">none</code> friikartuli juures tähendab "Ei soovi" valikut.
      </p>
    </div>

    <!-- ── Create/Edit Modal ── -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal" />
          <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
          >
            <div v-if="showModal" class="relative z-10 w-full max-w-md bg-[#111111] border border-[#222222] rounded-2xl shadow-2xl overflow-hidden">
              <!-- Header -->
              <div class="flex items-center justify-between px-6 py-4 border-b border-[#1e1e1e]">
                <h3 class="text-lg font-bold text-white">
                  {{ editingId ? 'Muuda lisandit' : 'Lisa uus lisand' }}
                </h3>
                <button @click="closeModal" class="p-1.5 rounded-lg text-gray-500 hover:text-white hover:bg-[#1e1e1e] transition">
                  <X :size="18" />
                </button>
              </div>

              <!-- Body -->
              <div class="px-6 py-5 space-y-4">
                <!-- Type -->
                <div>
                  <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Tüüp</label>
                  <div class="grid grid-cols-4 gap-2">
                    <button
                      v-for="(label, t) in typeLabels"
                      :key="t"
                      @click="form.type = t as any"
                      :class="[
                        'flex flex-col items-center gap-1 py-2.5 rounded-xl border-2 text-xs font-semibold transition-all',
                        form.type === t
                          ? 'border-[#D2691E] bg-[#D2691E]/10 text-white'
                          : 'border-[#1e1e1e] bg-[#0B0B0B] text-gray-500 hover:border-[#D2691E]/40'
                      ]"
                    >
                      <span class="text-lg">{{ typeIcons[t] }}</span>
                      {{ label }}
                    </button>
                  </div>
                </div>

                <!-- Name -->
                <div>
                  <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Nimi *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    placeholder="nt. Coca-Cola 0.5L"
                    class="w-full px-4 py-2.5 bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-[#D2691E] transition"
                  />
                </div>

                <!-- Price -->
                <div>
                  <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Lisahind (€)</label>
                  <input
                    v-model.number="form.price"
                    type="number"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    class="w-full px-4 py-2.5 bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-[#D2691E] transition"
                  />
                  <p class="text-xs text-gray-600 mt-1">0.00 = tasuta / kaasas arvel</p>
                </div>

                <!-- Slug + Sort order in row -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Slug (valikuline)</label>
                    <input
                      v-model="form.slug"
                      type="text"
                      placeholder="nt. coca-cola"
                      class="w-full px-4 py-2.5 bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg text-white text-sm placeholder-gray-600 focus:outline-none focus:border-[#D2691E] transition"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Järjekord</label>
                    <input
                      v-model.number="form.sort_order"
                      type="number"
                      min="0"
                      class="w-full px-4 py-2.5 bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg text-white text-sm text-center focus:outline-none focus:border-[#D2691E] transition"
                    />
                  </div>
                </div>

                <!-- Available toggle -->
                <div class="flex items-center gap-3">
                  <div
                    @click="form.is_available = !form.is_available"
                    class="relative w-11 h-6 rounded-full cursor-pointer transition-all"
                    :class="form.is_available ? 'bg-[#D2691E]' : 'bg-[#2a2a2a]'"
                  >
                    <span
                      class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                      :class="form.is_available ? 'translate-x-5' : 'translate-x-0'"
                    />
                  </div>
                  <span class="text-sm font-medium text-gray-300">Saadaval klientidele</span>
                </div>
              </div>

              <!-- Footer -->
              <div class="px-6 pb-5 flex gap-3 justify-end">
                <button @click="closeModal" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white bg-[#1a1a1a] hover:bg-[#222222] transition">
                  Tühista
                </button>
                <button
                  @click="submit"
                  class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#D2691E] hover:bg-[#B8571A] transition shadow-lg shadow-[#D2691E]/20"
                >
                  <Check :size="16" />
                  {{ editingId ? 'Salvesta' : 'Lisa lisand' }}
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- ── Delete Confirm Modal ── -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="deleteModal.show = false">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" />
          <div class="relative z-10 w-full max-w-sm bg-[#1a1a1a] border border-[#2a2a2a] rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 rounded-xl bg-red-500/15 text-red-400 flex items-center justify-center flex-shrink-0">
                <Trash2 :size="22" />
              </div>
              <div>
                <h3 class="text-lg font-bold text-white">Kustuta lisand</h3>
                <p class="text-sm text-gray-400 mt-0.5">„{{ deleteModal.name }}" kustutatakse jäädavalt.</p>
              </div>
            </div>
            <div class="flex gap-3 mt-6">
              <button @click="deleteModal.show = false" class="flex-1 py-3 rounded-xl border border-white/10 text-gray-300 hover:bg-white/5 transition font-semibold text-sm">
                Tühista
              </button>
              <button @click="doDelete" class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm transition">
                Kustuta
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>
