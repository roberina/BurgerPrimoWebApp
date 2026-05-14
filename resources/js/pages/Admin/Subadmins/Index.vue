<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useToast } from '@/composables/useToast';
import {
  Plus,
  Pencil,
  Trash2,
  ToggleLeft,
  ToggleRight,
  ShieldCheck,
  X,
  Eye,
  EyeOff,
} from 'lucide-vue-next';

const { success, error } = useToast();
const page = usePage();

interface Subadmin {
  id: number;
  name: string;
  email: string;
  admin_permissions: string[] | null;
  is_active: boolean;
  created_at: string;
}

const props = defineProps<{
  subadmins: Subadmin[];
  validPermissions: string[];
}>();

// ─── Permission label map ─────────────────────────────────────────────────────
const permissionLabels: Record<string, string> = {
  users:         'User Management',
  menu:          'Menu Management',
  announcements: 'Announcements',
  orders:        'Order Management',
};

// ─── Modal state ─────────────────────────────────────────────────────────────
const modalOpen  = ref(false);
const editingId  = ref<number | null>(null);
const showPass   = ref(false);

const form = reactive({
  name:        '',
  email:       '',
  password:    '',
  permissions: [] as string[],
});

const formErrors = reactive<Record<string, string>>({});

const modalTitle = computed(() => editingId.value ? 'Edit Subadmin' : 'Create Subadmin');

function openCreate() {
  editingId.value  = null;
  form.name        = '';
  form.email       = '';
  form.password    = '';
  form.permissions = [];
  Object.keys(formErrors).forEach(k => delete formErrors[k]);
  showPass.value   = false;
  modalOpen.value  = true;
}

function openEdit(sub: Subadmin) {
  editingId.value  = sub.id;
  form.name        = sub.name;
  form.email       = sub.email;
  form.password    = '';
  form.permissions = sub.admin_permissions ? [...sub.admin_permissions] : [];
  Object.keys(formErrors).forEach(k => delete formErrors[k]);
  showPass.value   = false;
  modalOpen.value  = true;
}

function closeModal() {
  modalOpen.value = false;
}

function togglePermission(perm: string) {
  const idx = form.permissions.indexOf(perm);
  if (idx === -1) form.permissions.push(perm);
  else form.permissions.splice(idx, 1);
}

// ─── Delete confirm modal ─────────────────────────────────────────────────────
const deleteModal = reactive({ open: false, id: null as number | null, name: '' });

function openDelete(sub: Subadmin) {
  deleteModal.id   = sub.id;
  deleteModal.name = sub.name;
  deleteModal.open = true;
}

function confirmDelete() {
  if (!deleteModal.id) return;
  router.delete(`/admin/subadmins/${deleteModal.id}`, {
    preserveScroll: true,
    onSuccess: () => success('Subadmin deleted.'),
    onError: () => error('Failed to delete subadmin.'),
  });
  deleteModal.open = false;
}

// ─── Submit (create / update) ─────────────────────────────────────────────────
function submit() {
  Object.keys(formErrors).forEach(k => delete formErrors[k]);

  const data = {
    name:        form.name,
    email:       form.email,
    password:    form.password || undefined,
    permissions: form.permissions,
  };

  if (editingId.value) {
    router.put(`/admin/subadmins/${editingId.value}`, data as any, {
      preserveScroll: true,
      onSuccess: () => { success('Subadmin updated.'); closeModal(); },
      onError: (errs) => Object.assign(formErrors, errs),
    });
  } else {
    router.post('/admin/subadmins', data as any, {
      preserveScroll: true,
      onSuccess: () => { success('Subadmin created.'); closeModal(); },
      onError: (errs) => Object.assign(formErrors, errs),
    });
  }
}

// ─── Toggle active status ─────────────────────────────────────────────────────
function toggleStatus(sub: Subadmin) {
  router.patch(`/admin/subadmins/${sub.id}/status`, {}, {
    preserveScroll: true,
    onSuccess: () => success(sub.is_active ? 'Subadmin deactivated.' : 'Subadmin activated.'),
    onError: () => error('Failed to update status.'),
  });
}
</script>

<template>
  <Head title="Subadmin Management" />
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Subadmin Management</h1>
          <p class="text-xs text-zinc-500">Create and manage subadmin accounts and their permissions</p>
        </div>
        <button
          @click="openCreate"
          class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
        >
          <Plus :size="13" />
          New Subadmin
        </button>
      </div>
    </template>

    <!-- Empty state -->
    <div v-if="subadmins.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
      <div class="w-12 h-12 rounded-full bg-zinc-800 flex items-center justify-center mb-4">
        <ShieldCheck :size="20" class="text-zinc-500" />
      </div>
      <p class="text-sm text-zinc-400 mb-1">No subadmins yet</p>
      <p class="text-xs text-zinc-600 mb-5">Create one to grant limited admin access.</p>
      <button
        @click="openCreate"
        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors"
      >
        Create First Subadmin
      </button>
    </div>

    <!-- Table -->
    <div v-else class="space-y-2">
      <div
        v-for="sub in subadmins"
        :key="sub.id"
        class="flex items-center gap-4 bg-[#18181b] border border-[#27272a] rounded-lg px-4 py-3 hover:border-[#3f3f46] transition-colors"
      >
        <!-- Avatar -->
        <div class="w-8 h-8 rounded-full bg-orange-500/10 flex items-center justify-center flex-shrink-0">
          <span class="text-orange-400 text-xs font-bold">{{ sub.name.charAt(0).toUpperCase() }}</span>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <p class="text-sm font-medium text-zinc-100 truncate">{{ sub.name }}</p>
            <span
              :class="[
                'inline-flex items-center text-[10px] px-1.5 py-0.5 rounded-full font-medium border',
                sub.is_active
                  ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                  : 'bg-zinc-700/40 text-zinc-500 border-zinc-600/30',
              ]"
            >{{ sub.is_active ? 'Active' : 'Inactive' }}</span>
          </div>
          <p class="text-xs text-zinc-500 truncate">{{ sub.email }}</p>
          <!-- Permissions summary -->
          <div class="flex flex-wrap gap-1 mt-1.5">
            <span
              v-if="!sub.admin_permissions || sub.admin_permissions.length === 0"
              class="text-[10px] text-zinc-600 italic"
            >No permissions</span>
            <span
              v-for="perm in sub.admin_permissions"
              :key="perm"
              class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-800 text-zinc-400 border border-zinc-700"
            >{{ permissionLabels[perm] ?? perm }}</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1 flex-shrink-0">
          <button
            @click="openEdit(sub)"
            class="p-1.5 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition-colors"
            title="Edit"
          >
            <Pencil :size="13" />
          </button>
          <button
            @click="toggleStatus(sub)"
            :class="[
              'p-1.5 rounded-md transition-colors',
              sub.is_active
                ? 'text-emerald-500 hover:text-emerald-400 hover:bg-emerald-500/10'
                : 'text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a]',
            ]"
            :title="sub.is_active ? 'Deactivate' : 'Activate'"
          >
            <component :is="sub.is_active ? ToggleRight : ToggleLeft" :size="15" />
          </button>
          <button
            @click="openDelete(sub)"
            class="p-1.5 rounded-md text-zinc-500 hover:text-red-400 hover:bg-red-500/10 transition-colors"
            title="Delete"
          >
            <Trash2 :size="13" />
          </button>
        </div>
      </div>
    </div>

    <!-- ─── Create / Edit Modal ──────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modalOpen" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="closeModal">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-md">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#27272a]">
              <h3 class="text-sm font-semibold text-zinc-100">{{ modalTitle }}</h3>
              <button @click="closeModal" class="p-1 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition-colors">
                <X :size="15" />
              </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="submit" class="p-5 space-y-4">
              <!-- Name -->
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Full name"
                  class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors"
                />
                <p v-if="formErrors.name" class="mt-1 text-xs text-red-400">{{ formErrors.name }}</p>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="email@example.com"
                  class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors"
                />
                <p v-if="formErrors.email" class="mt-1 text-xs text-red-400">{{ formErrors.email }}</p>
              </div>

              <!-- Password -->
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1">
                  Password
                  <span v-if="editingId" class="text-zinc-600 font-normal ml-1">(leave blank to keep current)</span>
                </label>
                <div class="relative">
                  <input
                    v-model="form.password"
                    :type="showPass ? 'text' : 'password'"
                    :required="!editingId"
                    placeholder="••••••••"
                    class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 pr-9 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors"
                  />
                  <button
                    type="button"
                    @click="showPass = !showPass"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300"
                  >
                    <component :is="showPass ? EyeOff : Eye" :size="14" />
                  </button>
                </div>
                <p v-if="formErrors.password" class="mt-1 text-xs text-red-400">{{ formErrors.password }}</p>
              </div>

              <!-- Permissions -->
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-2">Permissions</label>
                <div class="grid grid-cols-2 gap-2">
                  <label
                    v-for="perm in validPermissions"
                    :key="perm"
                    :class="[
                      'flex items-center gap-2.5 px-3 py-2.5 rounded-md border cursor-pointer transition-all select-none',
                      form.permissions.includes(perm)
                        ? 'border-orange-500/50 bg-orange-600/10 text-orange-300'
                        : 'border-[#3f3f46] bg-[#09090b] text-zinc-400 hover:border-[#52525b]',
                    ]"
                  >
                    <input
                      type="checkbox"
                      class="hidden"
                      :checked="form.permissions.includes(perm)"
                      @change="togglePermission(perm)"
                    />
                    <span
                      :class="[
                        'w-3.5 h-3.5 rounded border flex-shrink-0 flex items-center justify-center',
                        form.permissions.includes(perm)
                          ? 'border-orange-500 bg-orange-500'
                          : 'border-zinc-600',
                      ]"
                    >
                      <svg v-if="form.permissions.includes(perm)" viewBox="0 0 10 8" class="w-2 h-2 text-white fill-none stroke-current stroke-2">
                        <polyline points="1,4 4,7 9,1" />
                      </svg>
                    </span>
                    <span class="text-xs font-medium">{{ permissionLabels[perm] ?? perm }}</span>
                  </label>
                </div>
                <p v-if="formErrors.permissions" class="mt-1 text-xs text-red-400">{{ formErrors.permissions }}</p>
              </div>

              <!-- Footer -->
              <div class="flex gap-2 pt-1">
                <button
                  type="button"
                  @click="closeModal"
                  class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="flex-1 py-2 rounded-md bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium transition-colors"
                >
                  {{ editingId ? 'Save Changes' : 'Create Subadmin' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ─── Delete Confirm Modal ──────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="deleteModal.open" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="deleteModal.open = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Delete Subadmin</h3>
              <p class="text-xs text-zinc-400">
                Are you sure you want to permanently delete <span class="text-zinc-200 font-medium">{{ deleteModal.name }}</span>?
                This cannot be undone.
              </p>
              <div class="flex gap-2 mt-5">
                <button
                  @click="deleteModal.open = false"
                  class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors"
                >
                  Cancel
                </button>
                <button
                  @click="confirmDelete"
                  class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>
