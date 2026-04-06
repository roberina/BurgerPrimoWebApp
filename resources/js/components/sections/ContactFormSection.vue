<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
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

// ✅ Single source of truth — driven by Laravel session via shared Inertia prop
const alreadySubmitted = computed(() => !!(page.props.contactSubmitted))

const submitForm = async () => {
  if (alreadySubmitted.value) {
    submitError.value = 'Olete juba selle sessiooni jooksul sõnumi saatnud.'
    return
  }

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
      submitError.value = errors.error ?? 'Vabandust, sõnumi saatmisel tekkis viga. Palun proovige hiljem uuesti.'
    },
    onFinish: () => {
      isSubmitting.value = false
    },
  })
}

const save = () => {
  content.value = { ...editContent }
  router.post('/admin/page-content', {
    page: 'welcome',
    section: 'contact-form',
    content: content.value,
  })
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
            Kontaktivorm – redigeerimine
          </div>

          <!-- TITLE -->
          <div class="text-center mb-12">
            <h3 v-if="!isEditing" class="text-4xl md:text-5xl font-bold mb-4" :style="{ color: content.titleColor }">
              {{ content.title }}
            </h3>

            <div v-else class="mb-4 space-y-2">
              <input v-model="editContent.title" type="text" class="input" />
              <input v-model="editContent.titleColor" type="color" />
            </div>

            <p v-if="!isEditing" class="text-lg max-w-3xl mx-auto" :style="{ color: content.subtitleColor }">
              {{ content.subtitle }}
            </p>

            <div v-else class="space-y-2">
              <textarea v-model="editContent.subtitle" rows="2" class="input" />
              <input v-model="editContent.subtitleColor" type="color" />
            </div>
          </div>

          <!-- ✅ LOCKED STATE — shown when Laravel session has contact_submitted -->
          <div
            v-if="alreadySubmitted"
            class="max-w-2xl mx-auto p-8 bg-green-600/10 border border-green-500/30 rounded-lg text-center"
          >
            <div class="text-5xl mb-4">✓</div>
            <p class="text-green-400 font-semibold text-lg mb-2">
              Sõnum on saadetud!
            </p>
            <p class="text-gray-400">
              Olete juba selle sessiooni jooksul vormi täitnud.
            </p>
          </div>

          <!-- 🟢 FORM -->
          <template v-else>
            <div v-if="submitSuccess" class="success-box">
              ✓ Täname! Teie sõnum on saadetud.
            </div>

            <div v-if="submitError" class="error-box">
              {{ submitError }}
            </div>

            <form @submit.prevent="submitForm" class="space-y-6 max-w-2xl mx-auto">

              <div class="grid md:grid-cols-2 gap-6">
                <input v-model="formData.name" placeholder="Teie nimi" required class="input" />
                <input v-model="formData.email" type="email" placeholder="teie@email.ee" required class="input" />
              </div>

              <div class="grid md:grid-cols-2 gap-6">
                <input v-model="formData.phone" placeholder="+372 1234 5678" class="input" />

                <select v-model="formData.subject" required class="input">
                  <option value="">Valige teema</option>
                  <option value="reservation">Laua broneerimine</option>
                  <option value="catering">Catering teenus</option>
                  <option value="feedback">Tagasiside</option>
                  <option value="other">Muu</option>
                </select>
              </div>

              <textarea v-model="formData.message" rows="6" required class="input" />

              <div class="text-center">
                <button
                  type="submit"
                  :disabled="isSubmitting || alreadySubmitted"
                  class="btn"
                >
                  <span v-if="!isSubmitting">Saada Sõnum</span>
                  <span v-else>Saadan...</span>
                </button>
              </div>

            </form>
          </template>

        </template>
      </EditableSection>
    </div>
  </section>
</template>