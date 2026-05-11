<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { useToast } from '@/composables/useToast';

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

const props = defineProps<{
  item: MenuItemData;
}>();

const emit = defineEmits<{
  close: [];
  customize: [];
}>();

const { t } = useI18n();
const { success } = useToast();
const submitting = ref(false);

const addToCart = () => {
  submitting.value = true;
  router.post('/cart/add-menu-item', {
    menu_item_id: props.item.id,
    name: props.item.name,
    base_price: Number(props.item.price) || 0,
    size: 'standard',
    drinks: [],
    sauces: [],
    fries: null,
    needs_utensils: false,
    special_instructions: '',
    total_price: Number(props.item.price) || 0,
    quantity: 1,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      submitting.value = false;
      success(`${props.item.name} ${t('toast.cart.added')}`);
      emit('close');
    },
    onError: () => { submitting.value = false; },
  });
};
</script>

<template>
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
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 cursor-default"
        style="background: rgba(0,0,0,0.85); backdrop-filter: blur(6px);"
        @click.self="emit('close')"
      >
        <Transition
          enter-active-class="transition ease-out duration-250"
          enter-from-class="opacity-0 translate-y-8 scale-[0.97]"
          enter-to-class="opacity-100 translate-y-0 scale-100"
        >
          <div
            class="relative w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl overflow-hidden flex flex-col cursor-default"
            style="background: #111; border: 1px solid #1e1e1e; box-shadow: 0 32px 80px rgba(0,0,0,0.9);"
          >
            <!-- Close button -->
            <button
              @click="emit('close')"
              class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full flex items-center justify-center transition-all cursor-pointer"
              style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Image -->
            <div class="relative w-full overflow-hidden" style="aspect-ratio: 16/9; background: #0d0d0d;">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                :alt="item.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-7xl">🍔</div>

              <!-- Gradient overlay -->
              <div class="absolute inset-0" style="background: linear-gradient(to top, #111 0%, transparent 60%);"></div>

              <!-- Badges -->
              <div class="absolute top-3 left-3 flex gap-2">
                <span v-if="item.is_featured" class="text-xs bg-yellow-500 text-black px-2.5 py-1 rounded-full font-bold">
                  {{ t('menu.item.popular') }}
                </span>
                <span v-if="item.discount_percentage" class="text-xs bg-[#D2691E] text-white px-2.5 py-1 rounded-full font-bold">
                  -{{ item.discount_percentage }}%
                </span>
              </div>
            </div>

            <!-- Content -->
            <div class="px-5 pt-3 pb-5 flex flex-col gap-4">

              <!-- Name + price -->
              <div class="flex items-start justify-between gap-3">
                <h2 class="text-xl font-bold text-white leading-tight flex-1">{{ item.name }}</h2>
                <div class="text-right shrink-0">
                  <p class="text-2xl font-black" style="color: #D2691E;">{{ Number(item.price).toFixed(2) }}€</p>
                  <p v-if="item.original_price && Number(item.original_price) > Number(item.price)" class="text-xs text-gray-600 line-through">
                    {{ Number(item.original_price).toFixed(2) }}€
                  </p>
                </div>
              </div>

              <!-- Description -->
              <p v-if="item.description" class="text-sm leading-relaxed" style="color: #888;">
                {{ item.description }}
              </p>

              <!-- Divider -->
              <div style="border-top: 1px solid #1e1e1e;"></div>

              <!-- Buttons -->
              <div class="flex gap-3">
                <!-- Customize -->
                <button
                  @click="emit('customize')"
                  class="flex-1 py-3.5 rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
                  style="background: #1a1a1a; border: 1px solid #2a2a2a; color: #ccc;"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                  </svg>
                  {{ t('addon.customize') }}
                </button>

                <!-- Add to cart -->
                <button
                  @click="addToCart"
                  :disabled="submitting"
                  class="flex-2 py-3.5 rounded-xl font-bold text-sm text-white transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  style="background: linear-gradient(135deg, #D2691E, #B8571A); box-shadow: 0 4px 20px rgba(210,105,30,0.35);"
                >
                  <svg v-if="submitting" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  {{ submitting ? t('addon.adding') : t('addon.add') }}
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>