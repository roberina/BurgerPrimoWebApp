<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const { success, error } = useToast();
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
import Navbar from '@/components/Navbar.vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

const props = defineProps<Props>();

const page = usePage();
const user = computed(() => page.props.auth?.user as any);

// Profile form
const profileForm = useForm({
    name: user.value?.name ?? '',
    email: user.value?.email ?? '',
});

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Delete form
const deleteForm = useForm({
    password: '',
});

const activeTab = ref<'profile' | 'password' | 'danger'>('profile');
const showDeleteModal = ref(false);
const deletePassword = ref('');

const submitProfile = () => {
    profileForm.patch('/settings/profile', {
        preserveScroll: true,
        onSuccess: () => success(t('settings.profile.saved')),
        onError: () => error('Profiili uuendamine ebaõnnestus'),
    });
};

const submitPassword = () => {
    passwordForm.put('/settings/password', {
        preserveScroll: true,
        onSuccess: () => { passwordForm.reset(); success(t('settings.password.updated')); },
        onError: () => error('Parooli muutmine ebaõnnestus'),
    });
};

const confirmDelete = () => {
    deleteForm.password = deletePassword.value;
    deleteForm.delete('/settings/profile', {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
};

const tabs = computed(() => [
    { id: 'profile', label: t('settings.profile.tab'), icon: 'user' },
    { id: 'password', label: t('settings.password.tab'), icon: 'lock' },
    { id: 'danger', label: t('settings.danger.tab'), icon: 'trash' },
]);
</script>

<template>
    <Head title="Profiil" />

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
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id as any"
                    :class="[
                        'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200',
                        activeTab === tab.id
                            ? 'bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white shadow-lg shadow-[#D2691E]/20'
                            : 'text-gray-500 hover:text-gray-300 hover:bg-[#141414]'
                    ]"
                >
                    <svg v-if="tab.icon === 'user'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <svg v-if="tab.icon === 'lock'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <svg v-if="tab.icon === 'trash'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="hidden sm:inline">{{ tab.label }}</span>
                </button>
            </div>

            <!-- ── PROFILE TAB ── -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                mode="out-in"
            >
                <div v-if="activeTab === 'profile'" key="profile">
                    <div class="bg-[#0E0E0E] border border-[#1A1A1A] rounded-2xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-[#1A1A1A] flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#D2691E]/10 border border-[#D2691E]/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#D2691E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-white font-semibold text-sm">{{ t('settings.profile.heading') }}</h2>
                                <p class="text-gray-500 text-xs">{{ t('settings.profile.sub') }}</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitProfile" class="p-6 space-y-5">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ t('settings.profile.name') }}</label>
                                <input
                                    id="name"
                                    v-model="profileForm.name"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    :placeholder="t('settings.profile.name.ph')"
                                    class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                                />
                                <p v-if="profileForm.errors.name" class="mt-1.5 text-xs text-red-400">{{ profileForm.errors.name }}</p>
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ t('settings.profile.email') }}</label>
                                <input
                                    id="email"
                                    v-model="profileForm.email"
                                    type="email"
                                    required
                                    autocomplete="username"
                                    placeholder="sinu@email.ee"
                                    class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                                />
                                <p v-if="profileForm.errors.email" class="mt-1.5 text-xs text-red-400">{{ profileForm.errors.email }}</p>
                            </div>

                            <!-- Email unverified notice -->
                            <div v-if="mustVerifyEmail && !user?.email_verified_at" class="flex items-start gap-3 p-4 bg-yellow-500/5 border border-yellow-500/20 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-yellow-400 text-sm font-medium">{{ t('settings.profile.unverified') }}</p>
                                    <p class="text-yellow-500/70 text-xs mt-0.5">
                                        {{ t('settings.profile.unverified.sub') }}
                                        <Link href="/email/verification-notification" method="post" as="button" class="underline text-yellow-400 hover:text-yellow-300 transition-colors">
                                            Saada kinnituslink uuesti.
                                        </Link>
                                    </p>
                                    <p v-if="status === 'verification-link-sent'" class="mt-2 text-xs font-medium text-green-400">
                                        Uus kinnituslink on saadetud sinu e-postile.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-2">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white hover:from-[#E07A2E] hover:to-[#D2691E] shadow-lg shadow-[#D2691E]/25 hover:shadow-[#D2691E]/40 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ profileForm.processing ? t('settings.profile.saving') : t('settings.profile.save') }}
                                </button>

                                <Transition
                                    enter-active-class="transition ease-in-out duration-300"
                                    enter-from-class="opacity-0 translate-x-2"
                                    enter-to-class="opacity-100 translate-x-0"
                                    leave-active-class="transition ease-in-out duration-200"
                                    leave-to-class="opacity-0"
                                >
                                    <span v-if="profileForm.recentlySuccessful" class="flex items-center gap-1.5 text-sm text-green-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Salvestatud!
                                    </span>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ── PASSWORD TAB ── -->
                <div v-else-if="activeTab === 'password'" key="password">
                    <div class="bg-[#0E0E0E] border border-[#1A1A1A] rounded-2xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-[#1A1A1A] flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#D2691E]/10 border border-[#D2691E]/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#D2691E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-white font-semibold text-sm">Parool</h2>
                                <p class="text-gray-500 text-xs">Kasuta tugevat ja unikaalset parooli</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitPassword" class="p-6 space-y-5">
                            <div>
                                <label for="current_password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ t('settings.password.current') }}</label>
                                <input
                                    id="current_password"
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                                />
                                <p v-if="passwordForm.errors.current_password" class="mt-1.5 text-xs text-red-400">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ t('settings.password.new') }}</label>
                                <input
                                    id="password"
                                    v-model="passwordForm.password"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                                />
                                <p v-if="passwordForm.errors.password" class="mt-1.5 text-xs text-red-400">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ t('settings.password.confirm') }}</label>
                                <input
                                    id="password_confirmation"
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-[#D2691E]/60 focus:bg-[#161616] focus:ring-1 focus:ring-[#D2691E]/30 transition-all duration-200"
                                />
                                <p v-if="passwordForm.errors.password_confirmation" class="mt-1.5 text-xs text-red-400">{{ passwordForm.errors.password_confirmation }}</p>
                            </div>

                            <div class="flex items-center gap-4 pt-2">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-[#D2691E] to-[#B8571A] text-white hover:from-[#E07A2E] hover:to-[#D2691E] shadow-lg shadow-[#D2691E]/25 hover:shadow-[#D2691E]/40 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ passwordForm.processing ? t('settings.password.updating') : t('settings.password.submit') }}
                                </button>

                                <Transition
                                    enter-active-class="transition ease-in-out duration-300"
                                    enter-from-class="opacity-0 translate-x-2"
                                    enter-to-class="opacity-100 translate-x-0"
                                    leave-active-class="transition ease-in-out duration-200"
                                    leave-to-class="opacity-0"
                                >
                                    <span v-if="passwordForm.recentlySuccessful" class="flex items-center gap-1.5 text-sm text-green-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Parool uuendatud!
                                    </span>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ── DANGER TAB ── -->
                <div v-else-if="activeTab === 'danger'" key="danger">
                    <div class="bg-[#0E0E0E] border border-red-500/20 rounded-2xl overflow-hidden">
                        <div class="px-6 py-5 border-b border-red-500/10 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-red-400 font-semibold text-sm">{{ t('settings.danger.heading') }}</h2>
                                <p class="text-gray-500 text-xs">{{ t('settings.danger.sub') }}</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div>
                                    <h3 class="text-white font-semibold mb-1">{{ t('settings.danger.title') }}</h3>
                                    <p class="text-gray-500 text-sm leading-relaxed">
                                        {{ t('settings.danger.desc') }}
                                    </p>
                                </div>
                                <button
                                    @click="showDeleteModal = true"
                                    class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-semibold bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 hover:border-red-500/50 hover:text-red-300 transition-all duration-200"
                                >
                                    Kustuta konto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- ── DELETE MODAL ── -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showDeleteModal = false" />
                <div class="relative w-full max-w-md bg-[#0E0E0E] border border-red-500/20 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-red-500/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold">{{ t('settings.modal.sure') }}</h3>
                                <p class="text-gray-500 text-xs">{{ t('settings.modal.irreversible') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ t('settings.modal.desc') }}
                        </p>

                        <div>
                            <label for="delete_password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ t('settings.modal.password') }}</label>
                            <input
                                id="delete_password"
                                v-model="deletePassword"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 bg-[#141414] border border-[#1E1E1E] rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-red-500/60 focus:ring-1 focus:ring-red-500/30 transition-all duration-200"
                            />
                            <p v-if="deleteForm.errors.password" class="mt-1.5 text-xs text-red-400">{{ deleteForm.errors.password }}</p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button
                                @click="showDeleteModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-400 bg-[#141414] border border-[#1E1E1E] hover:text-white hover:border-[#2A2A2A] transition-all duration-200"
                            >
                                Tühista
                            </button>
                            <button
                                @click="confirmDelete"
                                :disabled="deleteForm.processing || !deletePassword"
                                class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:border-red-500 hover:text-white transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                {{ deleteForm.processing ? t('settings.modal.deleting') : t('settings.modal.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>