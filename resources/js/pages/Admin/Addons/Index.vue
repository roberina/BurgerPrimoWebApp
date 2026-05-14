<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, X, Check, Package } from 'lucide-vue-next'

const { success, error } = useToast()

interface AddonItem {
  id: number
  type: 'size' | 'drink' | 'sauce' | 'fries'
  name: string
  name_en: string | null
  price: number
  slug: string | null
  is_available: boolean
  sort_order: number
}

const props = defineProps<{
  grouped: Record<string, AddonItem[]>
  typeLabels: Record<string, string>
}>()

const page = usePage()
const flash = computed(() => (page.props.flash as any) ?? {})

watch(() => flash.value, (f) => {
  if (f?.success) success(f.success)
  if (f?.error) error(f.error)
}, { deep: true })

const activeType = ref<string>(Object.keys(props.grouped)[0] ?? 'size')
const showModal = ref(false)
const editingId = ref<number | null>(null)

const emptyForm = () => ({
  type: activeType.value as AddonItem['type'],
  name: '',
  name_en: '',
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
  if (!form.name.trim()) { error('Nimi on kohustuslik'); return }
  const data = { ...form, price: Number(form.price) || 0, sort_order: Number(form.sort_order) || 0 }
  if (editingId.value) {
    router.put(`/admin/addons/${editingId.value}`, data, { preserveScroll: true, onSuccess: closeModal })
  } else {
    router.post('/admin/addons', data, { preserveScroll: true, onSuccess: closeModal })
  }
}

const deleteModal = reactive({ show: false, id: 0, name: '' })
const confirmDelete = (item: AddonItem) => { deleteModal.id = item.id; deleteModal.name = item.name; deleteModal.show = true }
const doDelete = () => { router.delete(`/admin/addons/${deleteModal.id}`, { preserveScroll: true }); deleteModal.show = false }

const toggle = (item: AddonItem) => router.post(`/admin/addons/${item.id}/toggle`, {}, { preserveScroll: true })

const formatPrice = (price: number) => price === 0 ? 'Tasuta' : `+€${Number(price).toFixed(2)}`
</script>

<template>
  <Head title="Lisandid" />
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Lisandite haldus</h1>
          <p class="text-xs text-zinc-500">Suurused, joogid, kastmed, friikartulid</p>
        </div>
        <button
          @click="openCreate"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
        >
          <Plus :size="13" />
          <span class="hidden sm:inline">Lisa lisand</span>
        </button>
      </div>
    </template>

    <!-- Delete confirm modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="deleteModal.show = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Kustuta lisand</h3>
              <p class="text-xs text-zinc-400">„{{ deleteModal.name }}" kustutatakse jäädavalt.</p>
              <div class="flex gap-2 mt-5">
                <button @click="deleteModal.show = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="doDelete" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Kustuta</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Create/Edit modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal" />
          <div class="relative z-10 w-full max-w-md bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#27272a]">
              <h3 class="text-sm font-semibold text-zinc-100">{{ editingId ? 'Muuda lisandit' : 'Lisa uus lisand' }}</h3>
              <button @click="closeModal" class="p-1.5 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition"><X :size="15" /></button>
            </div>

            <div class="px-5 py-4 space-y-4">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Tüüp</label>
                <div class="grid grid-cols-4 gap-2">
                  <button
                    v-for="(label, t) in typeLabels"
                    :key="t"
                    @click="form.type = t as any"
                    :class="['flex flex-col items-center gap-1 py-2.5 rounded-lg border-2 text-xs font-medium transition-all', form.type === t ? 'border-orange-500/50 bg-orange-500/10 text-orange-400' : 'border-[#27272a] bg-[#09090b] text-zinc-500 hover:border-orange-500/30 hover:text-zinc-300']"
                  >
                    {{ label }}
                  </button>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-zinc-400 mb-1.5">Nimi (ET) *</label>
                  <input v-model="form.name" type="text" placeholder="nt. Coca-Cola 0.5L" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-zinc-400 mb-1.5">Name (EN)</label>
                  <input v-model="form.name_en" type="text" placeholder="e.g. Coca-Cola 0.5L" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Lisahind (€)</label>
                <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="0.00" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
                <p class="text-xs text-zinc-700 mt-1">0.00 = tasuta / kaasas arvel</p>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-zinc-400 mb-1.5">Slug (valikuline)</label>
                  <input v-model="form.slug" type="text" placeholder="nt. coca-cola" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-zinc-400 mb-1.5">Järjekord</label>
                  <input v-model.number="form.sort_order" type="number" min="0" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 text-center focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
                </div>
              </div>

              <label class="flex items-center gap-2.5 cursor-pointer">
                <input v-model="form.is_available" type="checkbox" class="w-4 h-4 rounded border-[#3f3f46] bg-[#09090b] text-orange-600 focus:ring-orange-600 focus:ring-offset-0" />
                <span class="text-sm text-zinc-300">Saadaval klientidele</span>
              </label>
            </div>

            <div class="px-5 pb-5 flex gap-2 justify-end border-t border-[#27272a] pt-4">
              <button @click="closeModal" class="px-4 py-2 rounded-md text-xs font-medium text-zinc-400 hover:text-zinc-100 bg-[#27272a] hover:bg-[#3f3f46] transition">Tühista</button>
              <button @click="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 transition">
                <Check :size="13" />
                {{ editingId ? 'Salvesta' : 'Lisa lisand' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Type tabs -->
    <div class="flex gap-1 mb-5 overflow-x-auto pb-1">
      <button
        v-for="(label, type) in typeLabels"
        :key="type"
        @click="activeType = type"
        :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex-shrink-0', activeType === type ? 'bg-orange-500/15 text-orange-400' : 'bg-[#18181b] border border-[#27272a] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a]']"
      >
        {{ label }}
        <span :class="['inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-semibold', activeType === type ? 'bg-orange-500/20 text-orange-300' : 'bg-[#27272a] text-zinc-600']">
          {{ grouped[type]?.length ?? 0 }}
        </span>
      </button>
    </div>

    <!-- Items table -->
    <div class="bg-[#18181b] border border-[#27272a] rounded-lg overflow-hidden">
      <div class="grid grid-cols-12 px-4 py-2.5 border-b border-[#27272a] text-xs font-medium text-zinc-500">
        <div class="col-span-1">#</div>
        <div class="col-span-4">Nimi</div>
        <div class="col-span-2">Slug</div>
        <div class="col-span-2">Hind</div>
        <div class="col-span-2">Staatus</div>
        <div class="col-span-1 text-right">Tegevus</div>
      </div>

      <div v-if="grouped[activeType]?.length > 0" class="divide-y divide-[#27272a]">
        <div
          v-for="item in grouped[activeType]"
          :key="item.id"
          class="grid grid-cols-12 items-center px-4 py-3 hover:bg-[#27272a]/30 transition-colors"
          :class="!item.is_available ? 'opacity-50' : ''"
        >
          <div class="col-span-1 text-zinc-600 font-mono text-xs">{{ item.sort_order }}</div>
          <div class="col-span-4"><span class="text-sm font-medium text-zinc-100">{{ item.name }}</span></div>
          <div class="col-span-2">
            <code v-if="item.slug" class="text-xs text-orange-400 bg-orange-500/10 px-1.5 py-0.5 rounded border border-orange-500/20">{{ item.slug }}</code>
            <span v-else class="text-zinc-700 text-xs">—</span>
          </div>
          <div class="col-span-2">
            <span :class="item.price === 0 ? 'text-zinc-500' : 'text-orange-400 font-medium'" class="text-sm">{{ formatPrice(item.price) }}</span>
          </div>
          <div class="col-span-2">
            <span :class="item.is_available ? 'bg-green-500/15 text-green-400' : 'bg-[#27272a] text-zinc-500'" class="text-xs font-medium px-2 py-0.5 rounded-full">
              {{ item.is_available ? 'Saadaval' : 'Peidetud' }}
            </span>
          </div>
          <div class="col-span-1 flex items-center justify-end gap-0.5">
            <button @click="toggle(item)" :title="item.is_available ? 'Peida' : 'Näita'" :class="item.is_available ? 'text-green-400 hover:bg-green-500/10' : 'text-zinc-600 hover:bg-[#27272a] hover:text-zinc-400'" class="p-1.5 rounded-md transition-all">
              <ToggleRight v-if="item.is_available" :size="14" /><ToggleLeft v-else :size="14" />
            </button>
            <button @click="openEdit(item)" class="p-1.5 rounded-md text-zinc-500 hover:text-orange-400 hover:bg-orange-500/10 transition-all"><Pencil :size="12" /></button>
            <button @click="confirmDelete(item)" class="p-1.5 rounded-md text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition-all"><Trash2 :size="12" /></button>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center py-12 text-center">
        <Package :size="24" class="text-zinc-700 mb-3" />
        <p class="text-sm text-zinc-500 mb-1">Seda tüüpi lisandeid pole veel lisatud</p>
        <button @click="openCreate" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors">
          <Plus :size="13" />
          Lisa esimene
        </button>
      </div>
    </div>

    <div class="mt-4 bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-xs text-zinc-500 leading-relaxed">
      <span class="font-medium text-zinc-400">Kuidas lisandid töötavad: </span>
      Lisandid kuvatakse kliendile menüü toote lisamise aknas. Suurused on valikuga (üks korraga), joogid ja kastmed on märkeruuduga (mitu korraga), friikartulid on raadionupuga (üks korraga). Slug <code class="text-zinc-400">none</code> friikartuli juures tähendab "Ei soovi" valikut.
    </div>
  </AdminLayout>
</template>
