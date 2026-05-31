<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <div
        v-if="cartCount > 0"
        class="fixed bottom-0 inset-x-0 z-60 px-4 pointer-events-none"
        style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 1rem))"
      >
        <div class="max-w-lg mx-auto pointer-events-auto">
          <button
            @click="router.visit('/cart')"
            class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl
                   bg-[#D2691E] hover:bg-[#E07A2E] active:bg-[#B8571A]
                   shadow-2xl shadow-black/50
                   transition-colors duration-150 cursor-pointer"
          >
            <!-- Cart icon + count badge -->
            <div class="relative shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="absolute -top-2 -right-2 w-4 h-4 rounded-full bg-white text-[#D2691E] text-[10px] font-bold flex items-center justify-center leading-none">
                {{ cartCount > 9 ? '9+' : cartCount }}
              </span>
            </div>

            <!-- Label -->
            <span class="flex-1 text-left text-sm font-semibold text-white">
              {{ t('cart.sticky.view') }}
            </span>

            <!-- Total -->
            <span class="text-base font-bold text-white">
              {{ cartTotal.toFixed(2) }}€
            </span>

            <!-- Arrow -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
const page = usePage();
const cartCount = computed(() => (page.props as any).cartCount as number);
const cartTotal = computed(() => (page.props as any).cartTotal as number ?? 0);
</script>
