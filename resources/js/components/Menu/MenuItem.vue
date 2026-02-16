<template>
  <div class="bg-[#121212] rounded-2xl overflow-hidden hover:ring-2 hover:ring-[#D2691E] transition-all duration-300 group">
    <!-- Image Section -->
    <div class="relative aspect-[4/3] bg-[#0B0B0B] overflow-hidden">
      <img
        v-if="item.image_url"
        :src="item.image_url"
        :alt="item.name"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-6xl">
        🍔
      </div>

      <!-- Badges -->
      <div class="absolute top-3 left-3 flex gap-2">
        <span v-if="item.is_featured" class="bg-yellow-500 text-black px-3 py-1 rounded-full text-xs font-bold">
          ⭐ Populaarne
        </span>
        <span v-if="item.discount_percentage" class="bg-[#D2691E] text-white px-3 py-1 rounded-full text-xs font-bold">
          -{{ item.discount_percentage }}%
        </span>
      </div>

      <!-- Favorite Button (Top Right) -->
      <button
        @click.stop="toggleFavorite"
        class="absolute top-3 right-3 w-10 h-10 bg-black/50 hover:bg-black/70 rounded-lg flex items-center justify-center transition-all duration-200 z-10"
      >
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          :class="[
            'h-6 w-6 transition-all',
            item.is_favorited ? 'fill-red-500 text-red-500' : 'fill-none text-white'
          ]"
          viewBox="0 0 24 24" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>

      <!-- Plus Button (Bottom Right) -->
      <button
        @click="openAddonModal"
        class="absolute bottom-3 right-3 w-12 h-12 bg-[#D2691E] hover:bg-[#E07A2E] text-white rounded-xl flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-[#D2691E]/50 hover:scale-110"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
      </button>
    </div>

    <!-- Content Section -->
    <div class="p-4">
      <!-- Title -->
      <h3 class="text-lg font-bold text-white mb-2">{{ item.name }}</h3>

      <!-- Description -->
      <p class="text-sm text-gray-400 mb-4 line-clamp-2 min-h-[40px]">
        {{ item.description }}
      </p>

      <!-- Price -->
      <div class="flex items-center gap-2">
        <span class="text-2xl font-bold text-[#D2691E]">€{{ Number(item.price).toFixed(2) }}</span>
        <span v-if="item.original_price && item.original_price > item.price" class="text-sm text-gray-500 line-through">
          €{{ Number(item.original_price).toFixed(2) }}
        </span>
      </div>
    </div>

    <!-- Addon Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showAddonModal"
          class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click.self="closeAddonModal"
        >
          <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="showAddonModal"
              class="bg-[#121212] rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-[#0B0B0B] shadow-2xl"
            >
              <!-- Modal Header -->
              <div class="sticky top-0 bg-[#0B0B0B] px-6 py-4 flex items-center justify-between border-b border-[#121212]">
                <div>
                  <h2 class="text-2xl font-bold text-white">{{ item.name }}</h2>
                  <p class="text-sm text-gray-400">Lisa lisandeid</p>
                </div>
                <button
                  @click="closeAddonModal"
                  class="w-10 h-10 rounded-lg bg-[#121212] hover:bg-[#D2691E] text-gray-400 hover:text-white transition-colors flex items-center justify-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Modal Content -->
              <div class="p-6 space-y-6">
                <!-- Size Selection -->
                <div>
                  <h3 class="text-lg font-bold text-white mb-3">Suurus</h3>
                  <div class="grid grid-cols-3 gap-3">
                    <button
                      v-for="size in sizes"
                      :key="size.id"
                      @click="selectedSize = size.id"
                      :class="[
                        'px-4 py-3 rounded-xl border-2 transition-all',
                        selectedSize === size.id
                          ? 'border-[#D2691E] bg-[#D2691E]/10 text-white'
                          : 'border-[#0B0B0B] bg-[#0B0B0B] text-gray-400 hover:border-[#D2691E]/50'
                      ]"
                    >
                      <div class="font-bold">{{ size.name }}</div>
                      <div class="text-sm">{{ size.price > 0 ? `+€${size.price.toFixed(2)}` : 'Standard' }}</div>
                    </button>
                  </div>
                </div>

                <!-- Drinks -->
                <div>
                  <h3 class="text-lg font-bold text-white mb-3">Joogid</h3>
                  <div class="space-y-2">
                    <label
                      v-for="drink in drinks"
                      :key="drink.id"
                      class="flex items-center justify-between p-3 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/10 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/30"
                    >
                      <div class="flex items-center gap-3">
                        <input
                          type="checkbox"
                          :value="drink.id"
                          v-model="selectedDrinks"
                          class="w-5 h-5 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                        />
                        <span class="text-white">{{ drink.name }}</span>
                      </div>
                      <span class="text-[#D2691E] font-bold">+€{{ drink.price.toFixed(2) }}</span>
                    </label>
                  </div>
                </div>

                <!-- Sauces -->
                <div>
                  <h3 class="text-lg font-bold text-white mb-3">Kastmed</h3>
                  <div class="grid grid-cols-2 gap-2">
                    <label
                      v-for="sauce in sauces"
                      :key="sauce.id"
                      class="flex items-center gap-2 p-3 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/10 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/30"
                    >
                      <input
                        type="checkbox"
                        :value="sauce.id"
                        v-model="selectedSauces"
                        class="w-5 h-5 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                      />
                      <div class="flex-1">
                        <div class="text-white text-sm">{{ sauce.name }}</div>
                        <div class="text-[#D2691E] text-xs font-bold">{{ sauce.price > 0 ? `+€${sauce.price.toFixed(2)}` : 'Tasuta' }}</div>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Fries -->
                <div>
                  <h3 class="text-lg font-bold text-white mb-3">Friikartul</h3>
                  <div class="space-y-2">
                    <label
                      v-for="fry in fries"
                      :key="fry.id"
                      class="flex items-center justify-between p-3 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/10 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/30"
                    >
                      <div class="flex items-center gap-3">
                        <input
                          type="radio"
                          name="fries"
                          :value="fry.id"
                          v-model="selectedFries"
                          class="w-5 h-5 border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                        />
                        <span class="text-white">{{ fry.name }}</span>
                      </div>
                      <span class="text-[#D2691E] font-bold">{{ fry.price > 0 ? `+€${fry.price.toFixed(2)}` : 'Kaasas' }}</span>
                    </label>
                  </div>
                </div>

                <!-- Eating Equipment -->
                <div>
                  <h3 class="text-lg font-bold text-white mb-3">Söögiriistad</h3>
                  <label class="flex items-center gap-3 p-3 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/10 cursor-pointer transition-colors">
                    <input
                      type="checkbox"
                      v-model="needsUtensils"
                      class="w-5 h-5 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                    />
                    <span class="text-white">Soovin söögiriistu</span>
                  </label>
                </div>

                <!-- Special Instructions -->
                <div>
                  <h3 class="text-lg font-bold text-white mb-3">Erisoovidused</h3>
                  <textarea
                    v-model="specialInstructions"
                    rows="3"
                    class="w-full bg-[#0B0B0B] border-2 border-[#121212] rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-[#D2691E] focus:ring-0 transition-colors"
                    placeholder="Nt: ilma sibulata, ekstra juust..."
                  ></textarea>
                </div>
              </div>

              <!-- Modal Footer -->
              <div class="sticky bottom-0 bg-[#0B0B0B] px-6 py-4 border-t border-[#121212]">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-gray-400">Kokku:</span>
                  <span class="text-3xl font-bold text-[#D2691E]">€{{ totalPrice }}</span>
                </div>
                <button
                  @click="addToCart"
                  class="w-full bg-gradient-to-r from-[#D2691E] to-[#B8571A] hover:from-[#E07A2E] hover:to-[#D2691E] text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-[#D2691E]/50"
                >
                  Lisa korvi
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const toggleFavorite = () => {
  router.post(`/menu/${props.item.id}/favorite`, {}, {
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
  discount_percentage: number | null;
  size: string | null;
  has_discount: boolean;
  is_favorited?: boolean;
}

interface Props {
  item: MenuItemData;
}

const props = defineProps<Props>();

const showAddonModal = ref(false);

// Size options
const sizes = ref([
  { id: 'small', name: 'Väike', price: 0 },
  { id: 'medium', name: 'Keskmine', price: 1.50 },
  { id: 'large', name: 'Suur', price: 3.00 },
]);
const selectedSize = ref('small');

// Drinks
const drinks = ref([
  { id: 1, name: 'Coca-Cola 0.5L', price: 2.50 },
  { id: 2, name: 'Coca-Cola Zero 0.5L', price: 2.50 },
  { id: 3, name: 'Fanta 0.5L', price: 2.50 },
  { id: 4, name: 'Sprite 0.5L', price: 2.50 },
  { id: 5, name: 'Vesi 0.5L', price: 1.50 },
]);
const selectedDrinks = ref<number[]>([]);

// Sauces
const sauces = ref([
  { id: 1, name: 'Ketchup', price: 0 },
  { id: 2, name: 'Majonees', price: 0 },
  { id: 3, name: 'BBQ kaste', price: 0.50 },
  { id: 4, name: 'Sinihallitusjuustu kaste', price: 0.50 },
  { id: 5, name: 'Küüslaugukaste', price: 0.50 },
  { id: 6, name: 'Chili kaste', price: 0.50 },
]);
const selectedSauces = ref<number[]>([]);

// Fries
const fries = ref([
  { id: 'none', name: 'Ei soovi', price: 0 },
  { id: 'small', name: 'Väike friikartul', price: 2.00 },
  { id: 'large', name: 'Suur friikartul', price: 3.50 },
]);
const selectedFries = ref('none');

// Utensils
const needsUtensils = ref(false);

// Special instructions
const specialInstructions = ref('');

// Calculate total price
const totalPrice = computed(() => {
  let total = Number(props.item.price) || 0;

  // Add size price
  const size = sizes.value.find(s => s.id === selectedSize.value);
  if (size) total += Number(size.price) || 0;

  // Add drinks
  selectedDrinks.value.forEach(drinkId => {
    const drink = drinks.value.find(d => d.id === drinkId);
    if (drink) total += Number(drink.price) || 0;
  });

  // Add sauces
  selectedSauces.value.forEach(sauceId => {
    const sauce = sauces.value.find(s => s.id === sauceId);
    if (sauce) total += Number(sauce.price) || 0;
  });

  // Add fries
  const friesOption = fries.value.find(f => f.id === selectedFries.value);
  if (friesOption) total += Number(friesOption.price) || 0;

  return total.toFixed(2);
});

const openAddonModal = () => {
  showAddonModal.value = true;
  // Prevent body scroll
  document.body.style.overflow = 'hidden';
};

const closeAddonModal = () => {
  showAddonModal.value = false;
  // Restore body scroll
  document.body.style.overflow = 'auto';
};

const addToCart = () => {
  const cartItem = {
    menu_item_id: props.item.id,
    name: props.item.name,
    base_price: Number(props.item.price) || 0,
    size: selectedSize.value,
    drinks: selectedDrinks.value.map(id => drinks.value.find(d => d.id === id)),
    sauces: selectedSauces.value.map(id => sauces.value.find(s => s.id === id)),
    fries: selectedFries.value !== 'none' ? fries.value.find(f => f.id === selectedFries.value) : null,
    needs_utensils: needsUtensils.value,
    special_instructions: specialInstructions.value,
    total_price: parseFloat(totalPrice.value),
    quantity: 1,
  };

  console.log('Adding to cart:', cartItem);

  // Send to menu item specific cart route
  router.post('/cart/add-menu-item', cartItem, {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Successfully added to cart!');
      closeAddonModal();
      // Reset selections
      selectedSize.value = 'small';
      selectedDrinks.value = [];
      selectedSauces.value = [];
      selectedFries.value = 'none';
      needsUtensils.value = false;
      specialInstructions.value = '';
    },
    onError: (errors) => {
      console.error('❌ Error adding to cart:', errors);
      alert('Viga: ' + (errors.message || 'Ei saanud toote korvi lisada'));
    },
    onFinish: () => {
      console.log('Request finished');
    }
  });
};

// Cleanup on unmount
onUnmounted(() => {
  document.body.style.overflow = 'auto';
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #0B0B0B;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #D2691E;
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #E07A2E;
}
</style>