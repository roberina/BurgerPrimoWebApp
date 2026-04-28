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
  <div class="bg-[#0B0B0B] text-white pt-16 lg:pt-20">
    <Navbar />

    <div
      class="lg:hidden sticky z-40 bg-[#0B0B0B]/95 backdrop-blur-lg border-b border-[#121212] px-4 py-2.5"
      :style="{ top: navbarHeight + 'px' }"
    >
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
          class="w-full bg-[#121212] border border-[#1a1a1a] focus:border-[#D2691E]/50 rounded-xl pl-9 pr-8 py-2.5 text-sm text-white placeholder-gray-600 outline-none transition-all"
        />
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          class="absolute inset-y-0 right-2.5 flex items-center text-gray-500 hover:text-white transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <div class="max-w-[1700px] gap-4 mx-auto flex min-h-screen">
      <aside
        class="hidden lg:flex flex-col w-56 flex-shrink-0 px-4 gap-1 sticky self-start"
        :style="{ top: navbarHeight + 16 + 'px', maxHeight: `calc(100vh - ${navbarHeight + 32}px)` }"
      >
        <div class="relative mt-60 mb-2">
          <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('menu.search.ph')"
            class="w-full bg-[#121212] border border-[#1a1a1a] focus:border-[#D2691E]/50 rounded-xl pl-9 pr-8 py-2.5 text-sm text-white placeholder-gray-600 outline-none transition-all"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute inset-y-0 right-2.5 flex items-center text-gray-500 hover:text-white transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <nav class="flex flex-col gap-1 overflow-y-auto">
          <button
            v-if="favorites && favorites.length > 0"
            @click="scrollTo('favorites')"
            :class="[
              'w-full text-left px-4 py-2.5 rounded-xl text-sm font-semibold transition-all',
              activeSection === 'favorites'
                ? 'bg-[#D2691E] text-white'
                : 'text-gray-400 hover:text-white hover:bg-[#1a1a1a]'
            ]"
          >★ {{ t('menu.favorites') }}</button>
          <button
            v-for="category in categories"
            :key="category.id"
            @click="scrollTo(`cat-${category.id}`)"
            :class="[
              'w-full text-left px-4 py-2.5 rounded-xl text-sm font-semibold transition-all uppercase tracking-wide',
              activeSection === `cat-${category.id}`
                ? 'bg-[#D2691E] text-white'
                : 'text-gray-400 hover:text-white hover:bg-[#1a1a1a]'
            ]"
          >{{ category.name }}</button>
        </nav>
      </aside>

      <main class="flex-1 min-w-0 px-4 py-8">
        <div class="w-full">

          <section v-if="searchQuery.trim()">
            <div class="mb-6">
              <h2 class="text-xl font-bold text-white">{{ t('menu.search.results') }}</h2>
              <p class="text-sm text-gray-500 mt-0.5">
                <span class="text-white font-medium">{{ totalSearchResults }}</span>
                {{ t('menu.search.count') }} — "<span class="text-[#D2691E]">{{ searchQuery }}</span>"
              </p>
            </div>

            <div v-if="totalSearchResults > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <MenuItem :horizontal="true" v-for="item in searchResults" :key="`si-${item.id}`" :item="item" />
            </div>

            <div v-else class="flex items-center justify-center py-16 bg-[#121212] rounded-2xl border border-[#1a1a1a]">
              <div class="text-center">
                <p class="text-4xl mb-3">🔍</p>
                <p class="text-gray-400 font-medium mb-1">{{ t('menu.search.empty') }}</p>
                <p class="text-gray-600 text-sm">{{ t('menu.search.empty.sub') }}</p>
              </div>
            </div>
          </section>

          <template v-else>

            <section v-if="favorites && favorites.length > 0" id="favorites" class="mb-10">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                  <span class="text-[#D2691E]">★</span> {{ t('menu.favorites') }}
                  <span class="text-xs text-gray-600 font-normal ml-1">{{ favorites.length }} {{ t('menu.products') }}</span>
                </h2>
                <button @click="favoritesCollapsed = !favoritesCollapsed" class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-white transition-colors">
                  {{ favoritesCollapsed ? t('menu.show') : t('menu.hide') }}
                  <component :is="favoritesCollapsed ? ChevronDown : ChevronUp" :size="14" />
                </button>
              </div>
              <div v-show="!favoritesCollapsed" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div v-for="item in favorites" :key="`fav-${item.id}`" :id="`item-${item.id}`">
                  <MenuItem :horizontal="true" :item="item" :highlighted="highlightedItemId === item.id" />
                </div>
              </div>
            </section>

            <section
              v-for="category in categories"
              :key="category.id"
              :id="`cat-${category.id}`"
              class="mb-10"
            >
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white uppercase tracking-wide flex items-center gap-3">
                  {{ category.name }}
                  <span class="text-xs text-gray-600 font-normal normal-case tracking-normal">{{ category.active_items?.length || 0 }} {{ t('menu.products') }}</span>
                </h2>
                <button @click="toggleCategory(category.id)" class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-white transition-colors">
                  {{ collapsedCategories.includes(category.id) ? t('menu.show') : t('menu.hide') }}
                  <component :is="collapsedCategories.includes(category.id) ? ChevronDown : ChevronUp" :size="14" />
                </button>
              </div>

              <div v-show="!collapsedCategories.includes(category.id)">
                <div v-if="category.active_items && category.active_items.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                  <div v-for="item in category.active_items" :key="item.id" :id="`item-${item.id}`">
                    <MenuItem :horizontal="true" :item="item" :highlighted="highlightedItemId === item.id" />
                  </div>
                </div>
                <div v-else class="flex items-center justify-center py-12 bg-[#121212] rounded-xl border border-[#1a1a1a]">
                  <p class="text-gray-600 text-sm">{{ t('menu.cat.empty') }}</p>
                </div>
              </div>
            </section>

            <div v-if="categories.length === 0" class="text-center py-32">
              <p class="text-6xl mb-6">🍔</p>
              <h3 class="text-2xl font-bold mb-3">{{ t('menu.empty') }}</h3>
              <p class="text-gray-500">{{ t('menu.empty.sub') }}</p>
            </div>

          </template>
        </div>
      </main>
    </div>
  </div>
  <Footer />
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { Head, usePage } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import MenuItem from '@/components/Menu/MenuItem.vue';
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
const activeSection = ref('');
const highlightedItemId = ref<number | null>(null);

const toggleCategory = (id: number) => {
  const index = collapsedCategories.value.indexOf(id);
  if (index > -1) collapsedCategories.value.splice(index, 1);
  else collapsedCategories.value.push(id);
};

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

const scrollTo = (id: string) => {
  const el = document.getElementById(id);
  if (el) {
    const top = el.getBoundingClientRect().top + window.scrollY - 80;
    window.scrollTo({ top, behavior: 'smooth' });
  }
};

let observer: IntersectionObserver | null = null;

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

  const sectionIds = [
    ...(props.favorites?.length ? ['favorites'] : []),
    ...props.categories.map(c => `cat-${c.id}`),
  ];

  observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter(e => e.isIntersecting)
        .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
      if (visible.length > 0) {
        activeSection.value = visible[0].target.id;
      }
    },
    { rootMargin: '-10% 0px -80% 0px' }
  );

  sectionIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) observer!.observe(el);
  });
});

onUnmounted(() => {
  observer?.disconnect();
  navRO?.disconnect();
});
</script>