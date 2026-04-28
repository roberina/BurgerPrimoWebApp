<template>
  <Head title="Menüü — Burger Primo">
    <meta name="description" content="Vaata Burger Primo täielikku menüüd – burgerid, snäkid, joogid ja palju muud. Telli juba täna!" head-key="description" />
    <meta name="robots" content="index, follow" head-key="robots" />
    <meta property="og:title" content="Menüü — Burger Primo" head-key="og:title" />
    <meta property="og:description" content="Vaata Burger Primo täielikku menüüd – burgerid, snäkid, joogid ja palju muud. Telli juba täna!" head-key="og:description" />
    <meta property="og:type" content="website" head-key="og:type" />
    <meta property="og:url" content="https://burgerprimo.ee/menu" head-key="og:url" />
    <meta property="og:image" content="/img/Logo45.png" head-key="og:image" />
  </Head>

  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <Navbar />

    <!-- Desktop sidebar -->
    <MenuSidebar
      :categories="categories"
      :hasFavorites="!!(favorites && favorites.length > 0)"
      :searchQuery="searchQuery"
      :selectedCategories="selectedCategories"
      :navbarHeight="navbarHeight"
      @update:searchQuery="searchQuery = $event"
      @toggleCategory="toggleCategory"
      @clearCategories="selectedCategories = []"
    />

    <!-- Mobile sticky bar: search + category chips -->
    <div
      class="lg:hidden sticky z-40 backdrop-blur-lg border-b bg-[#0B0B0B]/95 border-[#1a1a1a]"
      :style="{ top: navbarHeight + 'px' }"
    >
      <!-- Search row -->
      <div class="px-4 pt-2.5 pb-2">
        <div class="relative">
          <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('menu.search.ph')"
            class="w-full rounded-xl pl-9 pr-8 py-2.5 text-sm outline-none transition-all
                   bg-[#121212] border border-[#1a1a1a] focus:border-[#D2691E]/50 text-white placeholder-gray-600"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute inset-y-0 right-2.5 flex items-center transition-colors cursor-pointer
                   text-gray-500 hover:text-white"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Category filter chips -->
      <div class="flex gap-2 px-4 pb-2.5 overflow-x-auto no-scrollbar">
        <button
          v-if="favorites && favorites.length > 0"
          @click="toggleCategory('favorites')"
          :class="[
            'flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 cursor-pointer border',
            selectedCategories.includes('favorites')
              ? 'bg-[#D2691E] border-[#D2691E] text-white'
              : 'bg-transparent border-gray-700 text-gray-400 hover:border-gray-500 hover:text-white'
          ]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.86L12 17.77l-6.18 3.23L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          {{ t('menu.favorites') }}
        </button>
        <button
          v-for="category in categories"
          :key="`chip-${category.id}`"
          @click="toggleCategory(String(category.id))"
          :class="[
            'flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 cursor-pointer border uppercase tracking-wide',
            selectedCategories.includes(String(category.id))
              ? 'bg-[#D2691E] border-[#D2691E] text-white'
              : 'bg-transparent border-gray-700 text-gray-400 hover:border-gray-500 hover:text-white'
          ]"
        >{{ category.name }}</button>
        <button
          v-if="selectedCategories.length > 0"
          @click="selectedCategories = []"
          class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold text-[#D2691E] border border-[#D2691E]/40 hover:border-[#D2691E] transition-all duration-150 cursor-pointer"
        >{{ t('menu.all') }}</button>
      </div>
    </div>

    <!-- Main content area (offset for desktop sidebar and top navbar) -->
    <div class="pt-16 lg:pt-20 lg:pl-64">
      <main class="px-4 lg:px-8 py-8 min-h-screen">

        <!-- Search results -->
        <section v-if="searchQuery.trim()">
          <div class="mb-6">
            <h2 class="text-xl font-bold text-white">{{ t('menu.search.results') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">
              <span class="font-medium text-white">{{ totalSearchResults }}</span>
              {{ t('menu.search.count') }} — "<span class="text-[#D2691E]">{{ searchQuery }}</span>"
            </p>
          </div>

          <div v-if="totalSearchResults > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            <MenuItem :horizontal="true" v-for="item in searchResults" :key="`si-${item.id}`" :item="item" />
          </div>

          <div v-else class="flex items-center justify-center py-16 rounded-2xl border bg-[#121212] border-[#1a1a1a]">
            <div class="text-center">
              <p class="font-medium mb-1 text-gray-400">{{ t('menu.search.empty') }}</p>
              <p class="text-sm text-gray-600">{{ t('menu.search.empty.sub') }}</p>
            </div>
          </div>
        </section>

        <!-- Category sections -->
        <template v-else>

          <section v-if="showFavoritesSection" id="favorites" class="mb-10">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold flex items-center gap-2 text-white">
                <span class="text-[#D2691E]">★</span> {{ t('menu.favorites') }}
                <span class="text-xs font-normal ml-1 text-gray-600">{{ favorites!.length }} {{ t('menu.products') }}</span>
              </h2>
              <button @click="favoritesCollapsed = !favoritesCollapsed" class="flex items-center gap-1.5 text-xs transition-colors cursor-pointer text-gray-500 hover:text-white">
                {{ favoritesCollapsed ? t('menu.show') : t('menu.hide') }}
                <component :is="favoritesCollapsed ? ChevronDown : ChevronUp" :size="14" />
              </button>
            </div>
            <div v-show="!favoritesCollapsed" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
              <div v-for="item in favorites" :key="`fav-${item.id}`" :id="`item-${item.id}`">
                <MenuItem :horizontal="true" :item="item" :highlighted="highlightedItemId === item.id" />
              </div>
            </div>
          </section>

          <section
            v-for="category in visibleCategories"
            :key="category.id"
            :id="`cat-${category.id}`"
            class="mb-10"
          >
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold uppercase tracking-wide flex items-center gap-3 text-white">
                {{ category.name }}
                <span class="text-xs font-normal normal-case tracking-normal text-gray-600">{{ category.active_items?.length || 0 }} {{ t('menu.products') }}</span>
              </h2>
              <button @click="toggleCategoryCollapse(category.id)" class="flex items-center gap-1.5 text-xs transition-colors cursor-pointer text-gray-500 hover:text-white">
                {{ collapsedCategories.includes(category.id) ? t('menu.show') : t('menu.hide') }}
                <component :is="collapsedCategories.includes(category.id) ? ChevronDown : ChevronUp" :size="14" />
              </button>
            </div>

            <div v-show="!collapsedCategories.includes(category.id)">
              <div v-if="category.active_items && category.active_items.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                <div v-for="item in category.active_items" :key="item.id" :id="`item-${item.id}`">
                  <MenuItem :horizontal="true" :item="item" :highlighted="highlightedItemId === item.id" />
                </div>
              </div>
              <div v-else class="flex items-center justify-center py-12 rounded-xl border bg-[#121212] border-[#1a1a1a]">
                <p class="text-sm text-gray-600">{{ t('menu.cat.empty') }}</p>
              </div>
            </div>
          </section>

          <div v-if="visibleCategories.length === 0 && !showFavoritesSection" class="text-center py-32">
            <p class="text-6xl mb-6">🍔</p>
            <h3 class="text-2xl font-bold mb-3">{{ t('menu.empty') }}</h3>
            <p class="text-gray-500">{{ t('menu.empty.sub') }}</p>
          </div>

        </template>
      </main>
      <Footer />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Head } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import MenuItem from '@/components/Menu/MenuItem.vue';
import MenuSidebar from '@/components/Menu/MenuSidebar.vue';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';

interface MenuItemData {
  id: number;
  name: string;
  description: string;
  price: number;
  original_price: number | null;
  image_url: string | null;
  is_featured: boolean;
  discount_percentage: number | null;
  size: string | null;
  has_discount: boolean;
  type?: string;
  is_favorited?: boolean;
}

interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  active_items: MenuItemData[];
}

interface Props {
  categories: Category[];
  favorites?: MenuItemData[];
}

const props = defineProps<Props>();
const { t } = useI18n();

const navbarHeight = ref(64);
let navRO: ResizeObserver | null = null;

const favoritesCollapsed = ref(false);
const collapsedCategories = ref<number[]>([]);
const searchQuery = ref('');
const highlightedItemId = ref<number | null>(null);
const selectedCategories = ref<string[]>([]);

const toggleCategory = (id: string) => {
  const index = selectedCategories.value.indexOf(id);
  if (index > -1) selectedCategories.value.splice(index, 1);
  else selectedCategories.value.push(id);
};

const toggleCategoryCollapse = (id: number) => {
  const index = collapsedCategories.value.indexOf(id);
  if (index > -1) collapsedCategories.value.splice(index, 1);
  else collapsedCategories.value.push(id);
};

const visibleCategories = computed((): Category[] => {
  if (selectedCategories.value.length === 0) return props.categories;
  return props.categories.filter(c => selectedCategories.value.includes(String(c.id)));
});

const showFavoritesSection = computed(() => {
  if (!props.favorites?.length) return false;
  return selectedCategories.value.length === 0 || selectedCategories.value.includes('favorites');
});

const searchResults = computed((): MenuItemData[] => {
  const query = searchQuery.value.trim().toLowerCase();
  if (!query) return [];

  const items: MenuItemData[] = [];
  const seenIds = new Set<number>();

  for (const category of props.categories) {
    for (const item of category.active_items ?? []) {
      if (item.name.toLowerCase().includes(query) || item.description?.toLowerCase().includes(query)) {
        items.push(item);
        seenIds.add(item.id);
      }
    }
  }

  for (const fav of props.favorites ?? []) {
    if (!seenIds.has(fav.id)) {
      if (fav.name?.toLowerCase().includes(query) || (fav as any).description?.toLowerCase().includes(query)) {
        items.push(fav);
      }
    }
  }

  return items;
});

const totalSearchResults = computed(() => searchResults.value.length);

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  const highlightId = params.get('highlight');
  if (highlightId) {
    const itemId = parseInt(highlightId);
    highlightedItemId.value = itemId;
    setTimeout(() => {
      const el = document.getElementById(`item-${itemId}`);
      if (el) {
        const top = el.getBoundingClientRect().top + window.scrollY - 120;
        window.scrollTo({ top, behavior: 'smooth' });
      }
      setTimeout(() => { highlightedItemId.value = null; }, 3000);
    }, 100);
  }

  const headerEl = document.querySelector('header');
  if (headerEl) {
    navbarHeight.value = headerEl.offsetHeight;
    navRO = new ResizeObserver(() => {
      navbarHeight.value = (document.querySelector('header') as HTMLElement)?.offsetHeight ?? 64;
    });
    navRO.observe(headerEl);
  }
});

onUnmounted(() => {
  navRO?.disconnect();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
