<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import EntertainmentSection from '@/components/sections/EntertainmentSection.vue'
import AnnouncementBanner from '@/components/AnnouncementBanner.vue'

interface Announcement {
  id: number
  title: string
  message: string
  type: string
  bg_color: string
  text_color: string
}

defineProps<{ announcements?: Announcement[] }>()
</script>

<template>
  <Head title="Meelelahutus — Burger Primo" />

  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <Navbar />
    <AnnouncementBanner
      v-if="announcements && announcements.length > 0"
      :announcements="announcements"
    />

    <div class="relative overflow-hidden pt-20">
      <div
        class="absolute inset-0 bg-cover bg-center"
        style="background-image: url('/img/pool25.jpg'); filter: blur(3px) brightness(0.35);"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0B0B0B]" />
      <div class="relative z-10 py-20 text-center px-6">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-semibold uppercase tracking-widest mb-5">
          Meelelahutus
        </div>
        <h1 class="text-5xl md:text-6xl font-bold text-white mb-3">
          Lõbu ja <span class="text-[#D2691E]">mäng</span>
        </h1>
        <p class="text-gray-400 text-lg">Burger Primos</p>
      </div>
    </div>

    <main>
      <EntertainmentSection />
    </main>

    <Footer />
  </div>
</template>
