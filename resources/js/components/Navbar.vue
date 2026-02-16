<template>
  <header class="bg-[#0B0B0B] border-b border-[#121212] backdrop-blur-lg sticky top-0 z-50 shadow-lg shadow-black/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <Link 
          href="/" 
          class="flex items-center gap-3 group"
        >
          <span class="text-xl font-bold bg-gradient-to-r from-[#D2691E] to-[#E07A2E] bg-clip-text text-transparent">
            Burger Primo
          </span>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-1">
          <Link 
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative',
              isActive(item.href)
                ? 'text-white bg-[#D2691E]/10'
                : 'text-gray-400 hover:text-white hover:bg-[#121212]'
            ]"
          >
            <span class="relative z-10">{{ item.label }}</span>
            <div 
              v-if="isActive(item.href)"
              class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-0.5 bg-gradient-to-r from-transparent via-[#D2691E] to-transparent"
            />
          </Link>

          <!-- Cart with Badge -->
          <Link 
            href="/cart"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative flex items-center gap-2',
              isActive('/cart')
                ? 'text-white bg-[#D2691E]/10'
                : 'text-gray-400 hover:text-white hover:bg-[#121212]'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Korv</span>
            <span 
              v-if="actualCartCount > 0"
              class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-[#D2691E] to-[#B8571A] text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg shadow-[#D2691E]/50 animate-pulse"
            >
              {{ actualCartCount }}
            </span>
          </Link>
        </nav>

        <!-- Right Side: User Menu -->
        <div class="hidden lg:flex items-center gap-3">
          <!-- User Authenticated -->
          <template v-if="user">
            <!-- Admin Badge -->
            <Link 
              v-if="user.is_admin"
              href="/admin/dashboard" 
              class="px-3 py-1.5 rounded-lg bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-xs font-semibold hover:bg-yellow-500/20 transition-all duration-200 flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Admin
            </Link>

            <!-- User Dropdown -->
            <div class="relative" v-click-outside="closeDropdown">
              <button 
                @click="dropdownOpen = !dropdownOpen"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-[#121212] transition-all duration-200"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-[#D2691E] to-[#B8571A] rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-lg">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-sm font-medium text-gray-300">{{ user.name }}</span>
                <svg 
                  xmlns="http://www.w3.org/2000/svg" 
                  class="h-4 w-4 text-gray-400 transition-transform duration-200" 
                  :class="{ 'rotate-180': dropdownOpen }"
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <div 
                  v-if="dropdownOpen"
                  class="absolute right-0 mt-2 w-56 bg-[#121212] border border-[#0B0B0B] rounded-xl shadow-2xl py-2 overflow-hidden"
                >
                  <Link 
                    href="/profile" 
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-[#0B0B0B] hover:text-white transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profiil
                  </Link>
                  <Link 
                    href="/orders" 
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-[#0B0B0B] hover:text-white transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Tellimused
                  </Link>
                  <hr class="my-2 border-[#0B0B0B]" />
                  <Link 
                    href="/logout" 
                    method="post" 
                    as="button"
                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logi välja
                  </Link>
                </div>
              </Transition>
            </div>
          </template>

          <!-- User Not Authenticated -->
          <template v-else>
            <Link 
              href="/login" 
              class="px-4 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-[#121212] transition-all duration-200"
            >
              Logi sisse
            </Link>
            <Link 
              href="/register" 
              class="px-6 py-2 rounded-lg text-sm font-semibold bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white hover:from-[#E07A2E] hover:to-[#D2691E] shadow-lg shadow-[#D2691E]/25 hover:shadow-[#D2691E]/40 transition-all duration-200"
            >
              Registreeru
            </Link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button 
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-[#121212] transition-all duration-200"
        >
          <svg 
            v-if="!mobileMenuOpen" 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-6 w-6" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg 
            v-else 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-6 w-6" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 -translate-y-2"
      enter-to-class="transform opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="transform opacity-100 translate-y-0"
      leave-to-class="transform opacity-0 -translate-y-2"
    >
      <div 
        v-if="mobileMenuOpen"
        class="lg:hidden border-t border-[#121212] bg-[#0B0B0B]/95 backdrop-blur-lg"
      >
        <div class="px-4 py-6 space-y-3">
          <!-- Mobile Nav Items -->
          <Link 
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            @click="mobileMenuOpen = false"
            :class="[
              'block px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200',
              isActive(item.href)
                ? 'text-white bg-[#D2691E]/10 border-l-4 border-[#D2691E]'
                : 'text-gray-400 hover:text-white hover:bg-[#121212]'
            ]"
          >
            {{ item.label }}
          </Link>

          <!-- Mobile Cart -->
          <Link 
            href="/cart"
            @click="mobileMenuOpen = false"
            :class="[
              'flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200',
              isActive('/cart')
                ? 'text-white bg-[#D2691E]/10 border-l-4 border-[#D2691E]'
                : 'text-gray-400 hover:text-white hover:bg-[#121212]'
            ]"
          >
            <span>Korv</span>
            <span 
              v-if="actualCartCount > 0"
              class="px-2 py-1 bg-[#D2691E] text-white text-xs font-bold rounded-full"
            >
              {{ actualCartCount }}
            </span>
          </Link>

          <!-- Mobile User Menu -->
          <div class="pt-4 mt-4 border-t border-[#121212]">
            <template v-if="user">
              <div class="px-4 py-2 text-sm text-gray-400 mb-2">
                Tere, {{ user.name }}
              </div>
              
              <Link 
                v-if="user.is_admin"
                href="/admin/dashboard" 
                @click="mobileMenuOpen = false"
                class="block px-4 py-3 rounded-lg text-sm font-medium text-yellow-400 bg-yellow-500/10 hover:bg-yellow-500/20 transition-all duration-200 mb-2"
              >
                🔧 Admin Dashboard
              </Link>
              
              <Link 
                href="/profile" 
                @click="mobileMenuOpen = false"
                class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#121212] transition-all duration-200"
              >
                👤 Profiil
              </Link>
              
              <Link 
                href="/logout" 
                method="post" 
                as="button"
                @click="mobileMenuOpen = false"
                class="block w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-red-400 hover:bg-red-500/10 transition-all duration-200"
              >
                🚪 Logi välja
              </Link>
            </template>

            <template v-else>
              <Link 
                href="/login" 
                @click="mobileMenuOpen = false"
                class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-[#121212] transition-all duration-200 mb-2"
              >
                Logi sisse
              </Link>
              <Link 
                href="/register" 
                @click="mobileMenuOpen = false"
                class="block px-4 py-3 rounded-lg text-sm font-semibold text-center bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white hover:from-[#E07A2E] hover:to-[#D2691E] transition-all duration-200"
              >
                Registreeru
              </Link>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
}

interface Props {
  cartCount?: number;
}

const props = withDefaults(defineProps<Props>(), {
  cartCount: 0,
});

const page = usePage();
const user = computed(() => page.props.auth?.user as User | null);

// Get cart count from either prop or Inertia shared props
const actualCartCount = computed(() => {
  // First try the prop (for Cart page which passes it explicitly)
  if (props.cartCount > 0) {
    return props.cartCount;
  }
  
  // Then try to get from shared Inertia props (if available from backend)
  const sharedCart = page.props.cartCount as number | undefined;
  if (sharedCart !== undefined) {
    return sharedCart;
  }
  
  return 0;
});

const dropdownOpen = ref(false);
const mobileMenuOpen = ref(false);

const navItems = [
  { label: 'Avaleht', href: '/' },
  { label: 'Menüü', href: '/menu' },
  { label: 'Ehita burger', href: '/burger-builder' },
  { label: 'Tellimused', href: '/orders' },
];

const isActive = (path: string): boolean => {
  if (path === '/') {
    return page.url === '/';
  }
  return page.url.startsWith(path);
};

const closeDropdown = () => {
  dropdownOpen.value = false;
};

// Click outside directive
const vClickOutside = {
  mounted(el: HTMLElement, binding: any) {
    const clickOutsideEvent = (event: Event) => {
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value();
      }
    };
    (el as any).clickOutsideEvent = clickOutsideEvent;
    document.addEventListener('click', clickOutsideEvent);
  },
  unmounted(el: HTMLElement) {
    document.removeEventListener('click', (el as any).clickOutsideEvent);
  },
};
</script>

<style scoped>
/* Smooth animations */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform;
}
</style>