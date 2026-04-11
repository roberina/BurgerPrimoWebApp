<template>
  <MainLayout>
    <div class="bb-root">

      <div class="bb-page-header">
        <div class="bb-page-header-inner">
          <p class="bb-eyebrow">Burger Primo</p>
          <h1 class="bb-page-title">Ehita oma burger</h1>
          <p class="bb-page-sub">Vali koostisosad ja loo oma unistuste burger</p>
        </div>
      </div>

      <section id="bb-builder" class="bb-builder">
        <div class="bb-left">
          <div class="bb-burger-panel">
            <div class="bb-burger-title">
              <span class="bb-eyebrow">Sinu burger</span>
              <span class="bb-burger-name-display" v-if="burgerName">{{ burgerName }}</span>
            </div>

            <div class="bb-burger-visual">
              <svg :viewBox="`0 0 260 ${svgH}`" width="240" :height="svgH*240/260"
                xmlns="http://www.w3.org/2000/svg" style="display:block;margin:0 auto;overflow:visible">
                <defs>
                  <radialGradient id="gTB" cx="34%" cy="18%" r="74%">
                    <stop offset="0%"   stop-color="#F8B040"/>
                    <stop offset="32%"  stop-color="#D06818"/>
                    <stop offset="65%"  stop-color="#A04808"/>
                    <stop offset="100%" stop-color="#582004"/>
                  </radialGradient>
                  <radialGradient id="gBB" cx="34%" cy="20%" r="70%">
                    <stop offset="0%"   stop-color="#E09030"/>
                    <stop offset="44%"  stop-color="#B86018"/>
                    <stop offset="100%" stop-color="#4E1C04"/>
                  </radialGradient>
                  <radialGradient id="gSd" cx="28%" cy="25%" r="66%">
                    <stop offset="0%"   stop-color="#F5EEB5"/>
                    <stop offset="58%"  stop-color="#CCBA3A"/>
                    <stop offset="100%" stop-color="#8A6A14"/>
                  </radialGradient>
                  <filter id="fds">
                    <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="rgba(0,0,0,0.42)"/>
                  </filter>
                  <filter id="fds2">
                    <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="rgba(0,0,0,0.32)"/>
                  </filter>
                </defs>

                <ellipse cx="130" :cy="svgH-4" rx="108" ry="7" fill="rgba(0,0,0,0.3)"/>

                <!-- TOP BUN -->
                <g filter="url(#fds)">
                  <rect x="28" :y="svgTopRim" width="204" height="11" rx="5" fill="#4A1802"/>
                  <path :d="`M28,${svgTopRim} C28,${svgTopRim-BUN_DOME} 232,${svgTopRim-BUN_DOME} 232,${svgTopRim} Z`" fill="url(#gTB)"/>
                  <ellipse cx="96" :cy="svgTopRim-BUN_DOME*0.48" rx="54" :ry="BUN_DOME*0.22" fill="rgba(255,238,158,0.17)"/>
                  <g v-for="s in seeds" :key="s.x">
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d+1.5" :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="rgba(16,5,0,0.28)"/>
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d"     :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="url(#gSd)"/>
                    <line :x1="s.x-s.w+1" :y1="svgTopRim-s.d" :x2="s.x+s.w-1" :y2="svgTopRim-s.d" stroke="rgba(88,48,4,0.32)" stroke-width="0.8"/>
                  </g>
                </g>

                <!-- LAYERS -->
                <g v-for="(L,i) in layers3" :key="i" filter="url(#fds2)">
                  <g v-if="L.t==='p'">
                    <rect x="24" :y="L.y+L.h*0.58" width="212" :height="L.h*0.46+2" rx="7" :fill="L.s"/>
                    <rect x="24" :y="L.y" width="212" :height="L.h" rx="8" :fill="L.c"/>
                    <clipPath :id="`cp${i}`"><rect x="24" :y="L.y" width="212" :height="L.h" rx="8"/></clipPath>
                    <g :clip-path="`url(#cp${i})`">
                      <rect v-for="g in [48,80,112,145,178]" :key="g" :x="g" :y="L.y-1" width="5" :height="L.h+2" rx="2.5" fill="rgba(4,1,0,0.52)"/>
                      <rect v-for="g in [50,82,114,147,180]" :key="`h${g}`" :x="g" :y="L.y-1" width="2.5" :height="L.h+2" rx="1.5" fill="rgba(210,65,5,0.14)"/>
                    </g>
                  </g>
                  <g v-else-if="L.t==='c'">
                    <ellipse v-for="d in [30,65,104,140,176,210]" :key="d" :cx="d" :cy="L.y+L.h+5" rx="4" ry="5.5" :fill="L.c"/>
                    <rect x="8" :y="L.y+5" width="244" height="5" rx="2" :fill="L.s"/>
                    <rect x="8" :y="L.y" width="244" :height="L.h+1" rx="2" :fill="L.c"/>
                    <rect x="8" :y="L.y" width="244" height="3" rx="1.5" fill="rgba(255,248,185,0.24)"/>
                  </g>
                  <g v-else-if="L.t==='l'">
                    <path :d="lp(L.y,true)"  :fill="L.s"/>
                    <path :d="lp(L.y,false)" :fill="L.c"/>
                  </g>
                  <g v-else-if="L.t==='t'">
                    <rect x="18" :y="L.y+L.h*0.58" width="224" :height="L.h*0.46" rx="3" :fill="L.s"/>
                    <rect x="18" :y="L.y" width="224" :height="L.h" rx="5" :fill="L.c"/>
                    <line v-for="x in [70,112,154,196]" :key="x" :x1="x" :y1="L.y+1" :x2="x" :y2="L.y+L.h-1" stroke="rgba(255,158,140,0.28)" stroke-width="1.2"/>
                    <ellipse v-for="x in [56,98,136,175]" :key="x" :cx="x" :cy="L.y+L.h/2" rx="4.5" ry="2" fill="rgba(255,242,220,0.28)"/>
                  </g>
                  <g v-else-if="L.t==='k'">
                    <rect x="16" :y="L.y+L.h*0.58" width="228" :height="L.h*0.46" rx="3" fill="#1E5808"/>
                    <rect x="16" :y="L.y" width="228" :height="L.h" rx="4" :fill="L.c"/>
                    <line v-for="n in 6" :key="n" :x1="20+n*32" :y1="L.y+1" :x2="19+n*32" :y2="L.y+L.h-1" stroke="rgba(15,65,5,0.28)" stroke-width="1"/>
                  </g>
                  <g v-else-if="L.t==='o'">
                    <ellipse cx="70"  :cy="L.y+L.h/2" rx="50" :ry="L.h/2+1" fill="rgba(185,65,205,0.1)" stroke="#CC68E0" stroke-width="3.5"/>
                    <ellipse cx="70"  :cy="L.y+L.h/2" rx="33" :ry="L.h/2-1" fill="none" stroke="#AA50C0" stroke-width="2.5"/>
                    <ellipse cx="190" :cy="L.y+L.h/2" rx="50" :ry="L.h/2+1" fill="rgba(185,65,205,0.1)" stroke="#CC68E0" stroke-width="3.5"/>
                    <ellipse cx="190" :cy="L.y+L.h/2" rx="33" :ry="L.h/2-1" fill="none" stroke="#AA50C0" stroke-width="2.5"/>
                  </g>
                  <g v-else-if="L.t==='a'">
                    <rect x="16" :y="L.y+L.h*0.58" width="228" :height="L.h*0.46" rx="3" fill="#304E08"/>
                    <rect x="16" :y="L.y" width="228" :height="L.h" rx="4" :fill="L.c"/>
                  </g>
                  <g v-else-if="L.t==='s'">
                    <rect x="20" :y="L.y+L.h*0.6" width="220" :height="L.h*0.45" rx="2" :fill="L.s"/>
                    <rect x="20" :y="L.y" width="220" :height="L.h" rx="3" :fill="L.c"/>
                    <rect x="20" :y="L.y" width="220" height="2" rx="1" fill="rgba(255,255,255,0.14)"/>
                  </g>
                </g>

                <!-- BOTTOM BUN -->
                <g filter="url(#fds)">
                  <rect x="28" :y="svgBotY+18" width="204" height="8" rx="3" fill="#361002"/>
                  <rect x="28" :y="svgBotY"    width="204" height="22" rx="9" fill="url(#gBB)"/>
                  <ellipse cx="102" :cy="svgBotY+8" rx="46" ry="6" fill="rgba(255,208,115,0.17)"/>
                  <ellipse v-for="s in bseeds" :key="s.x" :cx="s.x" :cy="svgBotY+s.d" :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgBotY+s.d})`" fill="url(#gSd)"/>
                </g>
              </svg>
            </div>

            <div class="bb-ingredient-list" v-if="totalLayers > 0">
              <div v-for="item in allSelectedFlat" :key="item.id" class="bb-ing-row">
                <div class="bb-ing-dot" :style="{ background: categoryColor(item.category) }"></div>
                <span class="bb-ing-name">{{ item.name }}</span>
                <span class="bb-ing-qty" v-if="item.quantity > 1">×{{ item.quantity }}</span>
                <span class="bb-ing-price">{{ (item.price * item.quantity).toFixed(2) }}€</span>
              </div>
            </div>
            <div class="bb-ing-empty" v-else>Vali paremal koostisosad →</div>

            <div class="bb-price-row" v-if="totalLayers > 0">
              <span>Kokku</span>
              <span class="bb-price-total">{{ totalPrice.toFixed(2) }}€</span>
            </div>
          </div>
        </div>

        <div class="bb-right">
          <div class="bb-section">
            <div class="bb-section-header">
              <span class="bb-section-num">01</span>
              <h2 class="bb-section-title">Burgeri nimi</h2>
            </div>
            <input v-model="burgerName" type="text" placeholder="nt. Robby's Special..." class="bb-name-input"/>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['pitav']?.length">
            <div class="bb-section-header">
              <span class="bb-section-num">02</span>
              <h2 class="bb-section-title">Pihvid</h2>
              <span class="bb-section-hint">vali kuni 2</span>
            </div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['pitav']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('pitav', ing.id) }" @click="togglePatty(ing.id)">
                <div class="bb-card-icon">🥩</div>
                <div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('pitav', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['salat']?.length">
            <div class="bb-section-header">
              <span class="bb-section-num">03</span>
              <h2 class="bb-section-title">Köögiviljad</h2>
              <span class="bb-section-hint">vali mitu</span>
            </div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['salat']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('salat', ing.id) }" @click="toggleMulti('salat', ing.id)">
                <div class="bb-card-icon">🥬</div>
                <div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('salat', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['lisand']?.length">
            <div class="bb-section-header">
              <span class="bb-section-num">04</span>
              <h2 class="bb-section-title">Kastmed & lisandid</h2>
              <span class="bb-section-hint">vali mitu</span>
            </div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['lisand']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('lisand', ing.id) }" @click="toggleMulti('lisand', ing.id)">
                <div class="bb-card-icon">🫙</div>
                <div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('lisand', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['juust']?.length">
            <div class="bb-section-header">
              <span class="bb-section-num">05</span>
              <h2 class="bb-section-title">Juust</h2>
              <span class="bb-section-hint">vali mitu</span>
            </div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['juust']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('juust', ing.id) }" @click="toggleMulti('juust', ing.id)">
                <div class="bb-card-icon">🧀</div>
                <div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('juust', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-actions">
            <div class="bb-actions-price" v-if="totalLayers > 0">
              <span class="bb-actions-price-label">Kogusumma</span>
              <span class="bb-actions-price-val">{{ totalPrice.toFixed(2) }}€</span>
            </div>
            <div class="bb-actions-btns">
              <button @click="saveBurger(false)" :disabled="!canSave" class="bb-btn-secondary">Salvesta</button>
              <button @click="saveBurger(true)"  :disabled="!canSave" class="bb-btn-secondary">♥ Lemmik</button>
              <button @click="saveAndSubmit()"   :disabled="!canSave" class="bb-btn-review">Saada adminile ↗</button>
            </div>
          </div>
        </div>
      </section>

      <section v-if="customBurgers?.length > 0" class="bb-saved">
        <div class="bb-saved-inner">
          <div class="bb-saved-grid">
            <div v-for="burger in customBurgers" :key="burger.id" class="bb-saved-card">
              <div class="bb-saved-card-header">
                <span class="bb-saved-card-name">{{ burger.name }}</span>
                <span class="bb-status-badge" :class="'bb-status-' + (burger.status || 'draft')">{{ statusLabel(burger.status) }}</span>
              </div>
              <p class="bb-saved-card-price">{{ Number(burger.total_price).toFixed(2) }}€</p>
              <div v-if="burger.status === 'rejected' && burger.admin_note" class="bb-rejected-note">💬 {{ burger.admin_note }}</div>
              <div class="bb-saved-card-btns">
                <button @click="toggleBurgerFavorite(burger.id, burger.is_favorite)" class="bb-saved-btn bb-saved-btn-fav" :class="{ 'bb-saved-btn-fav--active': burger.is_favorite }">{{ burger.is_favorite ? '♥' : '♡' }}</button>
                <button @click="loadBurger(burger)" class="bb-saved-btn">Muuda</button>
                <button @click="quickOrder(burger)" class="bb-saved-btn bb-saved-btn-green">🛒 Telli</button>
                <button v-if="burger.status === 'draft' || burger.status === 'rejected'" @click="submitForReviewById(burger.id)" class="bb-saved-btn bb-saved-btn-orange">Saada adminile ↗</button>
                <span v-if="burger.status === 'pending'" class="bb-saved-pending-hint">Ootab kinnitust...</span>
                <button @click="deleteBurger(burger.id)" class="bb-saved-btn bb-saved-btn-red">✕</button>
              </div>
            </div>
          </div>
        </div>
      </section>

    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import CustomBurgerCard from '@/components/CustomBurgerCard.vue';
import type { Ingredient, SelectedIngredient, CustomBurger } from '@/types/burger-types';

interface Props {
  ingredients: Record<string, Ingredient[]> | any;
  customBurgers: CustomBurger[];
  canCreateMore: boolean;
  maxBurgers: number;
}
const props = defineProps<Props>();

// Normaliseeri ingredients
const normalizedIngredients = computed(() => {
  const raw = props.ingredients as any;
  if (!raw) return {};
  if (!Array.isArray(raw)) return raw;
  const result: Record<string, any[]> = {};
  raw.forEach((ing: any) => {
    if (!result[ing.category]) result[ing.category] = [];
    result[ing.category].push(ing);
  });
  return result;
});

const getAllIngredients = () => Object.values(normalizedIngredients.value).flat() as Ingredient[];

// State
const burgerName = ref('');
const selectedIngredients = ref<Record<string, SelectedIngredient[]>>({
  'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [],
});

// Toggle helpers
const isSelected = (cat: string, id: number) =>
  selectedIngredients.value[cat]?.some(i => i.id === id) ?? false;

const toggleOne = (cat: string, id: number) => {
  if (isSelected(cat, id)) {
    selectedIngredients.value[cat] = [];
  } else {
    selectedIngredients.value[cat] = [{ id, quantity: 1 }];
  }
};

const togglePatty = (id: number) => {
  const arr = selectedIngredients.value['pitav'] ?? [];
  const idx = arr.findIndex(i => i.id === id);
  if (idx >= 0) {
    selectedIngredients.value['pitav'] = arr.filter(i => i.id !== id);
  } else if (arr.length < 2) {
    selectedIngredients.value['pitav'] = [...arr, { id, quantity: 1 }];
  }
};

const toggleMulti = (cat: string, id: number) => {
  const arr = selectedIngredients.value[cat] ?? [];
  const idx = arr.findIndex(i => i.id === id);
  if (idx >= 0) {
    selectedIngredients.value[cat] = arr.filter(i => i.id !== id);
  } else {
    selectedIngredients.value[cat] = [...arr, { id, quantity: 1 }];
  }
};

// Computed
const allSelectedFlat = computed(() => {
  const result: { id: number; name: string; price: number; quantity: number; category: string }[] = [];
  Object.entries(selectedIngredients.value).forEach(([cat, items]) => {
    items.forEach(item => {
      const ing = getAllIngredients().find(i => i.id === item.id);
      if (ing) result.push({ id: item.id, name: ing.name, price: Number(ing.price), quantity: item.quantity, category: cat });
    });
  });
  return result;
});

const totalLayers = computed(() => allSelectedFlat.value.length);
const totalPrice = computed(() => allSelectedFlat.value.reduce((t, i) => t + i.price * i.quantity, 0));
const canSave = computed(() => burgerName.value.trim() !== '' && totalLayers.value > 0);

const categoryColor = (cat: string) => {
  const map: Record<string, string> = {
    'vöi': '#D4822E', 'pitav': '#8B4513', 'salat': '#4CAF50',
    'lisand': '#FDD835', 'juust': '#F9A825',
  };
  return map[cat] ?? '#666';
};

// Layer positioning
const salatCount = computed(() => selectedIngredients.value['salat']?.length ?? 0);
const juustCount = computed(() => selectedIngredients.value['juust']?.length ?? 0);
const pitavCount = computed(() => selectedIngredients.value['pitav']?.length ?? 0);
const lisandCount = computed(() => selectedIngredients.value['lisand']?.length ?? 0);




// Router
const getAllSelected = (): SelectedIngredient[] => {
  const all: SelectedIngredient[] = [];
  Object.values(selectedIngredients.value).forEach(i => all.push(...i));
  return all;
};
const saveBurger = (fav: boolean) => {
  if (!props.canCreateMore) { alert(`Limit: ${props.maxBurgers}`); return; }
  router.post('/burger-builder', { name: burgerName.value, ingredients: getAllSelected(), is_favorite: fav } as any);
};
const orderBurger = () => router.post('/cart/add-new', { name: burgerName.value, ingredients: getAllSelected() } as any, {
  onSuccess: () => router.visit('/cart'),
});
const submitForReviewById = (id: number) => {
  router.post(`/burger-builder/${id}/submit`, {}, { preserveScroll: true });
};

const statusLabel = (status: string) => {
  const map: Record<string, string> = {
    draft: 'Mustand', pending: 'Ootab kinnitust',
    approved: 'Kinnitatud', rejected: 'Tagasi lükatud',
  };
  return map[status] ?? status;
};

const saveAndSubmit = () => {
  if (!canSave.value) return;
  if (!props.canCreateMore) { alert(`Limit: ${props.maxBurgers}`); return; }
  // Salvesta ja saada kohe adminile — üks POST
  router.post('/burger-builder', {
    name: burgerName.value,
    ingredients: getAllSelected(),
    is_favorite: false,
    submit_for_review: true,
  } as any, {
    preserveScroll: true,
    onSuccess: () => {
      burgerName.value = '';
      selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
    }
  });
};

const submitForReview = () => {
  if (!props.canCreateMore && !burgerName.value.trim()) return;
  // Salvesta ja saada kohe adminile
  router.post('/burger-builder', {
    name: burgerName.value,
    ingredients: getAllSelected(),
    is_favorite: false,
    submit_for_review: true,
  } as any, {
    onSuccess: () => {
      burgerName.value = '';
      selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
    }
  });
};

// Kiirtellimus — lisab salvestatud burgeri otse korvi ilma admin kinnituseta
const quickOrder = (burger: CustomBurger) => {
  router.post('/cart/add-new', {
    name: burger.name,
    ingredients: burger.ingredients.map(ing => ({ id: ing.id, quantity: ing.pivot?.quantity ?? 1 })),
  } as any, {
    onSuccess: () => { success('Burger lisatud korvi! 🛒'); router.visit('/cart'); },
    onError: () => error('Korvi lisamine ebaõnnestus'),
  });
};

const toggleBurgerFavorite = (id: number, current: boolean) => {
  router.post(`/burger-builder/${id}/favorite`, {}, {
    preserveScroll: true,
    onSuccess: () => success(current ? 'Eemaldatud lemmikutest' : '♥ Lisatud lemmikusse'),
  });
};

const orderSavedBurger = (id: number) => router.post('/cart/add', { burger_id: id, quantity: 1 } as any, {
  onSuccess: () => router.visit('/cart'),
});
const deleteBurger = (id: number) => router.delete(`/burger-builder/${id}`, { preserveScroll: true });
const loadBurger = (burger: CustomBurger) => {
  burgerName.value = burger.name;
  selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
  burger.ingredients.forEach(ing => {
    const cat = ing.category;
    if (selectedIngredients.value[cat])
      selectedIngredients.value[cat].push({ id: ing.id, quantity: ing.pivot.quantity });
  });
};

// Canvas refs




// ═══ SVG BURGER ═══
const BUN_DOME = 62  // top bun dome height
const BOT_H    = 22  // bottom bun height
const RIM_H    = 10  // top bun rim thickness
const START_Y  = 8   // top of SVG content
const GAP      = 2

interface SL { t:string; y:number; h:number; c:string; s:string; }

function mkL(): SL[] {
  const out: SL[] = []
  const li = selectedIngredients.value['lisand']??[]
  const sa = selectedIngredients.value['salat']??[]
  const ju = selectedIngredients.value['juust']??[]
  const pi = selectedIngredients.value['pitav']??[]
  const push = (t:string,h:number,c:string,s:string) => out.push({t,y:0,h,c,s})

  if(pi.length>=2){
    push('p',20,pC(pi[0]),pS())
    ju.forEach(x=>{const n=gn(x.id);push('c',7,cC(n),cS(n))})
    sa.forEach(x=>{const n=gn(x.id);const[t,h,c,s]=vD(n);push(t,h,c,s)})
    li.forEach(x=>{const n=gn(x.id);push('s',6,sC(n),sS(n))})
    push('p',20,pC(pi[1]),pS())
  } else if(pi.length===1){
    push('p',20,pC(pi[0]),pS())
    ju.forEach(x=>{const n=gn(x.id);push('c',7,cC(n),cS(n))})
    sa.forEach(x=>{const n=gn(x.id);const[t,h,c,s]=vD(n);push(t,h,c,s)})
    li.forEach(x=>{const n=gn(x.id);push('s',6,sC(n),sS(n))})
  } else {
    ju.forEach(x=>{const n=gn(x.id);push('c',7,cC(n),cS(n))})
    sa.forEach(x=>{const n=gn(x.id);const[t,h,c,s]=vD(n);push(t,h,c,s)})
    li.forEach(x=>{const n=gn(x.id);push('s',6,sC(n),sS(n))})
  }

  // Y: start just below top bun rim
  let y = START_Y + BUN_DOME + RIM_H + GAP
  for(const l of out){ l.y=y; y+=l.h+GAP }
  return out
}

function gn(id:number):string{
  return getAllIngredients().find(i=>i.id===id)?.name?.toLowerCase()??''
}
function pC(item:{id:number}):string{
  const n=gn(item.id)
  if(n.includes('kana'))  return '#A87040'
  if(n.includes('vegan')) return '#607830'
  if(n.includes('grill')) return '#602010'
  if(n.includes('veise')) return '#8A3418'
  return '#904018'
}
function pS():string{return '#2A0802'}
function cC(n:string):string{if(n.includes('mozzarella'))return '#F5EEE0';if(n.includes('blue'))return '#A8B8C8';return '#EDB015'}
function cS(n:string):string{if(n.includes('mozzarella'))return '#CCBCA0';if(n.includes('blue'))return '#6878900';return '#A87808'}
function vD(n:string):[string,number,string,string]{
  if(n.includes('tomat'))return ['t',13,'#CC2818','#801010']
  if(n.includes('kurk')) return ['k',9, '#5A9820','#1E5808']
  if(n.includes('sibul')||n.includes('kastrull'))return ['o',11,'transparent','transparent']
  if(n.includes('avocado'))return ['a',10,'#6A9E20','#305008']
  return ['l',13,'#3A9818','#1A4E08']
}
function sC(n:string):string{
  if(n.includes('ketšup'))  return '#C02018'
  if(n.includes('majonees'))return '#F5F5E5'
  if(n.includes('bbq'))     return '#601408'
  if(n.includes('chipotle'))return '#A02808'
  if(n.includes('peekon'))  return '#A81E1E'
  if(n.includes('muna'))    return '#DCB818'
  return '#C89808'
}
function sS(n:string):string{
  if(n.includes('ketšup'))  return '#720808'
  if(n.includes('majonees'))return '#C0C098'
  return '#907008'
}

// lettuce wavy path
function lp(y:number, back:boolean):string{
  const b = back ? y+6 : y+1
  return `M14,${b+8} Q34,${b} 54,${b+7} Q74,${b+15} 94,${b+3} Q114,${b-8} 134,${b+3} Q154,${b+14} 174,${b+1} Q194,${b-9} 214,${b-1} Q234,${b+9} 254,${b+1} L254,${b+13} Q234,${b+21} 214,${b+11} Q194,${b+23} 174,${b+13} Q154,${b+26} 134,${b+13} Q114,${b+1} 94,${b+15} Q74,${b+27} 54,${b+15} Q34,${b+5} 14,${b+16} Z`
}

const layers3 = computed(()=>mkL())

const svgTopRim = computed(()=> START_Y + BUN_DOME)

const svgBotY = computed(()=>{
  const ls = layers3.value
  if(!ls.length) return svgTopRim.value + RIM_H + GAP + 4
  const last = ls[ls.length-1]
  return last.y + last.h + GAP + 4
})

const svgH = computed(()=> svgBotY.value + BOT_H + 14)

const seeds = [
  {x:82, d:12, w:8, h:3.8, r:-22},
  {x:114,d:24, w:8, h:3.8, r:-8},
  {x:143,d:30, w:8, h:3.8, r:0},
  {x:172,d:24, w:8, h:3.8, r:8},
  {x:206,d:12, w:8, h:3.8, r:22},
  {x:130,d:46, w:6.5,h:3.1,r:-14},
  {x:161,d:44, w:6.5,h:3.1,r:16},
]
const bseeds = [
  {x:94, d:8, w:6.5,h:3.1,r:-18},
  {x:125,d:4, w:6.5,h:3.1,r:5},
  {x:154,d:3, w:6.5,h:3.1,r:0},
  {x:182,d:4, w:6.5,h:3.1,r:-5},
  {x:206,d:7, w:6.5,h:3.1,r:15},
]

onMounted(async () => {});



</script>

<style scoped>
* { box-sizing: border-box; }
.bb-root { background: #0B0B0B; color: #fff; min-height: 100vh; }

/* ── PAGE HEADER ── */
.bb-page-header {
  background: #0B0B0B;
  border-bottom: 1px solid #141414;
  padding: 4rem 3rem 3rem;
}
.bb-page-header-inner {
  max-width: 1300px;
  margin: 0 auto;
}
.bb-eyebrow {
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: #D2691E;
  margin-bottom: .75rem;
  display: block;
}
.bb-page-title {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 900;
  color: #fff;
  letter-spacing: -.02em;
  margin-bottom: .5rem;
}
.bb-page-sub {
  font-size: 1rem;
  color: #555;
}

/* ── BUILDER ── */
.bb-builder {
  display: grid;
  grid-template-columns: 420px 1fr;
  align-items: start;
  border-bottom: 1px solid #141414;
}
@media (max-width: 960px) { .bb-builder { grid-template-columns: 1fr; } }

/* LEFT */
.bb-left {
  border-right: 1px solid #141414;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow: hidden;
}
.bb-burger-panel {
  height: 100%;
  overflow-y: auto;
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  scrollbar-width: none;
}
.bb-burger-panel::-webkit-scrollbar { display: none; }
.bb-burger-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.bb-burger-name-display {
  font-size: .8rem;
  color: #D2691E;
  font-weight: 600;
  max-width: 160px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Burger visuaal */
.bb-burger-visual {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  padding: 12px 0;
  background: radial-gradient(ellipse at 50% 78%, rgba(180,80,10,0.1) 0%, transparent 62%);
  border-radius: 12px;
}

.bb-burger-glow { display: none; }
.bb-burger-stack {
  position: relative;
  width: 248px;
  margin: 4px auto 0;
  filter: drop-shadow(0 16px 32px rgba(180,90,10,0.35));
  transform-origin: top center;
}
.bb-burger-shadow {
  position: absolute;
  bottom: -8px; left: 50%;
  width: 260px; height: 16px;
  border-radius: 50%;
  background: radial-gradient(ellipse, rgba(150,70,8,0.55) 0%, transparent 70%);
  transition: opacity .3s, transform .3s;
}
.bb-layer {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
}
.bb-layer-topbun { top: 2px; width: 248px; }
.bb-layer-botbun { width: 248px; transition: top .3s ease; }
.bb-layer-animate {
  transition: top .25s ease;
  animation: layerIn .35s cubic-bezier(.22,1,.36,1);
}
@keyframes layerIn {
  from { opacity: 0; transform: translateX(-50%) translateY(-18px) scaleX(.88); }
  to   { opacity: 1; transform: translateX(-50%) translateY(0) scaleX(1); }
}

/* Ingredient list */
.bb-ingredient-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
  border-top: 1px solid #161616;
  padding-top: 1rem;
}
.bb-ing-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 0;
  border-bottom: 1px solid #0e0e0e;
}
.bb-ing-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}
.bb-ing-name { flex: 1; font-size: .85rem; color: #aaa; }
.bb-ing-qty  { font-size: .75rem; color: #444; }
.bb-ing-price { font-size: .85rem; color: #D2691E; font-weight: 600; min-width: 46px; text-align: right; }
.bb-ing-empty { font-size: .85rem; color: #333; padding: .5rem 0; }

.bb-price-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: .75rem 0 0;
  border-top: 1px solid #1a1a1a;
  font-size: .75rem;
  color: #555;
  letter-spacing: .1em;
  text-transform: uppercase;
}
.bb-price-total { font-size: 1.8rem; font-weight: 900; color: #D2691E; }

/* RIGHT */
.bb-right {
  padding: 3rem 3rem 6rem;
  display: flex;
  flex-direction: column;
  gap: 3rem;
}
@media (max-width: 960px) { .bb-right { padding: 2rem 1.5rem 4rem; } }

.bb-section {}
.bb-section-header {
  display: flex;
  align-items: baseline;
  gap: 1rem;
  margin-bottom: 1.2rem;
  padding-bottom: .8rem;
  border-bottom: 1px solid #141414;
}
.bb-section-num {
  font-size: .72rem;
  font-weight: 700;
  color: #D2691E;
  letter-spacing: .1em;
}
.bb-section-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
  flex: 1;
}
.bb-section-hint {
  font-size: .7rem;
  color: #333;
  letter-spacing: .08em;
  text-transform: uppercase;
}

.bb-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 8px;
}

/* Cards */
.bb-card {
  background: #111;
  border: 1px solid #1c1c1c;
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: border-color .18s, background .18s, transform .14s;
  position: relative;
  user-select: none;
}
.bb-card:hover {
  border-color: #2a2a2a;
  background: #161616;
  transform: translateY(-2px);
}
.bb-card--active {
  border-color: #D2691E !important;
  background: rgba(210,105,30,.07) !important;
}
.bb-card-icon { font-size: 1.1rem; margin-bottom: .4rem; }
.bb-card-name { font-size: .88rem; font-weight: 500; color: #ccc; margin-bottom: .3rem; line-height: 1.3; }
.bb-card-price { font-size: .8rem; color: #D2691E; font-weight: 600; }
.bb-card-check {
  position: absolute;
  top: 6px; right: 6px;
  width: 18px; height: 18px;
  border-radius: 50%;
  background: #D2691E;
  display: flex; align-items: center; justify-content: center;
  font-size: .62rem; color: #fff; font-weight: 800;
}

.bb-name-input {
  width: 100%;
  background: #111;
  border: 1px solid #1c1c1c;
  border-radius: 6px;
  padding: .85rem 1.1rem;
  color: #fff;
  font-size: .95rem;
  outline: none;
  transition: border-color .18s;
}
.bb-name-input:focus { border-color: #D2691E; }

/* Actions */
.bb-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid #141414;
}
.bb-actions-price {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
}
.bb-actions-price-label { font-size: .72rem; color: #444; text-transform: uppercase; letter-spacing: .1em; }
.bb-actions-price-val { font-size: 2rem; font-weight: 900; color: #D2691E; }
.bb-actions-btns { display: flex; gap: .6rem; flex-wrap: wrap; }

.bb-btn-primary {
  background: #D2691E;
  color: #fff;
  border: none;
  padding: .85rem 1.6rem;
  border-radius: 6px;
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  transition: background .18s, opacity .18s;
  flex: 1;
}
.bb-btn-primary:hover { background: #E07A2E; }
.bb-btn-primary:disabled { opacity: .28; cursor: not-allowed; }

.bb-btn-review {
  background: transparent;
  color: #D2691E;
  border: 1px solid #D2691E;
  padding: .85rem 1.6rem;
  border-radius: 6px;
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  transition: background .18s, opacity .18s;
  white-space: nowrap;
}
.bb-btn-review:hover { background: rgba(210,105,30,.1); }
.bb-btn-review:disabled { opacity: .28; cursor: not-allowed; }

.bb-btn-secondary {
  background: transparent;
  color: #888;
  border: 1px solid #222;
  padding: .85rem 1.2rem;
  border-radius: 6px;
  font-size: .85rem;
  font-weight: 600;
  cursor: pointer;
  transition: border-color .18s, color .18s;
}
.bb-btn-secondary:hover { border-color: #444; color: #ccc; }
.bb-btn-secondary:disabled { opacity: .28; cursor: not-allowed; }

/* SAVED */
.bb-saved { padding: 3rem 0 5rem; border-top: 1px solid #141414; }
.bb-saved-card { background:#111; border:1px solid #1c1c1c; border-radius:12px; padding:1.2rem; display:flex; flex-direction:column; gap:.75rem; }
.bb-saved-card-header { display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:wrap; }
.bb-saved-card-name { font-size:1rem; font-weight:700; color:#fff; }
.bb-saved-card-price { font-size:1.1rem; font-weight:700; color:#D2691E; }
.bb-status-badge { font-size:.68rem; font-weight:700; padding:3px 8px; border-radius:99px; text-transform:uppercase; letter-spacing:.06em; }
.bb-status-draft    { background:rgba(100,100,100,.15); color:#888; border:1px solid #333; }
.bb-status-pending  { background:rgba(250,200,0,.1); color:#F0B800; border:1px solid rgba(250,200,0,.25); }
.bb-status-approved { background:rgba(34,197,94,.1); color:#22c55e; border:1px solid rgba(34,197,94,.25); }
.bb-status-rejected { background:rgba(239,68,68,.1); color:#ef4444; border:1px solid rgba(239,68,68,.25); }
.bb-rejected-note { font-size:.8rem; color:#888; background:#0e0e0e; border:1px solid #1a1a1a; border-radius:6px; padding:.5rem .75rem; }
.bb-saved-card-btns { display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
.bb-saved-pending-hint { font-size:.78rem; color:#F0B800; font-style:italic; }
.bb-saved-approved-hint { font-size:.78rem; color:#22c55e; font-weight:600; }
.bb-saved-btn { background:transparent; border:1px solid #222; color:#888; padding:.5rem .9rem; border-radius:6px; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .15s; }
.bb-saved-btn:hover { border-color:#444; color:#ccc; }
.bb-saved-btn-orange { border-color:#D2691E !important; color:#D2691E !important; }
.bb-saved-btn-orange:hover { background:rgba(210,105,30,.1) !important; }
.bb-saved-inner { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
.bb-saved-title { font-size: clamp(2rem, 6vw, 4rem); font-weight: 900; color: #fff; margin: .4rem 0 .3rem; letter-spacing: -.02em; }
.bb-saved-count { font-size: .8rem; color: #333; margin-bottom: 2.5rem; }
.bb-saved-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.2rem; }
</style>