<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useScrollAnimation, useStaggerAnimation } from '@/composables/useScrollAnimation'
import { Clock, Mail, MapPin, Phone } from 'lucide-vue-next'

const { elRef: badgeRef }        = useScrollAnimation('scale-up',   { delay: 0   })
const { elRef: headingRef }      = useScrollAnimation('fade-up',    { delay: 80  })
const { elRef: subRef }          = useScrollAnimation('fade-up',    { delay: 160 })
const { elRef: leftRef }         = useScrollAnimation('fade-right', { delay: 0,   threshold: 0.12 })
const { elRef: rightRef }        = useScrollAnimation('fade-left',  { delay: 180, threshold: 0.12 })
const { containerRef: hoursRef } = useStaggerAnimation('fade-right', { staggerMs: 80, childSelector: '[data-stagger]' })

const form         = ref({ name: '', email: '', subject: 'other' as string, message: '' })
const isSubmitting = ref(false)
const submitState  = ref<'idle' | 'success' | 'error'>('idle')
const focusedField = ref<string | null>(null)

const subjects = [
  { value: 'reservation', label: 'Laua broneerimine' },
  { value: 'catering',    label: 'Catering teenus'   },
  { value: 'feedback',    label: 'Tagasiside'         },
  { value: 'other',       label: 'Muu'                },
]

const hours = [
  { days: 'Esmaspäev – Neljapäev', time: '11:00 – 22:00' },
  { days: 'Reede – Laupäev',       time: '11:00 – 23:00' },
  { days: 'Pühapäev',              time: '12:00 – 21:00' },
]

function submitContact() {
  if (!form.value.name || !form.value.email || !form.value.message) return
  isSubmitting.value = true
  router.post('/contact', form.value, {
    preserveScroll: true,
    onSuccess: () => { submitState.value = 'success'; form.value = { name: '', email: '', subject: 'other', message: '' } },
    onError:   () => { submitState.value = 'error' },
    onFinish:  () => { isSubmitting.value = false },
  })
}
</script>

<template>
  <section id="contact" class="section-py relative z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="glass px-6 py-12 md:px-12 md:py-14">

        <div class="text-center mb-12 space-y-4">
          <div :ref="(el) => badgeRef = el as any" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/12 bg-white/6 text-gray-300 text-xs font-bold uppercase tracking-[0.22em]">
            Kontakt
          </div>
          <h2 :ref="(el) => headingRef = el as any" class="text-3xl md:text-5xl font-bold text-white">
            Külastage <span class="text-[#D2691E]">meid</span>
          </h2>
          <p :ref="(el) => subRef = el as any" class="text-gray-400 text-base max-w-md mx-auto">
            Leidke meid Kuressaare südamest — või kirjutage meile
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 lg:gap-12">

          <div :ref="(el) => leftRef = el as any" class="space-y-4">

            <div class="glass-card p-5 group hover:border-[#D2691E]/22 transition-colors duration-300 relative overflow-hidden">
              <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none shimmer" />
              <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-[#D2691E]/12 flex items-center justify-center text-lg group-hover:scale-110 transition-transform duration-300">
                  <MapPin class="size-5 stroke-1" />
                </div>
                <h3 class="text-sm font-bold text-[#F5DEB3]">Meie asukoht</h3>
              </div>
              <div class="space-y-1 text-sm">
                <p class="text-white font-semibold">Kauba tn 5/2</p>
                <p class="text-gray-500">Kuressaare, Saaremaa 93819</p>
                <p class="text-gray-600">Eesti</p>
                <div class="pt-3 border-t border-white/6 mt-3 space-y-1.5">
                  <a href="tel:+37257438483" class="flex items-center gap-2 text-gray-500 hover:text-[#D2691E] transition-colors underline-sweep w-fit text-sm"><Phone class="size-4 stroke-1" /> +372 5743 8483</a>
                  <a href="mailto:info@burgerprimo.ee" class="flex items-center gap-2 text-gray-500 hover:text-[#D2691E] transition-colors underline-sweep w-fit text-sm"><Mail class="size-4 stroke-1" /> info@burgerprimo.ee</a>
                </div>
              </div>
            </div>

            <div class="glass-card p-5 group hover:border-[#D2691E]/22 transition-colors duration-300 relative overflow-hidden">
              <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none shimmer" />
              <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-[#D2691E]/12 flex items-center justify-center text-lg group-hover:scale-110 transition-transform duration-300"><Clock class="size-5 stroke-1" /></div>
                <h3 class="text-sm font-bold text-[#F5DEB3]">Lahtiolekuajad</h3>
              </div>
              <div :ref="(el) => hoursRef = el as any" class="space-y-2.5">
                <div v-for="item in hours" :key="item.days" data-stagger class="flex justify-between items-center py-2 border-b border-white/6 last:border-0">
                  <span class="text-gray-500 text-xs">{{ item.days }}</span>
                  <span class="text-white text-xs font-bold">{{ item.time }}</span>
                </div>
              </div>
            </div>
          </div>

          <div :ref="(el) => rightRef = el as any">
            <div class="glass-card overflow-hidden relative">
              <div class="h-0.5 bg-gradient-to-r from-transparent via-[#D2691E]/50 to-transparent" />
              <div class="p-5 lg:p-7">
                <div class="flex items-center gap-3 mb-6">
                  <div class="w-9 h-9 rounded-xl bg-[#D2691E]/12 flex items-center justify-center text-lg"><Mail class="size-5 stroke-1" /></div>
                  <div><h3 class="text-sm font-bold text-[#F5DEB3]">Kirjuta meile</h3><p class="text-xs text-gray-600 mt-0.5">Vastame 24 tunni jooksul</p></div>
                </div>

                <Transition enter-active-class="transition duration-400 ease-out" enter-from-class="opacity-0 scale-90 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0">
                  <div v-if="submitState === 'success'" class="text-center py-10">
                    <p class="text-5xl mb-5 animate-bob inline-block">🎉</p>
                    <p class="text-green-400 font-bold text-xl">Sõnum saadetud!</p>
                    <p class="text-gray-500 text-sm mt-2">Võtame teiega ühendust peagi.</p>
                    <button @click="submitState = 'idle'" class="mt-6 px-6 py-2.5 text-sm bg-white/6 text-gray-400 rounded-xl hover:bg-white/10 transition-all font-semibold">Saada uus sõnum</button>
                  </div>
                </Transition>

                <form v-if="submitState !== 'success'" @submit.prevent="submitContact" class="space-y-4">
                  <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="submitState === 'error'" class="px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl"><p class="text-red-400 text-sm">Midagi läks valesti. Proovige uuesti.</p></div>
                  </Transition>

                  <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nimi <span class="text-[#D2691E]">*</span></label>
                    <div class="relative">
                      <input v-model="form.name" type="text" required placeholder="Teie täisnimi" @focus="focusedField = 'name'" @blur="focusedField = null" class="primo-input w-full px-4 py-3 text-sm bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-700" />
                      <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 scale-90" enter-to-class="opacity-100 scale-100">
                        <span v-if="focusedField === 'name' && form.name" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#D2691E] text-sm">✓</span>
                      </Transition>
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">E-post <span class="text-[#D2691E]">*</span></label>
                    <div class="relative">
                      <input v-model="form.email" type="email" required placeholder="teie@email.com" @focus="focusedField = 'email'" @blur="focusedField = null" class="primo-input w-full px-4 py-3 text-sm bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-700" />
                      <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 scale-90" enter-to-class="opacity-100 scale-100">
                        <span v-if="focusedField === 'email' && form.email.includes('@')" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#D2691E] text-sm">✓</span>
                      </Transition>
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Teema</label>
                    <select v-model="form.subject" class="primo-input w-full px-4 py-3 text-sm bg-black/40 border border-white/10 rounded-xl text-white appearance-none cursor-pointer">
                      <option v-for="s in subjects" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Sõnum <span class="text-[#D2691E]">*</span></label>
                    <textarea v-model="form.message" required rows="4" placeholder="Kirjutage oma sõnum siia..." @focus="focusedField = 'message'" @blur="focusedField = null" class="primo-input w-full px-4 py-3 text-sm bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-700 resize-none" />
                  </div>

                  <button type="submit" :disabled="isSubmitting" class="btn-magnetic w-full py-4 text-sm bg-[#D2691E] text-white font-bold rounded-xl hover:bg-[#B8511A] transition-all shadow-lg shadow-[#D2691E]/15 disabled:opacity-40 flex items-center justify-center gap-2.5">
                    <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    {{ isSubmitting ? 'Saadan...' : 'Saada sõnum' }}
                  </button>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>