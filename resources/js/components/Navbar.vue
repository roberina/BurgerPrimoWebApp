<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import ToastContainer from '@/components/ToastContainer.vue'

interface User {
  id: number
  name: string
  email: string
  is_admin: boolean
}

const page = usePage()
const user = computed(() => page.props.auth?.user as User | null)

const dropdownOpen   = ref(false)
const mobileMenuOpen = ref(false)
const scrolled       = ref(false)
const mounted        = ref(false)

// Which anchor section is currently scrolled into view
const activeAnchor = ref<string | null>(null)

const isHomePage = computed(() => page.url === '/')

const navItems = [
  { label: 'Avaleht',      href: '/',               anchor: null,            isMenuPage: false },
  { label: 'Populaarsed',  href: '/#popular',        anchor: 'popular',       isMenuPage: false },
  { label: 'Meelelahutus', href: '/#entertainment',  anchor: 'entertainment', isMenuPage: false },
  { label: 'Kontakt',      href: '/#contact',        anchor: 'contact',       isMenuPage: false },
  { label: 'Menüü',        href: '/menu',            anchor: null,            isMenuPage: true  },
]

// Returns true if this nav item should be highlighted
function isActive(item: typeof navItems[0]): boolean {
  if (item.isMenuPage) return page.url.startsWith('/menu')

  if (!isHomePage.value) return false

  // Anchor items: highlight based on scroll position
  if (item.anchor) return activeAnchor.value === item.anchor

  // "Avaleht" — active when no anchor section is in view
  if (item.href === '/') return activeAnchor.value === null

  return false
}

function handleNavClick(item: typeof navItems[0], e: MouseEvent) {
  if (item.anchor && isHomePage.value) {
    e.preventDefault()
    document.getElementById(item.anchor)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    mobileMenuOpen.value = false
  } else {
    mobileMenuOpen.value = false
  }
}

// Track which section is in view using IntersectionObserver
let sectionObserver: IntersectionObserver | null = null

function setupSectionObserver() {
  if (!isHomePage.value) return

  sectionObserver?.disconnect()

  const anchors = navItems.map(i => i.anchor).filter(Boolean) as string[]
  const elements = anchors.map(id => document.getElementById(id)).filter(Boolean) as HTMLElement[]

  if (!elements.length) return

  // Use a map to track which sections are visible and pick the topmost one
  const visibleSections = new Map<string, number>()

  sectionObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const id = entry.target.id
        if (entry.isIntersecting) {
          visibleSections.set(id, entry.boundingClientRect.top)
        } else {
          visibleSections.delete(id)
        }
      })

      if (visibleSections.size === 0) {
        // Nothing in view — check if we're near the top
        activeAnchor.value = window.scrollY < 100 ? null : activeAnchor.value
      } else {
        // Pick the section closest to the top of the viewport
        let topmost: string | null = null
        let topmostY = Infinity
        visibleSections.forEach((y, id) => {
          if (y < topmostY) { topmostY = y; topmost = id }
        })
        activeAnchor.value = topmost
      }
    },
    {
      // Trigger when section crosses the upper 30% of viewport
      rootMargin: '-5% 0px -65% 0px',
      threshold: 0,
    }
  )

  elements.forEach(el => sectionObserver!.observe(el))
}

function onScroll() {
  scrolled.value = window.scrollY > 20
  // When scrolled back to very top, clear active anchor
  if (window.scrollY < 80) activeAnchor.value = null
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
  setTimeout(() => {
    mounted.value = true
    setupSectionObserver()
  }, 50)
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  sectionObserver?.disconnect()
})

const closeDropdown = () => { dropdownOpen.value = false }

const vClickOutside = {
  mounted(el: HTMLElement, binding: any) {
    const handler = (e: Event) => {
      if (!(el === e.target || el.contains(e.target as Node))) binding.value()
    }
    ;(el as any).__co = handler
    document.addEventListener('click', handler)
  },
  unmounted(el: HTMLElement) {
    document.removeEventListener('click', (el as any).__co)
  },
}
</script>

<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 w-screen max-w-full"
    :class="[
      scrolled ? 'navbar-scrolled' : 'bg-transparent',
      mounted ? 'translate-y-0 opacity-100' : '-translate-y-full opacity-0'
    ]"
    style="transition: transform 0.6s cubic-bezier(0.22,1,0.36,1), opacity 0.6s ease, background 0.4s ease, border 0.4s ease, box-shadow 0.4s ease;"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-hidden">
      <div class="relative flex items-center justify-between h-16 lg:h-20">

        <!-- Logo -->
        <Link href="/" class="group flex items-center gap-2.5 z-10">
          <img
            src="/img/Logo45.png"
            alt="Burger Primo"
            class="w-9 h-9 object-contain transition-all duration-400 group-hover:scale-110 group-hover:rotate-3"
          />
          <span class="text-base font-bold tracking-tight text-white hidden sm:block">
            Burger <span class="text-[#D2691E] transition-all duration-300 group-hover:text-[#E87E32]">Primo</span>
          </span>
        </Link>

        <!-- Desktop nav -->
        <nav class="hidden lg:flex items-center gap-0.5 absolute left-1/2 -translate-x-1/2">
          <template v-for="item in navItems" :key="item.href">

            <!-- Menu page — full Inertia navigation -->
            <Link
              v-if="item.isMenuPage"
              :href="item.href"
              class="relative px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 group"
              :class="isActive(item) ? 'text-white' : 'text-gray-500 hover:text-white'"
            >
              <span class="relative z-10">{{ item.label }}</span>
              <span
                class="absolute inset-0 rounded-xl transition-all duration-200"
                :class="isActive(item) ? 'bg-[#D2691E]/12' : 'bg-transparent group-hover:bg-white/5'"
              />
              <span
                v-if="isActive(item)"
                class="absolute bottom-1.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#D2691E]"
              />
            </Link>

            <!-- Anchor / home items -->
            <a
              v-else
              :href="item.href"
              @click="handleNavClick(item, $event)"
              class="relative px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 group cursor-pointer"
              :class="isActive(item) ? 'text-white' : 'text-gray-500 hover:text-white'"
            >
              <span class="relative z-10">{{ item.label }}</span>
              <span
                class="absolute inset-0 rounded-xl transition-all duration-200"
                :class="isActive(item) ? 'bg-[#D2691E]/12' : 'bg-transparent group-hover:bg-white/5'"
              />
              <!-- Active indicator dot -->
              <span
                v-if="isActive(item)"
                class="absolute bottom-1.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#D2691E]"
              />
            </a>

          </template>
        </nav>

        <!-- Right side -->
        <div class="hidden lg:flex items-center gap-2 z-10">
          <a
            href="https://wolt.com/en/est/kuressaare/restaurant/primo-burger"
            target="_blank" rel="noopener"
            class="btn-magnetic px-3 py-1.5 bg-[#00c2e0]/8 border border-[#00c2e0]/18 text-[#00c2e0] rounded-full text-xs font-bold hover:bg-[#00c2e0]/18 transition-all duration-200"
          >Wolt</a>
          <a
            href="https://food.bolt.eu/en-US/164/p/90859-primo-burger"
            target="_blank" rel="noopener"
            class="btn-magnetic px-3 py-1.5 bg-[#21c93d]/8 border border-[#21c93d]/18 text-[#21c93d] rounded-full text-xs font-bold hover:bg-[#21c93d]/18 transition-all duration-200"
          >Bolt Food</a>

          <div class="w-px h-5 bg-white/8 mx-1" />

          <template v-if="user">
            <Link
              v-if="user.is_admin"
              href="/admin/dashboard"
              class="btn-magnetic px-3 py-1.5 rounded-xl bg-yellow-500/8 border border-yellow-500/18 text-yellow-400 text-xs font-bold hover:bg-yellow-500/18 transition-all flex items-center gap-1.5"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Admin
            </Link>

            <div class="relative" v-click-outside="closeDropdown">
              <button
                @click="dropdownOpen = !dropdownOpen"
                class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl hover:bg-white/5 transition-all duration-200"
              >
                <div class="w-7 h-7 bg-gradient-to-br from-[#D2691E] to-[#8B3A00] rounded-full flex items-center justify-center text-white text-xs font-black shadow">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-sm font-medium text-gray-400">{{ user.name }}</span>
                <svg
                  class="h-3 w-3 text-gray-600 transition-transform duration-200"
                  :class="{ 'rotate-180': dropdownOpen }"
                  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <Transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 scale-95 translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-52 bg-[#0d0d0d] border border-white/8 rounded-2xl shadow-2xl shadow-black/70 py-1.5 overflow-hidden">
                  <div class="h-0.5 bg-gradient-to-r from-transparent via-[#D2691E]/40 to-transparent mb-1" />
                  <Link href="/settings/profile" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 hover:bg-white/5 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profiil
                  </Link>
                  <hr class="my-1 border-white/5" />
                  <Link href="/logout" method="post" as="button" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-500/8 hover:text-red-400 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logi välja
                  </Link>
                </div>
              </Transition>
            </div>
          </template>
        </div>

        <!-- Mobile hamburger -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="lg:hidden p-2 rounded-xl text-gray-500 hover:text-white hover:bg-white/6 transition-all z-10"
        >
          <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <Transition
      enter-active-class="transition ease-out duration-250"
      enter-from-class="opacity-0 -translate-y-3"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-3"
    >
      <div v-if="mobileMenuOpen" class="lg:hidden border-t border-white/6 bg-[#080808]/98 backdrop-blur-xl w-full overflow-hidden">
        <div class="px-4 py-5 space-y-1">
          <template v-for="item in navItems" :key="item.href">
            <Link
              v-if="item.isMenuPage"
              :href="item.href"
              @click="mobileMenuOpen = false"
              class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200"
              :class="isActive(item) ? 'text-white bg-[#D2691E]/10 border-l-2 border-[#D2691E]' : 'text-gray-500 hover:text-white hover:bg-white/5'"
            >
              {{ item.label }}
            </Link>
            <a
              v-else
              :href="item.href"
              @click="handleNavClick(item, $event)"
              class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer"
              :class="isActive(item) ? 'text-white bg-[#D2691E]/10 border-l-2 border-[#D2691E]' : 'text-gray-500 hover:text-white hover:bg-white/5'"
            >
              {{ item.label }}
            </a>
          </template>

          <div class="flex gap-2 px-4 pt-3 pb-1">
            <a href="https://wolt.com/en/est/kuressaare/restaurant/primo-burger" target="_blank" class="px-3 py-1.5 bg-[#00c2e0]/8 border border-[#00c2e0]/15 text-[#00c2e0] rounded-full text-xs font-bold">Wolt</a>
            <a href="https://food.bolt.eu/en-US/164/p/90859-primo-burger" target="_blank" class="px-3 py-1.5 bg-[#21c93d]/8 border border-[#21c93d]/15 text-[#21c93d] rounded-full text-xs font-bold">Bolt Food</a>
          </div>

          <div class="pt-3 mt-2 border-t border-white/6 space-y-1">
            <template v-if="user">
              <p class="px-4 py-1 text-[10px] text-gray-700 uppercase tracking-wider font-bold">Konto</p>
              <Link v-if="user.is_admin" href="/admin/dashboard" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-sm font-medium text-yellow-400 bg-yellow-500/6 hover:bg-yellow-500/12 transition-all">⚙️ Admin Dashboard</Link>
              <Link href="/settings/profile" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-sm text-gray-500 hover:text-white hover:bg-white/5 transition-all">👤 Profiil</Link>
              <Link href="/logout" method="post" as="button" @click="mobileMenuOpen = false" class="block w-full text-left px-4 py-3 rounded-xl text-sm text-red-500 hover:bg-red-500/8 transition-all">🚪 Logi välja</Link>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </header>

  <ToastContainer />
</template>