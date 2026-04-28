<script setup lang="ts">
/**
 * /kontakt route — kept for backward compatibility.
 * The navbar on the home page links to /#contact for smooth scroll.
 * This page serves users who navigate directly to /kontakt.
 * Google Maps has been removed per requirements.
 */
import { Head } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import ContactSection from '@/components/sections/ContactSection.vue'
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
  <Head title="Kontakt — Burger Primo">
    <meta name="description" content="Võta Burger Primoga ühendust. Leiad meid Kuressaarest – kirjuta meile või tule kohale!" head-key="description" />
    <meta name="robots" content="index, follow" head-key="robots" />
    <meta property="og:title" content="Kontakt — Burger Primo" head-key="og:title" />
    <meta property="og:description" content="Võta Burger Primoga ühendust. Leiad meid Kuressaarest – kirjuta meile või tule kohale!" head-key="og:description" />
    <meta property="og:type" content="website" head-key="og:type" />
    <meta property="og:url" content="https://burgerprimo.ee/kontakt" head-key="og:url" />
    <meta property="og:image" content="/img/Logo45.png" head-key="og:image" />
  </Head>

  <div class="min-h-screen bg-[#0B0B0B] text-white">
    <Navbar />
    <AnnouncementBanner
      v-if="announcements && announcements.length > 0"
      :announcements="announcements"
    />

    <div class="relative overflow-hidden pt-20">
      <div
        class="absolute inset-0 bg-cover bg-center"
        style="background-image: url('/img/main25.jpg'); filter: blur(3px) brightness(0.3);"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0B0B0B]" />
      <div class="relative z-10 py-20 text-center px-6">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-semibold uppercase tracking-widest mb-5">
          📍 Kontakt
        </div>
        <h1 class="text-5xl md:text-6xl font-bold text-white mb-3">
          Võta <span class="text-[#D2691E]">ühendust</span>
        </h1>
        <p class="text-gray-400 text-lg">Oleme alati teie jaoks olemas</p>
      </div>
    </div>

    <main>
      <ContactSection />
    </main>

    <Footer />
  </div>
</template>