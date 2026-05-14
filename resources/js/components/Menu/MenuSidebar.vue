<template>
  <aside
    class="fixed left-0 w-64 z-30 hidden lg:flex flex-col sidebar-blend"
    :style="{ top: navbarHeight + 'px', bottom: sidebarBottom + 'px' }"
  >
    <!-- Search -->
    <div class="px-4 pt-4 pb-3.5 border-b border-white/5">
      <div class="relative">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
          </svg>
        </div>
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
          type="text"
          :placeholder="t('menu.search.ph')"
          class="w-full rounded-xl pl-9 pr-8 py-2.5 text-sm outline-none transition-all
                 bg-white/4 border border-white/6 focus:border-[#D2691E]/40 focus:bg-white/6 text-white placeholder-gray-600"
        />
        <button
          v-if="searchQuery"
          @click="$emit('update:searchQuery', '')"
          class="absolute inset-y-0 right-2.5 flex items-center transition-colors cursor-pointer
                 text-gray-600 hover:text-gray-300"
          aria-label="Tühjenda otsing"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Scrollable area with fade edges -->
    <div class="flex-1 overflow-y-auto sidebar-scroll relative">
      <!-- Top fade -->
      <div class="sticky top-0 left-0 right-0 h-4 pointer-events-none z-10 sidebar-fade-top" />
      <div class="px-4 pt-0 pb-4">
        <!-- Header row -->
        <div class="flex items-center justify-between mb-3 mt-4">
          <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600">{{ t('menu.filter') }}</span>
          <button
            v-if="selectedCategories.length > 0"
            @click="$emit('clearCategories')"
            class="text-xs font-semibold transition-colors cursor-pointer
                   text-[#D2691E]/80 hover:text-[#D2691E]"
          >{{ t('menu.all') }}</button>
        </div>

        <!-- Category toggles -->
        <div class="space-y-0.5">
          <!-- Favorites -->
          <button
            v-if="hasFavorites"
            @click="$emit('toggleCategory', 'favorites')"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer',
              selectedCategories.includes('favorites')
                ? 'bg-[#D2691E]/15 border border-[#D2691E]/30 text-[#D2691E]'
                : 'text-gray-500 border border-transparent hover:bg-white/4 hover:text-gray-300'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.86L12 17.77l-6.18 3.23L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="flex-1 text-left">{{ t('menu.favorites') }}</span>
            <div v-if="selectedCategories.includes('favorites')" class="w-1.5 h-1.5 rounded-full bg-[#D2691E] flex-shrink-0" />
          </button>

          <!-- Categories -->
          <button
            v-for="category in categories"
            :key="category.id"
            @click="$emit('toggleCategory', String(category.id))"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer',
              selectedCategories.includes(String(category.id))
                ? 'bg-[#D2691E]/15 border border-[#D2691E]/30 text-[#D2691E]'
                : 'text-gray-500 border border-transparent hover:bg-white/4 hover:text-gray-300'
            ]"
          >
            <span class="flex-1 text-left tracking-wide">{{ category.name }}</span>
            <div v-if="selectedCategories.includes(String(category.id))" class="w-1.5 h-1.5 rounded-full bg-[#D2691E] flex-shrink-0" />
          </button>
        </div>

        <!-- Empty state -->
        <p v-if="!hasFavorites && categories.length === 0" class="text-xs text-center mt-6 text-gray-600">
          {{ t('menu.sidebar.empty') }}
        </p>
      </div>
      <!-- Bottom fade -->
      <div class="sticky bottom-0 left-0 right-0 h-6 pointer-events-none sidebar-fade-bottom" />
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from '@/composables/useI18n';

const sidebarBottom = ref(0);

function updateBottom() {
  const footer = document.querySelector('footer');
  if (!footer) return;
  const footerTop = footer.getBoundingClientRect().top;
  const visible = window.innerHeight - footerTop;
  sidebarBottom.value = visible > 0 ? visible : 0;
}

onMounted(() => {
  updateBottom();
  window.addEventListener('scroll', updateBottom, { passive: true });
  window.addEventListener('resize', updateBottom, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', updateBottom);
  window.removeEventListener('resize', updateBottom);
});

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
.sidebar-blend {
  background: linear-gradient(to right, #0f0f0f 85%, rgba(10,10,10,0.95) 100%);
  border-right: 1px solid rgba(255,255,255,0.04);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  box-shadow: 2px 0 24px rgba(0,0,0,0.5), inset -1px 0 0 rgba(210,105,30,0.04);
}

.sidebar-fade-top {
  background: linear-gradient(to bottom, #0f0f0f 30%, transparent 100%);
}

.sidebar-fade-bottom {
  background: linear-gradient(to top, #0f0f0f 30%, transparent 100%);
}

.sidebar-scroll::-webkit-scrollbar {
  width: 3px;
}
.sidebar-scroll::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-scroll::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.08);
  border-radius: 2px;
}
.sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(255,255,255,0.14);
}
</style>
