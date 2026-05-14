<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, Megaphone, X, Check, ChevronLeft, ChevronRight, Calendar } from 'lucide-vue-next'
import { useToast } from '@/composables/useToast'

const { success, error } = useToast()

interface Announcement {
  id: number
  title: string
  title_en: string | null
  message: string
  message_en: string | null
  type: 'info' | 'warning' | 'success' | 'promo'
  bg_color: string
  text_color: string
  is_active: boolean
  starts_at: string | null
  ends_at: string | null
  sort_order: number
  created_at: string
}

const props = defineProps<{ announcements: Announcement[] }>()
const showModal = ref(false)
const editingId = ref<number | null>(null)
const modalEl = ref<HTMLElement | null>(null)
let touchStartY = 0

function handleModalScroll(e: WheelEvent) {
  e.preventDefault()
  const el = modalEl.value
  if (!el) return
  el.scrollTop += e.deltaY
}

function handleTouchStart(e: TouchEvent) { touchStartY = e.touches[0].clientY }

function handleTouchMove(e: TouchEvent) {
  e.preventDefault()
  const el = modalEl.value
  if (!el) return
  const delta = touchStartY - e.touches[0].clientY
  el.scrollTop += delta
  touchStartY = e.touches[0].clientY
}

const emptyForm = () => ({
  title: '',
  title_en: '',
  message: '',
  message_en: '',
  type: 'info' as const,
  bg_color: '#ea580c',
  text_color: '#FFFFFF',
  is_active: true,
  starts_at: '',
  ends_at: '',
  sort_order: 0,
})

const form = reactive(emptyForm())

const hueSwatches = [0, 20, 35, 55, 100, 160, 200, 220, 260, 290, 320]
const satSteps = [20, 40, 60, 80, 100]
const lightSteps = [85, 70, 55, 40, 25, 12]

interface ColorPicker { open: boolean; target: 'bg_color' | 'text_color'; hue: number; saturation: number; lightness: number }
const colorPicker = reactive<ColorPicker>({ open: false, target: 'bg_color', hue: 25, saturation: 65, lightness: 45 })

function hslToHex(h: number, s: number, l: number): string {
  s /= 100; l /= 100
  const k = (n: number) => (n + h / 30) % 12
  const a = s * Math.min(l, 1 - l)
  const f = (n: number) => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)))
  return '#' + [f(0), f(8), f(4)].map(x => Math.round(x * 255).toString(16).padStart(2, '0')).join('')
}

function hexToHsl(hex: string) {
  let r = 0, g = 0, b = 0
  const c = hex.replace('#', '')
  if (c.length === 6) { r = parseInt(c.slice(0,2),16)/255; g = parseInt(c.slice(2,4),16)/255; b = parseInt(c.slice(4,6),16)/255 }
  const max = Math.max(r,g,b), min = Math.min(r,g,b)
  let h = 0, s = 0
  const l = (max+min)/2
  if (max !== min) {
    const d = max-min
    s = l > 0.5 ? d/(2-max-min) : d/(max+min)
    switch(max) { case r: h=((g-b)/d+(g<b?6:0))/6; break; case g: h=((b-r)/d+2)/6; break; case b: h=((r-g)/d+4)/6; break }
  }
  return { h: Math.round(h*360), s: Math.round(s*100), l: Math.round(l*100) }
}

const pickerPreview = computed(() => hslToHex(colorPicker.hue, colorPicker.saturation, colorPicker.lightness))

function openColorPicker(target: 'bg_color' | 'text_color') {
  colorPicker.target = target
  const { h, s, l } = hexToHsl(target === 'bg_color' ? form.bg_color : form.text_color)
  colorPicker.hue = h; colorPicker.saturation = s; colorPicker.lightness = l
  colorPicker.open = true
  cal.open = false
}

function applyColor() {
  const hex = hslToHex(colorPicker.hue, colorPicker.saturation, colorPicker.lightness)
  if (colorPicker.target === 'bg_color') form.bg_color = hex
  else form.text_color = hex
  colorPicker.open = false
}

interface CalState { open: boolean; target: 'starts_at' | 'ends_at'; viewYear: number; viewMonth: number; selectedDate: string }
const now = new Date()
const cal = reactive<CalState>({ open: false, target: 'starts_at', viewYear: now.getFullYear(), viewMonth: now.getMonth(), selectedDate: '' })

const monthNames = ['Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni','Juuli','August','September','Oktoober','November','Detsember']
const dayNames = ['E','T','K','N','R','L','P']

const calDays = computed(() => {
  let startDow = new Date(cal.viewYear, cal.viewMonth, 1).getDay() - 1
  if (startDow < 0) startDow = 6
  const dim = new Date(cal.viewYear, cal.viewMonth + 1, 0).getDate()
  const cells: (number|null)[] = []
  for (let i = 0; i < startDow; i++) cells.push(null)
  for (let d = 1; d <= dim; d++) cells.push(d)
  return cells
})

function openCal(target: 'starts_at' | 'ends_at') {
  cal.target = target; cal.open = true; colorPicker.open = false
  const existing = target === 'starts_at' ? form.starts_at : form.ends_at
  if (existing) {
    const parts = existing.split('T')[0].split('-')
    if (parts.length === 3) { cal.viewYear = +parts[0]; cal.viewMonth = +parts[1]-1; cal.selectedDate = parts.join('-') }
  } else { cal.viewYear = now.getFullYear(); cal.viewMonth = now.getMonth(); cal.selectedDate = '' }
}

function calPrev() { if (cal.viewMonth===0){cal.viewMonth=11;cal.viewYear--}else cal.viewMonth-- }
function calNext() { if (cal.viewMonth===11){cal.viewMonth=0;cal.viewYear++}else cal.viewMonth++ }
function selectDay(day: number|null) {
  if (!day) return
  cal.selectedDate = `${cal.viewYear}-${String(cal.viewMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`
}
function isSelected(day: number|null) {
  if (!day) return false
  return cal.selectedDate === `${cal.viewYear}-${String(cal.viewMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`
}
function isToday(day: number|null) {
  return !!day && day===now.getDate() && cal.viewMonth===now.getMonth() && cal.viewYear===now.getFullYear()
}
function applyCal() {
  const val = cal.selectedDate ? cal.selectedDate + 'T00:00' : ''
  if (cal.target === 'starts_at') form.starts_at = val
  else form.ends_at = val
  cal.open = false
}
function clearCal() {
  if (cal.target === 'starts_at') form.starts_at = ''
  else form.ends_at = ''
  cal.selectedDate = ''; cal.open = false
}
function displayDate(iso: string|null|undefined) {
  if (!iso) return ''
  const d = iso.split('T')[0].split('-')
  return d.length===3 ? `${d[2]}.${d[1]}.${d[0]}` : iso
}

const openCreate = () => { editingId.value = null; Object.assign(form, emptyForm()); showModal.value = true }
const openEdit = (a: Announcement) => {
  editingId.value = a.id
  Object.assign(form, { ...a, starts_at: a.starts_at ? a.starts_at.slice(0,16) : '', ends_at: a.ends_at ? a.ends_at.slice(0,16) : '' })
  showModal.value = true
}
const closeModal = () => { showModal.value = false; editingId.value = null; colorPicker.open = false; cal.open = false }

const submit = () => {
  const data = { ...form, is_active: form.is_active ? 1 : 0 }
  if (editingId.value) router.put(`/admin/announcements/${editingId.value}`, data, {
    preserveScroll: true,
    onSuccess: () => { closeModal(); success('Teadaanne uuendatud') },
    onError: () => error('Salvestamine ebaõnnestus'),
  })
  else router.post('/admin/announcements', data, {
    preserveScroll: true,
    onSuccess: () => { closeModal(); success('Teadaanne lisatud') },
    onError: () => error('Lisamine ebaõnnestus'),
  })
}

const toggle = (id: number) => router.post(`/admin/announcements/${id}/toggle`, {}, { preserveScroll: true, onSuccess: () => success('Olek uuendatud') })
const destroyModal = reactive({ show: false, id: 0 })
const confirmDestroy = (id: number) => { destroyModal.id = id; destroyModal.show = true }
const doDestroy = () => {
  router.delete(`/admin/announcements/${destroyModal.id}`, {
    preserveScroll: true,
    onSuccess: () => success('Teadaanne kustutatud'),
    onError: () => error('Kustutamine ebaõnnestus'),
  })
  destroyModal.show = false
}
const formatDate = (d: string|null) => d ? new Date(d).toLocaleDateString('et-EE', {day:'2-digit',month:'2-digit',year:'numeric'}) : '—'
</script>

<template>
  <Head title="Teadaanded" />
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <div>
          <h1 class="text-sm font-semibold text-zinc-100">Teadaanded</h1>
          <p class="text-xs text-zinc-500">{{ announcements.length }} teadaannet kokku</p>
        </div>
        <button @click="openCreate" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors">
          <Plus :size="13" />
          Lisa teadaanne
        </button>
      </div>
    </template>

    <!-- Delete confirm modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="destroyModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="destroyModal.show = false">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" />
          <div class="relative bg-[#18181b] border border-[#27272a] rounded-xl shadow-2xl w-full max-w-sm">
            <div class="p-5">
              <h3 class="text-sm font-semibold text-zinc-100 mb-1">Kustuta teadaanne</h3>
              <p class="text-xs text-zinc-400">Kas oled kindel, et soovid selle teadaande kustutada?</p>
              <div class="flex gap-2 mt-5">
                <button @click="destroyModal.show = false" class="flex-1 py-2 rounded-md border border-[#3f3f46] text-zinc-400 hover:text-zinc-100 hover:bg-[#27272a] text-xs font-medium transition-colors">Tühista</button>
                <button @click="doDestroy" class="flex-1 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors">Kustuta</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Create/Edit modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal" />
        <div
          ref="modalEl"
          class="relative bg-[#18181b] border border-[#27272a] rounded-xl w-full max-w-lg shadow-2xl max-h-[90vh]"
          style="overflow-y: hidden;"
          @wheel.stop="handleModalScroll"
          @touchstart.stop="handleTouchStart"
          @touchmove.stop="handleTouchMove"
        >
          <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-[#27272a]">
            <h2 class="text-sm font-semibold text-zinc-100">{{ editingId ? 'Muuda teadaannet' : 'Lisa teadaanne' }}</h2>
            <button @click="closeModal" class="p-1.5 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition"><X :size="15" /></button>
          </div>

          <div class="px-5 py-4 space-y-4">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Pealkiri * <span class="text-zinc-600">(ET)</span></label>
                <input v-model="form.title" type="text" placeholder="nt. Suvine promo aktiivne!" maxlength="255" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
              </div>
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Title <span class="text-zinc-600">(EN)</span></label>
                <input v-model="form.title_en" type="text" placeholder="e.g. Summer promo active!" maxlength="255" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Sõnum * <span class="text-zinc-600">(ET)</span></label>
                <textarea v-model="form.message" rows="3" placeholder="nt. Kasuta koodi BURGER20 ja saa 20% soodustust!" maxlength="1000" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors resize-none" />
              </div>
              <div>
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">Message <span class="text-zinc-600">(EN)</span></label>
                <textarea v-model="form.message_en" rows="3" placeholder="e.g. Use code BURGER20 for 20% off!" maxlength="1000" class="w-full bg-[#09090b] border border-[#3f3f46] rounded-md px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors resize-none" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div v-for="field in (['bg_color','text_color'] as const)" :key="field">
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ field === 'bg_color' ? 'Taustavärv' : 'Tekstivärv' }}</label>
                <button @click="openColorPicker(field)" class="w-full flex items-center gap-2 px-3 py-2 bg-[#09090b] border border-[#3f3f46] rounded-md hover:border-orange-500/50 transition group">
                  <span class="w-5 h-5 rounded flex-shrink-0 border border-white/10" :style="{ backgroundColor: field === 'bg_color' ? form.bg_color : form.text_color }" />
                  <span class="text-xs font-mono text-zinc-400 group-hover:text-zinc-100 transition flex-1 text-left">{{ field === 'bg_color' ? form.bg_color : form.text_color }}</span>
                </button>
              </div>
            </div>

            <!-- Color picker -->
            <div v-if="colorPicker.open" class="bg-[#09090b] border border-[#3f3f46] rounded-lg p-4 space-y-4">
              <p class="text-xs font-medium text-zinc-400">{{ colorPicker.target === 'bg_color' ? 'Taustavärv' : 'Tekstivärv' }} — vali toon</p>
              <div>
                <p class="text-xs text-zinc-600 mb-2">Põhivärv</p>
                <div class="flex flex-wrap gap-1.5">
                  <button v-for="h in hueSwatches" :key="h" @click="colorPicker.hue=h; colorPicker.saturation=Math.max(colorPicker.saturation,20)" class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110" :class="colorPicker.hue===h && colorPicker.saturation>10 ? 'border-white scale-110' : 'border-transparent'" :style="{ backgroundColor: hslToHex(h, 70, 50) }" />
                  <button @click="colorPicker.hue=0; colorPicker.saturation=0; colorPicker.lightness=100" class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110 bg-white" :class="colorPicker.saturation===0 && colorPicker.lightness>=95 ? 'border-orange-500 scale-110' : 'border-transparent'" />
                  <button @click="colorPicker.hue=0; colorPicker.saturation=0; colorPicker.lightness=8" class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110 bg-zinc-900" :class="colorPicker.saturation===0 && colorPicker.lightness<=10 ? 'border-orange-500 scale-110' : 'border-zinc-700'" />
                </div>
              </div>
              <div>
                <p class="text-xs text-zinc-600 mb-2">Toon</p>
                <div class="space-y-1">
                  <div v-for="l in lightSteps" :key="l" class="flex gap-1">
                    <button v-for="s in satSteps" :key="s" @click="colorPicker.saturation=s; colorPicker.lightness=l" class="flex-1 h-6 rounded border-2 transition-transform hover:scale-105" :class="colorPicker.saturation===s && colorPicker.lightness===l ? 'border-white scale-105' : 'border-transparent'" :style="{ backgroundColor: hslToHex(colorPicker.hue, s, l) }" />
                  </div>
                </div>
              </div>
              <div>
                <div class="flex justify-between text-xs text-zinc-600 mb-1"><span>Küllastus</span><span>{{ colorPicker.saturation }}%</span></div>
                <div class="relative h-5 flex items-center">
                  <div class="absolute inset-y-1 left-0 right-0 rounded-full" :style="{ background: `linear-gradient(to right, ${hslToHex(colorPicker.hue,0,colorPicker.lightness)}, ${hslToHex(colorPicker.hue,100,colorPicker.lightness)})` }" />
                  <input v-model.number="colorPicker.saturation" type="range" min="0" max="100" step="1" class="shade-slider relative w-full appearance-none bg-transparent h-5 cursor-pointer" />
                </div>
              </div>
              <div>
                <div class="flex justify-between text-xs text-zinc-600 mb-1"><span>Heledus</span><span>{{ colorPicker.lightness }}%</span></div>
                <div class="relative h-5 flex items-center">
                  <div class="absolute inset-y-1 left-0 right-0 rounded-full" :style="{ background: `linear-gradient(to right, #000, ${hslToHex(colorPicker.hue,colorPicker.saturation,50)}, #fff)` }" />
                  <input v-model.number="colorPicker.lightness" type="range" min="0" max="100" step="1" class="shade-slider relative w-full appearance-none bg-transparent h-5 cursor-pointer" />
                </div>
              </div>
              <div class="flex items-center gap-2 pt-1">
                <div class="flex-1 h-9 rounded-md border border-[#27272a] flex items-center justify-center" :style="{ backgroundColor: pickerPreview }">
                  <span class="text-xs font-mono font-bold" :style="{ color: colorPicker.lightness > 50 ? '#000' : '#fff' }">{{ pickerPreview }}</span>
                </div>
                <button @click="colorPicker.open=false" class="px-3 py-2 text-xs rounded-md bg-[#27272a] text-zinc-400 hover:text-zinc-100 transition">Tühista</button>
                <button @click="applyColor" class="px-3 py-2 text-xs rounded-md bg-orange-600 text-white font-medium hover:bg-orange-700 transition">Kinnita</button>
              </div>
            </div>

            <!-- Preview -->
            <div>
              <label class="block text-xs font-medium text-zinc-400 mb-1.5">Eelvaade</label>
              <div class="w-full px-4 py-3 rounded-md flex items-start gap-2.5 transition-all" :style="{ backgroundColor: form.bg_color, color: form.text_color }">
                <Megaphone :size="14" class="flex-shrink-0 mt-0.5 opacity-80" />
                <div>
                  <p class="font-semibold text-sm">{{ form.title || 'Teadaande pealkiri' }}</p>
                  <p class="text-xs opacity-85 mt-0.5">{{ form.message || 'Teadaande sisu kuvatakse siin...' }}</p>
                </div>
              </div>
            </div>

            <!-- Date pickers -->
            <div class="grid grid-cols-2 gap-3">
              <div v-for="field in (['starts_at','ends_at'] as const)" :key="field">
                <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ field === 'starts_at' ? 'Algusaeg' : 'Lõpuaeg' }} (valikuline)</label>
                <button @click="openCal(field)" class="w-full flex items-center gap-2 px-3 py-2 bg-[#09090b] border border-[#3f3f46] rounded-md hover:border-orange-500/50 transition group">
                  <Calendar :size="13" class="text-zinc-600 group-hover:text-orange-400 transition flex-shrink-0" />
                  <span class="text-xs flex-1 text-left" :class="(field==='starts_at'?form.starts_at:form.ends_at) ? 'text-zinc-100' : 'text-zinc-600'">
                    {{ displayDate(field === 'starts_at' ? form.starts_at : form.ends_at) || 'PP.KK.AAAA' }}
                  </span>
                  <span v-if="field==='starts_at'?form.starts_at:form.ends_at" @click.stop="field==='starts_at'?form.starts_at='':form.ends_at=''" class="text-zinc-600 hover:text-red-400 transition">
                    <X :size="12" />
                  </span>
                </button>
              </div>
            </div>

            <!-- Calendar -->
            <div v-if="cal.open" class="bg-[#09090b] border border-[#3f3f46] rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <button @click="calPrev" class="p-1.5 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition"><ChevronLeft :size="14" /></button>
                <span class="text-xs font-semibold text-zinc-100">{{ monthNames[cal.viewMonth] }} {{ cal.viewYear }}</span>
                <button @click="calNext" class="p-1.5 rounded-md text-zinc-500 hover:text-zinc-100 hover:bg-[#27272a] transition"><ChevronRight :size="14" /></button>
              </div>
              <div class="grid grid-cols-7 mb-1">
                <span v-for="d in dayNames" :key="d" class="text-center text-xs font-medium text-zinc-600 py-1">{{ d }}</span>
              </div>
              <div class="grid grid-cols-7 gap-y-1">
                <button v-for="(day,i) in calDays" :key="i" @click="selectDay(day)" :disabled="!day" class="aspect-square flex items-center justify-center text-xs rounded-md transition-all"
                  :class="[!day ? 'invisible' : 'cursor-pointer', isSelected(day) ? 'bg-orange-600 text-white' : isToday(day) ? 'border border-orange-500/50 text-orange-400 hover:bg-orange-500/10' : day ? 'text-zinc-300 hover:bg-[#27272a] hover:text-zinc-100' : '']">
                  {{ day }}
                </button>
              </div>
              <div class="flex gap-2 mt-4 pt-3 border-t border-[#27272a]">
                <button @click="clearCal" class="flex-1 py-1.5 text-xs rounded-md bg-[#27272a] text-zinc-400 hover:text-zinc-100 transition">Tühjenda</button>
                <button @click="cal.open=false" class="flex-1 py-1.5 text-xs rounded-md bg-[#27272a] text-zinc-400 hover:text-zinc-100 transition">Tühista</button>
                <button @click="applyCal" :disabled="!cal.selectedDate" class="flex-1 py-1.5 text-xs rounded-md bg-orange-600 text-white font-medium hover:bg-orange-700 transition disabled:opacity-50">Kinnita</button>
              </div>
            </div>

            <!-- Active + sort order -->
            <div class="flex items-center justify-between gap-4">
              <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <div @click="form.is_active = !form.is_active" class="relative w-10 h-5 rounded-full transition-all cursor-pointer" :class="form.is_active ? 'bg-orange-600' : 'bg-[#3f3f46]'">
                  <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="form.is_active ? 'translate-x-5' : 'translate-x-0'" />
                </div>
                <span class="text-sm text-zinc-300">Aktiivne</span>
              </label>
              <div class="flex items-center gap-2">
                <label class="text-xs text-zinc-500">Järjekord:</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="w-16 px-2 py-1.5 bg-[#09090b] border border-[#3f3f46] rounded-md text-zinc-100 text-xs text-center focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-colors" />
              </div>
            </div>
          </div>

          <div class="px-5 pb-5 flex gap-2 justify-end border-t border-[#27272a] pt-4">
            <button @click="closeModal" class="px-4 py-2 rounded-md text-xs font-medium text-zinc-400 hover:text-zinc-100 bg-[#27272a] hover:bg-[#3f3f46] transition">Tühista</button>
            <button @click="submit" :disabled="!form.title || !form.message" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
              <Check :size="13" />
              {{ editingId ? 'Salvesta muudatused' : 'Lisa teadaanne' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Announcements list -->
    <div class="space-y-3">
      <div v-if="announcements.length === 0" class="flex flex-col items-center py-16 bg-[#18181b] border border-[#27272a] rounded-lg">
        <Megaphone :size="28" class="text-zinc-700 mb-3" />
        <p class="text-sm font-medium text-zinc-500 mb-1">Teadaandeid pole</p>
        <p class="text-xs text-zinc-700 mb-5">Lisa esimene teadaanne, mis kuvatakse avaleheküljel.</p>
        <button @click="openCreate" class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-md transition-colors">
          <Plus :size="13" />
          Lisa teadaanne
        </button>
      </div>

      <div v-for="a in announcements" :key="a.id" class="bg-[#18181b] border border-[#27272a] rounded-lg overflow-hidden hover:border-[#3f3f46] transition-colors">
        <div class="w-full px-4 py-2.5 flex items-start gap-2.5" :style="{ backgroundColor: a.bg_color, color: a.text_color }">
          <Megaphone :size="14" class="flex-shrink-0 mt-0.5 opacity-80" />
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm leading-snug">{{ a.title }}</p>
            <p class="text-xs opacity-85 mt-0.5 leading-relaxed">{{ a.message }}</p>
          </div>
          <span class="text-xs opacity-50 flex-shrink-0 font-medium">Eelvaade</span>
        </div>
        <div class="px-4 py-3 flex items-center gap-3">
          <div class="flex-1 min-w-0 text-xs text-zinc-500">
            <p v-if="a.starts_at || a.ends_at">
              <span v-if="a.starts_at">Algus: {{ formatDate(a.starts_at) }}</span>
              <span v-if="a.starts_at && a.ends_at" class="mx-2 text-zinc-700">·</span>
              <span v-if="a.ends_at">Lõpp: {{ formatDate(a.ends_at) }}</span>
            </p>
            <p v-else class="text-zinc-700">Kuvatakse alati</p>
          </div>
          <span :class="a.is_active ? 'bg-green-500/15 text-green-400' : 'bg-[#27272a] text-zinc-500'" class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0">
            {{ a.is_active ? 'Aktiivne' : 'Peidetud' }}
          </span>
          <div class="flex items-center gap-1 flex-shrink-0">
            <button @click="toggle(a.id)" :title="a.is_active?'Peida':'Näita'" class="p-1.5 rounded-md transition-all" :class="a.is_active?'text-green-400 hover:bg-green-500/10':'text-zinc-600 hover:bg-[#27272a] hover:text-zinc-400'">
              <ToggleRight v-if="a.is_active" :size="16" /><ToggleLeft v-else :size="16" />
            </button>
            <button @click="openEdit(a)" class="p-1.5 rounded-md text-zinc-500 hover:text-orange-400 hover:bg-orange-500/10 transition-all"><Pencil :size="14" /></button>
            <button @click="confirmDestroy(a.id)" class="p-1.5 rounded-md text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition-all"><Trash2 :size="14" /></button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.shade-slider::-webkit-slider-thumb {
  appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: white;
  border: 2px solid rgba(0,0,0,0.4);
  box-shadow: 0 1px 4px rgba(0,0,0,0.5);
  cursor: pointer;
}
.shade-slider::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: white;
  border: 2px solid rgba(0,0,0,0.4);
  box-shadow: 0 1px 4px rgba(0,0,0,0.5);
  cursor: pointer;
}
</style>
