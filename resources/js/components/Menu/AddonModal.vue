<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

interface AddonOption { id: number; name: string; price: number; slug: string | null }
interface Addons {
  size:  AddonOption[];
  drink: AddonOption[];
  sauce: AddonOption[];
  fries: AddonOption[];
}
interface MenuItemData {
  id: number; name: string; price: number; image_url: string | null;
}

const props = defineProps<{
  item: MenuItemData;
  addons: Addons | null;
  loading: boolean;
}>();

const emit = defineEmits<{
  close: [];
  added: [];
}>();

const scrollRef = ref<HTMLElement | null>(null);

// Selections
const selectedSizeId   = ref<number | null>(null);
const selectedDrinks   = ref<number[]>([]);
const selectedSauces   = ref<number[]>([]);
const selectedFriesId  = ref<number | null>(null);
const needsUtensils    = ref(false);
const specialInstructions = ref('');
const submitting = ref(false);

// Auto-select first size and fries(none) when addons load
const initDefaults = () => {
  if (!props.addons) return;
  if (props.addons.size?.length && selectedSizeId.value === null) {
    selectedSizeId.value = props.addons.size[0].id;
  }
  if (props.addons.fries?.length && selectedFriesId.value === null) {
    const none = props.addons.fries.find(f => f.slug === 'none');
    selectedFriesId.value = none?.id ?? props.addons.fries[0].id;
  }
};

// Watch addons prop
import { watch } from 'vue';
watch(() => props.addons, initDefaults, { immediate: true });

const selectedSize = computed(() =>
  props.addons?.size?.find(s => s.id === selectedSizeId.value) ?? null
);

const totalPrice = computed(() => {
  let total = Number(props.item.price) || 0;
  if (selectedSize.value) total += Number(selectedSize.value.price) || 0;
  selectedDrinks.value.forEach(id => {
    const d = props.addons?.drink?.find(d => d.id === id);
    if (d) total += Number(d.price) || 0;
  });
  selectedSauces.value.forEach(id => {
    const s = props.addons?.sauce?.find(s => s.id === id);
    if (s) total += Number(s.price) || 0;
  });
  const fry = props.addons?.fries?.find(f => f.id === selectedFriesId.value);
  if (fry) total += Number(fry.price) || 0;
  return total.toFixed(2);
});

// Wheel scroll handler
const handleWheel = (e: WheelEvent) => {
  e.stopPropagation();
  e.preventDefault();
  if (scrollRef.value) scrollRef.value.scrollTop += e.deltaY;
};

const onScrollRefMounted = (el: any) => {
  scrollRef.value = el as HTMLElement | null;
  if (el) el.addEventListener('wheel', handleWheel, { passive: false });
};

onUnmounted(() => {
  if (scrollRef.value) scrollRef.value.removeEventListener('wheel', handleWheel);
});

const addToCart = () => {
  const selectedFry = props.addons?.fries?.find(f => f.id === selectedFriesId.value);

  submitting.value = true;
  router.post('/cart/add-menu-item', {
    menu_item_id: props.item.id,
    name: props.item.name,
    base_price: Number(props.item.price) || 0,
    size: selectedSize.value?.slug ?? selectedSize.value?.name ?? 'standard',
    drinks: selectedDrinks.value.map(id => {
      const d = props.addons?.drink?.find(d => d.id === id);
      return d ? { id: d.id, name: d.name, price: d.price } : null;
    }).filter(Boolean),
    sauces: selectedSauces.value.map(id => {
      const s = props.addons?.sauce?.find(s => s.id === id);
      return s ? { id: s.id, name: s.name, price: s.price } : null;
    }).filter(Boolean),
    fries: selectedFry && selectedFry.slug !== 'none'
      ? { id: selectedFry.id, name: selectedFry.name, price: selectedFry.price }
      : null,
    needs_utensils: needsUtensils.value,
    special_instructions: specialInstructions.value,
    total_price: parseFloat(totalPrice.value),
    quantity: 1,
  }, {
    preserveScroll: true,
    onSuccess: () => { submitting.value = false; emit('added'); },
    onError: () => { submitting.value = false; },
  });
};
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="emit('close')">
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
        >
          <div class="bg-[#121212] rounded-2xl max-w-2xl w-full flex flex-col border border-[#1a1a1a] shadow-2xl" style="max-height: 90vh;">

            <!-- Header -->
            <div class="flex-shrink-0 bg-[#0B0B0B] px-6 py-4 flex items-center justify-between border-b border-[#1a1a1a] rounded-t-2xl">
              <div class="flex items-center gap-4">
                <div v-if="item.image_url" class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                  <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover" />
                </div>
                <div>
                  <h2 class="text-xl font-bold text-white">{{ item.name }}</h2>
                  <p class="text-sm text-gray-400">Kohanda oma tellimust</p>
                </div>
              </div>
              <button @click="emit('close')" class="w-9 h-9 rounded-lg bg-[#1a1a1a] hover:bg-[#D2691E] text-gray-400 hover:text-white transition-colors flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="flex-1 flex items-center justify-center py-20">
              <div class="flex flex-col items-center gap-3">
                <div class="w-8 h-8 border-2 border-[#D2691E] border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500">Laen lisandeid...</p>
              </div>
            </div>

            <!-- Content -->
            <div v-else-if="addons" :ref="onScrollRefMounted" class="modal-scroll p-6 space-y-7">

              <!-- SIZE -->
              <section v-if="addons.size?.length">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                  <span class="text-[#D2691E]">📏</span> Suurus
                </h3>
                <div class="grid grid-cols-3 gap-3">
                  <button
                    v-for="size in addons.size"
                    :key="size.id"
                    @click="selectedSizeId = size.id"
                    :class="[
                      'px-4 py-3.5 rounded-xl border-2 transition-all text-left',
                      selectedSizeId === size.id
                        ? 'border-[#D2691E] bg-[#D2691E]/10 text-white'
                        : 'border-[#1a1a1a] bg-[#0B0B0B] text-gray-400 hover:border-[#D2691E]/40 hover:text-white'
                    ]"
                  >
                    <div class="font-bold text-sm">{{ size.name }}</div>
                    <div class="text-xs mt-0.5" :class="size.price > 0 ? 'text-[#D2691E]' : 'text-gray-600'">
                      {{ size.price > 0 ? `+€${size.price.toFixed(2)}` : 'Standard' }}
                    </div>
                  </button>
                </div>
              </section>

              <!-- DRINKS -->
              <section v-if="addons.drink?.length">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                  <span class="text-[#D2691E]">🥤</span> Joogid
                </h3>
                <div class="space-y-2">
                  <label
                    v-for="drink in addons.drink"
                    :key="drink.id"
                    class="flex items-center justify-between p-3.5 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/8 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/20"
                    :class="selectedDrinks.includes(drink.id) ? 'border-[#D2691E]/30 bg-[#D2691E]/8' : ''"
                  >
                    <div class="flex items-center gap-3">
                      <input
                        type="checkbox"
                        :value="drink.id"
                        v-model="selectedDrinks"
                        class="w-4 h-4 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                      />
                      <span class="text-white text-sm font-medium">{{ drink.name }}</span>
                    </div>
                    <span class="text-[#D2691E] font-bold text-sm">+€{{ drink.price.toFixed(2) }}</span>
                  </label>
                </div>
              </section>

              <!-- SAUCES -->
              <section v-if="addons.sauce?.length">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                  <span class="text-[#D2691E]">🧴</span> Kastmed
                </h3>
                <div class="grid grid-cols-2 gap-2">
                  <label
                    v-for="sauce in addons.sauce"
                    :key="sauce.id"
                    class="flex items-center gap-3 p-3.5 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/8 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/20"
                    :class="selectedSauces.includes(sauce.id) ? 'border-[#D2691E]/30 bg-[#D2691E]/8' : ''"
                  >
                    <input
                      type="checkbox"
                      :value="sauce.id"
                      v-model="selectedSauces"
                      class="w-4 h-4 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                    />
                    <div class="flex-1 min-w-0">
                      <div class="text-white text-sm font-medium truncate">{{ sauce.name }}</div>
                      <div :class="sauce.price > 0 ? 'text-[#D2691E]' : 'text-gray-600'" class="text-xs font-semibold">
                        {{ sauce.price > 0 ? `+€${sauce.price.toFixed(2)}` : 'Tasuta' }}
                      </div>
                    </div>
                  </label>
                </div>
              </section>

              <!-- FRIES -->
              <section v-if="addons.fries?.length">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                  <span class="text-[#D2691E]">🍟</span> Friikartul
                </h3>
                <div class="space-y-2">
                  <label
                    v-for="fry in addons.fries"
                    :key="fry.id"
                    class="flex items-center justify-between p-3.5 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/8 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/20"
                    :class="selectedFriesId === fry.id ? 'border-[#D2691E]/30 bg-[#D2691E]/8' : ''"
                  >
                    <div class="flex items-center gap-3">
                      <input
                        type="radio"
                        :value="fry.id"
                        v-model="selectedFriesId"
                        class="w-4 h-4 border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]"
                      />
                      <span class="text-white text-sm font-medium">{{ fry.name }}</span>
                    </div>
                    <span :class="fry.price > 0 ? 'text-[#D2691E] font-bold' : 'text-gray-600'" class="text-sm">
                      {{ fry.price > 0 ? `+€${fry.price.toFixed(2)}` : 'Ei lisandu' }}
                    </span>
                  </label>
                </div>
              </section>

              <!-- UTENSILS -->
              <section>
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                  <span class="text-[#D2691E]">🍴</span> Söögiriistad
                </h3>
                <label class="flex items-center gap-3 p-3.5 rounded-xl bg-[#0B0B0B] hover:bg-[#D2691E]/8 cursor-pointer transition-colors border-2 border-transparent hover:border-[#D2691E]/20"
                  :class="needsUtensils ? 'border-[#D2691E]/30 bg-[#D2691E]/8' : ''"
                >
                  <input type="checkbox" v-model="needsUtensils" class="w-4 h-4 rounded border-gray-600 bg-[#121212] text-[#D2691E] focus:ring-[#D2691E] focus:ring-offset-[#0B0B0B]" />
                  <span class="text-white text-sm font-medium">Soovin söögiriistu</span>
                  <span class="ml-auto text-gray-600 text-xs">Tasuta</span>
                </label>
              </section>

              <!-- SPECIAL INSTRUCTIONS -->
              <section>
                <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-3 flex items-center gap-2">
                  <span class="text-[#D2691E]">📝</span> Erisoovidused
                </h3>
                <textarea
                  v-model="specialInstructions"
                  rows="3"
                  class="w-full bg-[#0B0B0B] border-2 border-[#1a1a1a] rounded-xl px-4 py-3 text-white text-sm placeholder-gray-600 focus:border-[#D2691E] focus:ring-0 transition-colors resize-none"
                  placeholder="Nt: ilma sibulata, ekstra juust..."
                />
              </section>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 bg-[#0B0B0B] px-6 py-4 border-t border-[#1a1a1a] rounded-b-2xl">
              <div class="flex items-center justify-between mb-4">
                <div class="text-gray-400 text-sm">
                  Kokku: <span class="text-xs text-gray-600 ml-1">(€{{ Number(item.price).toFixed(2) }} + lisandid)</span>
                </div>
                <span class="text-3xl font-bold text-[#D2691E]">€{{ totalPrice }}</span>
              </div>
              <button
                @click="addToCart"
                :disabled="submitting || loading"
                class="w-full bg-gradient-to-r from-[#D2691E] to-[#B8571A] hover:from-[#E07A2E] hover:to-[#D2691E] disabled:opacity-50 disabled:cursor-not-allowed text-white py-4 rounded-xl font-bold text-base transition-all shadow-lg hover:shadow-[#D2691E]/50 flex items-center justify-center gap-2"
              >
                <svg v-if="submitting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ submitting ? 'Lisandub...' : 'Lisa korvi' }}
              </button>
            </div>

          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-scroll {
  overflow-y: scroll;
  max-height: 60vh;
  overscroll-behavior: contain;
}
.modal-scroll::-webkit-scrollbar { width: 6px; }
.modal-scroll::-webkit-scrollbar-track { background: #0B0B0B; }
.modal-scroll::-webkit-scrollbar-thumb { background: #D2691E; border-radius: 4px; }
.modal-scroll::-webkit-scrollbar-thumb:hover { background: #E07A2E; }
</style>
