<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  ShoppingBag,
  ChefHat,
  Menu as MenuIcon,
  Package,
  FolderOpen,
  LogOut,
  User,
  X,
  ClipboardList,
  Users,
  Star,
  Megaphone,
  UtensilsCrossed,
  Home,
  ChevronDown,
} from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user as any);
const adminRole = computed(() => page.props.adminRole as string | null);
const adminPermissions = computed(() => (page.props.adminPermissions as string[]) ?? []);

const isSuperAdmin = computed(() => adminRole.value === 'superadmin');

const can = (permission: string | null) => {
  if (isSuperAdmin.value) return true;
  if (!permission) return true;
  return adminPermissions.value.includes(permission);
};

const sidebarOpen = ref(false);
const currentPath = computed(() => window.location.pathname);

const isActive = (path: string, exact = false) =>
  exact ? currentPath.value === path : currentPath.value.startsWith(path);

interface NavChild { title: string; url: string; icon: any; active: () => boolean }
interface NavItem  { title: string; icon: any; url?: string; active?: () => boolean; children?: NavChild[] }

const navigation = computed<NavItem[]>(() => {
  const items: NavItem[] = [];

  if (isSuperAdmin.value) {
    items.push({ title: 'Ülevaade', url: '/admin/dashboard', icon: LayoutDashboard, active: () => isActive('/admin/dashboard', true) });
  }

  if (can('orders')) {
    items.push({ title: 'Tellimused', url: '/admin/orders', icon: ShoppingBag, active: () => isActive('/admin/orders') });
  }

  if (can('menu')) {
    items.push({
      title: 'Menüü', icon: Package,
      children: [
        { title: 'Kategooriad', url: '/admin/menu/categories', icon: FolderOpen,      active: () => isActive('/admin/menu/categories') },
        { title: 'Tooted',      url: '/admin/menu/items',      icon: Package,         active: () => isActive('/admin/menu/items') },
        { title: 'Lisandid',    url: '/admin/addons',          icon: UtensilsCrossed, active: () => isActive('/admin/addons') },
      ],
    });
  }

  if (can('burger')) {
    items.push({
      title: 'Burger', icon: ChefHat,
      children: [
        { title: 'Koostisosad', url: '/admin/ingredients',   icon: ChefHat,       active: () => isActive('/admin/ingredients') },
        { title: 'Ülevaatus',   url: '/admin/burger-review', icon: ClipboardList, active: () => isActive('/admin/burger-review') },
      ],
    });
  }

  if (can('users')) {
    items.push({
      title: 'Kliendid', icon: Users,
      children: [
        { title: 'Kasutajad',  url: '/admin/users',   icon: Users, active: () => isActive('/admin/users') },
        { title: 'Arvustused', url: '/admin/reviews', icon: Star,  active: () => isActive('/admin/reviews') },
      ],
    });
  }

  if (can('announcements')) {
    items.push({ title: 'Teadaanded', url: '/admin/announcements', icon: Megaphone, active: () => isActive('/admin/announcements') });
  }

  return items;
});

const NAV_STORAGE_KEY = 'admin_nav_open_groups';

const getInitialOpenGroups = (): Record<string, boolean> => {
  try {
    const stored = sessionStorage.getItem(NAV_STORAGE_KEY);
    if (stored) {
      const parsed = JSON.parse(stored);
      navigation.value.filter(item => item.children).forEach(item => {
        if (item.children!.some(c => c.active())) parsed[item.title] = true;
      });
      return parsed;
    }
  } catch {}
  return Object.fromEntries(
    navigation.value
      .filter(item => item.children)
      .map(item => [item.title, item.children!.some(c => c.active())])
  );
};

const openGroups = ref<Record<string, boolean>>(getInitialOpenGroups());

const toggleGroup = (title: string) => {
  openGroups.value[title] = !openGroups.value[title];
  try { sessionStorage.setItem(NAV_STORAGE_KEY, JSON.stringify(openGroups.value)); } catch {}
};

const logoutModal = ref(false);
const logout = () => { logoutModal.value = false; router.post('/logout'); };
const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value; };
const closeSidebar  = () => { sidebarOpen.value = false; };
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100">

    <!-- Logout confirm modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="logoutModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="logoutModal = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Logi välja</h3>
              <p class="text-xs text-zinc-400">Kas soovite välja logida?</p>
              <div class="flex gap-2 mt-5">
                <button @click="logoutModal = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="logout" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Logi välja</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Mobile Header -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-zinc-900 border-b border-[#27272a] px-4 py-3">
      <div class="flex items-center justify-between">
        <Link href="/admin/dashboard" class="flex items-center gap-2.5">
          <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <span class="text-white text-xs font-bold">BP</span>
          </div>
          <span class="text-sm font-semibold text-zinc-100">Admin</span>
        </Link>
        <button @click="toggleSidebar" class="p-2 hover:bg-[#27272a] rounded-md transition">
          <MenuIcon :size="20" class="text-zinc-400" />
        </button>
      </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div v-if="sidebarOpen" @click="closeSidebar" class="lg:hidden fixed inset-0 bg-black/50 z-40" />

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 bottom-0 w-56 bg-zinc-900 border-r border-[#27272a] z-50 transition-transform duration-300',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      ]"
      style="display: grid; grid-template-rows: auto 1fr auto;"
    >
      <!-- Logo -->
      <div class="px-4 py-5 border-b border-[#27272a]">
        <Link href="/admin/dashboard" class="flex items-center gap-2.5" @click="closeSidebar">
          <div class="w-9 h-9 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <span class="text-white text-sm font-bold">BP</span>
          </div>
          <div>
            <p class="text-sm font-bold text-zinc-100">Burger Primo</p>
            <p class="text-[10px] text-zinc-500">Admin</p>
          </div>
        </Link>
        <button @click="closeSidebar" class="lg:hidden absolute top-5 right-4 p-1.5 hover:bg-[#27272a] rounded-md transition">
          <X :size="16" class="text-zinc-400" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="overflow-y-auto p-3 space-y-0.5" style="-webkit-overflow-scrolling: touch;">
        <template v-for="item in navigation" :key="item.title">

          <!-- Single item (no children) -->
          <Link
            v-if="!item.children"
            :href="item.url!"
            @click="closeSidebar"
            :class="[
              'flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium transition-all',
              item.active?.()
                ? 'bg-orange-600/15 text-orange-400'
                : 'text-zinc-400 hover:bg-[#27272a] hover:text-zinc-100',
            ]"
          >
            <component :is="item.icon" :size="15" />
            {{ item.title }}
          </Link>

          <!-- Group item (with children) -->
          <div v-else>
            <button
              @click="toggleGroup(item.title)"
              :class="[
                'w-full flex items-center justify-between gap-2.5 px-3 py-2 rounded-md text-xs font-medium transition-all',
                item.children.some(c => c.active())
                  ? 'text-orange-400'
                  : 'text-zinc-400 hover:bg-[#27272a] hover:text-zinc-100',
              ]"
            >
              <span class="flex items-center gap-2.5">
                <component :is="item.icon" :size="15" />
                {{ item.title }}
              </span>
              <ChevronDown
                :size="13"
                :class="['transition-transform duration-200', openGroups[item.title] ? 'rotate-180' : '']"
              />
            </button>

            <div v-show="openGroups[item.title]" class="mt-0.5 ml-3 pl-3 border-l border-[#3f3f46] space-y-0.5">
              <Link
                v-for="child in item.children"
                :key="child.url"
                :href="child.url"
                @click="closeSidebar"
                :class="[
                  'flex items-center gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-all',
                  child.active()
                    ? 'bg-orange-600/15 text-orange-400'
                    : 'text-zinc-500 hover:bg-[#27272a] hover:text-zinc-100',
                ]"
              >
                <component :is="child.icon" :size="13" />
                {{ child.title }}
              </Link>
            </div>
          </div>

        </template>
      </nav>

      <!-- User Section -->
      <div class="p-3 border-t border-[#27272a] space-y-1.5">
        <div class="flex items-center gap-2.5 px-3 py-2 bg-[#09090b] rounded-md">
          <div class="w-7 h-7 bg-orange-600/20 rounded-full flex items-center justify-center flex-shrink-0">
            <User :size="13" class="text-orange-400" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-zinc-100 truncate">{{ user?.name || 'Admin' }}</p>
            <p class="text-[10px] text-zinc-500 truncate">
              <span
                :class="[
                  'inline-block mr-1 px-1 rounded text-[9px] font-semibold',
                  isSuperAdmin ? 'bg-orange-600/20 text-orange-400' : 'bg-zinc-700 text-zinc-400',
                ]"
              >{{ isSuperAdmin ? 'Super Admin' : 'Subadmin' }}</span>
            </p>
          </div>
        </div>
        <Link
          href="/"
          class="flex items-center gap-2 px-3 py-2 text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] rounded-md text-xs font-medium transition-colors"
        >
          <Home :size="13" />
          Avalehele
        </Link>
        <button
          @click="logoutModal = true"
          class="w-full flex items-center gap-2 px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-md text-xs font-medium transition-colors"
        >
          <LogOut :size="13" />
          Logi välja
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-56">
      <header class="bg-zinc-900 border-b border-[#27272a] px-4 lg:px-6 py-4 mt-14 lg:mt-0">
        <slot name="header" />
      </header>

      <main class="p-4 lg:p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
aside nav::-webkit-scrollbar { width: 4px; }
aside nav::-webkit-scrollbar-track { background: transparent; }
aside nav::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 2px; }
aside nav::-webkit-scrollbar-thumb:hover { background: #52525b; }
</style>
