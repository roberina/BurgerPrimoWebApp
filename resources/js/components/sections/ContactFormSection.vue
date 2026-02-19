<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import EditableSection from '@/components/EditableSection.vue'

const page = usePage()

const content = ref({
  title: 'VÕTA MEIEGA ÜHENDUST',
  titleColor: '#D2691E',
  subtitle: 'Kas teil on küsimusi või soovite lauda broneerida? Täitke vorm ja võtame peagi ühendust.',
  subtitleColor: '#ffffff',
})

const editContent = reactive(JSON.parse(JSON.stringify(content.value)))

const formData = reactive({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
})

const isSubmitting = ref(false)
const submitSuccess = ref(false)
const submitError = ref('')

const submitForm = async () => {
  if (!formData.name || !formData.email || !formData.message || !formData.subject) {
    submitError.value = 'Palun täitke kõik kohustuslikud väljad'
    return
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(formData.email)) {
    submitError.value = 'Palun sisestage kehtiv e-posti aadress'
    return
  }

  isSubmitting.value = true
  submitError.value = ''

  router.post('/contact', formData, {
    preserveScroll: true,
    onSuccess: () => {
      submitSuccess.value = true
      formData.name = ''
      formData.email = ''
      formData.phone = ''
      formData.subject = ''
      formData.message = ''

      setTimeout(() => {
        submitSuccess.value = false
      }, 5000)
    },
    onError: (errors) => {
      if (errors.error) {
        submitError.value = errors.error
      } else {
        submitError.value = 'Vabandust, sõnumi saatmisel tekkis viga. Palun proovige uuesti.'
      }
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

const save = () => {
  content.value = { ...editContent }
  router.post('/admin/page-content', { page: 'welcome', section: 'contact-form', content: content.value })
}

const cancel = () => {
  Object.assign(editContent, content.value)
}
</script>

<template>
  <section class="py-20">
    <div class="max-w-7xl mx-auto px-6">
      <EditableSection section-id="contact-form" @save="save" @cancel="cancel">
        <template #default="{ isEditing }">
          <div v-if="isEditing" class="fixed top-4 left-4 z-[999] bg-green-500 text-white px-4 py-2 rounded font-bold">
            EDIT MODE ACTIVE - CONTACT FORM
          </div>

          <div class="text-center mb-12">
            <h3 v-if="!isEditing" class="text-4xl md:text-5xl font-bold mb-4" :style="{ color: content.titleColor }">
              {{ content.title }}
            </h3>
            <div v-else class="mb-4 space-y-2">
              <input v-model="editContent.title" type="text" placeholder="Title..." class="w-full max-w-md mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white" />
              <div class="flex gap-2 justify-center items-center">
                <label class="text-sm">Title color:</label>
                <input v-model="editContent.titleColor" type="color" class="p-1 bg-gray-800 rounded border-2 border-white" />
              </div>
            </div>

            <p v-if="!isEditing" class="text-lg max-w-3xl mx-auto" :style="{ color: content.subtitleColor }">
              {{ content.subtitle }}
            </p>
            <div v-else class="space-y-2">
              <textarea v-model="editContent.subtitle" placeholder="Subtitle..." rows="2" class="w-full max-w-3xl mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white" />
              <div class="flex gap-2 justify-center items-center">
                <label class="text-sm">Subtitle color:</label>
                <input v-model="editContent.subtitleColor" type="color" class="p-1 bg-gray-800 rounded border-2 border-white" />
              </div>
            </div>
          </div>

          <div v-if="submitSuccess" class="max-w-2xl mx-auto mb-6 p-4 bg-green-600/20 border border-green-500 rounded-lg text-center">
            <p class="text-green-400 font-medium">✓ Täname! Teie sõnum on edukalt saadetud. Võtame teiega peagi ühendust.</p>
          </div>

          <div v-if="submitError" class="max-w-2xl mx-auto mb-6 p-4 bg-red-600/20 border border-red-500 rounded-lg text-center">
            <p class="text-red-400 font-medium">{{ submitError }}</p>
          </div>

          <div class="max-w-2xl mx-auto">
            <form @submit.prevent="submitForm" class="space-y-6">
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                    Nimi <span class="text-[#D2691E]">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="formData.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#D2691E] focus:ring-1 focus:ring-[#D2691E] transition-colors"
                    placeholder="Teie nimi"
                  />
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    E-post <span class="text-[#D2691E]">*</span>
                  </label>
                  <input
                    id="email"
                    v-model="formData.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#D2691E] focus:ring-1 focus:ring-[#D2691E] transition-colors"
                    placeholder="teie@email.ee"
                  />
                </div>
              </div>

              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
                    Telefon
                  </label>
                  <input
                    id="phone"
                    v-model="formData.phone"
                    type="tel"
                    class="w-full px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#D2691E] focus:ring-1 focus:ring-[#D2691E] transition-colors"
                    placeholder="+372 1234 5678"
                  />
                </div>

                <div>
                  <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">
                    Teema <span class="text-[#D2691E]">*</span>
                  </label>
                  <select
                    id="subject"
                    v-model="formData.subject"
                    class="w-full px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-lg text-white focus:outline-none focus:border-[#D2691E] focus:ring-1 focus:ring-[#D2691E] transition-colors"
                    required
                  >
                    <option value="">Valige teema</option>
                    <option value="reservation">Laua broneerimine</option>
                    <option value="catering">Catering teenus</option>
                    <option value="feedback">Tagasiside</option>
                    <option value="other">Muu</option>
                  </select>
                </div>
              </div>

              <div>
                <label for="message" class="block text-sm font-medium text-gray-300 mb-2">
                  Sõnum <span class="text-[#D2691E]">*</span>
                </label>
                <textarea
                  id="message"
                  v-model="formData.message"
                  required
                  rows="6"
                  class="w-full px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#D2691E] focus:ring-1 focus:ring-[#D2691E] transition-colors resize-none"
                  placeholder="Kirjutage oma sõnum siia..."
                />
              </div>

              <div class="text-center">
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="px-8 py-3 bg-[#D2691E] text-white font-semibold rounded-lg hover:bg-[#B8511A] focus:outline-none focus:ring-2 focus:ring-[#D2691E] focus:ring-offset-2 focus:ring-offset-[#121212] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="!isSubmitting">Saada Sõnum</span>
                  <span v-else class="flex items-center gap-2 justify-center">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saadan...
                  </span>
                </button>
              </div>
            </form>
          </div>
        </template>
      </EditableSection>
    </div>
  </section>
</template>