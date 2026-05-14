import { ref, computed } from 'vue'
import et from '@/locales/et'

export type Locale = 'et' | 'en'

const locale = ref<Locale>(
    (typeof localStorage !== 'undefined'
        ? (localStorage.getItem('locale') as Locale | null)
        : null) ?? 'et'
)

const translations: Record<Locale, Record<string, string>> = {
    et,
    en: {},
}

// Eagerly load EN in background if user already has EN selected
if (locale.value === 'en') {
    import('@/locales/en').then(m => { translations.en = m.default })
}

export function useI18n() {
    function setLocale(l: Locale) {
        if (l === 'en' && Object.keys(translations.en).length === 0) {
            import('@/locales/en').then(m => {
                translations.en = m.default
                locale.value = l
            })
        } else {
            locale.value = l
        }
        if (typeof localStorage !== 'undefined') localStorage.setItem('locale', l)
    }

    function t(key: string): string {
        const dict = translations[locale.value] as Record<string, string>
        return dict[key] ?? (translations.et[key] ?? key)
    }

    return { locale: computed(() => locale.value), setLocale, t }
}
