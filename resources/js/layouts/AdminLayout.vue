<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  ShoppingBag,
  ChefHat,
  Menu,
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
} from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const sidebarOpen = ref(false);
const currentPath = computed(() => window.location.pathname);

const navigation = [
  {
    title: 'Ülevaade',
    url: '/admin/dashboard',
    icon: LayoutDashboard,
    isActive: currentPath.value === '/admin/dashboard',
  },
  {
    title: 'Tellimused',
    url: '/admin/orders',
    icon: ShoppingBag,
    isActive: currentPath.value.startsWith('/admin/orders'),
  },
  {
    title: 'Burger Koostisosad',
    url: '/admin/ingredients',
    icon: ChefHat,
    isActive: currentPath.value.startsWith('/admin/ingredients'),
  },
  {
    title: 'Burger Ülevaatus',
    url: '/admin/burger-review',
    icon: ClipboardList,
    isActive: currentPath.value.startsWith('/admin/burger-review'),
  },
  {
    title: 'Menüü Kategooriad',
    url: '/admin/menu/categories',
    icon: FolderOpen,
    isActive: currentPath.value.startsWith('/admin/menu/categories'),
  },
  {
    title: 'Menüü Tooted',
    url: '/admin/menu/items',
    icon: Package,
    isActive: currentPath.value.startsWith('/admin/menu/items'),
  },
  {
    title: 'Menüü Lisandid',
    url: '/admin/addons',
    icon: UtensilsCrossed,
    isActive: currentPath.value.startsWith('/admin/addons'),
  },
  {
    title: 'Arvustused',
    url: '/admin/reviews',
    icon: Star,
    isActive: currentPath.value.startsWith('/admin/reviews'),
  },
  {
    title: 'Teadaanded',
    url: '/admin/announcements',
    icon: Megaphone,
    isActive: currentPath.value.startsWith('/admin/announcements'),
  },
  {
    title: 'Kasutajad',
    url: '/admin/users',
    icon: Users,
    isActive: currentPath.value.startsWith('/admin/users'),
  },
];

const logout = () => {
  if (confirm('Kas soovite välja logida?')) {
    router.post('/logout');
  }
};

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const closeSidebar = () => {
  sidebarOpen.value = false;
};
</script>

<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <!-- Mobile Header -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-[#111111] border-b border-gray-800 px-4 py-3">
      <div class="flex items-center justify-between">
        <Link href="/admin/dashboard" class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-[#D2691E] to-[#B8571A] rounded-lg flex items-center justify-center">
            <span class="text-xl font-bold">🍔</span>
          </div>
          <span class="text-lg font-bold">Admin</span>
        </Link>
        <button @click="toggleSidebar" class="p-2 hover:bg-gray-800 rounded-lg transition">
          <Menu :size="24" />
        </button>
      </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div
      v-if="sidebarOpen"
      @click="closeSidebar"
      class="lg:hidden fixed inset-0 bg-black/50 z-40"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 bottom-0 w-64 bg-[#111111] border-r border-gray-800 z-50 transition-transform duration-300',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
      style="display: grid; grid-template-rows: auto 1fr auto;"
    >
      <!-- Logo -->
      <div class="p-6 border-b border-gray-800">
        <Link href="/admin/dashboard" class="flex items-center gap-3" @click="closeSidebar">
          <div class="w-12 h-12 bg-gradient-to-br from-[#D2691E] to-[#B8571A] rounded-xl flex items-center justify-center">
            <span class="text-2xl">🍔</span>
          </div>
          <div>
            <h1 class="text-xl font-bold">Burger Primo</h1>
            <p class="text-xs text-gray-400">Admin</p>
          </div>
        </Link>
        <button
          @click="closeSidebar"
          class="lg:hidden absolute top-6 right-6 p-2 hover:bg-gray-800 rounded-lg transition"
        >
          <X :size="20" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="overflow-y-auto p-4 space-y-1" style="-webkit-overflow-scrolling: touch;">
        <Link
          v-for="item in navigation"
          :key="item.url"
          :href="item.url"
          @click="closeSidebar"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-lg transition-all',
            item.isActive
              ? 'bg-orange-600 text-white'
              : 'text-gray-400 hover:bg-gray-800 hover:text-white'
          ]"
        >
          <component :is="item.icon" :size="20" />
          <span class="font-semibold">{{ item.title }}</span>
        </Link>
      </nav>

      <!-- User Section -->
      <div class="p-4 border-t border-gray-800">
        <div class="flex items-center gap-3 mb-3 px-4 py-3 bg-[#0a0a0a] rounded-lg">
          <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center">
            <User :size="20" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white truncate">{{ user?.name || 'Admin' }}</p>
            <p class="text-xs text-gray-400 truncate">{{ user?.email || 'admin@burgerprimo.ee' }}</p>
          </div>
        </div>
        <a
          href="/"
          class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-800/50 hover:bg-gray-700/50 text-gray-300 hover:text-white rounded-lg font-semibold transition mb-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Avalehele
        </a>
        <button
          @click="logout"
          class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600/10 hover:bg-red-600/20 text-red-500 rounded-lg font-semibold transition"
        >
          <LogOut :size="18" />
          Logi välja
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Header -->
      <header class="bg-[#111111] border-b border-gray-800 px-4 lg:px-8 py-6 mt-16 lg:mt-0">
        <slot name="header" />
      </header>

      <!-- Page Content -->
      <main class="p-4 lg:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
aside nav::-webkit-scrollbar {
  width: 6px;
}

aside nav::-webkit-scrollbar-track {
  background: transparent;
}

aside nav::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 3px;
}

aside nav::-webkit-scrollbar-thumb:hover {
  background: #4b5563;
}
</style>