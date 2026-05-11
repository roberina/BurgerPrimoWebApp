<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ShieldCheck, Bike, Users } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { success } = useToast();

interface User {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
  is_courier: boolean;
}

const props = defineProps<{ users: User[] }>();

const toggleCourier = (userId: number) => {
  router.post(`/admin/users/${userId}/toggle-courier`, {}, {
    preserveScroll: true,
    onSuccess: () => success('Kasutaja roll uuendatud'),
  });
};
</script>

<template>
  <Head title="Kasutajad" />
  <AdminLayout>
    <template #header>
      <div>
        <h1 class="text-sm font-semibold text-zinc-100">Kasutajahaldus</h1>
        <p class="text-xs text-zinc-500">Kulleri õiguste haldamine</p>
      </div>
    </template>

    <div class="max-w-2xl space-y-2">
      <div
        v-for="user in props.users"
        :key="user.id"
        class="flex items-center justify-between bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 hover:border-[#3f3f46] transition-colors"
      >
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-8 h-8 rounded-full bg-orange-500/10 flex items-center justify-center flex-shrink-0">
            <span class="text-orange-400 text-xs font-bold">{{ user.name.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="min-w-0">
            <p class="text-sm font-medium text-zinc-100 truncate">{{ user.name }}</p>
            <p class="text-xs text-zinc-500 truncate">{{ user.email }}</p>
            <div class="flex gap-1.5 mt-1">
              <span
                v-if="user.is_admin"
                class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-orange-500/10 text-orange-400 border border-orange-500/20 font-medium"
              >
                <ShieldCheck :size="9" />
                Admin
              </span>
              <span
                v-if="user.is_courier"
                class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-medium"
              >
                <Bike :size="9" />
                Kuller
              </span>
            </div>
          </div>
        </div>

        <button
          v-if="!user.is_admin"
          @click="toggleCourier(user.id)"
          :class="user.is_courier
            ? 'text-red-400 bg-red-500/10 hover:bg-red-500/20 border-red-500/20'
            : 'text-cyan-400 bg-cyan-500/10 hover:bg-cyan-500/20 border-cyan-500/20'"
          class="text-xs px-3 py-1.5 rounded-md border font-medium transition-colors flex-shrink-0 ml-3"
        >
          {{ user.is_courier ? 'Eemalda kuller' : 'Tee kulleriks' }}
        </button>
      </div>

      <div
        v-if="props.users.length === 0"
        class="flex flex-col items-center py-12 bg-[#18181b] border border-[#27272a] rounded-lg"
      >
        <Users :size="28" class="text-zinc-700 mb-3" />
        <p class="text-sm text-zinc-500">Kasutajaid pole</p>
      </div>
    </div>
  </AdminLayout>
</template>
