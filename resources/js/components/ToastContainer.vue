<script setup lang="ts">
import { useToast } from '@/composables/useToast';

const { toasts, dismiss } = useToast();
</script>

<template>
  <Teleport to="body">
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col items-center gap-2 pointer-events-none">
      <TransitionGroup
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-4 scale-95"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          @click="dismiss(toast.id)"
          class="pointer-events-auto flex items-center gap-3 min-w-[280px] max-w-sm px-4 py-3 rounded-2xl shadow-2xl shadow-black/50 border cursor-pointer select-none"
          :class="{
            'bg-[#0f1f0f] border-green-500/30 text-green-300': toast.type === 'success',
            'bg-[#1f0f0f] border-red-500/30   text-red-300':   toast.type === 'error',
            'bg-[#1f180a] border-yellow-500/30 text-yellow-300': toast.type === 'warning',
            'bg-[#0d1520] border-blue-500/30   text-blue-300':  toast.type === 'info',
          }"
        >
          <!-- Icon -->
          <div
            class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center"
            :class="{
              'bg-green-500/20':  toast.type === 'success',
              'bg-red-500/20':    toast.type === 'error',
              'bg-yellow-500/20': toast.type === 'warning',
              'bg-blue-500/20':   toast.type === 'info',
            }"
          >
            <!-- Success check -->
            <svg v-if="toast.type === 'success'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <!-- Error X -->
            <svg v-else-if="toast.type === 'error'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <!-- Warning ! -->
            <svg v-else-if="toast.type === 'warning'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <!-- Info i -->
            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>

          <span class="text-sm font-semibold flex-1">{{ toast.message }}</span>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>
