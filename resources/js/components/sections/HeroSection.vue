<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useCountUp } from '@/composables/useScrollAnimation'

const logoX = ref(0)
const logoY = ref(0)
const titleRef = ref<HTMLElement | null>(null)

const { elRef: statRatingRef, displayValue: statRating } = useCountUp(4.8, { decimals: 1, duration: 1400, delay: 300 })

function onMouseMove(e: MouseEvent) {
  const cx = window.innerWidth / 2
  const cy = window.innerHeight / 2
  logoX.value = (e.clientX - cx) * 0.014
  logoY.value = (e.clientY - cy) * 0.014
}

onMounted(() => {
  window.addEventListener('mousemove', onMouseMove, { passive: true })
  setTimeout(() => titleRef.value?.classList.add('words-visible'), 450)
})
onUnmounted(() => window.removeEventListener('mousemove', onMouseMove))

function scrollTo(id: string) {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <section class="relative flex items-center justify-center" style="min-height: 100svh; padding-top: 5rem; padding-bottom: 3rem;">

    <div class="relative z-10 w-full max-w-4xl mx-auto px-4 sm:px-6">
      <div class="px-2 py-10 md:px-14 md:py-12 flex flex-col items-center text-center">

        <!-- Logo parallax -->
        <div
          class="mb-5 transition-transform duration-75 ease-linear"
          :style="{ transform: `translate(${logoX}px, ${logoY}px)` }"
        >
          <img
            src="/img/Logo45.png"
            alt="Burger Primo"
            class="w-24 h-24 md:w-36 md:h-36 object-contain"
            style="filter: drop-shadow(0 16px 40px rgba(0,0,0,0.8));"
          />
        </div>

        <!-- Eyebrow -->
        <div class="hero-eyebrow inline-flex items-center gap-3 mb-4">
          <div class="h-px w-8 bg-gradient-to-r from-transparent to-[#D2691E]/60" />
          <span class="inline-flex items-center gap-1.5 text-[#D2691E] text-xs font-semibold uppercase tracking-[0.3em]">
            <span class="live-dot w-1.5 h-1.5 rounded-full bg-[#D2691E] inline-block" />
            Kuressaare · Saaremaa
          </span>
          <div class="h-px w-8 bg-gradient-to-l from-transparent to-[#D2691E]/60" />
        </div>

        <!-- Word-reveal title -->
        <h1
          ref="titleRef"
          class="word-reveal-parent text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-bold leading-[1.15] tracking-tight mb-4"
          aria-label="Kirega valmistatud Primo burgerid"
        >
          <span class="word text-white"><span class="word-inner">Kirega </span></span>
          <span class="word text-white"><span class="word-inner">valmistatud </span></span>
          <span class="word text-[#D2691E]"><span class="word-inner">Primo </span></span>
          <span class="word text-white"><span class="word-inner">burgerid</span></span>
        </h1>

        <!-- Subtitle -->
        <p class="hero-subtitle text-base md:text-lg text-gray-300 font-light max-w-lg leading-relaxed mb-7">
          Unikaalsed maitsed, kõrgekvaliteetsed koostisosad ja
          sõbralik õhkkond Kuressaare südames.
        </p>

        <!-- CTAs -->
        <div class="hero-cta flex flex-col sm:flex-row gap-3 mb-8">
          <Link
            href="/menu"
            class="btn-magnetic group inline-flex items-center gap-2.5 px-8 py-4 bg-[#D2691E] text-white font-bold rounded-2xl text-sm uppercase tracking-wider shadow-lg shadow-[#D2691E]/25"
          >
            Avasta Menüüd
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </Link>
          <button
            @click="scrollTo('popular')"
            class="btn-magnetic inline-flex items-center gap-2 px-8 py-4 bg-white/8 border border-white/15 text-white font-semibold rounded-2xl text-sm uppercase tracking-wider"
          >
            Populaarsed
          </button>
        </div>

      </div>
    </div>

    <!-- Scroll indicator -->
    <div class="hero-scroll absolute bottom-7 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/30 z-10">
      <span class="text-[9px] tracking-[0.35em] uppercase">Keri alla</span>
      <div class="relative w-px h-9 overflow-hidden bg-white/10 rounded-full">
        <div class="absolute top-0 left-0 w-full bg-[#D2691E] rounded-full" style="height:40%; animation: scroll-line 1.8s ease-in-out infinite;" />
      </div>
    </div>

  </section>
</template>

<style scoped>
@keyframes scroll-line {
  0%   { top: -40%; opacity: 1; }
  70%  { top: 100%;  opacity: 1; }
  71%  { top: -40%;  opacity: 0; }
  80%  { top: -40%;  opacity: 0.3; }
  100% { top: -40%;  opacity: 1; }
}
</style>