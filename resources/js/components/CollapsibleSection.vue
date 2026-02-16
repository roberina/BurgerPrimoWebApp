<template>
  <div class="bg-[#121212] rounded-2xl border border-gray-800 overflow-hidden transition-all">
    <!-- Header -->
    <button
      @click="toggleCollapse"
      class="w-full px-6 py-4 flex items-center justify-between hover:bg-[#1a1a1a] transition-colors"
    >
      <div class="flex items-center gap-3">
        <component :is="icon" :size="24" class="text-[#D2691E]" />
        <div class="text-left">
          <h3 class="text-xl font-bold text-white">{{ title }}</h3>
          <p v-if="subtitle" class="text-sm text-gray-400">{{ subtitle }}</p>
        </div>
        <span v-if="count !== undefined" class="px-3 py-1 bg-[#D2691E]/20 text-[#D2691E] rounded-full text-sm font-bold">
          {{ count }}
        </span>
      </div>
      <component
        :is="isCollapsed ? ChevronDown : ChevronUp"
        :size="24"
        class="text-gray-400 transition-transform"
      />
    </button>

    <!-- Content -->
    <div
      v-show="!isCollapsed"
      class="px-6 pb-6"
    >
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';

interface Props {
  title: string;
  subtitle?: string;
  icon: any;
  count?: number;
  defaultCollapsed?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  defaultCollapsed: false,
});

const isCollapsed = ref(props.defaultCollapsed);

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
};
</script>
