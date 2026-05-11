<script setup lang="ts">
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Check, X, MessageSquare, UtensilsCrossed } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();

interface BurgerItem {
  id: number;
  name: string;
  total_price: number;
  created_at: string;
  status: string;
  admin_note?: string;
  user: { name: string; email: string };
  ingredients: { name: string; category: string; quantity: number; price: number }[];
}

interface Props {
  pendingBurgers: BurgerItem[];
  approvedBurgers: BurgerItem[];
  rejectedBurgers: BurgerItem[];
}

const props = defineProps<Props>();
const activeTab = ref<'pending' | 'approved' | 'rejected'>('pending');

const tabs = [
  { key: 'pending' as const, label: 'Ootavad' },
  { key: 'approved' as const, label: 'Kinnitatud' },
  { key: 'rejected' as const, label: 'Tagasi lükatud' },
];

const rejectModal = reactive({ show: false, burgerId: null as number | null, note: '' });

const openReject = (id: number) => {
  rejectModal.burgerId = id;
  rejectModal.note = '';
  rejectModal.show = true;
};

const approve = (id: number) => {
  router.post(`/admin/burger-review/${id}/approve`, {}, {
    preserveScroll: true,
    onSuccess: () => success('Burger kinnitatud'),
  });
};

const submitReject = () => {
  if (!rejectModal.burgerId || !rejectModal.note.trim()) return;
  router.post(
    `/admin/burger-review/${rejectModal.burgerId}/reject`,
    { admin_note: rejectModal.note },
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Burger tagasi lükatud');
        rejectModal.show = false;
        rejectModal.burgerId = null;
        rejectModal.note = '';
      },
      onError: () => error('Toiming ebaõnnestus'),
    }
  );
};

const formatDate = (d: string) => new Date(d).toLocaleDateString('et-EE');

const currentList = () =>
  activeTab.value === 'pending'
    ? props.pendingBurgers
    : activeTab.value === 'approved'
    ? props.approvedBurgers
    : props.rejectedBurgers;
</script>

<template>
  <Head title="Burger ülevaatus" />
  <AdminLayout>
    <template #header>
      <div>
        <h1 class="text-sm font-semibold text-zinc-100">Burger ülevaatus</h1>
        <p class="text-xs text-zinc-500">Klientide saadetud burgerid</p>
      </div>
    </template>

    <!-- Reject modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-150"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
      >
        <div
          v-if="rejectModal.show"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
          @click.self="rejectModal.show = false"
        >
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Tagasilükkamise põhjus</h3>
              <p class="text-xs text-zinc-400 mb-3">Kasutaja näeb seda põhjust</p>
              <textarea
                v-model="rejectModal.note"
                placeholder="nt. Koostisosad ei sobi meie menüüga..."
                rows="3"
                class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors resize-none"
              />
              <div class="flex gap-2 mt-4">
                <button
                  @click="rejectModal.show = false"
                  class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors"
                >
                  Tühista
                </button>
                <button
                  @click="submitReject"
                  :disabled="!rejectModal.note.trim()"
                  class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white text-xs font-medium transition-colors"
                >
                  Lükka tagasi
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-3 mb-5">
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-center">
        <p class="text-lg font-bold text-yellow-400">{{ pendingBurgers.length }}</p>
        <p class="text-xs text-zinc-600 mt-0.5">Ootab kinnitust</p>
      </div>
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-center">
        <p class="text-lg font-bold text-green-400">{{ approvedBurgers.length }}</p>
        <p class="text-xs text-zinc-600 mt-0.5">Kinnitatud</p>
      </div>
      <div class="bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 text-center">
        <p class="text-lg font-bold text-red-400">{{ rejectedBurgers.length }}</p>
        <p class="text-xs text-zinc-600 mt-0.5">Tagasi lükatud</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-5">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key"
        :class="[
          'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-colors',
          activeTab === tab.key
            ? 'bg-orange-500/15 text-orange-400'
            : 'bg-[#18181b] border border-[#27272a] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a]',
        ]"
      >
        {{ tab.label }}
        <span
          :class="[
            'inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-semibold',
            activeTab === tab.key ? 'bg-orange-500/20 text-orange-300' : 'bg-[#27272a] text-zinc-600',
          ]"
        >
          {{ tab.key === 'pending' ? pendingBurgers.length : tab.key === 'approved' ? approvedBurgers.length : rejectedBurgers.length }}
        </span>
      </button>
    </div>

    <!-- Burger list -->
    <div class="space-y-3">
      <template v-for="burger in currentList()" :key="burger.id">
        <div class="bg-[#18181b] border border-[#27272a] rounded-lg p-4">
          <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="text-sm font-semibold text-zinc-100">{{ burger.name }}</span>
                <span v-if="activeTab === 'pending'" class="text-[10px] bg-yellow-500/10 text-yellow-400 px-1.5 py-0.5 rounded-full border border-yellow-500/20 font-medium">Ootab kinnitust</span>
                <span v-else-if="activeTab === 'approved'" class="text-[10px] bg-green-500/10 text-green-400 px-1.5 py-0.5 rounded-full border border-green-500/20 font-medium">Kinnitatud</span>
                <span v-else class="text-[10px] bg-red-500/10 text-red-400 px-1.5 py-0.5 rounded-full border border-red-500/20 font-medium">Tagasi lükatud</span>
              </div>
              <p class="text-xs text-zinc-500">{{ burger.user.name }} · {{ burger.user.email }}</p>
              <p class="text-xs text-zinc-700 mb-3">{{ formatDate(burger.created_at) }}</p>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="ing in burger.ingredients"
                  :key="ing.name + ing.category"
                  class="bg-[#09090b] border border-[#27272a] text-zinc-400 text-xs px-2 py-0.5 rounded-md"
                >
                  {{ ing.name }}<span v-if="ing.quantity > 1" class="text-zinc-600 ml-0.5">×{{ ing.quantity }}</span>
                </span>
              </div>
              <div v-if="burger.admin_note" class="mt-3 flex items-start gap-2 text-xs text-zinc-400 bg-[#09090b] rounded-md px-3 py-2 border border-[#27272a]">
                <MessageSquare :size="12" class="flex-shrink-0 mt-0.5 text-zinc-600" />
                <span>{{ burger.admin_note }}</span>
              </div>
            </div>
            <div class="flex flex-col items-end gap-3 flex-shrink-0">
              <span class="text-sm font-bold text-orange-500">{{ burger.total_price.toFixed(2) }}€</span>
              <div v-if="activeTab === 'pending'" class="flex gap-2">
                <button @click="openReject(burger.id)" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-md text-xs font-medium transition-colors">
                  <X :size="11" />
                  Lükka tagasi
                </button>
                <button @click="approve(burger.id)" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-500/10 text-green-400 hover:bg-green-500/20 rounded-md text-xs font-medium transition-colors">
                  <Check :size="11" />
                  Kinnita
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>

      <div v-if="currentList().length === 0" class="flex flex-col items-center py-12 bg-[#18181b] border border-[#27272a] rounded-lg">
        <UtensilsCrossed :size="28" class="text-zinc-700 mb-3" />
        <p class="text-sm text-zinc-500">Burgereid pole</p>
      </div>
    </div>
  </AdminLayout>
</template>
