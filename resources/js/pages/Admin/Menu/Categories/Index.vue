<script setup lang="ts">
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import { Plus, Pencil, Trash2, Eye, EyeOff, FolderOpen } from 'lucide-vue-next'

const { success, error } = useToast()

interface Category {
  id: number
  name: string
  slug: string
  description: string | null
  sort_order: number
  is_active: boolean
  items_count: number
}

defineProps<{ categories: Category[] }>()

const modal = reactive({ show: false, title: '', message: '', onConfirm: () => {} })
const openModal = (opts: Omit<typeof modal, 'show'>) => Object.assign(modal, { show: true, ...opts })

const deleteCategory = (id: number, name: string) => openModal({
  title: 'Kustuta kategooria',
  message: `„${name}" kustutatakse jäädavalt.`,
  onConfirm: () => router.delete(`/admin/menu/categories/${id}` as any, {
    preserveScroll: true,
    onSuccess: () => success('Kategooria kustutatud'),
    onError: () => error('Kustutamine ebaõnnestus'),
  }),
})
</script>

<template>
  <Head title="Menüü Kategooriad" />
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Menüü kategooriad</h1>
          <p class="text-xs text-zinc-500">Halda menüü kategooriaid ja järjekorda</p>
        </div>
        <Link
          href="/admin/menu/categories/create"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
        >
          <Plus :size="13" />
          <span class="hidden sm:inline">Lisa kategooria</span>
        </Link>
      </div>
    </template>

    <!-- Delete modal -->
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
    <div v-if="categories.length > 0" class="grid grid-cols-3 gap-3 mb-5">
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-center">
        <p class="text-xl font-bold text-zinc-100">{{ categories.length }}</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">Kategooriaid</p>
      </div>
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-center">
        <p class="text-xl font-bold text-green-400">{{ categories.filter(c => c.is_active).length }}</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">Aktiivseid</p>
      </div>
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-center">
        <p class="text-xl font-bold text-orange-400">{{ categories.reduce((s, c) => s + c.items_count, 0) }}</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">Tooteid kokku</p>
      </div>
    </div>

    <!-- Table -->
    <div v-if="categories.length > 0" class="bg-[#18181b] border border-[#27272a] rounded-lg overflow-hidden">
      <div class="grid grid-cols-12 px-4 py-2.5 border-b border-[#27272a] text-xs font-medium text-zinc-500">
        <div class="col-span-1">#</div>
        <div class="col-span-4">Kategooria</div>
        <div class="col-span-2">Slug</div>
        <div class="col-span-2">Tooteid</div>
        <div class="col-span-2">Staatus</div>
        <div class="col-span-1 text-right">Tegevus</div>
      </div>

      <div class="divide-y divide-[#27272a]">
        <div
          v-for="category in categories"
          :key="category.id"
          class="grid grid-cols-12 items-center px-4 py-3 hover:bg-[#27272a]/30 transition-colors"
          :class="!category.is_active ? 'opacity-60' : ''"
        >
          <div class="col-span-1 text-zinc-600 font-mono text-xs">{{ category.sort_order }}</div>

          <div class="col-span-4">
            <p class="text-sm font-medium text-zinc-100">{{ category.name }}</p>
            <p v-if="category.description" class="text-xs text-zinc-600 truncate max-w-[180px]">{{ category.description }}</p>
          </div>

          <div class="col-span-2">
            <code class="text-xs text-orange-400 bg-orange-500/10 px-1.5 py-0.5 rounded border border-orange-500/20">{{ category.slug }}</code>
          </div>

          <div class="col-span-2">
            <span class="text-xs text-zinc-400 bg-[#27272a] px-2 py-0.5 rounded-full">{{ category.items_count }} tk</span>
          </div>

          <div class="col-span-2">
            <span :class="category.is_active ? 'bg-green-500/15 text-green-400' : 'bg-[#27272a] text-zinc-500'" class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full">
              <component :is="category.is_active ? Eye : EyeOff" :size="10" />
              {{ category.is_active ? 'Aktiivne' : 'Peidetud' }}
            </span>
          </div>

          <div class="col-span-1 flex items-center justify-end gap-0.5">
            <Link
              :href="`/admin/menu/items/create?category_id=${category.id}`"
              class="p-1.5 rounded-md text-zinc-500 hover:text-orange-400 hover:bg-orange-500/10 transition-all"
              title="Lisa toode sellesse kategooriasse"
            >
              <Plus :size="12" />
            </Link>
            <Link
              :href="`/admin/menu/categories/${category.id}/edit`"
              class="p-1.5 rounded-md text-zinc-500 hover:text-orange-400 hover:bg-orange-500/10 transition-all"
            >
              <Pencil :size="12" />
            </Link>
            <button
              @click="deleteCategory(category.id, category.name)"
              class="p-1.5 rounded-md text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition-all"
            >
              <Trash2 :size="12" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="flex flex-col items-center py-16 bg-[#18181b] border border-[#27272a] rounded-lg">
      <FolderOpen :size="28" class="text-zinc-700 mb-3" />
      <p class="text-sm text-zinc-500 mb-1">Kategooriaid pole veel lisatud</p>
      <Link
        href="/admin/menu/categories/create"
        class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
      >
        <Plus :size="13" /> Lisa esimene kategooria
      </Link>
    </div>
  </AdminLayout>
</template>
