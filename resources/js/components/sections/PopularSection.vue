<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useScrollAnimation, useStaggerAnimation } from '@/composables/useScrollAnimation'

interface MenuItem {
  id: number
  name: string
  description: string
  price: number
  original_price: number | null
  image: string | null
  image_url: string | null
  is_featured: boolean
  discount_percentage: number | null
  category: { id: number; name: string }
}

const props = defineProps<{ featuredItems?: MenuItem[] }>()

const { elRef: badgeRef }   = useScrollAnimation('scale-up',      { delay: 0   })
const { elRef: headingRef } = useScrollAnimation('fade-up',       { delay: 80  })
const { elRef: subRef }     = useScrollAnimation('fade-up',       { delay: 160 })
const { containerRef }      = useStaggerAnimation('fade-up-hard', { staggerMs: 130, childSelector: '[data-stagger]' })
const { elRef: ctaRef }     = useScrollAnimation('fade-up',       { delay: 0   })

const displayedItems = ref<MenuItem[]>([])

function shuffleArray<T>(arr: T[]): T[] {
  const a = [...arr]
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]]
  }
  return a
}

onMounted(() => {
  if (props.featuredItems?.length)
    displayedItems.value = shuffleArray(props.featuredItems).slice(0, 3)
})

function onCardMouseMove(e: MouseEvent) {
  const card = e.currentTarget as HTMLElement
  const rect = card.getBoundingClientRect()
  const x = ((e.clientX - rect.left) / rect.width  - 0.5) * 14
  const y = ((e.clientY - rect.top)  / rect.height - 0.5) * -14
  card.style.transform = `perspective(800px) rotateY(${x}deg) rotateX(${y}deg) translateY(-8px) scale(1.02)`
}
function onCardMouseLeave(e: MouseEvent) {
  (e.currentTarget as HTMLElement).style.transform = ''
}

const slide = ref(0)
const total = computed(() => displayedItems.value.length || 3)
const next  = () => { slide.value = (slide.value + 1) % total.value }
const prev  = () => { slide.value = (slide.value - 1 + total.value) % total.value }
let touchX = 0
const onTouchStart = (e: TouchEvent) => { touchX = e.touches[0].clientX }
const onTouchEnd   = (e: TouchEvent) => {
  const d = touchX - e.changedTouches[0].clientX
  if (Math.abs(d) > 40) d > 0 ? next() : prev()
}
</script>

<template>
  <section id="popular" class="section-py relative z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="glass px-6 py-12 md:px-12 md:py-14">

        <!-- Heading -->
        <div class="text-center mb-12 space-y-4">
          <div :ref="(el) => badgeRef = el as any" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-[#D2691E]/30 bg-[#D2691E]/10 text-[#D2691E] text-xs font-bold uppercase tracking-[0.22em]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 icon-hover-spin" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            Populaarsed Valikud
          </div>
          <h2 :ref="(el) => headingRef = el as any" class="text-3xl md:text-5xl font-bold text-white">
            Enim tellitud <span class="text-[#D2691E]">toidud</span>
          </h2>
          <p :ref="(el) => subRef = el as any" class="text-gray-400 text-base max-w-md mx-auto">
            Klientide poolt korduvalt valitud absoluutsed lemmikud
          </p>
        </div>

        <!-- Desktop grid -->
        <div :ref="(el) => containerRef = el as any" class="hidden md:grid md:grid-cols-3 gap-5 mb-10">
          <Link
            v-for="item in displayedItems"
            :key="item.id"
            data-stagger
            :href="`/menu?highlight=${item.id}`"
            class="group glass-card overflow-hidden cursor-pointer transition-all duration-300 hover:border-[#D2691E]/30"
            style="transition: transform 0.3s cubic-bezier(0.22,1,0.36,1), box-shadow 0.3s ease, border-color 0.25s ease;"
            @mousemove="onCardMouseMove"
            @mouseleave="onCardMouseLeave"
          >
            <div class="relative aspect-[4/3] overflow-hidden bg-black/30">
              <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="text-6xl font-black text-white/20">{{ item.name.charAt(0) }}</span>
              </div>
              <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/70 to-transparent" />
              <div v-if="item.discount_percentage" class="absolute top-3 right-3 px-2.5 py-1 bg-[#D2691E] text-white text-xs font-black rounded-xl shadow-lg overflow-hidden">
                <span class="relative z-10">-{{ item.discount_percentage }}%</span>
                <div class="absolute inset-0 shimmer" />
              </div>
              <div class="absolute bottom-3 left-3 px-2 py-0.5 bg-black/60 backdrop-blur-sm text-gray-300 text-[10px] font-medium rounded-md uppercase tracking-wider">{{ item.category?.name }}</div>
            </div>
            <div class="p-5">
              <h3 class="text-base font-bold text-white mb-1.5 group-hover:text-[#F5DEB3] transition-colors duration-300">{{ item.name }}</h3>
              <p class="text-gray-500 text-xs line-clamp-2 mb-4 leading-relaxed">{{ item.description }}</p>
              <div class="flex items-center justify-between">
                <div class="flex items-baseline gap-2">
                  <span class="text-xl font-black text-[#D2691E]">€{{ Number(item.price).toFixed(2) }}</span>
                  <span v-if="item.original_price && Number(item.original_price) > Number(item.price)" class="text-xs text-gray-600 line-through">€{{ Number(item.original_price).toFixed(2) }}</span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center transition-all duration-300 group-hover:bg-[#D2691E] group-hover:border-[#D2691E] group-hover:scale-110">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </div>
            </div>
          </Link>

          <template v-if="displayedItems.length === 0">
            <div v-for="i in 3" :key="`ph-${i}`" class="glass-card overflow-hidden animate-pulse">
              <div class="aspect-[4/3] bg-white/5" />
              <div class="p-5 space-y-3">
                <div class="h-4 bg-white/5 rounded w-3/4" />
                <div class="h-3 bg-white/4 rounded w-full" />
                <div class="h-6 bg-white/5 rounded w-1/3" />
              </div>
            </div>
          </template>
        </div>

        <!-- Mobile slider -->
        <div class="md:hidden mb-10 relative px-1">
          <div class="overflow-hidden rounded-2xl" @touchstart="onTouchStart" @touchend="onTouchEnd">
            <div class="flex transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${slide * 100}%)` }">
              <Link v-for="item in displayedItems" :key="item.id" :href="`/menu?highlight=${item.id}`" class="w-full flex-shrink-0">
                <div class="glass-card overflow-hidden">
                  <div class="aspect-[4/3] overflow-hidden relative bg-black/30">
                    <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover" />
                    <div v-if="item.discount_percentage" class="absolute top-3 right-3 px-2.5 py-1 bg-[#D2691E] text-white text-xs font-black rounded-xl">-{{ item.discount_percentage }}%</div>
                  </div>
                  <div class="p-5">
                    <h3 class="text-base font-bold text-white mb-1.5">{{ item.name }}</h3>
                    <p class="text-gray-500 text-xs line-clamp-2 mb-4">{{ item.description }}</p>
                    <span class="text-xl font-black text-[#D2691E]">€{{ Number(item.price).toFixed(2) }}</span>
                  </div>
                </div>
              </Link>
            </div>
          </div>
          <button v-if="total > 1" @click="prev" class="absolute -left-2 top-1/3 -translate-y-1/2 bg-black/80 text-white w-8 h-8 rounded-full flex items-center justify-center z-10 hover:bg-[#D2691E] transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
          </button>
          <button v-if="total > 1" @click="next" class="absolute -right-2 top-1/3 -translate-y-1/2 bg-black/80 text-white w-8 h-8 rounded-full flex items-center justify-center z-10 hover:bg-[#D2691E] transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
          </button>
          <div class="flex justify-center gap-2 mt-4">
            <button v-for="i in total" :key="i" @click="slide = i - 1" class="h-1.5 rounded-full transition-all duration-300" :class="slide === i - 1 ? 'w-6 bg-[#D2691E]' : 'w-1.5 bg-white/20'" />
          </div>
        </div>

        <!-- CTA -->
        <div :ref="(el) => ctaRef = el as any" class="text-center">
          <Link href="/menu" class="btn-magnetic group inline-flex items-center gap-3 px-9 py-3.5 border border-[#D2691E]/40 text-[#D2691E] font-bold rounded-2xl text-sm uppercase tracking-wider hover:bg-[#D2691E] hover:text-white hover:border-[#D2691E] transition-colors duration-200">
            Avasta Kogu Menüü
            <span class="w-6 h-6 rounded-full border border-current flex items-center justify-center group-hover:bg-white/15 transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
              </svg>
            </span>
          </Link>
        </div>

      </div>
    </div>
  </section>
</template>