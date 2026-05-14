<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldOff } from 'lucide-vue-next';

defineProps<{ message?: string }>();
</script>

<template>
  <Head title="Access Denied" />
  <AdminLayout>
    <template #header>
      <h1 class="text-sm font-semibold text-zinc-100">Access Denied</h1>
    </template>

    <div class="flex flex-col items-center justify-center py-24 text-center">
      <div class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center mb-5">
        <ShieldOff :size="28" class="text-red-400" />
      </div>
      <h2 class="text-lg font-semibold text-zinc-100 mb-2">Access Denied</h2>
      <p class="text-sm text-zinc-400 max-w-xs mb-6">
        {{ message || 'You do not have permission to view this section. Contact your super admin.' }}
      </p>
      <Link
        href="/admin/dashboard"
        class="px-4 py-2 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium transition-colors"
      >
        Back to Dashboard
      </Link>
    </div>
  </AdminLayout>
</template>
