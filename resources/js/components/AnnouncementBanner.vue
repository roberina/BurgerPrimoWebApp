<script setup lang="ts">
import { ref } from 'vue'
import { Megaphone, X, ChevronLeft, ChevronRight } from 'lucide-vue-next'

interface Announcement {
  id: number
  title: string
  message: string
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

const prev = () => {
  current.value = (current.value - 1 + props.announcements.length) % props.announcements.length
}

const next = () => {
  current.value = (current.value + 1) % props.announcements.length
}

const active = () => props.announcements[current.value]

const dismiss = () => {
  dismissed.value = true
  sessionStorage.setItem(STORAGE_KEY, '1')
}
</script>

<template>
  <!--
    Announcement banner sits just BELOW the fixed navbar.
    Navbar height: h-16 (4rem) on mobile, h-20 (5rem) on lg.
    We use top-16 lg:top-20 to position it correctly.
  -->
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
        class="fixed top-16 lg:top-20 left-0 right-0 z-40 w-full"
        :style="{ backgroundColor: active().bg_color, color: active().text_color }"
      >
        <div class="max-w-6xl mx-auto px-4 py-2.5 flex items-center gap-3">
          <Megaphone :size="18" class="flex-shrink-0 opacity-80" />

          <button
            v-if="announcements.length > 1"
            @click="prev"
            class="flex-shrink-0 hover:opacity-70 transition p-0.5 rounded"
          >
            <ChevronLeft :size="18" />
          </button>

          <div class="flex-1 flex items-center gap-2 min-w-0 justify-center text-center">
            <p class="text-sm font-semibold truncate">{{ active().title }}</p>
            <span class="hidden sm:inline opacity-60 text-xs">·</span>
            <p class="hidden sm:block text-xs opacity-80 truncate">{{ active().message }}</p>
          </div>

          <button
            v-if="announcements.length > 1"
            @click="next"
            class="flex-shrink-0 hover:opacity-70 transition p-0.5 rounded"
          >
            <ChevronRight :size="18" />
          </button>

          <div v-if="announcements.length > 1" class="flex items-center gap-1 flex-shrink-0">
            <span
              v-for="(_, i) in announcements"
              :key="i"
              @click="current = i"
              class="w-1.5 h-1.5 rounded-full cursor-pointer transition-all"
              :class="i === current ? 'opacity-100 scale-125' : 'opacity-40'"
              :style="{ backgroundColor: active().text_color }"
            />
          </div>

          <button
            @click="dismiss"
            class="flex-shrink-0 hover:opacity-70 transition p-1 rounded hover:bg-black/10"
            aria-label="Sulge"
          >
            <X :size="16" />
          </button>
        </div>

        <div
          v-if="active().message"
          class="sm:hidden px-4 pb-2 text-xs opacity-80 text-center"
        >
          {{ active().message }}
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
