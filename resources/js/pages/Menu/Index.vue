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

      <!-- Categories with Items -->
      <section
        v-for="category in filteredCategories"
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
            @toggle-favorite="toggleFavorite"
          />
        </div>
        <div v-else class="text-center py-12 text-gray-500 bg-[#121212] rounded-2xl">
          Selles kategoorias pole hetkel tooteid
        </div>
      </section>

      <!-- Empty State -->
      <div v-if="filteredCategories.length === 0" class="text-center py-16">
        <div class="text-6xl mb-4">🍔</div>
        <h3 class="text-2xl font-bold mb-2">Menüüsid pole veel lisatud</h3>
        <p class="text-gray-400">Tulge hiljem tagasi!</p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue';
import MenuItem from '@/components/Menu/MenuItem.vue';

const toggleFavorite = (itemId: number) => {
  router.post(`/menu/${itemId}/favorite`, {}, {
    preserveScroll: true,
  });
};

interface MenuItemData {
  id: number;
  name: string;
  description: string;
  price: number;
  original_price: number | null;
  image_url: string | null;
  is_featured: boolean;
  is_favorited?: boolean;
  discount_percentage: number | null;
  size: string | null;
  has_discount: boolean;
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
}

const props = defineProps<Props>();

const filteredCategories = computed(() => {
  return props.categories;
});
</script>