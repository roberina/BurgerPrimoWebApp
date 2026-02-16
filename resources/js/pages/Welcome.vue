<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import EditableSection from '@/components/EditableSection.vue';

const cartItems = ref([]);

const reviews = [
  { text: 'Parim', initial: 'R', name: 'Robby' },
  { text: 'Mu lemmik burger ja pitsa ja asi ja...', initial: 'T', name: 'Taaniel' },
  { text: 'Robby on läks', initial: 'K', name: 'Keanu' }
];

const content = ref({
  hero: {
    title: 'Kirega valmistatud preemium burgerid, mida serveeritakse unkusega',
    titleColor: '#FFFFFF',
    subtitle: 'Kurssaare südames',
    subtitleColor: '#FFFFFF',
    buttonText: 'Avasta menüüd',
    buttonLink: '/menu',
    buttonBgColor: '#D2691E',
  },
  popular: {
    label: 'MEIE VALIK',
    labelColor: '#D2691E',
    title: 'Populaarsed',
    titleColor: '#F5DEB3',
    subtitle: 'Primos enim tuntud ja rohkelt tellitud toidud',
    buttonText: 'Avasta Kogu Menüüd',
    buttonLink: '/menu',
    buttonBgColor: '#D2691E',
  },
  contact: {
    label: 'Külasta Burger Primot Kuressaare südames',
    labelColor: '#F5DEB3',
    title: 'Külastage Meid',
    titleColor: '#D2691E',
    addressTitle: 'Meie Asukoht',
    address: ['Kauba tn 5/2', 'Kuressaare, Saaremaa 93819', 'Eesti', '', 'Telefon: +372 5743 8483', 'Email: info@burgerprimo.ee'],
    hoursTitle: 'Lahtiolekuajad',
    hours: ['Esmaspäev - Neljapäev: 11:00 - 22:00', 'Reede - Laupäev: 11:00 - 23:00', 'Pühapäev: 12:00 - 21:00'],
  },
  reviews: {
    label: 'ARVUSTUSED',
    labelColor: '#D2691E',
    title: 'Klientide Kogemus',
    titleColor: '#F5DEB3',
  },
  entertain: {
    label: 'Nautige sõpradega piljardimängu, nautides samal ajal meie maitsvaid toite ja jooke.',
    labelColor: '#fffff',
    title: 'Mängi piljardit',
    sectionTitle: 'Piljardilaud',
    titleColor: '#D2691E',
    sectionTitleColor: '#F5DEB3',
    description: 'Nautige piljardilauda koos vastupidava villase segukanga ja pallikomplektiga. Ideaalne algajatele ja ka edasijõudnutele mänginiseks, nautides samal ajal oma lemmik Primo burgereid ja jooke.',
    pricingTitle: 'Hinnakiri',
    pricingTitleColor: '#F5DEB3',
    pricingItems: [
      { label: '8-palli mäng', price: '2€ mäng' },
    ],
    galleryTitle: 'Galerii',
    galleryTitleColor: '#F5DEB3',
    galleryImages: [
      '/img/pool2.jpg',
      '/img/pool1.jpg',
      '/img/pool3.jpg',
    ]
  }
});

const editContent = reactive(JSON.parse(JSON.stringify(content.value)));

const saveHero = () => {
  content.value.hero = { ...editContent.hero };
  router.post('/admin/page-content', { page: 'welcome', section: 'hero', content: content.value.hero });
};

const savePopular = () => {
  content.value.popular = { ...editContent.popular };
  router.post('/admin/page-content', { page: 'welcome', section: 'popular', content: content.value.popular });
};

const saveContact = () => {
  content.value.contact = { ...editContent.contact };
  router.post('/admin/page-content', { page: 'welcome', section: 'contact', content: content.value.contact });
};

const saveReviews = () => {
  content.value.reviews = { ...editContent.reviews };
  router.post('/admin/page-content', { page: 'welcome', section: 'reviews', content: content.value.reviews });
};

const saveEntertain = () => {
  content.value.entertain = { ...editContent.entertain };
  router.post('/admin/page-content', { page: 'welcome', section: 'entertainment', content: content.value.entertain });
};

const cancelHero = () => {
  Object.assign(editContent.hero, content.value.hero);
};

const cancelPopular = () => {
  Object.assign(editContent.popular, content.value.popular);
};

const cancelContact = () => {
  Object.assign(editContent.contact, content.value.contact);
};

const cancelReviews = () => {
  Object.assign(editContent.reviews, content.value.reviews);
};

const cancelEntertain = () => {
  Object.assign(editContent.entertain, content.value.entertain);
};

const currentSlide = ref(0)

function nextSlide() {
  const images = content.value.entertain.galleryImages
  if (!images || images.length === 0) return

  currentSlide.value =
    (currentSlide.value + 1) % images.length
}

function prevSlide() {
  const images = content.value.entertain.galleryImages
  if (!images || images.length === 0) return

  currentSlide.value =
    (currentSlide.value - 1 + images.length) % images.length
}


</script>

<template>
  <Head title="Avaleht" />
  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <Navbar :cartCount="cartItems.length" />

    <section class="w-full h-screen overflow-hidden">
      <EditableSection section-id="hero" container-class="h-full" @save="saveHero" @cancel="cancelHero">
        <template #default="{ isEditing }">
          <div class="w-full h-full flex items-center justify-center">
            <div v-if="isEditing" class="fixed top-4 left-4 z-[999] bg-red-500 text-white px-4 py-2 rounded font-bold">
              EDIT MODE ACTIVE - HERO
            </div>
            
            <div class="absolute inset-0 w-full h-full">
              <img 
                src="/img/main2.jpg" 
                alt="Burger Primo Interior" 
                class="w-full h-full object-cover"
              />
              <div class="absolute inset-0 backdrop-blur-xl bg-black/40 bg-gradient-to-b from-black/80 via-black/40 to-black z-0"></div>
            </div>
            
            <div class="relative max-w-4xl mx-auto px-6 text-center w-full z-10 flex flex-col justify-center min-h-[calc(100vh-8rem)]">

              <div class="mb-6 flex flex-col items-center">
                  <img 
                    src="/img/Logo4.png" 
                    alt="Burger Primo Logo" 
                    class="w-64 h-64 md:w-96 md:h-96 object-contain"
                    style="filter: drop-shadow(0 25px 25px rgba(0, 0, 0, 0.6));"
                  />
                </div>


              <h1 
                v-if="!isEditing"
                class="text-xl md:text-2xl font-light mb-8 leading-tight max-w-2xl mx-auto"
                :style="{ color: content.hero.titleColor }"
              >
                {{ content.hero.title }}
              </h1>
              <div v-else class="mb-8 space-y-2">
                <textarea
                  v-model="editContent.hero.title"
                  placeholder="Enter title..."
                  class="w-full max-w-2xl mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white"
                  rows="2"
                ></textarea>
                <div class="flex gap-2 justify-center items-center">
                  <label class="text-sm">Title color:</label>
                  <input
                    v-model="editContent.hero.titleColor"
                    type="color"
                    class="p-2 bg-gray-800 rounded border-2 border-white"
                  />
                </div>
              </div>
              <div v-if="!isEditing">
                <Link
                  :href="content.hero.buttonLink"
                  class="inline-block px-10 py-4 rounded-md font-semibold transition hover:opacity-90 text-white uppercase tracking-wide"
                  :style="{ backgroundColor: content.hero.buttonBgColor }"
                >
                  {{ content.hero.buttonText }}
                </Link>
              </div>
              <div v-else class="space-y-2">
                <input
                  v-model="editContent.hero.buttonText"
                  type="text"
                  placeholder="Button text..."
                  class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
                />
                <input
                  v-model="editContent.hero.buttonLink"
                  type="text"
                  placeholder="Button link..."
                  class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
                />
                <div class="flex gap-2 justify-center">
                  <label class="text-sm">Button color:</label>
                  <input
                    v-model="editContent.hero.buttonBgColor"
                    type="color"
                    class="p-1 bg-gray-800 rounded border-2 border-white"
                  />
                </div>
              </div>
            </div>
            <div class="absolute bottom-18 left-1/2 -translate-x-1/2 flex flex-col items-center text-white animate-bounce z-20">
              <span class="text-sm tracking-widest uppercase mb-2">Keri alla</span>
              
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="w-6 h-6"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
                stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>
        </template>
      </EditableSection>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-20">
      <EditableSection section-id="popular" @save="savePopular" @cancel="cancelPopular">
        <template #default="{ isEditing }">
          <div v-if="isEditing" class="fixed top-4 left-4 z-[999] bg-blue-500 text-white px-4 py-2 rounded font-bold">
            EDIT MODE ACTIVE - POPULAR
          </div>
          
          <div class="text-center mb-16">
            <h2 
              v-if="!isEditing"
              class="text-sm uppercase mb-3 tracking-widest font-semibold"
              :style="{ color: content.popular.labelColor }"
            >
              {{ content.popular.label }}
            </h2>
            <div v-else class="mb-3 space-y-2">
              <input
                v-model="editContent.popular.label"
                type="text"
                placeholder="Label..."
                class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
              />
              <div class="flex gap-2 justify-center items-center">
                <label class="text-sm">Label color:</label>
                <input
                  v-model="editContent.popular.labelColor"
                  type="color"
                  class="p-1 bg-gray-800 rounded border-2 border-white"
                />
              </div>
            </div>
            
            <h3 
              v-if="!isEditing"
              class="text-4xl md:text-5xl font-bold mb-4"
              :style="{ color: content.popular.titleColor }"
            >
              {{ content.popular.title }}
            </h3>
            <div v-else class="mb-4 space-y-2">
              <input
                v-model="editContent.popular.title"
                type="text"
                placeholder="Title..."
                class="w-full max-w-md mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white"
              />
              <div class="flex gap-2 justify-center items-center">
                <label class="text-sm">Title color:</label>
                <input
                  v-model="editContent.popular.titleColor"
                  type="color"
                  class="p-1 bg-gray-800 rounded border-2 border-white"
                />
              </div>
            </div>

            <p 
              v-if="!isEditing"
              class="text-gray-300 text-lg"
            >
              {{ content.popular.subtitle }}
            </p>
            <textarea
              v-else
              v-model="editContent.popular.subtitle"
              placeholder="Subtitle..."
              class="w-full max-w-md mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white mb-4"
              rows="2"
            ></textarea>
          </div>

          <div class="grid md:grid-cols-3 gap-8 mb-12">
            <div v-for="i in 3" :key="i" class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition">
              <div class="aspect-square bg-gray-200 flex items-center justify-center">
                <span class="text-6xl font-bold text-gray-400">Pilt</span>
              </div>
            </div>
          </div>

          <div class="text-center">
            <div v-if="!isEditing">
              <Link
                :href="content.popular.buttonLink"
                class="inline-block px-10 py-4 rounded-md font-semibold transition hover:opacity-90 text-white uppercase tracking-wide"
                :style="{ backgroundColor: content.popular.buttonBgColor }"
              >
                {{ content.popular.buttonText }}
              </Link>
            </div>
            <div v-else class="space-y-2">
              <input
                v-model="editContent.popular.buttonText"
                type="text"
                placeholder="Button text..."
                class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
              />
              <input
                v-model="editContent.popular.buttonLink"
                type="text"
                placeholder="Button link..."
                class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
              />
              <div class="flex gap-2 justify-center">
                <label class="text-sm">Button color:</label>
                <input
                  v-model="editContent.popular.buttonBgColor"
                  type="color"
                  class="p-1 bg-gray-800 rounded border-2 border-white"
                />
              </div>
            </div>
          </div>
        </template>
      </EditableSection>
    </section>

    <section class="bg-[#121212] py-20">
      <div class="max-w-7xl mx-auto px-6">
        <EditableSection section-id="contact" @save="saveContact" @cancel="cancelContact">
          <template #default="{ isEditing }">
            <div v-if="isEditing" class="fixed top-4 left-4 z-[999] bg-purple-500 text-white px-4 py-2 rounded font-bold">
              EDIT MODE ACTIVE - CONTACT
            </div>
            
            <div class="text-center mb-12">
             <h3 
                v-if="!isEditing"
                class="text-4xl md:text-5xl font-bold"
                :style="{ color: content.contact.titleColor }"
              >
                {{ content.contact.title }}
              </h3>
              <div v-else class="space-y-2">
                <input
                  v-model="editContent.contact.title"
                  type="text"
                  placeholder="Title..."
                  class="w-full max-w-md mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white"
                />
                <div class="flex gap-2 justify-center items-center">
                  <label class="text-sm">Title color:</label>
                  <input
                    v-model="editContent.contact.titleColor"
                    type="color"
                    class="p-1 bg-gray-800 rounded border-2 border-white"
                />
                </div>
              </div> 
              <h2 
                v-if="!isEditing"
                class="text-sm uppercase mt-4 tracking-widest font-semibold"
                :style="{ color: content.contact.labelColor }"
              >
                {{ content.contact.label }}
              </h2>
              <div v-else class="mb-3 space-y-2">
                <input
                  v-model="editContent.contact.label"
                  type="text"
                  placeholder="Label..."
                  class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
                />
                <div class="flex gap-2 justify-center items-center">
                  <label class="text-sm">Label color:</label>
                  <input
                    v-model="editContent.contact.labelColor"
                    type="color"
                    class="p-1 bg-gray-800 rounded border-2 border-white"
                  />
                </div>
              </div>
              
              
            </div>

            <div class="grid md:grid-cols-2 gap-12 max-w-5xl mx-auto">
              <div>
                <h4 
                  v-if="!isEditing"
                  class="text-xl font-bold mb-6 text-white"
                >
                  {{ content.contact.addressTitle }}
                </h4>
                <input
                  v-else
                  v-model="editContent.contact.addressTitle"
                  type="text"
                  placeholder="Address title..."
                  class="w-full p-2 bg-gray-800 text-white rounded border-2 border-white mb-6"
                />
                
                <div v-if="!isEditing" class="text-gray-300  text-base">
                  <p v-for="(line, i) in content.contact.address" :key="i">{{ line }}</p>
                </div>
                <div v-else class="space-y-2">
                  <input
                    v-for="(line, i) in editContent.contact.address"
                    :key="i"
                    v-model="editContent.contact.address[i]"
                    type="text"
                    :placeholder="`Address line ${(i as number) + 1}`"
                    class="w-full p-2 bg-gray-800 text-white rounded border-2 border-white"
                  />
                </div>

                <div class="rounded-lg overflow-hidden h-64 mt-6 shadow-lg">
                      <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d8397.343520194667!2d22.483251!3d58.252736999999996!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46f26ce2774f2b63%3A0xea0f9eafb8fa644c!2sPrimo%20Burger!5e0!3m2!1sen!2see!4v1770927348729!5m2!1sen!2see" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen=true 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                      </iframe>
                    </div>
              </div>

              <div>
                <h4 
                  v-if="!isEditing"
                  class="text-xl font-bold mb-6 text-white"
                >
                  {{ content.contact.hoursTitle }}
                </h4>
                <input
                  v-else
                  v-model="editContent.contact.hoursTitle"
                  type="text"
                  placeholder="Hours title..."
                  class="w-full p-2 bg-gray-800 text-white rounded border-2 border-white mb-6"
                />
                
                <div v-if="!isEditing" class="space-y-4 text-base">
                  <div v-for="(line, i) in content.contact.hours" :key="i" class="flex justify-between items-center">
                    <span class="text-gray-300">{{ line.split(':')[0] }}</span>
                    <span class="text-white font-medium">{{ line.split(':').slice(1).join(':').trim() }}</span>
                  </div>
                </div>
                <div v-else class="space-y-2">
                  <input
                    v-for="(line, i) in editContent.contact.hours"
                    :key="i"
                    v-model="editContent.contact.hours[i]"
                    type="text"
                    :placeholder="`Hours line ${(i as number) + 1}`"
                    class="w-full p-2 bg-gray-800 text-white rounded border-2 border-white"
                  />
                </div>

                <div v-if="!isEditing" class="mt-8 p-4 bg-[#1a1a1a] rounded-lg">
                  <p class="text-sm text-gray-300 mb-2">Toidu kohaletoimetamine</p>
                  <p class="text-sm text-gray-400">Toitu toimetab kohale Bolt Food ja Wolt, restorani lahtiolekuaegadel</p>
                  <div class="mt-3 flex gap-3">
                    <a href="https://wolt.com/en/est/kuressaare/restaurant/primo-burger?srsltid=AfmBOoou9cug0gwJK-jJ-cEYiEtXBpvTEVDKnMaQkcQmfLMcCPv-CaLl" class="px-3 py-1 bg-[#12dcff] text-white rounded-full text-sm hover:scale-105 transition-transform">Wolt</a>
                    <a href="https://food.bolt.eu/en-US/164/p/90859-primo-burger" class="px-3 py-1 bg-[#21c93d] text-white rounded-full text-sm hover:scale-105 transition-transform">Bolt Food</a>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </EditableSection>
      </div>
    </section>

<section class="bg-[#121212] py-20">
  <div class="max-w-7xl mx-auto px-6">
    <EditableSection section-id="entertainment" @save="saveEntertain" @cancel="cancelEntertain">
      <template #default="{ isEditing }">

        <div v-if="isEditing" class="fixed top-4 left-4 z-[999] bg-purple-500 text-white px-4 py-2 rounded font-bold">
          EDIT MODE ACTIVE - ENTERTAINMENT
        </div>

        <div class="text-center mb-12">
          <h3 
            v-if="!isEditing"
            class="text-4xl md:text-5xl font-bold"
            :style="{ color: content.entertain.titleColor }"
          >
            {{ content.entertain.title }}
          </h3>

          <div v-else class="space-y-2">
            <input
              v-model="editContent.entertain.title"
              type="text"
              placeholder="Title..."
              class="w-full max-w-md mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white"
            />
            <div class="flex gap-2 justify-center items-center">
              <label class="text-sm">Title color:</label>
              <input
                v-model="editContent.entertain.titleColor"
                type="color"
                class="p-1 bg-gray-800 rounded border-2 border-white"
              />
            </div>
          </div>

          <h2 
            v-if="!isEditing"
            class="text-sm uppercase mt-4 tracking-widest font-semibold"
            :style="{ color: content.entertain.labelColor }"
          >
            {{ content.entertain.label }}
          </h2>

          <div v-else class="mb-3 space-y-2">
            <input
              v-model="editContent.entertain.label"
              type="text"
              placeholder="Label..."
              class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
            />
            <div class="flex gap-2 justify-center items-center">
              <label class="text-sm">Label color:</label>
              <input
                v-model="editContent.entertain.labelColor"
                type="color"
                class="p-1 bg-gray-800 rounded border-2 border-white"
              />
            </div>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-start">
          <div class="relative rounded-lg overflow-hidden">

          <div class="w-full aspect-[4/3] overflow-hidden">
          
            <div
              class="flex h-full transition-transform duration-500 ease-in-out"
              :style="{
                transform: `translateX(-${currentSlide * 100}%)`
              }"
            >
              <div
                v-for="(image, i) in content.entertain.galleryImages"
                :key="i"
                class="w-full flex-shrink-0 h-full relative"
              >
                <img
                  :src="image"
                  class="absolute inset-0 w-full h-full object-cover"
                  alt="Billiard image"
                />
              </div>
            </div>
          
          </div>
        
          <button
            v-if="content.entertain.galleryImages.length > 1"
            @click="prevSlide"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-white px-3 py-2 rounded-full hover:cursor-pointer"
          >
            ‹
          </button>
        
          <button
            v-if="content.entertain.galleryImages.length > 1"
            @click="nextSlide"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-white px-3 py-2 rounded-full hover:cursor-pointer"
          >
            ›
          </button>
        
          <div
            v-if="content.entertain.galleryImages.length > 1"
            class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2"
          >
            <button
              v-for="(img, i) in content.entertain.galleryImages"
              :key="i"
              @click="currentSlide = i"
              class="w-3 h-3 rounded-full hover:cursor-pointer"
              :class="i === currentSlide ? 'bg-white' : 'bg-white/40' "
            />
          </div>
        
        </div>
          <div>

            <h2 
              v-if="!isEditing"
              class="text-3xl md:text-4xl font-bold mb-6"
              :style="{ color: content.entertain.sectionTitleColor }"
            >
              {{ content.entertain.sectionTitle }}
            </h2>

            <div v-else class="space-y-2 mb-6">
              <input
                v-model="editContent.entertain.sectionTitle"
                type="text"
                placeholder="Section title..."
                class="w-full p-3 bg-gray-800 text-white rounded border-2 border-white"
              />
              <div class="flex gap-2 items-center">
                <label class="text-sm">Title color:</label>
                <input
                  v-model="editContent.entertain.sectionTitleColor"
                  type="color"
                  class="p-1 bg-gray-800 rounded border-2 border-white"
                />
              </div>
            </div>

            <p v-if="!isEditing" class="text-gray-300 text-base leading-relaxed mb-8">
              {{ content.entertain.description }}
            </p>

            <textarea
              v-else
              v-model="editContent.entertain.description"
              rows="4"
              class="w-full p-3 bg-gray-800 text-white rounded border-2 border-white mb-8"
            ></textarea>

            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-gray-800">
              <h3 
                v-if="!isEditing"
                class="text-xl font-bold mb-4"
                :style="{ color: content.entertain.pricingTitleColor }"
              >
                {{ content.entertain.pricingTitle }}
              </h3>

              <div v-else class="space-y-2 mb-4">
                <input
                  v-model="editContent.entertain.pricingTitle"
                  type="text"
                  placeholder="Pricing title..."
                  class="w-full p-2 bg-gray-800 text-white rounded border-2 border-white"
                />
                <div class="flex gap-2 items-center">
                  <label class="text-sm">Pricing title color:</label>
                  <input
                    v-model="editContent.entertain.pricingTitleColor"
                    type="color"
                    class="p-1 bg-gray-800 rounded border-2 border-white"
                  />
                </div>
              </div>

              <div v-if="!isEditing" class="space-y-3">
                <div 
                  v-for="(item, i) in content.entertain.pricingItems"
                  :key="i"
                  class="flex justify-between items-center"
                >
                  <span class="text-gray-300">{{ item.label }}</span>
                  <span class="text-white font-semibold text-lg">{{ item.price }}</span>
                </div>
              </div>

              <div v-else class="space-y-2">
                <div 
                  v-for="(item, i) in editContent.entertain.pricingItems"
                  :key="i"
                  class="flex gap-2"
                >
                  <input
                    v-model="editContent.entertain.pricingItems[i].label"
                    type="text"
                    class="flex-1 p-2 bg-gray-800 text-white rounded border-2 border-white"
                  />
                  <input
                    v-model="editContent.entertain.pricingItems[i].price"
                    type="text"
                    class="w-32 p-2 bg-gray-800 text-white rounded border-2 border-white"
                  />
                </div>
              </div>
            </div>

          </div>
        </div>

      </template>
    </EditableSection>
  </div>
</section>

    <section class="max-w-7xl mx-auto px-6 py-20">
      <EditableSection section-id="reviews" @save="saveReviews" @cancel="cancelReviews">
        <template #default="{ isEditing }">
          <div v-if="isEditing" class="fixed top-4 left-4 z-[999] bg-yellow-500 text-black px-4 py-2 rounded font-bold">
            EDIT MODE ACTIVE - REVIEWS
          </div>
          
          <div class="text-center mb-12">
            <h2 
              v-if="!isEditing"
              class="text-sm uppercase mb-3 tracking-widest font-semibold"
              :style="{ color: content.reviews.labelColor }"
            >
              {{ content.reviews.label }}
            </h2>
            <div v-else class="mb-3 space-y-2">
              <input
                v-model="editContent.reviews.label"
                type="text"
                placeholder="Label..."
                class="w-full max-w-md mx-auto p-2 bg-gray-800 text-white rounded border-2 border-white"
              />
              <div class="flex gap-2 justify-center items-center">
                <label class="text-sm">Label color:</label>
                <input
                  v-model="editContent.reviews.labelColor"
                  type="color"
                  class="p-1 bg-gray-800 rounded border-2 border-white"
                />
              </div>
            </div>
            
            <h3 
              v-if="!isEditing"
              class="text-4xl md:text-5xl font-bold mb-4"
              :style="{ color: content.reviews.titleColor }"
            >
              {{ content.reviews.title }}
            </h3>
            <div v-else class="space-y-2 mb-4">
              <input
                v-model="editContent.reviews.title"
                type="text"
                placeholder="Title..."
                class="w-full max-w-md mx-auto p-3 bg-gray-800 text-white rounded border-2 border-white"
              />
              <div class="flex gap-2 justify-center items-center">
                <label class="text-sm">Title color:</label>
                <input
                  v-model="editContent.reviews.titleColor"
                  type="color"
                  class="p-1 bg-gray-800 rounded border-2 border-white"
                />
              </div>
            </div>

            <p v-if="!isEditing" class="text-gray-300 text-lg">
              Mida meie kliendid Burger Primo kogemuse kohta ütlevad
            </p>
          </div>
          
          <div class="grid md:grid-cols-3 gap-6">
            <div v-for="(review, i) in reviews" :key="i" class="bg-[#1a1a1a] p-8 rounded-lg">
              <div class="flex gap-1 mb-4">
                <span v-for="j in 5" :key="j" class="text-yellow-500 text-xl">★</span>
              </div>
              <p class="text-gray-200 mb-6 text-base leading-relaxed">{{ review.text }}</p>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center">
                  <span class="text-white font-semibold">{{ review.initial }}</span>
                </div>
                <p class="text-sm text-gray-300 font-medium">{{ review.name }}</p>
              </div>
            </div>
          </div>
        </template>
      </EditableSection>
    </section>

    <footer class="bg-black py-12 border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-6 text-center">
        <h3 class="text-2xl font-bold mb-2">Burger Primo</h3>
        <p class="text-gray-400 mb-1">Kirglikult valmistatud, unkusega serveeritud</p>
        <p class="text-gray-500 text-sm">© 2026 Burger Primo. Kõik õigused kaitstud.</p>
      </div>
    </footer>
  </div>
</template>