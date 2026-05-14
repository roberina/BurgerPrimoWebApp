<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, ChefHat, Leaf, Droplets, Package, Wheat, Flame, LayoutGrid, X } from 'lucide-vue-next'

const { success, error } = useToast()

interface Ingredient {
  id: number
  name: string
  category: string
  price: number
  is_available: boolean
}

interface Props {
  ingredients: Record<string, Ingredient[]>
  stats: { total: number; buns: number; patties: number; vegetables: number; sauces: number; extras: number }
}

const props = defineProps<Props>()

const categoryMeta: Record<string, { label: string; icon: any; statKey: keyof Props['stats'] }> = {
  patties:    { label: 'Pihvid', icon: Flame,    statKey: 'patties' },
  vegetables: { label: 'Köögiviljad',  icon: Leaf,     statKey: 'vegetables' },
  sauces:     { label: 'Kastmed ja lisandid',      icon: Droplets, statKey: 'sauces' },
  extras:     { label: 'Juust',     icon: Package,  statKey: 'extras' },
}

// ── Filters (multi-select) ────────────────────────────────────────────────────
const selectedCategories = ref<Set<string>>(new Set())

const toggleCategory = (key: string) => {
  const s = new Set(selectedCategories.value)
  if (s.has(key)) s.delete(key)
  else s.add(key)
  selectedCategories.value = s
  selectedIds.value = new Set()
}
const clearCategories = () => {
  selectedCategories.value = new Set()
  selectedIds.value = new Set()
}
const isAllFilter = computed(() => selectedCategories.value.size === 0)
const showCategoryBadge = computed(() => selectedCategories.value.size !== 1)

const allItems = computed(() => Object.values(props.ingredients).flat())
const activeItems = computed(() =>
  isAllFilter.value
    ? allItems.value
    : allItems.value.filter(i => selectedCategories.value.has(i.category))
)

// ── Row selection (bulk) ──────────────────────────────────────────────────────
const selectedIds = ref<Set<number>>(new Set())

const toggleSelect = (id: number) => {
  const s = new Set(selectedIds.value)
  if (s.has(id)) s.delete(id)
  else s.add(id)
  selectedIds.value = s
}
const isAllSelected = computed(() =>
  activeItems.value.length > 0 && activeItems.value.every(i => selectedIds.value.has(i.id))
)
const isIndeterminate = computed(() =>
  !isAllSelected.value && activeItems.value.some(i => selectedIds.value.has(i.id))
)
const toggleSelectAll = () => {
  const s = new Set(selectedIds.value)
  if (isAllSelected.value) activeItems.value.forEach(i => s.delete(i.id))
  else activeItems.value.forEach(i => s.add(i.id))
  selectedIds.value = s
}

// ── Modals ────────────────────────────────────────────────────────────────────
const modal = reactive({ show: false, title: '', message: '', onConfirm: () => {} })
const openModal = (opts: Omit<typeof modal, 'show'>) => Object.assign(modal, { show: true, ...opts })

const deleteIngredient = (id: number, name: string) => openModal({
  title: 'Kustuta koostisosa',
  message: `„${name}" kustutatakse jäädavalt.`,
  onConfirm: () => router.delete(`/admin/ingredients/${id}`, {
    preserveScroll: true,
    onSuccess: () => success('Koostisosa kustutatud'),
    onError: () => error('Kustutamine ebaõnnestus'),
  }),
})

const bulkDelete = () => {
  const count = selectedIds.value.size
  openModal({
    title: `Kustuta ${count} koostisosa`,
    message: `${count} valitud koostisosa kustutatakse jäädavalt.`,
    onConfirm: () => router.delete('/admin/ingredients/bulk', {
      data: { ids: [...selectedIds.value] },
      preserveScroll: true,
      onSuccess: () => { success(`${count} koostisosa kustutatud`); selectedIds.value = new Set() },
      onError: () => error('Kustutamine ebaõnnestus'),
    }),
  })
}

const toggle = (id: number) => router.post(`/admin/ingredients/${id}/toggle`, {}, {
  preserveScroll: true,
  onSuccess: () => success('Saadavus uuendatud'),
})

const formatPrice = (price: number) => price === 0 ? 'Tasuta' : `+€${Number(price).toFixed(2)}`
</script>

<template>
  <Head title="Burger Koostisosad" />
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Burger koostisosad</h1>
          <p class="text-xs text-zinc-500">Burgeri ehitaja koostisosad kategooriate kaupa</p>
        </div>
        <Link
          href="/admin/ingredients/create"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
        >
          <Plus :size="13" />
          <span class="hidden sm:inline">Lisa koostisosa</span>
        </Link>
      </div>
    </template>

    <!-- Confirm modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="modal.show = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">{{ modal.title }}</h3>
              <p class="text-xs text-zinc-400">{{ modal.message }}</p>
              <div class="flex gap-2 mt-5">
                <button @click="modal.show = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="modal.onConfirm(); modal.show = false" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Kustuta</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Stats -->
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-5">
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-3 py-3 text-center sm:col-span-1 col-span-3">
        <p class="text-xl font-bold text-zinc-100">{{ stats.total }}</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">Kokku</p>
      </div>
      <div v-for="(meta, key) in categoryMeta" :key="key" class="bg-[#18181b] border border-[#27272a] rounded-lg px-3 py-3 text-center">
        <p class="text-xl font-bold text-zinc-100">{{ stats[meta.statKey] }}</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">{{ meta.label }}</p>
      </div>
    </div>

    <!-- Category filters (multi-select) -->
    <div class="flex gap-1 mb-5 overflow-x-auto pb-1">
      <button
        @click="clearCategories"
        :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex-shrink-0', isAllFilter ? 'bg-orange-500/15 text-orange-400' : 'bg-[#18181b] border border-[#27272a] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a]']"
      >
        <LayoutGrid :size="13" />
        Kõik
        <span :class="['inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-semibold', isAllFilter ? 'bg-orange-500/20 text-orange-300' : 'bg-[#27272a] text-zinc-600']">
          {{ stats.total }}
        </span>
      </button>
      <button
        v-for="(meta, key) in categoryMeta"
        :key="key"
        @click="toggleCategory(key)"
        :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex-shrink-0', selectedCategories.has(key) ? 'bg-orange-500/15 text-orange-400 border border-orange-500/30' : 'bg-[#18181b] border border-[#27272a] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a]']"
      >
        <component :is="meta.icon" :size="13" />
        {{ meta.label }}
        <span :class="['inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-semibold', selectedCategories.has(key) ? 'bg-orange-500/20 text-orange-300' : 'bg-[#27272a] text-zinc-600']">
          {{ ingredients[key]?.length ?? 0 }}
        </span>
      </button>
    </div>

    <!-- Bulk action bar -->
    <Transition
      enter-active-class="transition-all duration-150 ease-out"
      enter-from-class="opacity-0 -translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-1"
    >
      <div v-if="selectedIds.size > 0" class="flex items-center justify-between gap-3 mb-3 px-3 py-2 bg-[#1c1c1f] border border-[#3f3f46] rounded-lg">
        <span class="text-xs text-zinc-400">
          <span class="font-semibold text-zinc-100">{{ selectedIds.size }}</span> valitud
        </span>
        <div class="flex items-center gap-2">
          <button
            @click="selectedIds = new Set()"
            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs text-zinc-500 hover:text-zinc-200 transition-colors rounded-md hover:bg-[#27272a]"
          >
            <X :size="11" /> Tühista valik
          </button>
          <button
            @click="bulkDelete"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors"
          >
            <Trash2 :size="12" /> Kustuta valitud
          </button>
        </div>
      </div>
    </Transition>

    <!-- Table -->
    <div class="bg-[#18181b] border border-[#27272a] rounded-lg overflow-hidden">
      <div class="grid grid-cols-12 px-4 py-2.5 border-b border-[#27272a] text-xs font-medium text-zinc-500">
        <div class="col-span-1 flex items-center">
          <input
            type="checkbox"
            :checked="isAllSelected"
            :indeterminate="isIndeterminate"
            @change="toggleSelectAll"
            class="w-3.5 h-3.5 rounded border-[#3f3f46] bg-[#27272a] accent-orange-500 cursor-pointer"
          />
        </div>
        <div class="col-span-4">Nimi</div>
        <div class="col-span-3">Hind</div>
        <div class="col-span-2">Staatus</div>
        <div class="col-span-2 text-right">Tegevus</div>
      </div>

      <div v-if="activeItems.length > 0" class="divide-y divide-[#27272a]">
        <div
          v-for="item in activeItems"
          :key="item.id"
          class="grid grid-cols-12 items-center px-4 py-3 hover:bg-[#27272a]/30 transition-colors"
          :class="[!item.is_available ? 'opacity-50' : '', selectedIds.has(item.id) ? 'bg-orange-500/5' : '']"
        >
          <div class="col-span-1 flex items-center">
            <input
              type="checkbox"
              :checked="selectedIds.has(item.id)"
              @change="toggleSelect(item.id)"
              class="w-3.5 h-3.5 rounded border-[#3f3f46] bg-[#27272a] accent-orange-500 cursor-pointer"
            />
          </div>
          <div class="col-span-4 flex items-center gap-2 min-w-0">
            <span class="text-sm font-medium text-zinc-100 truncate">{{ item.name }}</span>
            <span v-if="showCategoryBadge" class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-[#27272a] text-zinc-500">{{ categoryMeta[item.category]?.label ?? item.category }}</span>
          </div>
          <div class="col-span-3">
            <span :class="item.price === 0 ? 'text-zinc-500' : 'text-orange-400 font-medium'" class="text-sm">{{ formatPrice(item.price) }}</span>
          </div>
          <div class="col-span-2">
            <span :class="item.is_available ? 'bg-green-500/15 text-green-400' : 'bg-[#27272a] text-zinc-500'" class="text-xs font-medium px-2 py-0.5 rounded-full">
              {{ item.is_available ? 'Saadaval' : 'Peidetud' }}
            </span>
          </div>
          <div class="col-span-2 flex items-center justify-end gap-0.5">
            <button @click="toggle(item.id)" :title="item.is_available ? 'Peida' : 'Näita'" :class="item.is_available ? 'text-green-400 hover:bg-green-500/10' : 'text-zinc-600 hover:bg-[#27272a] hover:text-zinc-400'" class="p-1.5 rounded-md transition-all">
              <ToggleRight v-if="item.is_available" :size="14" /><ToggleLeft v-else :size="14" />
            </button>
            <Link :href="`/admin/ingredients/${item.id}/edit`" class="p-1.5 rounded-md text-zinc-500 hover:text-orange-400 hover:bg-orange-500/10 transition-all">
              <Pencil :size="12" />
            </Link>
            <button @click="deleteIngredient(item.id, item.name)" class="p-1.5 rounded-md text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition-all">
              <Trash2 :size="12" />
            </button>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center py-12 text-center">
        <ChefHat :size="24" class="text-zinc-700 mb-3" />
        <p class="text-sm text-zinc-500 mb-1">Seda tüüpi koostisosi pole veel lisatud</p>
        <Link href="/admin/ingredients/create" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors">
          <Plus :size="13" /> Lisa esimene
        </Link>
      </div>
    </div>
  </AdminLayout>
</template>
