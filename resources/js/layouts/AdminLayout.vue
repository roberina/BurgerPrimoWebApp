<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  Menu,
  Package,
  FolderOpen,
  LogOut,
  User,
  X,
  Star,
  ExternalLink,
  Megaphone,
  UtensilsCrossed,
} from 'lucide-vue-next';
import ToastContainer from '@/components/ToastContainer.vue';

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
    title: 'Lisandid',
    url: '/admin/addons',
    icon: UtensilsCrossed,
    isActive: currentPath.value.startsWith('/admin/addons'),
  },
];

const logout = () => {
  if (confirm('Kas soovite välja logida?')) {
    window.location.href = '/logout';
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
    <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-[#0B0B0B] border-b border-[#121212] backdrop-blur-lg shadow-lg shadow-black/50 px-4 py-3">
      <div class="flex items-center justify-between h-10">
        <Link href="/admin/dashboard" class="flex items-center gap-2">
          <span class="text-lg font-bold bg-gradient-to-r from-[#D2691E] to-[#E07A2E] bg-clip-text text-transparent">
            Burger Primo
          </span>
          <span class="text-xs text-gray-500 font-medium">/ Admin</span>
        </Link>
        <button @click="toggleSidebar" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-[#121212] transition-all duration-200">
          <Menu :size="22" />
        </button>
      </div>
    </div>

    <!-- Mobile Overlay -->
    <div
      v-if="sidebarOpen"
      @click="closeSidebar"
      class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
    />

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 bottom-0 w-64 bg-[#0B0B0B] border-r border-[#121212] z-50 flex flex-col transition-transform duration-300',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="px-6 py-5 border-b border-[#121212] flex items-center justify-between">
        <Link href="/admin/dashboard" class="flex items-center gap-2 group" @click="closeSidebar">
          <span class="text-xl font-bold bg-gradient-to-r from-[#D2691E] to-[#E07A2E] bg-clip-text text-transparent">
            Burger Primo
          </span>
          <span class="text-xs text-gray-500 font-medium mt-0.5">/ Admin</span>
        </Link>
        <button @click="closeSidebar" class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:text-white hover:bg-[#121212] transition-all">
          <X :size="18" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <Link
          v-for="item in navigation"
          :key="item.url"
          :href="item.url"
          @click="closeSidebar"
          :class="[
            'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 relative',
            item.isActive
              ? 'text-white bg-[#D2691E]/10'
              : 'text-gray-400 hover:text-white hover:bg-[#121212]'
          ]"
        >
          <component :is="item.icon" :size="18" />
          <span>{{ item.title }}</span>
          <!-- Active underline accent matching navbar -->
          <div
            v-if="item.isActive"
            class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-0.5 bg-gradient-to-r from-transparent via-[#D2691E] to-transparent"
          />
        </Link>
      </nav>

      <!-- User + Actions -->
      <div class="px-3 py-4 border-t border-[#121212] space-y-1">
        <!-- User info -->
        <div class="flex items-center gap-3 px-4 py-2.5 mb-2">
          <div class="w-8 h-8 bg-gradient-to-br from-[#D2691E] to-[#B8571A] rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-lg flex-shrink-0">
            {{ (user?.name || 'A').charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white truncate">{{ user?.name || 'Admin' }}</p>
            <p class="text-xs text-gray-500 truncate">{{ user?.email || '' }}</p>
          </div>
        </div>

        <!-- View site -->
        <Link
          href="/"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#121212] transition-all duration-200"
        >
          <ExternalLink :size="18" />
          Vaata lehte
        </Link>

        <!-- Logout -->
        <button
          @click="logout"
          class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200"
        >
          <LogOut :size="18" />
          Logi välja
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Top bar -->
      <header class="bg-[#0B0B0B] border-b border-[#121212] px-6 lg:px-8 py-4 mt-16 lg:mt-0 shadow-sm shadow-black/30">
        <slot name="header" />
      </header>

      <!-- Page Content -->
      <main class="p-4 lg:p-8">
        <slot />
      </main>
    </div>
  </div>
  <ToastContainer />
</template>

<style scoped>
aside nav::-webkit-scrollbar {
  width: 4px;
}
aside nav::-webkit-scrollbar-track {
  background: transparent;
}
aside nav::-webkit-scrollbar-thumb {
  background: #1f1f1f;
  border-radius: 4px;
}
aside nav::-webkit-scrollbar-thumb:hover {
  background: #2a2a2a;
}
</style>