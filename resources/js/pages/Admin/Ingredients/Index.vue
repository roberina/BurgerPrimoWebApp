<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, Eye, EyeOff } from 'lucide-vue-next';

interface Ingredient {
  id: number;
  name: string;
  category: string;
  price: number;
  is_available: boolean;
}

interface Props {
  ingredients: Record<string, Ingredient[]>;
  stats: {
    total: number;
    buns: number;
    patties: number;
    vegetables: number;
    sauces: number;
    extras: number;
  };
}

const props = defineProps<Props>();

const categoryNames: Record<string, string> = {
  buns: 'Saiakesed',
  patties: 'Lihakotletid',
  vegetables: 'Köögiviljad',
  sauces: 'Kastmed',
  extras: 'Lisandid',
};

const categoryIcons: Record<string, string> = {
  buns: '🍞',
  patties: '🍖',
  vegetables: '🥬',
  sauces: '🧴',
  extras: '🧀',
};

const deleteIngredient = (id: number) => {
  if (confirm('Kas olete kindel, et soovite selle koostisosa kustutada?')) {
    router.delete(`/admin/ingredients/${id}`);
  }
};

const toggleAvailability = (id: number) => {
  router.post(`/admin/ingredients/${id}/toggle`);
};
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-xl lg:text-2xl font-bold">Burger Koostisosad</h2>
          <p class="text-sm text-gray-400 mt-1">Halda burgeri ehitaja koostisosi</p>
        </div>
        <Link
          :href="'/admin/ingredients/create'"
          class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
        >
          <Plus :size="20" />
          Lisa koostisosa
        </Link>
      </div>
    </template>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
      <div class="bg-[#111111] border border-gray-800 rounded-xl p-4">
        <p class="text-sm text-gray-400 mb-1">Kokku</p>
        <p class="text-3xl font-bold text-white">{{ stats.total }}</p>
      </div>
      <div class="bg-[#111111] border border-gray-800 rounded-xl p-4">
        <p class="text-sm text-gray-400 mb-1">🍞 Saiakesed</p>
        <p class="text-3xl font-bold text-white">{{ stats.buns }}</p>
      </div>
      <div class="bg-[#111111] border border-gray-800 rounded-xl p-4">
        <p class="text-sm text-gray-400 mb-1">🍖 Lihakotletid</p>
        <p class="text-3xl font-bold text-white">{{ stats.patties }}</p>
      </div>
      <div class="bg-[#111111] border border-gray-800 rounded-xl p-4">
        <p class="text-sm text-gray-400 mb-1">🥬 Köögiviljad</p>
        <p class="text-3xl font-bold text-white">{{ stats.vegetables }}</p>
      </div>
      <div class="bg-[#111111] border border-gray-800 rounded-xl p-4">
        <p class="text-sm text-gray-400 mb-1">🧴 Kastmed</p>
        <p class="text-3xl font-bold text-white">{{ stats.sauces }}</p>
      </div>
      <div class="bg-[#111111] border border-gray-800 rounded-xl p-4">
        <p class="text-sm text-gray-400 mb-1">🧀 Lisandid</p>
        <p class="text-3xl font-bold text-white">{{ stats.extras }}</p>
      </div>
    </div>

    <!-- Ingredients by Category -->
    <div class="space-y-6">
      <div v-for="(items, category) in ingredients" :key="category">
        <div class="flex items-center gap-3 mb-4">
          <span class="text-2xl">{{ categoryIcons[category] }}</span>
          <h3 class="text-xl font-bold text-white">{{ categoryNames[category] }}</h3>
          <span class="text-sm text-gray-400">({{ items.length }})</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="ingredient in items"
            :key="ingredient.id"
            :class="[
              'bg-[#111111] border rounded-xl p-4 transition-all',
              ingredient.is_available 
                ? 'border-gray-800 hover:border-orange-600' 
                : 'border-gray-800 opacity-50'
            ]"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="flex-1">
                <h4 class="text-lg font-bold text-white mb-1">{{ ingredient.name }}</h4>
                <p class="text-2xl font-bold text-orange-500">€{{ Number(ingredient.price).toFixed(2) }}</p>
              </div>
              <button
                @click="toggleAvailability(ingredient.id)"
                :class="[
                  'p-2 rounded-lg transition-colors',
                  ingredient.is_available
                    ? 'bg-green-600/20 text-green-500 hover:bg-green-600/30'
                    : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                ]"
                :title="ingredient.is_available ? 'Saadaval' : 'Pole saadaval'"
              >
                <component :is="ingredient.is_available ? Eye : EyeOff" :size="20" />
              </button>
            </div>

            <div class="flex gap-2">
              <Link
                :href="`/admin/ingredients/${ingredient.id}/edit`"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-[#0a0a0a] hover:bg-gray-800 text-white rounded-lg font-semibold transition"
              >
                <Edit :size="16" />
                Muuda
              </Link>
              <button
                @click="deleteIngredient(ingredient.id)"
                class="px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-500 rounded-lg font-semibold transition flex items-center gap-2"
              >
                <Trash2 :size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="stats.total === 0" class="text-center py-16 bg-[#111111] rounded-xl border border-gray-800">
      <p class="text-xl font-semibold text-gray-400 mb-4">Koostisosi pole veel lisatud</p>
      <Link
        :href="'/admin/ingredients/create'"
        class="inline-flex items-center gap-2 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold transition"
      >
        <Plus :size="20" />
        Lisa esimene koostisosa
      </Link>
    </div>
  </AdminLayout>
</template>