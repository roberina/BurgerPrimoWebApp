<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()

const page = usePage()
const formData = ref({ name: '', content: '', rating: 5 })
const isSubmitting = ref(false)
const submitted = computed(() => !!(page.props.flash as any)?.review_success)
const errorMsg = computed(() => (page.props.flash as any)?.review_error ?? '')

const close = () => emit('update:modelValue', false)

const submitReview = () => {
  if (!formData.value.name || !formData.value.content) return
  isSubmitting.value = true
  router.post('/reviews', formData.value, {
    preserveScroll: true,
    onFinish: () => { isSubmitting.value = false },
  })
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="close"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="close" />

        <!-- Modal -->
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-2"
        >
          <div
            v-if="modelValue"
            class="relative z-10 w-full max-w-sm bg-[#1a1a1a] rounded-2xl border border-[#2a2a2a] shadow-2xl shadow-black/60 p-6"
          >
            <!-- Close button -->
            <button
              @click="close"
              class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:text-white hover:bg-[#2a2a2a] transition-all duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Header -->
            <div class="mb-6">
              <p class="text-2xl mb-1">⭐</p>
              <h4 class="text-lg font-bold text-white">Jäta arvustus</h4>
              <p class="text-sm text-gray-500 mt-0.5">Mida arvate Burger Primost?</p>
            </div>

            <!-- Success state -->
            <div v-if="submitted" class="text-center py-6">
              <p class="text-4xl mb-3">🎉</p>
              <p class="text-green-400 font-medium">Täname!</p>
              <p class="text-gray-500 text-sm mt-1">Teie arvustus on saadetud ülevaatamiseks.</p>
              <button
                @click="close"
                class="mt-5 w-full py-2.5 text-sm bg-[#D2691E] text-white font-semibold rounded-xl hover:bg-[#B8511A] transition-all"
              >
                Sulge
              </button>
            </div>

            <!-- Error state -->
            <div v-else-if="errorMsg" class="text-center py-4">
              <p class="text-yellow-400 text-sm">{{ errorMsg }}</p>
            </div>

            <!-- Form -->
            <form v-else @submit.prevent="submitReview" class="space-y-4">
              <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">
                  Nimi <span class="text-[#D2691E]">*</span>
                </label>
                <input
                  v-model="formData.name"
                  type="text"
                  required
                  placeholder="Teie nimi"
                  class="w-full px-3 py-2.5 text-sm bg-[#0B0B0B] border border-gray-700 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#D2691E] transition-colors"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">
                  Hinne <span class="text-[#D2691E]">*</span>
                </label>
                <div class="flex gap-1">
                  <button
                    v-for="star in 5"
                    :key="star"
                    type="button"
                    @click="formData.rating = star"
                    :class="star <= formData.rating ? 'text-yellow-500' : 'text-gray-600'"
                    class="text-2xl hover:text-yellow-400 transition-colors"
                  >★</button>
                </div>
              </div>

              <div>
                <div class="flex justify-between items-center mb-1.5">
                  <label class="text-xs font-medium text-gray-400">
                    Arvustus <span class="text-[#D2691E]">*</span>
                  </label>
                  <span class="text-xs" :class="formData.content.length >= 85 ? 'text-red-400' : 'text-gray-600'">
                    {{ formData.content.length }}/85
                  </span>
                </div>
                <textarea
                  v-model="formData.content"
                  required
                  rows="3"
                  maxlength="85"
                  placeholder="Kirjutage oma kogemusest..."
                  class="w-full px-3 py-2.5 text-sm bg-[#0B0B0B] border border-gray-700 rounded-xl text-white placeholder-gray-600 focus:outline-none focus:border-[#D2691E] transition-colors resize-none"
                />
              </div>

              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full py-2.5 text-sm bg-[#D2691E] text-white font-semibold rounded-xl hover:bg-[#B8511A] transition-all disabled:opacity-50"
              >
                {{ isSubmitting ? 'Saadan...' : 'Saada arvustus' }}
              </button>
            </form>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>