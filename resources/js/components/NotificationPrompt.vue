<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Bell, X } from 'lucide-vue-next'

const STORAGE_KEY = 'push_prompt_dismissed'

const visible = ref(false)

onMounted(() => {
    if (!('Notification' in window) || !('serviceWorker' in navigator) || !('PushManager' in window)) return
    if (Notification.permission !== 'default') return
    if (localStorage.getItem(STORAGE_KEY)) return

    // Slight delay so the page settles before showing the prompt
    setTimeout(() => { visible.value = true }, 2000)
})

function dismiss() {
    visible.value = false
    localStorage.setItem(STORAGE_KEY, '1')
}

async function enable() {
    visible.value = false
    const fn = (window as any).__requestPushPermission
    if (typeof fn === 'function') {
        const result = await fn()
        if (result !== 'granted') {
            localStorage.setItem(STORAGE_KEY, '1')
        }
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div
                v-if="visible"
                class="fixed bottom-6 right-4 z-50 max-w-sm w-[calc(100vw-2rem)] sm:w-80"
            >
                <div class="bg-[#161616] border border-white/10 rounded-2xl shadow-2xl shadow-black/60 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center">
                            <Bell :size="18" class="text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white leading-snug">Luba teavitused</p>
                            <p class="text-xs text-white/50 mt-0.5 leading-relaxed">Saa teada uutest pakkumistest ja uudistest esimesena.</p>
                        </div>
                        <button
                            @click="dismiss"
                            class="flex-shrink-0 text-white/30 hover:text-white/60 transition-colors cursor-pointer p-0.5 -mt-0.5"
                            aria-label="Sulge"
                        >
                            <X :size="16" />
                        </button>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button
                            @click="enable"
                            class="flex-1 bg-amber-500 hover:bg-amber-400 text-black text-xs font-semibold py-2 rounded-lg transition-colors cursor-pointer"
                        >
                            Luba
                        </button>
                        <button
                            @click="dismiss"
                            class="flex-1 bg-white/5 hover:bg-white/10 text-white/60 hover:text-white/80 text-xs font-medium py-2 rounded-lg transition-colors cursor-pointer"
                        >
                            Mitte praegu
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
