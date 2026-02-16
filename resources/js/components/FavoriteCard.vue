<template>
  <div class="bg-[#0B0B0B] rounded-xl overflow-hidden border border-gray-800 hover:border-[#D2691E] transition-all group">
    <!-- Menu Item -->
    <div v-if="item.type === 'menu_item'" class="p-4">
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs bg-[#D2691E]/20 text-[#D2691E] px-2 py-1 rounded-full font-bold">
              {{ item.category?.name || 'Menüü' }}
            </span>
          </div>
          <h4 class="text-lg font-bold text-white mb-1">{{ item.name }}</h4>
          <p class="text-sm text-gray-400 line-clamp-2">{{ item.description }}</p>
        </div>
        <button
          @click="$emit('toggle-favorite', item.id, 'menu_item')"
          class="p-2 hover:bg-gray-800 rounded-lg transition"
        >
          <Heart :size="20" class="fill-[#D2691E] text-[#D2691E]" />
        </button>
      </div>

      <div class="flex items-center justify-between">
        <p class="text-2xl font-bold text-[#D2691E]">
          €{{ Number(item.price).toFixed(2) }}
        </p>
        <Link
          :href="`/menu/${item.id}`"
          class="px-4 py-2 bg-[#D2691E] hover:bg-[#E07A2E] text-white rounded-lg font-semibold transition"
        >
          Vaata
        </Link>
      </div>
    </div>

    <!-- Custom Burger -->
    <div v-else-if="item.type === 'custom_burger'" class="p-4">
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs bg-orange-500/20 text-orange-400 px-2 py-1 rounded-full font-bold">
              🍔 Kohandatud
            </span>
          </div>
          <h4 class="text-lg font-bold text-white mb-2">{{ item.name }}</h4>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="(ing, idx) in item.ingredients.slice(0, 3)"
              :key="idx"
              class="text-xs bg-gray-800 px-2 py-1 rounded text-gray-400"
            >
              {{ ing.name }}
            </span>
            <span v-if="item.ingredients.length > 3" class="text-xs bg-gray-800 px-2 py-1 rounded text-gray-400">
              +{{ item.ingredients.length - 3 }}
            </span>
          </div>
        </div>
        <button
          @click="$emit('toggle-favorite', item.id, 'custom_burger')"
          class="p-2 hover:bg-gray-800 rounded-lg transition"
        >
          <Heart :size="20" class="fill-[#D2691E] text-[#D2691E]" />
        </button>
      </div>

      <div class="flex items-center justify-between">
        <p class="text-2xl font-bold text-[#D2691E]">
          €{{ Number(item.total_price).toFixed(2) }}
        </p>
        <button
          @click="$emit('add-to-cart', item.id, 'custom_burger')"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition"
        >
          Lisa korvi
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Heart } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Props {
  item: any;
}

defineProps<Props>();
defineEmits(['toggle-favorite', 'add-to-cart']);
</script>
