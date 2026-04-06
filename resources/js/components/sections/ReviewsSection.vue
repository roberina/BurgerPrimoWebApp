<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useScrollAnimation, useStaggerAnimation } from '@/composables/useScrollAnimation'

interface Review {
  id: number; name: string; content: string; rating: number
}

const props = defineProps<{ reviews?: Review[] }>()
const page  = usePage()

const { elRef: headingRef }           = useScrollAnimation('fade-up',  { delay: 0   })
const { elRef: subRef }               = useScrollAnimation('fade-up',  { delay: 100 })
const { elRef: ctaRef }               = useScrollAnimation('scale-up', { delay: 0   })
const { containerRef: staticGridRef } = useStaggerAnimation('fade-up', { staggerMs: 120, childSelector: '[data-stagger]' })

const alreadySubmitted = computed(() => !!(page.props as any).review_submitted)
const displayReviews   = computed(() => props.reviews ?? [])

const scrollReviews1 = computed(() => {
  if (!displayReviews.value.length) return []
  const copies = Math.max(3, Math.ceil(8 / displayReviews.value.length))
  return Array(copies).fill(displayReviews.value).flat()
})
const scrollReviews2 = computed(() => [...scrollReviews1.value].reverse())

const reviewSlide = ref(0)
const total       = computed(() => displayReviews.value.length)
const nextSlide   = () => { reviewSlide.value = (reviewSlide.value + 1) % total.value }
const prevSlide   = () => { reviewSlide.value = (reviewSlide.value - 1 + total.value) % total.value }

const reviewModalOpen = ref(false)
const submitSuccess   = ref(false)
const formData        = ref({ name: '', content: '', rating: 5 })
const isSubmitting    = ref(false)
const hoveredStar     = ref(0)
const errorMsg        = computed(() => (page.props.flash as any)?.review_error ?? '')

const submitReview = () => {
  if (!formData.value.name || !formData.value.content) return
  isSubmitting.value = true
  router.post('/reviews', formData.value, {
    preserveScroll: true,
    onSuccess: () => {
      submitSuccess.value = true
      formData.value = { name: '', content: '', rating: 5 }
      setTimeout(() => { reviewModalOpen.value = false; submitSuccess.value = false }, 2600)
    },
    onFinish: () => { isSubmitting.value = false },
  })
}
const closeModal = () => { reviewModalOpen.value = false; submitSuccess.value = false }
</script>

<template>
  <section class="section-py relative z-10">

    <!-- Heading inside glass -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 mb-8">
      <div class="px-6 py-10 md:px-12 md:py-12">
        <div class="text-center space-y-4">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-yellow-500/25 bg-yellow-500/8 text-yellow-400 text-xs font-bold uppercase tracking-[0.22em]">
            ★ Arvustused
          </div>
          <h2 :ref="(el) => headingRef = el as any" class="text-3xl md:text-5xl font-bold text-white">
            Klientide <span class="text-[#D2691E]">kogemus</span>
          </h2>
          <p :ref="(el) => subRef = el as any" class="text-gray-400 text-base max-w-md mx-auto">
            Mida meie kliendid Burger Primo kogemuse kohta ütlevad
          </p>
        </div>
      </div>
    </div>

    <!-- Dual marquee — full width, outside glass so it bleeds edge-to-edge -->
    <template v-if="displayReviews.length >= 4">
      <div class="relative w-full mb-4 overflow-hidden">
        <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-black/80 to-transparent z-10" />
        <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-black/80 to-transparent z-10" />
        <div class="flex gap-4 scroll-track">
          <div v-for="(review, i) in scrollReviews1" :key="`r1-${i}`" class="flex-shrink-0 w-72 glass-card p-5 hover:border-[#D2691E]/22 transition-colors duration-300 group">
            <div class="flex gap-0.5 mb-3">
              <span v-for="j in 5" :key="j" class="text-sm transition-transform duration-150 group-hover:scale-110" :class="j <= review.rating ? 'text-yellow-400' : 'text-gray-700'" :style="{ transitionDelay: `${j * 30}ms` }">★</span>
            </div>
            <p class="text-gray-300 text-xs leading-relaxed line-clamp-3 mb-3">{{ review.content }}</p>
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#D2691E]/30 to-[#8B3A00]/30 flex items-center justify-center flex-shrink-0 border border-[#D2691E]/20">
                <span class="text-[#D2691E] font-black text-xs">{{ review.name.charAt(0).toUpperCase() }}</span>
              </div>
              <span class="text-xs text-gray-400 font-medium">{{ review.name }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="relative w-full mb-8 overflow-hidden">
        <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-black/80 to-transparent z-10" />
        <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-black/80 to-transparent z-10" />
        <div class="flex gap-4 scroll-track-reverse">
          <div v-for="(review, i) in scrollReviews2" :key="`r2-${i}`" class="flex-shrink-0 w-64 glass-card p-4 hover:border-[#D2691E]/15 transition-colors duration-300">
            <div class="flex gap-0.5 mb-2"><span v-for="j in 5" :key="j" class="text-xs" :class="j <= review.rating ? 'text-yellow-400' : 'text-gray-700'">★</span></div>
            <p class="text-gray-500 text-xs leading-relaxed line-clamp-2 mb-2">{{ review.content }}</p>
            <span class="text-xs text-gray-600">— {{ review.name }}</span>
          </div>
        </div>
      </div>
    </template>

    <!-- Static grid (<4 reviews) -->
    <template v-else-if="displayReviews.length > 0">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 mb-8">
        <div :ref="(el) => staticGridRef = el as any" class="hidden md:grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="(review, i) in displayReviews" :key="review.id" data-stagger class="glass-card p-6 hover:border-[#D2691E]/20 transition-colors duration-300 group">
            <div class="flex gap-1 mb-4"><span v-for="j in 5" :key="j" class="text-lg group-hover:scale-110 transition-transform duration-150" :class="j <= review.rating ? 'text-yellow-400' : 'text-gray-700'" :style="{ transitionDelay: `${j * 40}ms` }">★</span></div>
            <p class="text-gray-300 text-sm leading-relaxed mb-4">{{ review.content }}</p>
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#D2691E]/25 to-[#8B3A00]/25 flex items-center justify-center border border-[#D2691E]/20"><span class="text-[#D2691E] font-black text-sm">{{ review.name.charAt(0).toUpperCase() }}</span></div>
              <span class="text-sm text-gray-400 font-medium">{{ review.name }}</span>
            </div>
          </div>
        </div>
        <!-- Mobile -->
        <div class="md:hidden relative">
          <div class="overflow-hidden rounded-2xl">
            <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${reviewSlide * 100}%)` }">
              <div v-for="review in displayReviews" :key="review.id" class="w-full flex-shrink-0">
                <div class="glass-card p-6">
                  <div class="flex gap-1 mb-4"><span v-for="j in 5" :key="j" class="text-lg" :class="j <= review.rating ? 'text-yellow-400' : 'text-gray-700'">★</span></div>
                  <p class="text-gray-300 text-sm leading-relaxed mb-4">{{ review.content }}</p>
                  <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-[#D2691E]/15 flex items-center justify-center"><span class="text-[#D2691E] font-black text-sm">{{ review.name.charAt(0).toUpperCase() }}</span></div><span class="text-sm text-gray-400 font-medium">{{ review.name }}</span></div>
                </div>
              </div>
            </div>
          </div>
          <button v-if="total > 1" @click="prevSlide" class="absolute -left-3 top-1/2 -translate-y-1/2 bg-black/80 text-white w-8 h-8 rounded-full flex items-center justify-center z-10 hover:bg-[#D2691E] transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg></button>
          <button v-if="total > 1" @click="nextSlide" class="absolute -right-3 top-1/2 -translate-y-1/2 bg-black/80 text-white w-8 h-8 rounded-full flex items-center justify-center z-10 hover:bg-[#D2691E] transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg></button>
          <div class="flex justify-center gap-1.5 mt-4"><button v-for="(_, i) in displayReviews" :key="i" @click="reviewSlide = i" class="h-1.5 rounded-full transition-all duration-300" :class="reviewSlide === i ? 'w-5 bg-[#D2691E]' : 'w-1.5 bg-white/20'" /></div>
        </div>
      </div>
    </template>

    <div v-else class="max-w-6xl mx-auto px-4 mb-8 text-center py-10">
      <p class="text-4xl mb-3 animate-bob inline-block">⭐</p>
      <p class="text-gray-500 mt-2">Arvustusi pole veel. Ole esimene!</p>
    </div>

    <!-- CTA -->
    <div :ref="(el) => ctaRef = el as any" class="flex justify-center">
      <button
        @click="reviewModalOpen = true"
        :disabled="alreadySubmitted"
        class="btn-magnetic group inline-flex items-center gap-2.5 px-7 py-3.5 rounded-2xl text-sm font-bold transition-all duration-250"
        :class="alreadySubmitted ? 'bg-black/40 text-gray-600 cursor-not-allowed border border-white/8' : 'bg-[#D2691E]/12 border border-[#D2691E]/35 text-[#D2691E] hover:bg-[#D2691E] hover:text-white hover:border-[#D2691E]'"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200 group-hover:rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        {{ alreadySubmitted ? 'Arvustus saadetud' : 'Jäta arvustus' }}
      </button>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-250 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="reviewModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="closeModal" />
          <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 scale-90 translate-y-6" enter-to-class="opacity-100 scale-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95 translate-y-3">
            <div v-if="reviewModalOpen" class="relative z-10 w-full max-w-sm glass rounded-3xl overflow-hidden">
              <div class="h-0.5 bg-gradient-to-r from-transparent via-[#D2691E] to-transparent" />
              <div class="p-6">
                <button @click="closeModal" class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full text-gray-600 hover:text-white hover:bg-white/8 transition-all">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <Transition enter-active-class="transition duration-400 ease-out" enter-from-class="opacity-0 scale-90" enter-to-class="opacity-100 scale-100">
                  <div v-if="submitSuccess" class="text-center py-8">
                    <p class="text-5xl mb-4 animate-bob inline-block">🎉</p>
                    <p class="text-green-400 font-bold text-lg">Täname!</p>
                    <p class="text-gray-500 text-sm mt-2">Teie arvustus on saadetud ülevaatamiseks.</p>
                  </div>
                </Transition>
                <template v-if="!alreadySubmitted && !submitSuccess">
                  <div class="mb-6"><h4 class="text-lg font-bold text-white">Jäta arvustus</h4><p class="text-sm text-gray-500 mt-1">Mida arvate Burger Primost?</p></div>
                  <div v-if="errorMsg" class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-2xl"><p class="text-red-400 text-sm">{{ errorMsg }}</p></div>
                  <form @submit.prevent="submitReview" class="space-y-4">
                    <div>
                      <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nimi <span class="text-[#D2691E]">*</span></label>
                      <input v-model="formData.name" type="text" required placeholder="Teie nimi" class="primo-input w-full px-4 py-3 text-sm bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-700" />
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Hinne <span class="text-[#D2691E]">*</span></label>
                      <div class="flex gap-1">
                        <button v-for="star in 5" :key="star" type="button" @click="formData.rating = star" @mouseenter="hoveredStar = star" @mouseleave="hoveredStar = 0" class="text-2xl transition-all duration-100 hover:scale-125" :class="star <= (hoveredStar || formData.rating) ? 'text-yellow-400' : 'text-gray-700'">★</button>
                      </div>
                    </div>
                    <div>
                      <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Arvustus <span class="text-[#D2691E]">*</span></label>
                        <span class="text-xs font-mono" :class="formData.content.length >= 80 ? 'text-red-400' : 'text-gray-600'">{{ formData.content.length }}/85</span>
                      </div>
                      <textarea v-model="formData.content" required rows="3" maxlength="85" placeholder="Kirjutage oma kogemusest..." class="primo-input w-full px-4 py-3 text-sm bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-700 resize-none" />
                    </div>
                    <button type="submit" :disabled="isSubmitting" class="btn-magnetic w-full py-3.5 text-sm bg-[#D2691E] text-white font-bold rounded-xl hover:bg-[#B8511A] transition-all disabled:opacity-40 flex items-center justify-center gap-2">
                      <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                      {{ isSubmitting ? 'Saadan...' : 'Saada arvustus' }}
                    </button>
                  </form>
                </template>
                <template v-else-if="alreadySubmitted && !submitSuccess">
                  <div class="text-center py-6">
                    <p class="text-4xl mb-4">✅</p><p class="text-white font-bold">Arvustus juba saadetud</p>
                    <p class="text-gray-500 text-sm mt-2">Olete selle seansi jooksul juba arvustuse saatnud.</p>
                    <button @click="closeModal" class="mt-5 w-full py-3 text-sm bg-white/6 text-gray-400 font-semibold rounded-xl hover:bg-white/10 transition-all">Sulge</button>
                  </div>
                </template>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </section>
</template>

<style scoped>
.scroll-track         { animation: scroll-left  55s linear infinite; width: max-content; }
.scroll-track:hover   { animation-play-state: paused; }
.scroll-track-reverse { animation: scroll-right 70s linear infinite; width: max-content; }
.scroll-track-reverse:hover { animation-play-state: paused; }
@keyframes scroll-left  { from { transform: translateX(0);    } to { transform: translateX(-50%); } }
@keyframes scroll-right { from { transform: translateX(-50%); } to { transform: translateX(0);    } }
</style>