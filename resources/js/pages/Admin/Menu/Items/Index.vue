<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();
const modal = reactive({ show: false, title: '', message: '', confirmLabel: 'Kinnita', onConfirm: () => {} });
const openModal = (opts: Omit<typeof modal, 'show'>) => { Object.assign(modal, { show: true, ...opts }); };
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, Star, EyeOff, Search, Filter, ChevronDown, ChevronUp } from 'lucide-vue-next';

interface Category {
  id: number;
  name: string;
}

interface MenuItem {
  id: number;
  name: string;
  description: string;
  price: number;
  original_price: number | null;
  image_url: string | null;
  is_featured: boolean;
  is_active: boolean;
  discount_percentage: number | null;
  category: Category;
}

interface CategoryWithItems {
  id: number;
  name: string;
  is_active: boolean;
  items: MenuItem[];
}

interface Props {
  categories: CategoryWithItems[];
  all_categories: Category[];
  filters: {
    category_id?: string;
    search?: string;
  };
}

const props = defineProps<Props>();

const filterForm = ref({
  category_id: props.filters.category_id || '',
  search: props.filters.search || '',
});

const collapsed = ref<Record<number, boolean>>({});

const toggleCollapse = (id: number) => {
  collapsed.value[id] = !collapsed.value[id];
};

let timeout: ReturnType<typeof setTimeout>;
const applyFilters = () => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    router.get('/admin/menu/items', filterForm.value, {
      preserveState: true,
      preserveScroll: true,
    });
  }, 300);
};

const deleteItem = (itemId: number, name: string) => {
  openModal({
    title: 'Kustuta toode',
    message: `Kas oled kindel, et soovid kustutada "${name}"?`,
    confirmLabel: 'Kustuta',
    onConfirm: () => router.delete(`/admin/menu/items/${itemId}` as any, {
      preserveScroll: true,
      onSuccess: () => success('Toode kustutatud'),
      onError: () => error('Kustutamine ebaõnnestus'),
    }),
  });
};

const totalItems = () => props.categories.reduce((sum, c) => sum + c.items.length, 0);
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-xl lg:text-2xl font-bold">Menüü tooted</h2>
          <p class="text-sm text-gray-400 mt-1">Halda menüü tooteid ja hindu</p>
        </div>
        <Link
          href="/admin/menu/items/create"
          class="bg-[#D2691E] hover:bg-[#C05A18] text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2 text-sm"
        >
          <Plus :size="16" />
          <span class="hidden sm:inline">Lisa toode</span>
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

    <!-- Filters -->
    <div class="bg-[#121212] rounded-xl border border-[#1e1e1e] px-4 py-3 mb-5 flex flex-col sm:flex-row gap-3">
      <div class="flex items-center gap-2 flex-1">
        <Search :size="16" class="text-gray-500 flex-shrink-0" />
        <input
          v-model="filterForm.search"
          @input="applyFilters"
          type="text"
          class="flex-1 bg-transparent text-white text-sm placeholder-gray-600 outline-none"
          placeholder="Otsi toodet..."
        />
      </div>
      <div class="flex items-center gap-2 sm:border-l border-[#1e1e1e] sm:pl-3">
        <Filter :size="16" class="text-gray-500 flex-shrink-0" />
        <select
          v-model="filterForm.category_id"
          @change="applyFilters"
          class="bg-transparent text-sm text-gray-300 outline-none cursor-pointer"
        >
          <option value="" class="bg-[#121212]">Kõik kategooriad</option>
          <option v-for="cat in all_categories" :key="cat.id" :value="cat.id" class="bg-[#121212]">
            {{ cat.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Categories with items -->
    <div v-if="categories.length > 0" class="space-y-4">
      <div v-for="category in categories" :key="category.id" class="bg-[#121212] border border-[#1e1e1e] rounded-xl overflow-hidden">

        <!-- Category header -->
        <div
          class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-[#161616] transition-colors"
          @click="toggleCollapse(category.id)"
        >
          <div class="flex items-center gap-3">
            <h3 class="font-bold text-white text-sm uppercase tracking-wide">{{ category.name }}</h3>
            <span class="text-xs bg-[#0B0B0B] border border-[#2a2a2a] text-gray-400 px-2 py-0.5 rounded-full">
              {{ category.items.length }} toode(t)
            </span>
            <span v-if="!category.is_active" class="text-xs bg-red-500/10 text-red-400 px-2 py-0.5 rounded-full">Peidetud</span>
          </div>
          <div class="flex items-center gap-2">
            <Link
              :href="`/admin/menu/items/create?category_id=${category.id}`"
              @click.stop
              class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium text-[#D2691E] bg-[#D2691E]/10 hover:bg-[#D2691E]/20 transition-all"
              title="Lisa toode sellesse kategooriasse"
            >
              <Plus :size="13" />
              Lisa toode
            </Link>
            <component :is="collapsed[category.id] ? ChevronDown : ChevronUp" :size="16" class="text-gray-500" />
          </div>
        </div>

        <!-- Items grid -->
        <div v-show="!collapsed[category.id]" class="border-t border-[#1e1e1e]">
          <div v-if="category.items.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 p-3">
            <div
              v-for="item in category.items"
              :key="item.id"
              class="group relative bg-[#0B0B0B] hover:bg-[#161616] border border-[#1e1e1e] hover:border-[#D2691E]/20 rounded-xl overflow-hidden transition-all duration-200 flex items-stretch min-h-[100px]"
              :class="!item.is_active ? 'opacity-55' : ''"
            >
              <!-- Text -->
              <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
                <div>
                  <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                    <span v-if="item.is_featured" class="text-xs bg-yellow-500/15 text-yellow-400 px-1.5 py-0.5 rounded-full font-semibold">⭐</span>
                    <span v-if="item.discount_percentage" class="text-xs bg-[#D2691E]/15 text-[#D2691E] px-1.5 py-0.5 rounded-full font-semibold">-{{ item.discount_percentage }}%</span>
                    <span v-if="!item.is_active" class="text-xs bg-red-500/15 text-red-400 px-1.5 py-0.5 rounded-full font-semibold flex items-center gap-1">
                      <EyeOff :size="9" /> Peidetud
                    </span>
                  </div>
                  <h3 class="font-bold text-white text-sm leading-snug">{{ item.name }}</h3>
                  <p class="text-xs text-gray-500 mt-0.5 leading-relaxed line-clamp-2">{{ item.description }}</p>
                </div>
                <div class="flex items-center justify-between mt-2">
                  <div class="flex items-center gap-1.5">
                    <span v-if="item.original_price && Number(item.original_price) > Number(item.price)" class="text-xs text-gray-600 line-through">€{{ Number(item.original_price).toFixed(2) }}</span>
                    <span class="font-bold text-white text-sm">€{{ Number(item.price).toFixed(2) }}</span>
                  </div>
                  <div class="flex items-center gap-0.5">
                    <Link
                      :href="`/admin/menu/items/${item.id}/edit`"
                      class="p-1.5 rounded-lg text-gray-600 hover:text-[#D2691E] hover:bg-[#D2691E]/10 transition-all"
                      title="Muuda"
                    >
                      <Edit :size="14" />
                    </Link>
                    <button
                      @click="deleteItem(item.id, item.name)"
                      class="p-1.5 rounded-lg text-gray-600 hover:text-red-400 hover:bg-red-500/10 transition-all"
                      title="Kustuta"
                    >
                      <Trash2 :size="14" />
                    </button>
                  </div>
                </div>
              </div>
              <!-- Image -->
              <div class="relative flex-shrink-0 w-24">
                <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full bg-[#0d0d0d] flex items-center justify-center text-2xl">🍔</div>
              </div>
            </div>
          </div>

          <div v-else class="px-4 py-6 text-center text-gray-600 text-sm">
            Selles kategoorias pole tooteid.
            <Link :href="`/admin/menu/items/create?category_id=${category.id}`" class="text-[#D2691E] hover:underline ml-1">Lisa esimene →</Link>
          </div>
        </div>
      </div>

      <!-- Stats bar -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
        <div class="bg-[#121212] rounded-lg border border-[#1e1e1e] px-4 py-3">
          <p class="text-xs text-gray-500 mb-0.5">Kokku tooteid</p>
          <p class="text-xl font-bold text-white">{{ totalItems() }}</p>
        </div>
        <div class="bg-[#121212] rounded-lg border border-[#1e1e1e] px-4 py-3">
          <p class="text-xs text-gray-500 mb-0.5">Kategooriaid</p>
          <p class="text-xl font-bold text-[#D2691E]">{{ categories.length }}</p>
        </div>
        <div class="bg-[#121212] rounded-lg border border-[#1e1e1e] px-4 py-3">
          <p class="text-xs text-gray-500 mb-0.5">Populaarsed</p>
          <p class="text-xl font-bold text-yellow-400">{{ categories.flatMap(c => c.items).filter(i => i.is_featured).length }}</p>
        </div>
        <div class="bg-[#121212] rounded-lg border border-[#1e1e1e] px-4 py-3">
          <p class="text-xs text-gray-500 mb-0.5">Allahindlusi</p>
          <p class="text-xl font-bold text-green-400">{{ categories.flatMap(c => c.items).filter(i => i.discount_percentage).length }}</p>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16 bg-[#121212] rounded-xl border border-[#1e1e1e]">
      <p class="text-5xl mb-4">🍔</p>
      <h3 class="text-lg font-bold text-white mb-2">Tooteid pole veel</h3>
      <p class="text-gray-500 text-sm mb-6">Lisa oma esimene menüü toode alustamiseks</p>
      <Link href="/admin/menu/items/create" class="inline-flex items-center gap-2 bg-[#D2691E] hover:bg-[#C05A18] text-white px-5 py-2.5 rounded-lg font-semibold transition text-sm">
        <Plus :size="16" /> Lisa toode
      </Link>
    </div>
  </AdminLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>