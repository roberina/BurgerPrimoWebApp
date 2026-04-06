<template>
  <MainLayout>
    <div class="min-h-screen bg-[#0B0B0B] text-white">
      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <!-- Title Section -->
        <div class="mb-12">
          <h2 class="text-4xl font-bold mb-2">Ehita enda burger</h2>
          <p class="text-gray-400">Lisa enda unistuste burger</p>
        </div>

        <!-- Burger Name Input -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold">Burgeri nimi</label>
            <span v-if="!canCreateMore" class="text-sm text-red-400">
              ⚠️ Limit saavutatud ({{ customBurgers.length }}/{{ maxBurgers }})
            </span>
          </div>
          <input
            v-model="burgerName"
            type="text"
            placeholder="Sisesta burgeri nimi"
            class="w-full max-w-md bg-[#121212] border border-[#0B0B0B] rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#D2691E]"
          />
        </div>

       <!-- Ingredients Grid -->
        <div class="space-y-12">
          <!-- Buns Section -->
          <IngredientSection
            v-if="ingredients['buns']"
            title="Saiakesed"
            subtitle="Vali endale sobiv saiake"
            :items="ingredients['buns']"
            :selected="selectedIngredients['buns'] || []"
            @update="(items) => updateIngredients('buns', items)"
          />

          <!-- Patties Section -->
          <IngredientSection
            v-if="ingredients.patties"
            title="Lihakotletid"
            subtitle="Vali endale sobiv kotlet"
            :items="ingredients.patties"
            :selected="selectedIngredients.patties || []"
            @update="(items) => updateIngredients('patties', items)"
          />

          <!-- Vegetables Section -->
          <IngredientSection
            v-if="ingredients.vegetables"
            title="Köögiviljad"
            subtitle="Vali endale sobivad köögiviljad"
            :items="ingredients.vegetables"
            :selected="selectedIngredients.vegetables || []"
            @update="(items) => updateIngredients('vegetables', items)"
          />

          <!-- Sauces Section -->
          <IngredientSection
            v-if="ingredients.sauces"
            title="Kastmed"
            subtitle="Vali endale sobiv kaste"
            :items="ingredients.sauces"
            :selected="selectedIngredients.sauces || []"
            @update="(items) => updateIngredients('sauces', items)"
          />

          <!-- Extras Section -->
          <IngredientSection
            v-if="ingredients.extras"
            title="Lisandid"
            subtitle="Vali endale sobivad lisandid"
            :items="ingredients.extras"
            :selected="selectedIngredients.extras || []"
            @update="(items) => updateIngredients('extras', items)"
          />
        </div>

        <!-- Action Buttons -->
        <div class="mt-12 flex flex-wrap gap-4">
          <button
            @click="saveBurger(false)"
            :disabled="!canSave"
            class="bg-gradient-to-r from-[#D2691E] to-[#B8571A] hover:from-[#E07A2E] hover:to-[#D2691E] disabled:bg-gray-700 disabled:cursor-not-allowed text-white px-8 py-3 rounded-lg font-semibold transition shadow-lg hover:shadow-[#D2691E]/50"
          >
            Salvesta burger
          </button>
          <button
            @click="saveBurger(true)"
            :disabled="!canSave"
            class="bg-transparent border-2 border-[#D2691E] hover:bg-[#D2691E]/10 disabled:border-gray-700 disabled:cursor-not-allowed text-white px-8 py-3 rounded-lg font-semibold transition"
          >
            Lisa lemmikutesse
          </button>
          <button
            @click="() => orderBurger()"
            :disabled="!canSave"
            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white px-8 py-3 rounded-lg font-semibold transition"
          >
            Lisa ostukorvi ({{ totalPrice.toFixed(2) }}€)
          </button>
        </div>

        <!-- Total Price Display -->
        <div class="mt-8 text-2xl font-bold">
          Kogusumma: <span class="text-[#D2691E]">{{ totalPrice.toFixed(2) }}€</span>
        </div>

       <!-- My Custom Burgers Section -->
        <div v-if="customBurgers && customBurgers.length > 0" class="mt-16">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-2xl font-bold">Minu burgerid</h3>
              <p class="text-gray-400">Sinu loodud burgerid ({{ customBurgers.length }}/{{ maxBurgers }})</p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <CustomBurgerCard
              v-for="burger in customBurgers"
              :key="burger.id"
              :burger="burger"
              @load="loadBurger"
              @order="orderSavedBurger"
              @toggle-favorite="toggleBurgerFavorite"
              @delete="deleteBurger"
            />
          </div>
        </div>
      </main>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { useToast } from '@/composables/useToast';

const { success, error, warning } = useToast();
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import IngredientSection from '@/components/IngredientSection.vue';
import CustomBurgerCard from '@/components/CustomBurgerCard.vue';
import type { Ingredient, SelectedIngredient, CustomBurger } from '@/types/burger-types';

interface Props {
  ingredients: Record<string, Ingredient[]>;
  customBurgers: CustomBurger[];
  canCreateMore: boolean;
  maxBurgers: number;
}

const toggleBurgerFavorite = (burgerId: number) => {
  router.post(`/burger-builder/${burgerId}/favorite`, {}, {
    preserveScroll: true,
  });
};

const props = defineProps<Props>();

const burgerName = ref('');
const selectedIngredients = ref<Record<string, SelectedIngredient[]>>({
  buns: [],
  patties: [],
  vegetables: [],
  sauces: [],
  extras: [],
});

const updateIngredients = (category: string, items: SelectedIngredient[]) => {
  selectedIngredients.value[category] = items;
};

const totalPrice = computed(() => {
  let total = 0;
  Object.values(selectedIngredients.value).forEach((categoryItems) => {
    categoryItems.forEach((item) => {
      const ingredient = Object.values(props.ingredients)
        .flat()
        .find((ing) => ing.id === item.id);
      if (ingredient) {
        total += Number(ingredient.price) * item.quantity;
      }
    });
  });
  return total;
});

const canSave = computed(() => {
  return burgerName.value.trim() !== '' && 
         Object.values(selectedIngredients.value).some(items => items.length > 0);
});

const getAllSelectedIngredients = (): SelectedIngredient[] => {
  const allIngredients: SelectedIngredient[] = [];
  Object.values(selectedIngredients.value).forEach((categoryItems) => {
    allIngredients.push(...categoryItems);
  });
  return allIngredients;
};

const saveBurger = (isFavorite: boolean) => {
  if (!props.canCreateMore) {
    warning(`Oled jõudnud maksimaalse burgeri limiidini (${props.maxBurgers}). Kustuta mõni olemasolev burger.`);
    return;
  }

  router.post('/burger-builder', {
    name: burgerName.value,
    ingredients: getAllSelectedIngredients(),
    is_favorite: isFavorite,
  } as any, {
    onSuccess: () => { success('Burger salvestatud! 🍔'); burgerName.value = ''; },
    onError: () => error('Salvestamine ebaõnnestus'),
  });
};

const orderBurger = () => {
  router.post('/cart/add-new', {
    name: burgerName.value,
    ingredients: getAllSelectedIngredients(),
  } as any, {
    onSuccess: () => { success('Burger lisatud ostukorvi! 🛒'); router.visit('/cart'); },
    onError: () => error('Korvi lisamine ebaõnnestus'),
  });
};

const orderSavedBurger = (burgerId: number) => {
  router.post('/cart/add', {
    burger_id: burgerId,
    quantity: 1,
  } as any, {
    onSuccess: () => {
      router.visit('/cart');
    },
  });
};

const deleteBurger = (burgerId: number) => {
  router.delete(`/burger-builder/${burgerId}`, {
    preserveScroll: true,
  });
};

const loadBurger = (burger: CustomBurger) => {
  burgerName.value = burger.name;
  selectedIngredients.value = {
    buns: [],
    patties: [],
    vegetables: [],
    sauces: [],
    extras: [],
  };
  
  burger.ingredients.forEach((ingredient) => {
    const category = ingredient.category;
    if (selectedIngredients.value[category]) {
      selectedIngredients.value[category].push({
        id: ingredient.id,
        quantity: ingredient.pivot.quantity,
      });
    }
  });
};

const orderFavorite = (burgerId: number) => {
  router.post('/cart/add', {
    burger_id: burgerId,
    quantity: 1,
  } as any);
};
</script>