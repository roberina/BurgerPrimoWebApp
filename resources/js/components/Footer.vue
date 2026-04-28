<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useScrollAnimation, useStaggerAnimation } from '@/composables/useScrollAnimation'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

const currentYear = new Date().getFullYear()

const { elRef: brandRef }   = useScrollAnimation('fade-up',   { delay: 0   })
const { containerRef: linksRef } = useStaggerAnimation('fade-up', { staggerMs: 70, childSelector: '[data-stagger]' })

const openSections = ref({ links: false, contact: false, hours: false })
const toggle = (s: keyof typeof openSections.value) => { openSections.value[s] = !openSections.value[s] }

const navLinks = computed(() => [
  { label: t('nav.home'),          href: '/'               },
  { label: t('nav.menu'),          href: '/menu'            },
  { label: t('nav.entertainment'), href: '/#entertainment'  },
  { label: t('nav.contact'),       href: '/#contact'        },
])

const hours = computed(() => [
  { days: t('footer.hours.mon'), time: '11:00 – 22:00' },
  { days: t('footer.hours.fri'), time: '11:00 – 23:00' },
  { days: t('footer.hours.sun'), time: '12:00 – 21:00' },
])
</script>

<template>
  <footer class="relative bg-[#060606] border-t border-white/5 overflow-hidden">

    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#D2691E]/40 to-transparent" />

    <div class="relative max-w-7xl mx-auto px-6 pt-14 pb-8">

      <div
        :ref="(el) => linksRef = el as any"
        class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12"
      >

        <div data-stagger>
          <div
            :ref="(el) => brandRef = el as any"
            class="flex items-center gap-3 mb-5 group w-fit"
          >
            <img
              src="/img/Logo45.png"
              alt="Burger Primo"
              class="w-10 h-10 object-contain transition-transform duration-400 group-hover:scale-110 group-hover:rotate-6"
            />
            <span class="font-bold text-white text-lg">
              Burger <span class="text-[#D2691E]">Primo</span>
            </span>
          </div>

          <p class="text-gray-600 text-sm leading-relaxed mb-6">
            {{ t('footer.tagline') }}
          </p>

          <div class="flex gap-2">
            <a
              href="https://wolt.com/en/est/kuressaare/restaurant/primo-burger"
              target="_blank" rel="noopener"
              class="btn-magnetic px-3 py-1.5 bg-[#00c2e0]/8 border border-[#00c2e0]/15 text-[#00c2e0] rounded-full text-xs font-bold hover:bg-[#00c2e0]/18 transition-all duration-200"
            >Wolt</a>
            <a
              href="https://food.bolt.eu/en-US/164/p/90859-primo-burger"
              target="_blank" rel="noopener"
              class="btn-magnetic px-3 py-1.5 bg-[#21c93d]/8 border border-[#21c93d]/15 text-[#21c93d] rounded-full text-xs font-bold hover:bg-[#21c93d]/18 transition-all duration-200"
            >Bolt Food</a>
          </div>
        </div>

        <div data-stagger>
          <button
            class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none group"
            @click="toggle('links')"
          >
            <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-[0.18em]">{{ t('footer.links') }}</h4>
            <span class="md:hidden text-gray-400 mb-4 text-xs transition-transform duration-300" :class="openSections.links ? 'rotate-180' : ''">▾</span>
          </button>
          <ul
            class="space-y-2.5 overflow-hidden transition-all duration-300"
            :class="openSections.links ? 'max-h-48 opacity-100' : 'max-h-0 opacity-0 md:max-h-48 md:opacity-100'"
          >
            <li v-for="link in navLinks" :key="link.href">
              <Link
                :href="link.href"
                class="group inline-flex items-center gap-1.5 text-gray-600 hover:text-[#D2691E] text-sm transition-colors duration-200"
              >
                <span class="w-0 group-hover:w-2 h-px bg-[#D2691E] transition-all duration-300 rounded" />
                {{ link.label }}
              </Link>
            </li>
          </ul>
        </div>

        <div data-stagger>
          <button
            class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none"
            @click="toggle('contact')"
          >
            <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-[0.18em]">{{ t('footer.contact') }}</h4>
            <span class="md:hidden text-gray-400 mb-4 text-xs transition-transform duration-300" :class="openSections.contact ? 'rotate-180' : ''">▾</span>
          </button>
          <ul
            class="space-y-2.5 overflow-hidden transition-all duration-300"
            :class="openSections.contact ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0 md:max-h-40 md:opacity-100'"
          >
            <li>
              <a href="tel:+37257438483" class="text-gray-600 hover:text-[#D2691E] text-sm transition-colors underline-sweep w-fit block">+372 5743 8483</a>
            </li>
            <li>
              <a href="mailto:info@burgerprimo.ee" class="text-gray-600 hover:text-[#D2691E] text-sm transition-colors underline-sweep w-fit block">info@burgerprimo.ee</a>
            </li>
            <li class="text-gray-400 text-sm leading-relaxed pt-1">
              Kauba tn 5/2<br />Kuressaare, 93819
            </li>
          </ul>
        </div>

        <div data-stagger>
          <button
            class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none"
            @click="toggle('hours')"
          >
            <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-[0.18em]">{{ t('footer.hours') }}</h4>
            <span class="md:hidden text-gray-400 mb-4 text-xs transition-transform duration-300" :class="openSections.hours ? 'rotate-180' : ''">▾</span>
          </button>
          <ul
            class="space-y-2.5 overflow-hidden transition-all duration-300"
            :class="openSections.hours ? 'max-h-32 opacity-100' : 'max-h-0 opacity-0 md:max-h-32 md:opacity-100'"
          >
            <li
              v-for="item in hours"
              :key="item.days"
              class="flex justify-between items-center text-sm gap-4"
            >
              <span class="text-gray-400">{{ item.days }}</span>
              <span class="text-gray-500 font-medium">{{ item.time }}</span>
            </li>
          </ul>
        </div>

      </div>

      <div class="border-t border-white/5 pt-7 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-gray-400 text-xs">
          &copy; {{ currentYear }} Burger Primo. {{ t('footer.copy') }}
        </p>
      </div>

    </div>
  </footer>
</template>