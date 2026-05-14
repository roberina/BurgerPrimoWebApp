<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, defineAsyncComponent, ref, type Component } from 'vue';

interface Props {
    name: string;
    class?: string;
    size?: number | string;
    color?: string;
    strokeWidth?: number | string;
}

const props = withDefaults(defineProps<Props>(), {
    class: '',
    size: 16,
    strokeWidth: 2,
});

const className = computed(() => cn('h-4 w-4', props.class));

const icon = ref<Component | null>(null);

import('lucide-vue-next').then((icons) => {
    const key = props.name.charAt(0).toUpperCase() + props.name.slice(1);
    icon.value = (icons as Record<string, any>)[key] ?? null;
});
</script>

<template>
    <component
        v-if="icon"
        :is="icon"
        :class="className"
        :size="size"
        :stroke-width="strokeWidth"
        :color="color"
    />
</template>
