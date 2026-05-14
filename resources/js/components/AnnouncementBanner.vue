<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Megaphone, X, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { useI18n } from '@/composables/useI18n'

const { t, locale } = useI18n()
const ln = (et: string, en: string | null | undefined) => (locale.value === 'en' && en) ? en : et

interface Announcement {
  id: number
  title: string
  title_en?: string | null
  message: string
  message_en?: string | null
  type: string
  bg_color: string
  text_color: string
}

const props = defineProps<{
  announcements: Announcement[]
}>()

const STORAGE_KEY = 'announcements_dismissed'
const dismissed = ref(sessionStorage.getItem(STORAGE_KEY) === '1')
const current = ref(0)
const isHovered = ref(false)
const transitioning = ref(false)

const prev = () => {
  current.value = (current.value - 1 + props.announcements.length) % props.announcements.length
}

const next = () => {
  transitioning.value = true
  setTimeout(() => {
    current.value = (current.value + 1) % props.announcements.length
    transitioning.value = false
  }, 180)
}

const active = () => props.announcements[current.value]

const dismiss = () => {
  dismissed.value = true
  sessionStorage.setItem(STORAGE_KEY, '1')
}

let rotateTimer: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  if (props.announcements.length > 1) {
    rotateTimer = setInterval(() => {
      if (!isHovered.value) next()
    }, 15000)
  }
})

onUnmounted(() => {
  if (rotateTimer) clearInterval(rotateTimer)
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="!dismissed && announcements.length > 0"
        class="fixed top-16 lg:top-20 left-0 right-0 z-30 w-full"
        :style="{ backgroundColor: active().bg_color, color: active().text_color }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
      >
        <!-- Desktop row layout -->
        <div class="hidden sm:flex max-w-6xl mx-auto px-4 py-2.5 items-center gap-3">
          <Megaphone :size="18" class="flex-shrink-0 opacity-80" />

          <button
            v-if="announcements.length > 1"
            @click="prev"
            :aria-label="t('aria.prev')"
            class="flex-shrink-0 hover:opacity-70 transition p-0.5 rounded"
          >
            <ChevronLeft :size="18" />
          </button>

          <div
            class="flex-1 flex items-center gap-2 min-w-0 justify-center text-center transition-opacity duration-180"
            :class="transitioning ? 'opacity-0' : 'opacity-100'"
          >
            <p class="text-sm font-semibold truncate">{{ ln(active().title, active().title_en) }}</p>
            <span class="opacity-60 text-xs">·</span>
            <p class="text-xs opacity-80 truncate">{{ ln(active().message, active().message_en) }}</p>
          </div>

          <button
            v-if="announcements.length > 1"
            @click="next"
            :aria-label="t('aria.next')"
            class="flex-shrink-0 hover:opacity-70 transition p-0.5 rounded"
          >
            <ChevronRight :size="18" />
          </button>

          <div v-if="announcements.length > 1" class="flex items-center gap-0 shrink-0">
            <button
              v-for="(_, i) in announcements"
              :key="i"
              @click="current = i"
              :aria-label="`${t('aria.announcement')} ${i + 1}`"
              :aria-current="i === current ? 'true' : undefined"
              class="h-6 w-6 flex items-center justify-center cursor-pointer"
            >
              <span
                class="block w-1.5 h-1.5 rounded-full transition-all"
                :class="i === current ? 'opacity-100 scale-125' : 'opacity-40'"
                :style="{ backgroundColor: active().text_color }"
              />
            </button>
          </div>

          <button
            @click="dismiss"
            class="flex-shrink-0 hover:opacity-70 transition p-1 rounded hover:bg-black/10"
            aria-label="Sulge"
          >
            <X :size="16" />
          </button>
        </div>

        <!-- Mobile layout -->
        <div class="sm:hidden px-4 pt-2.5 pb-2" :class="transitioning ? 'opacity-0' : 'opacity-100'" style="transition: opacity 0.18s;">
          <!-- Row 1: title -->
          <p class="text-base font-semibold text-center truncate mb-1">{{ ln(active().title, active().title_en) }}</p>

          <!-- Row 2: 🔊 ← | message | → X -->
          <div class="flex items-center gap-1.5">
            <Megaphone :size="20" class="shrink-0 opacity-80" />
            <button
              v-if="announcements.length > 1"
              @click="prev"
              :aria-label="t('aria.prev')"
              class="shrink-0 hover:opacity-70 transition p-0.5 rounded"
            >
              <ChevronLeft :size="22" />
            </button>
            <p class="flex-1 min-w-0 text-sm opacity-90 text-center truncate">{{ ln(active().message, active().message_en) }}</p>
            <button
              v-if="announcements.length > 1"
              @click="next"
              :aria-label="t('aria.next')"
              class="shrink-0 hover:opacity-70 transition p-0.5 rounded"
            >
              <ChevronRight :size="22" />
            </button>
            <button
              @click="dismiss"
              class="shrink-0 hover:opacity-70 transition p-1 rounded hover:bg-black/10"
              aria-label="Sulge"
            >
              <X :size="20" />
            </button>
          </div>

          <!-- Row 3: dots -->
          <div v-if="announcements.length > 1" class="flex justify-center items-center gap-0 mt-1.5">
            <button
              v-for="(_, i) in announcements"
              :key="i"
              @click="current = i"
              :aria-label="`${t('aria.announcement')} ${i + 1}`"
              :aria-current="i === current ? 'true' : undefined"
              class="h-5 w-6 flex items-center justify-center cursor-pointer"
            >
              <span
                class="block w-1.5 h-1.5 rounded-full transition-all"
                :class="i === current ? 'opacity-100 scale-125' : 'opacity-40'"
                :style="{ backgroundColor: active().text_color }"
              />
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>