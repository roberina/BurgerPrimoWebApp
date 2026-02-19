<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Navbar from '@/components/Navbar.vue'
import HeroSection from '@/components/sections/HeroSection.vue'
import PopularSection from '@/components/sections/PopularSection.vue'
import ContactSection from '@/components/sections/ContactSection.vue'
import EntertainmentSection from '@/components/sections/EntertainmentSection.vue'
import ReviewsSection from '@/components/sections/ReviewsSection.vue'
import ContactFormSection from '@/components/sections/ContactFormSection.vue'
import Footer from '@/components/Footer.vue'

interface MenuItem {
  id: number
  name: string
  description: string
  price: number
  original_price: number | null
  image: string | null
  image_url: string | null
  is_featured: boolean
  category: {
    id: number
    name: string
  }
}

defineProps<{
  featuredItems?: MenuItem[]
}>()

const cartItems = ref([])
</script>

<template>
  <Head title="Avaleht" />
  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <Navbar :cartCount="cartItems.length" />

    <HeroSection />
    <PopularSection :featuredItems="featuredItems" />
    <ContactSection />
    <EntertainmentSection />
    <ReviewsSection />
    <ContactFormSection />

  </div>

  <Footer />
</template>