<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import AnnouncementBanner from '@/components/AnnouncementBanner.vue'
import HeroSection from '@/components/sections/HeroSection.vue'
import PopularSection from '@/components/sections/PopularSection.vue'
import EntertainmentSection from '@/components/sections/EntertainmentSection.vue'
import ReviewsSection from '@/components/sections/ReviewsSection.vue'
import ContactSection from '@/components/sections/ContactSection.vue'
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
  discount_percentage: number | null
  category: { id: number; name: string }
}

interface Review {
  id: number
  name: string
  content: string
  rating: number
}

interface Announcement {
  id: number
  title: string
  message: string
  type: string
  bg_color: string
  text_color: string
}

defineProps<{
  featuredItems?: MenuItem[]
  reviews?: Review[]
  announcements?: Announcement[]
}>()
</script>

<template>
  <Head title="Avaleht" />

  <div class="page-bg" aria-hidden="true">
    <div class="page-bg-img" />
    <div class="page-bg-overlay" />
  </div>

  <div class="page-root text-white overflow-x-hidden">
    <Navbar />
    <AnnouncementBanner
      v-if="announcements && announcements.length > 0"
      :announcements="announcements"
    />

    <main class="flex flex-col gap-24 pb-24">
      <HeroSection />
      <PopularSection :featuredItems="featuredItems" />
      <EntertainmentSection />
      <ReviewsSection :reviews="reviews" />
      <ContactSection />
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.page-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
}
.page-bg-img {
  width: 100%;
  height: 100%;
  background-image: url('/img/main25.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  filter: brightness(0.28) saturate(0.65);
}
.page-bg-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.55) 0%,
    rgba(0, 0, 0, 0.35) 40%,
    rgba(0, 0, 0, 0.50) 100%
  );
}

/* Content wrapper — sits above the fixed background */
.page-root {
  position: relative;
  z-index: 1;
  min-height: 100vh;
}
</style>