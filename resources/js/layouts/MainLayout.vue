<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <Navbar />
    <main>
      <slot />
    </main>
    <Footer />
    <NotificationPrompt />
  </div>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import NotificationPrompt from '@/components/NotificationPrompt.vue';
import { useToast } from '@/composables/useToast';

const page = usePage();
const { success, error } = useToast();

const flash = computed(() => {
  try { return page.props.flash as any; } catch { return null; }
});

watch(flash, (val) => {
  if (val?.success) success(val.success);
  if (val?.error)   error(val.error);
}, { immediate: true });
</script>