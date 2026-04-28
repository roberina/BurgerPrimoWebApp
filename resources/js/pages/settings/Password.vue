<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
import Navbar from '@/components/Navbar.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user as any);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put('/settings/password', {
        preserveScroll: true,
        onSuccess: () => { form.reset(); success(t('settings.password.updated')); },
        onError: () => {
            error('Parooli muutmine ebaõnnestus');
            if (form.errors.password) form.reset('password', 'password_confirmation');
            if (form.errors.current_password) form.reset('current_password');
        },
    });
};
</script>

<template>
    <Head title="Parool" />

    <div class="min-h-screen bg-[#080808] pt-16 lg:pt-20">
        <Navbar />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            <!-- Page Header -->
            <div class="mb-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-[#D2691E] to-[#B8571A] rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-[#D2691E]/30">
                        {{ user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ user?.name }}</h1>
                        <p class="text-gray-500 text-sm">{{ user?.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex gap-1 mb-8 bg-[#0E0E0E] border border-[#1A1A1A] rounded-xl p-1">
                <Link
                    href="/settings/profile"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-500 hover:text-gray-300 hover:bg-[#141414]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="hidden sm:inline">{{ t('settings.profile.tab') }}</span>
                </Link>
                <Link
                    href="/settings/password"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white shadow-lg shadow-[#D2691E]/20"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="hidden sm:inline">{{ t('settings.password.tab') }}</span>
                </Link>
                <Link
                    href="/settings/profile#danger"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 text-gray-500 hover:text-gray-300 hover:bg-[#141414]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="hidden sm:inline">{{ t('settings.danger.tab') }}</span>
                </Link>
            </div>

            <!-- Password Card -->
            <div class="bg-[#0E0E0E] border border-[#1A1A1A] rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-[#1A1A1A] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#D2691E]/10 border border-[#D2691E]/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#D2691E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-semibold text-sm">{{ t('settings.password.heading') }}</h2>
                        <p class="text-gray-500 text-xs">{{ t('settings.password.sub') }}</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-5">
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            {{ t('settings.password.current') }}
                        </label>
                        <input
                            id="current_password"
                            v-model="form.current_password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                            :class="{ 'border-red-500/60': form.errors.current_password }"
                        />
                        <p v-if="form.errors.current_password" class="mt-1.5 text-xs text-red-400">
                            {{ form.errors.current_password }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            {{ t('settings.password.new') }}
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                            :class="{ 'border-red-500/60': form.errors.password }"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-400">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            {{ t('settings.password.confirm') }}
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                            :class="{ 'border-red-500/60': form.errors.password_confirmation }"
                        />
                        <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-400">
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white hover:from-[#E07A2E] hover:to-[#D2691E] shadow-lg shadow-[#D2691E]/25 hover:shadow-[#D2691E]/40 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? t('settings.password.updating') : t('settings.password.submit') }}
                        </button>

                        <Transition
                            enter-active-class="transition ease-in-out duration-300"
                            enter-from-class="opacity-0 translate-x-2"
                            enter-to-class="opacity-100 translate-x-0"
                            leave-active-class="transition ease-in-out duration-200"
                            leave-to-class="opacity-0"
                        >
                            <span v-if="form.recentlySuccessful" class="flex items-center gap-1.5 text-sm text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ t('settings.password.updated') }}
                            </span>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>