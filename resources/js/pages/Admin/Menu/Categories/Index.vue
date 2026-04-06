<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { reactive } from 'vue';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();
const modal = reactive({ show: false, title: '', message: '', confirmLabel: 'Kinnita', onConfirm: () => {} });
const openModal = (opts: Omit<typeof modal, 'show'>) => { Object.assign(modal, { show: true, ...opts }); };
import { Plus, Edit, Trash2, GripVertical, Eye, EyeOff } from 'lucide-vue-next';

interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  sort_order: number;
  is_active: boolean;
  items_count: number;
}

interface Props {
  categories: Category[];
}

defineProps<Props>();

const deleteCategory = (categoryId: number, name: string) => {
  openModal({
    title: 'Kustuta kategooria',
    message: `Kas oled kindel, et soovid kustutada "${name}"?`,
    confirmLabel: 'Kustuta',
    onConfirm: () => router.delete(`/admin/menu/categories/${categoryId}` as any, {
      preserveScroll: true,
      onSuccess: () => success('Kategooria kustutatud'),
      onError: () => error('Kustutamine ebaõnnestus'),
    }),
  });
};
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-xl lg:text-2xl font-bold">Menüü kategooriad</h2>
          <p class="text-sm text-gray-400 mt-1">Halda menüü kategooriaid ja järjekorda</p>
        </div>
        <Link
          href="/admin/menu/categories/create"
          class="bg-[#D2691E] hover:bg-[#B8571A] text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2"
        >
          <Plus :size="18" />
          <span class="hidden sm:inline">Lisa kategooria</span>
        </Link>
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

    <!-- Categories Table -->
    <div v-if="categories.length > 0" class="bg-[#111111] rounded-xl border border-[#1e1e1e] overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-[#0a0a0a] border-b border-[#1e1e1e]">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Järjekord
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Kategooria
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Slug
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Tooteid
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Staatus
              </th>
              <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Tegevused
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-800">
            <tr
              v-for="category in categories"
              :key="category.id"
              class="hover:bg-[#121212] transition"
            >
              <!-- Sort Order -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <GripVertical :size="16" class="text-gray-600" />
                  <span class="text-gray-400 font-mono text-sm">#{{ category.sort_order }}</span>
                </div>
              </td>

              <!-- Category Name -->
              <td class="px-6 py-4">
                <div>
                  <p class="font-semibold text-white">{{ category.name }}</p>
                  <p v-if="category.description" class="text-xs text-gray-500 mt-1 line-clamp-1">
                    {{ category.description }}
                  </p>
                </div>
              </td>

              <!-- Slug -->
              <td class="px-6 py-4">
                <code class="text-xs text-[#D2691E] bg-[#0a0a0a] px-2 py-1 rounded border border-[#1e1e1e]">
                  {{ category.slug }}
                </code>
              </td>

              <!-- Items Count -->
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#0B0B0B] text-gray-300 text-xs font-medium">
                  <div class="w-1.5 h-1.5 rounded-full bg-[#D2691E]"></div>
                  {{ category.items_count }} toode(t)
                </span>
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold',
                    category.is_active
                      ? 'bg-green-600/10 text-green-400'
                      : 'bg-[#1a1a1a] text-gray-400'
                  ]"
                >
                  <component :is="category.is_active ? Eye : EyeOff" :size="14" />
                  {{ category.is_active ? 'Aktiivne' : 'Peidetud' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/menu/items/create?category_id=${category.id}`"
                    class="p-2 bg-[#D2691E]/10 hover:bg-[#D2691E]/20 text-[#D2691E] rounded-lg transition"
                    title="Lisa toode sellesse kategooriasse"
                  >
                    <Plus :size="16" />
                  </Link>
                  <Link
                    :href="`/admin/menu/categories/${category.id}/edit`"
                    class="p-2 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 rounded-lg font-semibold transition"
                    title="Muuda"
                  >
                    <Edit :size="16" />
                  </Link>
                  <button
                    @click="deleteCategory(category.id, category.name)"
                    class="p-2 bg-red-600/10 hover:bg-red-600/20 text-red-400 rounded-lg font-semibold transition"
                    title="Kustuta"
                  >
                    <Trash2 :size="16" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16 bg-[#111111] rounded-xl border border-[#1e1e1e]">
      <div class="w-20 h-20 bg-[#0B0B0B] rounded-full flex items-center justify-center mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-white mb-2">Kategooriaid pole veel</h3>
      <p class="text-gray-400 mb-6">Lisa oma esimene menüü kategooria alustamiseks</p>
      <Link
        href="/admin/menu/categories/create"
        class="inline-flex items-center gap-2 bg-[#D2691E] hover:bg-[#B8571A] text-white px-6 py-3 rounded-lg font-semibold transition"
      >
        <Plus :size="18" />
        Lisa kategooria
      </Link>
    </div>

    <!-- Quick Info Cards -->
    <div v-if="categories.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
      <div class="bg-[#111111] rounded-lg border border-[#1e1e1e] p-4">
        <p class="text-sm text-gray-400 mb-1">Kokku kategooriaid</p>
        <p class="text-2xl font-bold text-white">{{ categories.length }}</p>
      </div>
      <div class="bg-[#111111] rounded-lg border border-[#1e1e1e] p-4">
        <p class="text-sm text-gray-400 mb-1">Aktiivseid</p>
        <p class="text-2xl font-bold text-green-400">{{ categories.filter(c => c.is_active).length }}</p>
      </div>
      <div class="bg-[#111111] rounded-lg border border-[#1e1e1e] p-4">
        <p class="text-sm text-gray-400 mb-1">Kokku tooteid</p>
        <p class="text-2xl font-bold text-[#D2691E]">{{ categories.reduce((sum, c) => sum + c.items_count, 0) }}</p>
      </div>
    </div>
  </AdminLayout>
</template>