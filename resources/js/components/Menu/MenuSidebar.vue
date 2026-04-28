<template>
  <aside
    class="fixed left-0 bottom-0 w-64 z-30 hidden lg:flex flex-col bg-[#111111] border-r border-gray-800"
    :style="{ top: navbarHeight + 'px' }"
  >
    <!-- Search -->
    <div class="p-4 border-b border-gray-800">
      <div class="relative">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
          </svg>
        </div>
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
          type="text"
          :placeholder="t('menu.search.ph')"
          class="w-full rounded-lg pl-9 pr-8 py-2 text-sm outline-none transition-all
                 bg-[#0B0B0B] border border-gray-800 focus:border-[#D2691E]/50 text-white placeholder-gray-600"
        />
        <button
          v-if="searchQuery"
          @click="$emit('update:searchQuery', '')"
          class="absolute inset-y-0 right-2.5 flex items-center transition-colors cursor-pointer
                 text-gray-500 hover:text-white"
          aria-label="Tühjenda otsing"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Scrollable area -->
    <div class="flex-1 overflow-y-auto sidebar-scroll">
      <div class="p-4">
        <!-- Header row -->
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ t('menu.filter') }}</span>
          <button
            v-if="selectedCategories.length > 0"
            @click="$emit('clearCategories')"
            class="text-xs font-semibold transition-colors cursor-pointer
                   text-[#D2691E] hover:text-[#b85c19]"
          >{{ t('menu.all') }}</button>
        </div>

        <!-- Category toggles -->
        <div class="space-y-0.5">
          <!-- Favorites -->
          <button
            v-if="hasFavorites"
            @click="$emit('toggleCategory', 'favorites')"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 cursor-pointer',
              selectedCategories.includes('favorites')
                ? 'bg-[#D2691E] text-white'
                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.86L12 17.77l-6.18 3.23L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="flex-1 text-left">{{ t('menu.favorites') }}</span>
            <svg v-if="selectedCategories.includes('favorites')" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
          </button>

          <!-- Categories -->
          <button
            v-for="category in categories"
            :key="category.id"
            @click="$emit('toggleCategory', String(category.id))"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 cursor-pointer',
              selectedCategories.includes(String(category.id))
                ? 'bg-[#D2691E] text-white'
                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
            ]"
          >
            <span class="flex-1 text-left uppercase tracking-wide">{{ category.name }}</span>
            <svg v-if="selectedCategories.includes(String(category.id))" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>

        <!-- Empty state -->
        <p v-if="!hasFavorites && categories.length === 0" class="text-xs text-center mt-6 text-gray-600">
          Kategooriad puuduvad
        </p>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';

interface Category {
  id: number;
  name: string;
}

defineProps<{
  categories: Category[];
  hasFavorites: boolean;
  searchQuery: string;
  selectedCategories: string[];
  navbarHeight: number;
}>();

defineEmits<{
  'update:searchQuery': [value: string];
  'toggleCategory': [id: string];
  'clearCategories': [];
}>();

const { t } = useI18n();
</script>

<style scoped>
.sidebar-scroll::-webkit-scrollbar {
  width: 4px;
}
.sidebar-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-scroll::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 2px;
}
.sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background: #4b5563;
}
</style>
