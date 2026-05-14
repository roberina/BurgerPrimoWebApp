<script setup lang="ts">
import { useI18n } from '@/composables/useI18n'

const { locale, setLocale } = useI18n()
</script>

<template>
  <div class="flex items-center gap-0.5 p-0.5 bg-white/6 border border-white/10 rounded-full">
    <button
       @click="setLocale('et')"
      class="cursor-pointer px-2.5 py-1 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer"
      :class="locale === 'et'
        ? 'bg-[#A8450E] text-white shadow-sm'
        : 'text-gray-400 hover:text-gray-300'"
    >
      EST
    </button>
    <button
       @click="setLocale('en')"
      class="cursor-pointer px-2.5 py-1 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer"
      :class="locale === 'en'
        ? 'bg-[#A8450E] text-white shadow-sm'
        : 'text-gray-400 hover:text-gray-300'"
    >
      ENG
    </button>
  </div>
</template>