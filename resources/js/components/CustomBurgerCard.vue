<template>
  <div 
    :class="[
      'bg-[#121212] rounded-2xl p-6 border-2 transition-all',
      isAvailable 
        ? 'border-[#0B0B0B] hover:border-[#D2691E]' 
        : 'border-red-900/30 opacity-75'
    ]"
  >
    <!-- Header with Favorite and Delete -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <h4 class="text-xl font-bold text-white">{{ burger.name }}</h4>
          <button
            @click="$emit('toggle-favorite', burger.id)"
            class="p-1 hover:bg-gray-800 rounded transition"
            title="Lemmik"
          >
            <Heart 
              :size="20" 
              :class="burger.is_favorite ? 'fill-[#D2691E] text-[#D2691E]' : 'text-gray-400'"
            />
          </button>
          <button
            @click="handleDelete"
            class="p-1 hover:bg-red-900 rounded transition text-red-400 hover:text-red-300"
            title="Kustuta"
          >
            <Trash2 :size="20" />
          </button>
        </div>
        <p class="text-2xl font-bold text-[#D2691E]">€{{ Number(burger.total_price).toFixed(2) }}</p>
      </div>
    </div>

    <!-- Unavailable Warning -->
    <div v-if="!isAvailable" class="mb-4 p-3 bg-red-900/20 border border-red-900/50 rounded-lg">
      <div class="flex items-start gap-2">
        <AlertCircle :size="18" class="text-red-500 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-sm font-bold text-red-500">Pole saadaval</p>
          <p class="text-xs text-red-400 mt-1">Mõned koostisosad puuduvad</p>
        </div>
      </div>
    </div>

    <!-- Ingredients -->
    <div class="mb-4">
      <p class="text-xs font-bold text-gray-400 uppercase mb-2">Koostisosad:</p>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="(ingredient, idx) in burger.ingredients"
          :key="idx"
          :class="[
            'text-xs px-2 py-1 rounded',
            ingredient.is_available 
              ? 'bg-[#0B0B0B] text-gray-300' 
              : 'bg-red-900/20 text-red-400 line-through'
          ]"
        >
          <span v-if="ingredient.pivot.quantity > 1" class="font-bold">
            {{ ingredient.pivot.quantity }}x
          </span>
          {{ ingredient.name }}
        </span>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-2">
      <button
        @click="$emit('load', burger)"
        :disabled="!isAvailable"
        class="flex-1 bg-[#0B0B0B] hover:bg-gray-800 disabled:bg-gray-900 disabled:cursor-not-allowed disabled:text-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition"
      >
        Lae
      </button>
      <button
        @click="$emit('order', burger.id)"
        :disabled="!isAvailable"
        class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-900 disabled:cursor-not-allowed disabled:text-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition"
      >
        Telli
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Heart, Trash2, AlertCircle } from 'lucide-vue-next';
import type { CustomBurger } from '@/types/burger-types';

interface Props {
  burger: CustomBurger;
}

const props = defineProps<Props>();
const emit = defineEmits(['load', 'order', 'toggle-favorite', 'delete']);

// Check if all ingredients are available
const isAvailable = computed(() => {
  return props.burger.ingredients.every(ing => ing.is_available !== false);
});

const handleDelete = () => {
  if (confirm(`Kas oled kindel, et soovid "${props.burger.name}" kustutada?`)) {
    emit('delete', props.burger.id);
  }
};
</script>
