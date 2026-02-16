<template>
  <Head title="Menüü" />
  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <!-- Navbar -->
    <Navbar />
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Title -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">Menüü</h1>
        <p class="text-gray-400">Valige oma lemmitooted</p>
      </div>

      <!-- Favorites Section -->
      <section v-if="favorites && favorites.length > 0" class="mb-12">
        <div class="bg-[#121212] rounded-2xl border border-gray-800 overflow-hidden">
          <!-- Header -->
          <button
            @click="favoritesCollapsed = !favoritesCollapsed"
            class="w-full px-6 py-4 flex items-center justify-between hover:bg-[#1a1a1a] transition-colors"
          >
            <div class="flex items-center gap-3">
              <Star :size="24" class="text-[#D2691E]" />
              <div class="text-left">
                <h2 class="text-2xl font-bold text-white">Lemmikud</h2>
                <p class="text-sm text-gray-400">Sinu lemmiktooted ja burgerid</p>
              </div>
              <span class="px-3 py-1 bg-[#D2691E]/20 text-[#D2691E] rounded-full text-sm font-bold">
                {{ favorites.length }}
              </span>
            </div>
            <component
              :is="favoritesCollapsed ? ChevronDown : ChevronUp"
              :size="24"
              class="text-gray-400"
            />
          </button>

          <!-- Content -->
          <div v-show="!favoritesCollapsed" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- Menu Item Favorites -->
              <MenuItem
                v-for="item in favorites.filter(f => f.type === 'menu_item')"
                :key="`menu-${item.id}`"
                :item="item"
              />

              <!-- Custom Burger Favorites -->
              <div
                v-for="burger in favorites.filter(f => f.type === 'custom_burger')"
                :key="`burger-${burger.id}`"
                class="bg-[#0B0B0B] rounded-xl overflow-hidden border border-gray-800 hover:border-[#D2691E] transition-all"
              >
                <div class="p-4">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs bg-orange-500/20 text-orange-400 px-2 py-1 rounded-full font-bold">
                      🍔 Kohandatud
                    </span>
                  </div>
                  <h4 class="text-lg font-bold text-white mb-2">{{ burger.name }}</h4>
                  <div class="flex flex-wrap gap-1 mb-4">
                    <span
                      v-for="(ing, idx) in burger.ingredients.slice(0, 3)"
                      :key="idx"
                      class="text-xs bg-gray-800 px-2 py-1 rounded text-gray-400"
                    >
                      {{ ing.name }}
                    </span>
                    <span v-if="burger.ingredients.length > 3" class="text-xs bg-gray-800 px-2 py-1 rounded text-gray-400">
                      +{{ burger.ingredients.length - 3 }}
                    </span>
                  </div>

                  <div class="flex items-center justify-between">
                    <p class="text-2xl font-bold text-[#D2691E]">€{{ Number(burger.total_price).toFixed(2) }}</p>
                    <button
                      @click="addCustomBurgerToCart(burger.id)"
                      class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition"
                    >
                      Lisa korvi
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Categories with Items -->
      <section
        v-for="category in categories"
        :key="category.id"
        class="mb-12"
      >
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-[#D2691E] mb-2">{{ category.name }}</h2>
          <p v-if="category.description" class="text-gray-400">{{ category.description }}</p>
        </div>
        
        <div v-if="category.active_items && category.active_items.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <MenuItem
            v-for="item in category.active_items"
            :key="item.id"
            :item="item"
          />
        </div>
        
        <div v-else class="text-center py-12 text-gray-500 bg-[#121212] rounded-2xl">
          Selles kategoorias pole hetkel tooteid
        </div>
      </section>

      <!-- Empty State -->
      <div v-if="categories.length === 0" class="text-center py-16">
        <div class="text-6xl mb-4">🍔</div>
        <h3 class="text-2xl font-bold mb-2">Menüüsid pole veel lisatud</h3>
        <p class="text-gray-400">Tulge hiljem tagasi!</p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import MenuItem from '@/components/Menu/MenuItem.vue';
import { Star, ChevronDown, ChevronUp } from 'lucide-vue-next';

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
  favorites?: any[];
}

const props = defineProps<Props>();

const favoritesCollapsed = ref(false);

const addCustomBurgerToCart = (burgerId: number) => {
  router.post('/cart/add', {
    burger_id: burgerId,
    quantity: 1,
  } as any, {
    onSuccess: () => {
      router.visit('/cart');
    },
  });
};
</script>