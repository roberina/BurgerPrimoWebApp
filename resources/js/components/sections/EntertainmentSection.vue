<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useScrollAnimation } from '@/composables/useScrollAnimation'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

const { elRef: badgeRef }   = useScrollAnimation('scale-up',   { delay: 0   })
const { elRef: headingRef } = useScrollAnimation('fade-up',    { delay: 80  })
const { elRef: subRef }     = useScrollAnimation('fade-up',    { delay: 160 })
const { elRef: imgRef }     = useScrollAnimation('fade-left',  { delay: 0,   threshold: 0.15 })
const { elRef: textRef }    = useScrollAnimation('fade-right', { delay: 150, threshold: 0.15 })
const { elRef: priceRef }   = useScrollAnimation('scale-up',   { delay: 400 })

const currentSlide = ref(0)
const isAnimating  = ref(false)
const images = ['/img/pool25.jpg', '/img/pool15.jpg', '/img/pool35.jpg']

function goTo(i: number) {
  if (isAnimating.value || i === currentSlide.value) return
  isAnimating.value = true
  currentSlide.value = i
  setTimeout(() => { isAnimating.value = false }, 550)
}
const next = () => goTo((currentSlide.value + 1) % images.length)
const prev = () => goTo((currentSlide.value - 1 + images.length) % images.length)

let touchX = 0
const onTouchStart = (e: TouchEvent) => { touchX = e.touches[0].clientX }
const onTouchEnd   = (e: TouchEvent) => {
  const d = touchX - e.changedTouches[0].clientX
  if (Math.abs(d) > 40) d > 0 ? next() : prev()
}

const featureListRef = ref<HTMLElement | null>(null)
let timer: ReturnType<typeof setInterval>

const features = computed(() => [t('ent.feat.1'), t('ent.feat.2')])

onMounted(() => {
  timer = setInterval(next, 4500)
  if (featureListRef.value) {
    const items = featureListRef.value.querySelectorAll<HTMLElement>('li')
    items.forEach((el) => {
      el.style.opacity = '0'
      el.style.transform = 'translateX(20px)'
      el.style.transition = 'all 0.5s cubic-bezier(0.22,1,0.36,1)'
    })
    const obs = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        items.forEach((el, i) => {
          window.setTimeout(() => { el.style.opacity = '1'; el.style.transform = 'none' }, 300 + i * 110)
        })
        obs.disconnect()
      }
    }, { threshold: 0.2 })
    obs.observe(featureListRef.value)
  }
})
onUnmounted(() => clearInterval(timer))
</script>

<template>
  <section id="entertainment" class="section-py relative z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="glass px-6 py-12 md:px-12 md:py-14">

        <div class="text-center mb-12 space-y-4">
          <div :ref="(el) => badgeRef = el as any" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/12 bg-white/6 text-gray-300 text-xs font-bold uppercase tracking-[0.22em]">
            {{ t('ent.badge') }}
          </div>
          <h2 :ref="(el) => headingRef = el as any" class="text-3xl md:text-5xl font-bold text-white">
            {{ t('ent.heading') }} <span class="text-[#D2691E]">{{ t('ent.heading.accent') }}</span>
          </h2>
          <p :ref="(el) => subRef = el as any" class="text-gray-400 text-base max-w-xl mx-auto">
            {{ t('ent.sub') }}
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-10 lg:gap-16 items-center">

          <div
            :ref="(el) => imgRef = el as any"
            class="relative rounded-2xl overflow-hidden aspect-[4/3] bg-black/40 border border-white/8 shadow-2xl shadow-black/50"
          >
            <div class="relative w-full h-full" @touchstart="onTouchStart" @touchend="onTouchEnd">
              <Transition v-for="(img, i) in images" :key="i" name="slide-fade">
                <img v-show="currentSlide === i" :src="img" :alt="`Pool table ${i + 1}`" class="absolute inset-0 w-full h-full object-cover" />
              </Transition>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent pointer-events-none z-10" />
            <button @click="prev" aria-label="Eelmine" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/60 backdrop-blur-sm text-white flex items-center justify-center hover:bg-[#D2691E] transition-all duration-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button @click="next" aria-label="Järgmine" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/60 backdrop-blur-sm text-white flex items-center justify-center hover:bg-[#D2691E] transition-all duration-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </button>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
              <button v-for="(_, i) in images" :key="i" @click="goTo(i)" :aria-label="`Slaid ${i + 1}`" :aria-current="i === currentSlide ? 'true' : undefined" class="rounded-full transition-all duration-300" :class="i === currentSlide ? 'w-6 h-2 bg-[#D2691E]' : 'w-2 h-2 bg-white/35 hover:bg-white/60'" />
            </div>
          </div>

          <div :ref="(el) => textRef = el as any" class="space-y-6">
            <div>
              <h3 class="text-2xl md:text-3xl font-bold text-[#F5DEB3] mb-3">{{ t('ent.table.title') }}</h3>
              <p class="text-gray-400 text-sm leading-relaxed">
                {{ t('ent.table.desc') }}
              </p>
            </div>

            <ul ref="featureListRef" class="space-y-3">
              <li v-for="(feat, i) in features" :key="i" class="flex items-center gap-3 text-gray-300 text-sm">
                <span class="w-5 h-5 rounded-full bg-[#D2691E]/15 flex items-center justify-center flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-[#D2691E]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </span>
                {{ feat }}
              </li>
            </ul>

            <div :ref="(el) => priceRef = el as any" class="glass-card p-5 hover:border-[#D2691E]/25 transition-colors duration-300 relative overflow-hidden">
              <div class="absolute inset-0 opacity-0 hover:opacity-100 transition-opacity duration-500 pointer-events-none shimmer" />
              <div class="flex items-center gap-3 mb-4">
                <div>
                  <p class="text-[#F5DEB3] font-bold text-sm">{{ t('ent.price.title') }}</p>
                </div>
              </div>
              <div class="flex items-center justify-between py-3 border-t border-white/8">
                <div>
                  <p class="text-white font-semibold text-sm">{{ t('ent.price.game') }}</p>
                </div>
                <div class="text-right">
                  <span class="text-3xl font-black text-[#D2691E]">2€</span>
                  <p class="text-gray-600 text-xs">{{ t('ent.price.per') }}</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: opacity 0.55s ease, transform 0.55s cubic-bezier(0.22,1,0.36,1);
  position: absolute; inset: 0;
}
.slide-fade-enter-from { opacity: 0; transform: scale(1.04); }
.slide-fade-leave-to   { opacity: 0; transform: scale(0.97); }
.slide-fade-enter-to, .slide-fade-leave-from { opacity: 1; transform: scale(1); }
</style>