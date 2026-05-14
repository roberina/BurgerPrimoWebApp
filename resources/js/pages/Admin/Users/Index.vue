<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import {
  ShieldCheck, Bike, Users, Plus, Pencil, Trash2,
  ToggleLeft, ToggleRight, Eye, EyeOff, X,
  Search, ChevronDown, ArrowUpDown,
} from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();
const page = usePage();
const isSuperAdmin = computed(() => page.props.adminRole === 'superadmin');

interface RegularUser {
  id: number; name: string; email: string;
  is_admin: boolean; admin_role: string | null; admin_permissions: string[] | null;
  is_active: boolean; is_courier: boolean; created_at: string;
}

const props = defineProps<{
  users: RegularUser[];
  validPermissions?: string[];
}>();

const permissionLabels: Record<string, string> = {
  orders: 'Tellimused', menu: 'Menüü', burger: 'Burger',
  users: 'Kliendid', announcements: 'Teadaanded',
};

// ─── Section collapse ─────────────────────────────────────────────────────────
const usersOpen = ref(true);

// ─── Search, sort & filter ────────────────────────────────────────────────────
type SortKey   = 'newest' | 'oldest' | 'name_asc' | 'name_desc';
type FilterKey = 'all' | 'couriers' | 'admins';

const search    = ref('');
const sortKey   = ref<SortKey>('newest');
const filterKey = ref<FilterKey>('all');

const sortOptions = [
  { value: 'newest' as SortKey,    label: 'Uuem ees' },
  { value: 'oldest' as SortKey,    label: 'Vanem ees' },
  { value: 'name_asc' as SortKey,  label: 'Nimi A–Z' },
  { value: 'name_desc' as SortKey, label: 'Nimi Z–A' },
];

const filterOptions: { value: FilterKey; label: string }[] = [
  { value: 'all',      label: 'Kõik' },
  { value: 'couriers', label: 'Kullerid' },
  { value: 'admins',   label: 'Adminid' },
];

const filteredUsers = computed(() => {
  let list = [...props.users];

  if (filterKey.value === 'couriers') list = list.filter(u => u.is_courier);
  if (filterKey.value === 'admins')   list = list.filter(u => u.is_admin && u.admin_role !== 'superadmin');

  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    list = list.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
  }

  switch (sortKey.value) {
    case 'oldest':    list.sort((a, b) => a.created_at.localeCompare(b.created_at)); break;
    case 'name_asc':  list.sort((a, b) => a.name.localeCompare(b.name)); break;
    case 'name_desc': list.sort((a, b) => b.name.localeCompare(a.name)); break;
    default:          list.sort((a, b) => b.created_at.localeCompare(a.created_at)); break;
  }

  return list;
});

// ─── Dropdown ─────────────────────────────────────────────────────────────────
const openDropdownId = ref<string | null>(null);

function toggleDropdown(key: string) {
  openDropdownId.value = openDropdownId.value === key ? null : key;
}

function closeDropdowns() { openDropdownId.value = null; }

onMounted(() => document.addEventListener('click', closeDropdowns));
onUnmounted(() => document.removeEventListener('click', closeDropdowns));

// ─── Regular user actions ─────────────────────────────────────────────────────
const deleteUserModal = reactive({ open: false, id: null as number | null, name: '' });

function toggleCourier(user: RegularUser) {
  router.post(`/admin/users/${user.id}/toggle-courier`, {}, {
    preserveScroll: true,
    onSuccess: () => success('Kasutaja roll uuendatud'),
  });
}

function openDeleteUser(user: RegularUser) {
  deleteUserModal.id = user.id; deleteUserModal.name = user.name; deleteUserModal.open = true;
}

function confirmDeleteUser() {
  if (!deleteUserModal.id) return;
  router.delete(`/admin/users/${deleteUserModal.id}`, {
    preserveScroll: true,
    onSuccess: () => success('Kasutaja kustutatud.'),
    onError: () => error('Kustutamine ebaõnnestus.'),
  });
  deleteUserModal.open = false;
}

// ─── Demote subadmin → regular user ──────────────────────────────────────────
const demoteModal = reactive({ open: false, id: null as number | null, name: '' });

function openDemote(user: RegularUser) {
  demoteModal.id = user.id; demoteModal.name = user.name; demoteModal.open = true;
}

function confirmDemote() {
  if (!demoteModal.id) return;
  router.post(`/admin/subadmins/${demoteModal.id}/demote`, {}, {
    preserveScroll: true,
    onSuccess: () => success('Admin õigused eemaldatud.'),
    onError: () => error('Eemaldamine ebaõnnestus.'),
  });
  demoteModal.open = false;
}

// ─── Promote to subadmin ──────────────────────────────────────────────────────
const promoteModal = reactive({ open: false, userId: null as number | null, name: '', permissions: [] as string[] });
const promoteErrors = reactive<Record<string, string>>({});

function openPromote(user: RegularUser) {
  promoteModal.userId = user.id;
  promoteModal.name = user.name;
  promoteModal.permissions = [];
  Object.keys(promoteErrors).forEach(k => delete promoteErrors[k]);
  promoteModal.open = true;
}

function togglePromotePerm(perm: string) {
  const idx = promoteModal.permissions.indexOf(perm);
  if (idx === -1) promoteModal.permissions.push(perm);
  else promoteModal.permissions.splice(idx, 1);
}

function submitPromote() {
  Object.keys(promoteErrors).forEach(k => delete promoteErrors[k]);
  router.post(`/admin/subadmins/${promoteModal.userId}/promote`, { permissions: promoteModal.permissions }, {
    preserveScroll: true,
    onSuccess: () => { success(`${promoteModal.name} on nüüd subadmin.`); promoteModal.open = false; },
    onError: (errs) => Object.assign(promoteErrors, errs),
  });
}

// ─── Subadmin modal (create / edit) ──────────────────────────────────────────
const subadminModal = reactive({ open: false, editingId: null as number | null });
const showPass = ref(false);
const form = reactive({ name: '', email: '', password: '', permissions: [] as string[] });
const formErrors = reactive<Record<string, string>>({});
const modalTitle = computed(() => subadminModal.editingId ? 'Muuda subadmini' : 'Loo subadmin');

function openCreate() {
  subadminModal.editingId = null;
  form.name = ''; form.email = ''; form.password = ''; form.permissions = [];
  Object.keys(formErrors).forEach(k => delete formErrors[k]);
  showPass.value = false; subadminModal.open = true;
}

function openEdit(user: RegularUser) {
  subadminModal.editingId = user.id;
  form.name = user.name; form.email = user.email; form.password = '';
  form.permissions = user.admin_permissions ? [...user.admin_permissions] : [];
  Object.keys(formErrors).forEach(k => delete formErrors[k]);
  showPass.value = false; subadminModal.open = true;
}

function togglePerm(perm: string) {
  const idx = form.permissions.indexOf(perm);
  if (idx === -1) form.permissions.push(perm);
  else form.permissions.splice(idx, 1);
}

function submitSubadmin() {
  Object.keys(formErrors).forEach(k => delete formErrors[k]);
  const data = { name: form.name, email: form.email, password: form.password || undefined, permissions: form.permissions };
  if (subadminModal.editingId) {
    router.put(`/admin/subadmins/${subadminModal.editingId}`, data as any, {
      preserveScroll: true,
      onSuccess: () => { success('Subadmin uuendatud.'); subadminModal.open = false; },
      onError: (errs) => Object.assign(formErrors, errs),
    });
  } else {
    router.post('/admin/subadmins', data as any, {
      preserveScroll: true,
      onSuccess: () => { success('Subadmin loodud.'); subadminModal.open = false; },
      onError: (errs) => Object.assign(formErrors, errs),
    });
  }
}

// ─── Subadmin toggle status & delete ─────────────────────────────────────────
const deleteSubModal = reactive({ open: false, id: null as number | null, name: '' });

function openDeleteSub(user: RegularUser) {
  deleteSubModal.id = user.id; deleteSubModal.name = user.name; deleteSubModal.open = true;
}

function confirmDeleteSub() {
  if (!deleteSubModal.id) return;
  router.delete(`/admin/subadmins/${deleteSubModal.id}`, {
    preserveScroll: true,
    onSuccess: () => success('Subadmin kustutatud.'),
    onError: () => error('Kustutamine ebaõnnestus.'),
  });
  deleteSubModal.open = false;
}

function toggleStatus(user: RegularUser) {
  router.patch(`/admin/subadmins/${user.id}/status`, {}, {
    preserveScroll: true,
    onSuccess: () => success(user.is_active ? 'Subadmin deaktiveeritud.' : 'Subadmin aktiveeritud.'),
    onError: () => error('Oleku muutmine ebaõnnestus.'),
  });
}
</script>

<template>
  <Head title="Kasutajad" />
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Kasutajahaldus</h1>
          <p class="text-xs text-zinc-500">Kulleri õiguste ja subadminide haldamine</p>
        </div>
        <button v-if="isSuperAdmin" @click="openCreate"
          class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors">
          <Plus :size="13" /> Loo subadmin
        </button>
      </div>
    </template>

    <div class="space-y-4">

      <!-- ─── Users ─────────────────────────────────────────────────────────── -->
      <section class="bg-[#18181b] border border-[#27272a] rounded-xl">
        <button @click="usersOpen = !usersOpen"
          class="w-full flex items-center justify-between px-4 py-3 hover:bg-[#1f1f22] rounded-t-xl transition-colors">
          <div class="flex items-center gap-2">
            <Users :size="14" class="text-zinc-400" />
            <span class="text-xs font-semibold text-zinc-300">Kasutajad</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-zinc-800 text-zinc-500">{{ filteredUsers.length }}</span>
          </div>
          <ChevronDown :size="14" :class="['text-zinc-500 transition-transform duration-200', usersOpen ? 'rotate-180' : '']" />
        </button>

        <div v-show="usersOpen">
          <!-- Toolbar -->
          <div class="flex flex-col gap-2 px-4 pb-3 pt-2 border-t border-[#27272a]">
            <!-- Filter chips -->
            <div class="flex gap-1.5">
              <button
                v-for="opt in filterOptions" :key="opt.value"
                @click="filterKey = opt.value"
                :class="[
                  'px-2.5 py-1 rounded-md text-[11px] font-medium transition-colors',
                  filterKey === opt.value
                    ? 'bg-orange-600 text-white'
                    : 'bg-[#09090b] border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:border-[#52525b]',
                ]"
              >{{ opt.label }}</button>
            </div>
            <!-- Search + sort -->
            <div class="flex gap-2">
              <div class="relative flex-1">
                <Search :size="12" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" />
                <input v-model="search" type="text" placeholder="Otsi nime või e-posti järgi…"
                  class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md pl-7 pr-3 py-1.5 text-xs text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors" />
              </div>
              <div class="relative">
                <ArrowUpDown :size="11" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" />
                <select v-model="sortKey"
                  class="bg-[#09090b] border border-[#3f3f46] rounded-md pl-7 pr-6 py-1.5 text-xs text-zinc-100 focus:outline-none focus:border-orange-500 appearance-none cursor-pointer transition-colors">
                  <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Grid -->
          <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div v-for="user in filteredUsers" :key="user.id" class="relative">
              <!-- Card -->
              <div
                @click.stop="toggleDropdown(`u-${user.id}`)"
                :class="['flex items-center gap-3 bg-[#09090b] border rounded-lg px-4 py-3 cursor-pointer transition-colors', openDropdownId === `u-${user.id}` ? 'border-orange-500/40' : 'border-[#27272a] hover:border-[#3f3f46]']"
              >
                <div :class="['w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0', user.admin_role === 'subadmin' ? 'bg-violet-500/10' : 'bg-orange-500/10']">
                  <span :class="['text-xs font-bold', user.admin_role === 'subadmin' ? 'text-violet-400' : 'text-orange-400']">{{ user.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-zinc-100 truncate">{{ user.name }}</p>
                  <p class="text-xs text-zinc-500 truncate">{{ user.email }}</p>
                  <div class="flex gap-1.5 mt-1 flex-wrap">
                    <span v-if="user.admin_role === 'superadmin'" class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-orange-500/10 text-orange-400 border border-orange-500/20 font-medium">
                      <ShieldCheck :size="9" /> Super Admin
                    </span>
                    <span v-else-if="user.admin_role === 'subadmin'" class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-violet-500/10 text-violet-400 border border-violet-500/20 font-medium">
                      <ShieldCheck :size="9" /> Subadmin
                    </span>
                    <span v-if="user.admin_role === 'subadmin' && !user.is_active"
                      class="inline-flex items-center text-[10px] px-1.5 py-0.5 rounded-full bg-zinc-700/40 text-zinc-500 border border-zinc-600/30 font-medium">
                      Mitteaktiivne
                    </span>
                    <span v-if="user.is_courier" class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-medium">
                      <Bike :size="9" /> Kuller
                    </span>
                  </div>
                  <!-- Permission tags for subadmins -->
                  <div v-if="user.admin_role === 'subadmin' && user.admin_permissions && user.admin_permissions.length > 0" class="flex flex-wrap gap-1 mt-1.5">
                    <span v-for="perm in user.admin_permissions" :key="perm" class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-800 text-zinc-400 border border-zinc-700">
                      {{ permissionLabels[perm] ?? perm }}
                    </span>
                  </div>
                </div>
                <ChevronDown :size="13" :class="['text-zinc-600 flex-shrink-0 transition-transform duration-150', openDropdownId === `u-${user.id}` ? 'rotate-180 text-zinc-400' : '']" />
              </div>

              <!-- Dropdown -->
              <Transition enter-active-class="transition-all duration-100 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
                <div v-if="openDropdownId === `u-${user.id}`"
                  @click.stop
                  class="absolute right-0 bottom-full mb-1 z-50 w-52 bg-[#18181b] border border-[#3f3f46] rounded-lg shadow-xl py-1 overflow-hidden"
                >
                  <!-- Regular user actions -->
                  <template v-if="!user.is_admin">
                    <button @click="toggleCourier(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:bg-[#27272a] transition-colors text-left">
                      <Bike :size="13" :class="user.is_courier ? 'text-red-400' : 'text-cyan-400'" />
                      {{ user.is_courier ? 'Eemalda kuller' : 'Tee kulleriks' }}
                    </button>
                    <button v-if="isSuperAdmin"
                      @click="openPromote(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:bg-[#27272a] transition-colors text-left">
                      <ShieldCheck :size="13" class="text-violet-400" />
                      Tee subadminiks
                    </button>
                    <div class="my-1 border-t border-[#27272a]" />
                    <button @click="openDeleteUser(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-red-400 hover:bg-red-500/10 transition-colors text-left">
                      <Trash2 :size="13" /> Kustuta kasutaja
                    </button>
                  </template>

                  <!-- Subadmin actions -->
                  <template v-else-if="user.admin_role === 'subadmin' && isSuperAdmin">
                    <button @click="openEdit(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:bg-[#27272a] transition-colors text-left">
                      <Pencil :size="13" class="text-zinc-400" /> Muuda
                    </button>
                    <button @click="toggleStatus(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:bg-[#27272a] transition-colors text-left">
                      <component :is="user.is_active ? ToggleLeft : ToggleRight" :size="13" :class="user.is_active ? 'text-zinc-400' : 'text-emerald-400'" />
                      {{ user.is_active ? 'Deaktiveeri' : 'Aktiveeri' }}
                    </button>
                    <button @click="toggleCourier(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:bg-[#27272a] transition-colors text-left">
                      <Bike :size="13" :class="user.is_courier ? 'text-red-400' : 'text-cyan-400'" />
                      {{ user.is_courier ? 'Eemalda kuller' : 'Tee kulleriks' }}
                    </button>
                    <button @click="openDemote(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:bg-[#27272a] transition-colors text-left">
                      <ShieldCheck :size="13" class="text-zinc-400" /> Eemalda admin õigused
                    </button>
                    <div class="my-1 border-t border-[#27272a]" />
                    <button @click="openDeleteSub(user); closeDropdowns()"
                      class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-red-400 hover:bg-red-500/10 transition-colors text-left">
                      <Trash2 :size="13" /> Kustuta kasutaja
                    </button>
                  </template>

                  <!-- Superadmin — no actions -->
                  <template v-else>
                    <p class="px-3 py-2 text-xs text-zinc-600 italic">Toimingud puuduvad</p>
                  </template>
                </div>
              </Transition>
            </div>

            <div v-if="filteredUsers.length === 0" class="col-span-full flex flex-col items-center py-10">
              <Users :size="24" class="text-zinc-700 mb-2" />
              <p class="text-xs text-zinc-500">{{ search ? 'Tulemusi ei leitud' : 'Kasutajaid pole' }}</p>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- ─── Subadmin create/edit modal ────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="subadminModal.open" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="subadminModal.open = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#27272a]">
              <h3 class="text-sm font-semibold text-zinc-100">{{ modalTitle }}</h3>
              <button @click="subadminModal.open = false" class="p-1 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition-colors">
                <X :size="15" />
              </button>
            </div>
            <form @submit.prevent="submitSubadmin" class="p-5 space-y-4">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Nimi</label>
                <input v-model="form.name" type="text" required placeholder="Täisnimi"
                  class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors" />
                <p v-if="formErrors.name" class="mt-1 text-xs text-red-400">{{ formErrors.name }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">E-post</label>
                <input v-model="form.email" type="email" required placeholder="email@example.com"
                  class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors" />
                <p v-if="formErrors.email" class="mt-1 text-xs text-red-400">{{ formErrors.email }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">
                  Parool <span v-if="subadminModal.editingId" class="text-zinc-600 font-normal ml-1">(jäta tühjaks, et mitte muuta)</span>
                </label>
                <div class="relative">
                  <input v-model="form.password" :type="showPass ? 'text' : 'password'" :required="!subadminModal.editingId" placeholder="••••••••"
                    class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 pr-9 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors" />
                  <button type="button" @click="showPass = !showPass" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300">
                    <component :is="showPass ? EyeOff : Eye" :size="14" />
                  </button>
                </div>
                <p v-if="formErrors.password" class="mt-1 text-xs text-red-400">{{ formErrors.password }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-2">Õigused</label>
                <div class="grid grid-cols-2 gap-2">
                  <label v-for="perm in (validPermissions ?? [])" :key="perm"
                    :class="['flex items-center gap-2.5 px-3 py-2.5 rounded-md border cursor-pointer transition-all select-none', form.permissions.includes(perm) ? 'border-orange-500/50 bg-orange-600/10 text-orange-300' : 'border-[#3f3f46] bg-[#09090b] text-zinc-400 hover:border-[#52525b]']">
                    <input type="checkbox" class="hidden" :checked="form.permissions.includes(perm)" @change="togglePerm(perm)" />
                    <span :class="['w-3.5 h-3.5 rounded border flex-shrink-0 flex items-center justify-center', form.permissions.includes(perm) ? 'border-orange-500 bg-orange-500' : 'border-zinc-600']">
                      <svg v-if="form.permissions.includes(perm)" viewBox="0 0 10 8" class="w-2 h-2 text-white fill-none stroke-current stroke-2"><polyline points="1,4 4,7 9,1" /></svg>
                    </span>
                    <span class="text-xs font-medium">{{ permissionLabels[perm] ?? perm }}</span>
                  </label>
                </div>
                <p v-if="formErrors.permissions" class="mt-1 text-xs text-red-400">{{ formErrors.permissions }}</p>
              </div>
              <div class="flex gap-2 pt-1">
                <button type="button" @click="subadminModal.open = false"
                  class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button type="submit"
                  class="flex-1 py-2 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium transition-colors">
                  {{ subadminModal.editingId ? 'Salvesta' : 'Loo subadmin' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ─── Promote to subadmin modal ─────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="promoteModal.open" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="promoteModal.open = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#27272a]">
              <div>
                <h3 class="text-sm font-semibold text-zinc-100">Tee subadminiks</h3>
                <p class="text-xs text-zinc-500 mt-0.5">{{ promoteModal.name }}</p>
              </div>
              <button @click="promoteModal.open = false" class="p-1 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition-colors">
                <X :size="15" />
              </button>
            </div>
            <form @submit.prevent="submitPromote" class="p-5 space-y-4">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-2">Vali õigused</label>
                <div class="grid grid-cols-2 gap-2">
                  <label v-for="perm in (validPermissions ?? [])" :key="perm"
                    :class="['flex items-center gap-2.5 px-3 py-2.5 rounded-md border cursor-pointer transition-all select-none', promoteModal.permissions.includes(perm) ? 'border-orange-500/50 bg-orange-600/10 text-orange-300' : 'border-[#3f3f46] bg-[#09090b] text-zinc-400 hover:border-[#52525b]']">
                    <input type="checkbox" class="hidden" :checked="promoteModal.permissions.includes(perm)" @change="togglePromotePerm(perm)" />
                    <span :class="['w-3.5 h-3.5 rounded border flex-shrink-0 flex items-center justify-center', promoteModal.permissions.includes(perm) ? 'border-orange-500 bg-orange-500' : 'border-zinc-600']">
                      <svg v-if="promoteModal.permissions.includes(perm)" viewBox="0 0 10 8" class="w-2 h-2 text-white fill-none stroke-current stroke-2"><polyline points="1,4 4,7 9,1" /></svg>
                    </span>
                    <span class="text-xs font-medium">{{ permissionLabels[perm] ?? perm }}</span>
                  </label>
                </div>
                <p v-if="promoteErrors.permissions" class="mt-1 text-xs text-red-400">{{ promoteErrors.permissions }}</p>
              </div>
              <div class="flex gap-2 pt-1">
                <button type="button" @click="promoteModal.open = false"
                  class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button type="submit"
                  class="flex-1 py-2 rounded-md bg-violet-600 hover:bg-violet-700 text-white text-xs font-medium transition-colors">Tee subadminiks</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ─── Demote confirm ───────────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="demoteModal.open" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="demoteModal.open = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Eemalda admin õigused</h3>
              <p class="text-xs text-zinc-400">Kas oled kindel? <span class="text-zinc-200 font-medium">{{ demoteModal.name }}</span> kaotab admin õigused, aga konto jääb alles.</p>
              <div class="flex gap-2 mt-5">
                <button @click="demoteModal.open = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="confirmDemote" class="flex-1 py-2 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium transition-colors">Eemalda</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ─── Delete user confirm ───────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteUserModal.open" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="deleteUserModal.open = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Kustuta kasutaja</h3>
              <p class="text-xs text-zinc-400">Kas oled kindel, et soovid kustutada <span class="text-zinc-200 font-medium">{{ deleteUserModal.name }}</span>? Seda ei saa tagasi võtta.</p>
              <div class="flex gap-2 mt-5">
                <button @click="deleteUserModal.open = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="confirmDeleteUser" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Kustuta</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ─── Delete subadmin confirm ───────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteSubModal.open" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="deleteSubModal.open = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Kustuta subadmin</h3>
              <p class="text-xs text-zinc-400">Kas oled kindel, et soovid kustutada <span class="text-zinc-200 font-medium">{{ deleteSubModal.name }}</span>? Seda ei saa tagasi võtta.</p>
              <div class="flex gap-2 mt-5">
                <button @click="deleteSubModal.open = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="confirmDeleteSub" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Kustuta</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>
