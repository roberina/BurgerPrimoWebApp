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

      <!-- KIIRTELLIMUS RIBA -->
      <section v-if="favoriteBurgers.length > 0" class="bb-quick">
        <div class="bb-quick-inner">
          <p class="bb-quick-label">⚡ Kiirtellimus</p>
          <div class="bb-quick-scroll">
            <div v-for="burger in favoriteBurgers" :key="burger.id" class="bb-quick-card">
              <div class="bb-quick-card-info">
                <span class="bb-quick-card-name">{{ burger.name }}</span>
                <span class="bb-quick-card-price">{{ Number(burger.total_price).toFixed(2) }}€</span>
              </div>
              <button
                v-if="burger.status === 'approved'"
                @click="quickOrder(burger)"
                class="bb-quick-btn"
              >🛒 Telli</button>
              <span v-else class="bb-quick-pending">{{ statusLabel(burger.status) }}</span>
            </div>
          </div>
        </div>
      </section>

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
                  <!-- Top bun: warm golden-amber 3D -->
                  <radialGradient id="gTB" cx="32%" cy="14%" r="80%">
                    <stop offset="0%"   stop-color="#FFC84A"/>
                    <stop offset="22%"  stop-color="#E07820"/>
                    <stop offset="55%"  stop-color="#A84808"/>
                    <stop offset="100%" stop-color="#501800"/>
                  </radialGradient>
                  <!-- Top bun rim -->
                  <linearGradient id="gTBRim" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#6A2808"/>
                    <stop offset="100%" stop-color="#2A0C00"/>
                  </linearGradient>
                  <!-- Bottom bun -->
                  <radialGradient id="gBB" cx="30%" cy="16%" r="74%">
                    <stop offset="0%"   stop-color="#EEA040"/>
                    <stop offset="38%"  stop-color="#C06820"/>
                    <stop offset="100%" stop-color="#481800"/>
                  </radialGradient>
                  <!-- Sesame seed -->
                  <radialGradient id="gSd" cx="24%" cy="20%" r="72%">
                    <stop offset="0%"   stop-color="#FFF4CC"/>
                    <stop offset="52%"  stop-color="#D4BC40"/>
                    <stop offset="100%" stop-color="#806010"/>
                  </radialGradient>
                  <!-- Soft drop shadow -->
                  <filter id="fds" x="-12%" y="-12%" width="124%" height="150%">
                    <feDropShadow dx="0" dy="5" stdDeviation="5" flood-color="rgba(0,0,0,0.55)"/>
                  </filter>
                  <filter id="fds2" x="-8%" y="-8%" width="116%" height="140%">
                    <feDropShadow dx="0" dy="3" stdDeviation="3.5" flood-color="rgba(0,0,0,0.40)"/>
                  </filter>
                  <!-- Ambient glow under burger -->
                  <radialGradient id="gGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="rgba(210,105,30,0.35)"/>
                    <stop offset="100%" stop-color="rgba(210,105,30,0)"/>
                  </radialGradient>
                </defs>

                <!-- Ambient glow under burger -->
                <ellipse cx="130" :cy="svgH-2" rx="100" ry="12" fill="url(#gGlow)"/>
                <!-- Ground shadow -->
                <ellipse cx="130" :cy="svgH+2" rx="90" ry="5" fill="rgba(0,0,0,0.45)"/>

                <!-- TOP BUN -->
                <g filter="url(#fds)">
                  <!-- Bun rim (base strip) -->
                  <rect x="26" :y="svgTopRim" width="208" height="14" rx="7" fill="url(#gTBRim)"/>
                  <!-- Main dome -->
                  <path :d="`M26,${svgTopRim+2} C26,${svgTopRim-BUN_DOME+4} 234,${svgTopRim-BUN_DOME+4} 234,${svgTopRim+2} Z`" fill="url(#gTB)"/>
                  <!-- Large primary shine -->
                  <ellipse cx="98" :cy="svgTopRim-BUN_DOME*0.50" rx="56" :ry="BUN_DOME*0.20" fill="rgba(255,235,150,0.20)"/>
                  <!-- Small specular highlight -->
                  <ellipse cx="82" :cy="svgTopRim-BUN_DOME*0.64" rx="22" :ry="BUN_DOME*0.10" fill="rgba(255,252,210,0.18)"/>
                  <!-- Rim shadow line -->
                  <rect x="26" :y="svgTopRim+6" width="208" height="8" rx="4" fill="rgba(0,0,0,0.20)"/>
                  <!-- Seeds -->
                  <g v-for="s in seeds" :key="s.x">
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d+2" :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="rgba(10,2,0,0.35)"/>
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d"   :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="url(#gSd)"/>
                    <ellipse :cx="s.x-s.w*0.3" :cy="svgTopRim-s.d-s.h*0.3" :rx="s.w*0.35" :ry="s.h*0.3" fill="rgba(255,248,200,0.45)" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`"/>
                    <line :x1="s.x-s.w+1.5" :y1="svgTopRim-s.d" :x2="s.x+s.w-1.5" :y2="svgTopRim-s.d" stroke="rgba(70,38,2,0.4)" stroke-width="0.9"/>
                  </g>
                </g>

                <!-- INGREDIENT LAYERS -->
                <g v-for="(L,i) in layers3" :key="i" filter="url(#fds2)">

                  <!-- PATTY -->
                  <g v-if="L.t==='p'">
                    <!-- Bottom shadow -->
                    <rect x="22" :y="L.y+L.h*0.55" width="216" :height="L.h*0.48+3" rx="10" :fill="L.s"/>
                    <!-- Main patty body -->
                    <rect x="22" :y="L.y" width="216" :height="L.h" rx="10" :fill="L.c"/>
                    <clipPath :id="`cp${i}`"><rect x="22" :y="L.y" width="216" :height="L.h" rx="10"/></clipPath>
                    <g :clip-path="`url(#cp${i})`">
                      <!-- Grill marks (dark) -->
                      <rect v-for="g in [44,72,102,132,162,192]" :key="g" :x="g" :y="L.y" width="7" :height="L.h" fill="rgba(2,0,0,0.58)"/>
                      <!-- Grill mark hot-glow edge -->
                      <rect v-for="g in [51,79,109,139,169,199]" :key="`e${g}`" :x="g" :y="L.y" width="2" :height="L.h" fill="rgba(200,60,0,0.18)"/>
                      <!-- Caramelised dark spots -->
                      <ellipse cx="65"  :cy="L.y+L.h*0.55" rx="12" :ry="L.h*0.28" fill="rgba(20,2,0,0.38)"/>
                      <ellipse cx="118" :cy="L.y+L.h*0.4"  rx="9"  :ry="L.h*0.22" fill="rgba(20,2,0,0.30)"/>
                      <ellipse cx="175" :cy="L.y+L.h*0.6"  rx="14" :ry="L.h*0.26" fill="rgba(20,2,0,0.32)"/>
                      <!-- Top surface sheen -->
                      <rect x="22" :y="L.y" width="216" height="3" fill="rgba(255,110,20,0.12)"/>
                    </g>
                  </g>

                  <!-- CHEESE -->
                  <g v-else-if="L.t==='c'">
                    <!-- Melt drips hanging below -->
                    <ellipse v-for="d in [28,58,92,124,158,192,220]" :key="d" :cx="d" :cy="L.y+L.h+6" rx="5.5" ry="7" :fill="L.c"/>
                    <!-- Darker base shadow -->
                    <rect x="5" :y="L.y+5" width="250" height="7" rx="3" :fill="L.s"/>
                    <!-- Main cheese slab -->
                    <rect x="5" :y="L.y" width="250" :height="L.h+2" rx="3" :fill="L.c"/>
                    <!-- Top gloss -->
                    <rect x="5" :y="L.y" width="250" height="3.5" rx="2" fill="rgba(255,255,200,0.32)"/>
                    <!-- Oil sheen streaks -->
                    <ellipse v-for="(e,ei) in [{x:55},{x:108},{x:165},{x:215}]" :key="ei" :cx="e.x" :cy="L.y+L.h/2" rx="22" ry="2" fill="rgba(255,255,160,0.18)"/>
                  </g>

                  <!-- LETTUCE -->
                  <g v-else-if="L.t==='l'">
                    <path :d="lp(L.y,true)"  :fill="L.s"/>
                    <path :d="lp(L.y,false)" :fill="L.c"/>
                    <!-- Vein highlights -->
                    <line x1="60" :y1="L.y+4" x2="80" :y2="L.y+8" stroke="rgba(255,255,255,0.12)" stroke-width="1.2" stroke-linecap="round"/>
                    <line x1="130" :y1="L.y+3" x2="155" :y2="L.y+7" stroke="rgba(255,255,255,0.10)" stroke-width="1" stroke-linecap="round"/>
                    <line x1="185" :y1="L.y+5" x2="200" :y2="L.y+9" stroke="rgba(255,255,255,0.10)" stroke-width="1" stroke-linecap="round"/>
                  </g>

                  <!-- TOMATO -->
                  <g v-else-if="L.t==='t'">
                    <!-- Shadow edge -->
                    <rect x="16" :y="L.y+L.h*0.55" width="228" :height="L.h*0.48" rx="4" :fill="L.s"/>
                    <!-- Main slice -->
                    <rect x="16" :y="L.y" width="228" :height="L.h" rx="5" :fill="L.c"/>
                    <!-- Seed pockets (lighter gel) -->
                    <ellipse v-for="x in [46,84,126,166,206]" :key="x" :cx="x" :cy="L.y+L.h/2" rx="7" ry="4" fill="rgba(255,220,190,0.55)"/>
                    <!-- Seeds inside pockets -->
                    <ellipse v-for="x in [46,84,126,166,206]" :key="`s${x}`" :cx="x" :cy="L.y+L.h/2" rx="2.5" ry="1.5" fill="rgba(200,120,60,0.7)"/>
                    <!-- Surface sheen -->
                    <rect x="16" :y="L.y" width="228" height="3" rx="2" fill="rgba(255,200,180,0.28)"/>
                  </g>

                  <!-- CUCUMBER -->
                  <g v-else-if="L.t==='k'">
                    <rect x="14" :y="L.y+L.h*0.55" width="232" :height="L.h*0.48" rx="3" fill="#1A5006"/>
                    <rect x="14" :y="L.y" width="232" :height="L.h" rx="4" :fill="L.c"/>
                    <!-- Seed line -->
                    <ellipse v-for="x in [42,82,124,164,204]" :key="x" :cx="x" :cy="L.y+L.h/2" rx="5" ry="2.5" fill="rgba(220,255,200,0.35)"/>
                    <rect x="14" :y="L.y" width="232" height="2.5" rx="2" fill="rgba(180,255,160,0.22)"/>
                  </g>

                  <!-- ONION -->
                  <g v-else-if="L.t==='o'">
                    <!-- Left ring -->
                    <ellipse cx="72"  :cy="L.y+L.h/2" rx="52" :ry="L.h/2+2" fill="rgba(200,80,220,0.09)" stroke="#D070E8" stroke-width="3"/>
                    <ellipse cx="72"  :cy="L.y+L.h/2" rx="34" :ry="L.h/2-2" fill="none" stroke="#B058CC" stroke-width="2"/>
                    <ellipse cx="72"  :cy="L.y+L.h/2" rx="16" :ry="L.h/2-5" fill="none" stroke="#9040AA" stroke-width="1.5" stroke-dasharray="3,2"/>
                    <!-- Right ring -->
                    <ellipse cx="188" :cy="L.y+L.h/2" rx="52" :ry="L.h/2+2" fill="rgba(200,80,220,0.09)" stroke="#D070E8" stroke-width="3"/>
                    <ellipse cx="188" :cy="L.y+L.h/2" rx="34" :ry="L.h/2-2" fill="none" stroke="#B058CC" stroke-width="2"/>
                    <ellipse cx="188" :cy="L.y+L.h/2" rx="16" :ry="L.h/2-5" fill="none" stroke="#9040AA" stroke-width="1.5" stroke-dasharray="3,2"/>
                  </g>

                  <!-- AVOCADO -->
                  <g v-else-if="L.t==='a'">
                    <rect x="14" :y="L.y+L.h*0.55" width="232" :height="L.h*0.48" rx="3" fill="#254008"/>
                    <rect x="14" :y="L.y" width="232" :height="L.h" rx="4" :fill="L.c"/>
                    <!-- Lighter patches (fat marbling) -->
                    <ellipse v-for="(e,ei) in [{x:60},{x:120},{x:180}]" :key="ei" :cx="e.x" :cy="L.y+L.h/2" rx="20" ry="3.5" fill="rgba(160,220,100,0.22)"/>
                    <rect x="14" :y="L.y" width="232" height="2.5" rx="2" fill="rgba(200,255,150,0.18)"/>
                  </g>

                  <!-- SAUCE -->
                  <g v-else-if="L.t==='s'">
                    <rect x="18" :y="L.y+L.h*0.55" width="224" :height="L.h*0.48" rx="2" :fill="L.s"/>
                    <rect x="18" :y="L.y" width="224" :height="L.h" rx="3" :fill="L.c"/>
                    <!-- Sauce sheen / gloss -->
                    <rect x="18" :y="L.y" width="224" height="2.5" rx="1.5" fill="rgba(255,255,255,0.20)"/>
                    <!-- Sauce blobs -->
                    <ellipse v-for="(e,ei) in [{x:55,r:18},{x:120,r:22},{x:185,r:16}]" :key="ei" :cx="e.x" :cy="L.y+L.h/2" :rx="e.r" ry="1.8" fill="rgba(255,255,255,0.10)"/>
                  </g>

                </g>

                <!-- BOTTOM BUN -->
                <g filter="url(#fds)">
                  <!-- Shadow base -->
                  <rect x="26" :y="svgBotY+20" width="208" height="8" rx="4" fill="#280A00"/>
                  <!-- Main bun body -->
                  <rect x="26" :y="svgBotY" width="208" height="24" rx="10" fill="url(#gBB)"/>
                  <!-- Primary shine -->
                  <ellipse cx="100" :cy="svgBotY+9" rx="48" ry="7" fill="rgba(255,210,120,0.20)"/>
                  <!-- Small specular -->
                  <ellipse cx="82"  :cy="svgBotY+6" rx="20" ry="3.5" fill="rgba(255,240,180,0.16)"/>
                  <!-- Bottom edge shadow -->
                  <rect x="26" :y="svgBotY+17" width="208" height="7" rx="4" fill="rgba(0,0,0,0.22)"/>
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

            <button v-if="customBurgers?.length > 0" @click="scrollToSaved" class="bb-my-burgers-btn">
              <span>🍔</span>
              <span>Minu burgerid</span>
              <span class="bb-my-burgers-count">{{ customBurgers.length }}</span>
            </button>
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
            <div class="bb-section-header"><span class="bb-section-num">02</span><h2 class="bb-section-title">Pihvid</h2><span class="bb-section-hint">vali kuni 2</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['pitav']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('pitav', ing.id) }" @click="togglePatty(ing.id)">
                <div class="bb-card-icon">🥩</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('pitav', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['salat']?.length">
            <div class="bb-section-header"><span class="bb-section-num">03</span><h2 class="bb-section-title">Köögiviljad</h2><span class="bb-section-hint">vali mitu</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['salat']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('salat', ing.id) }" @click="toggleMulti('salat', ing.id)">
                <div class="bb-card-icon">🥬</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('salat', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['lisand']?.length">
            <div class="bb-section-header"><span class="bb-section-num">04</span><h2 class="bb-section-title">Kastmed & lisandid</h2><span class="bb-section-hint">vali mitu</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['lisand']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('lisand', ing.id) }" @click="toggleMulti('lisand', ing.id)">
                <div class="bb-card-icon">🫙</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('lisand', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['juust']?.length">
            <div class="bb-section-header"><span class="bb-section-num">05</span><h2 class="bb-section-title">Juust</h2><span class="bb-section-hint">vali mitu</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['juust']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('juust', ing.id) }" @click="toggleMulti('juust', ing.id)">
                <div class="bb-card-icon">🧀</div><div class="bb-card-name">{{ ing.name }}</div>
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
              <button @click="saveAndSubmit()"   :disabled="!canSave" class="bb-btn-review">Saada adminile ↗</button>
            </div>
          </div>
        </div>
      </section>

      <section v-if="customBurgers?.length > 0" class="bb-saved" id="my-burgers">
        <div class="bb-saved-inner">
          <h2 class="bb-saved-title">Minu burgerid</h2>
          <p class="bb-saved-count">{{ customBurgers.length }} burger{{ customBurgers.length !== 1 ? "it" : "" }} salvestatud</p>
          <div class="bb-saved-grid">
            <div v-for="burger in customBurgers" :key="burger.id" class="bb-saved-card">
              <div class="bb-saved-card-header">
                <span class="bb-saved-card-name">{{ burger.name }}</span>
                <span class="bb-status-badge" :class="'bb-status-' + (burger.status || 'draft')">{{ statusLabel(burger.status) }}</span>
              </div>
              <p class="bb-saved-card-price">{{ Number(burger.total_price).toFixed(2) }}€</p>
              <div v-if="burger.status === 'rejected' && burger.admin_note" class="bb-rejected-note">💬 {{ burger.admin_note }}</div>
              <div class="bb-saved-card-btns">
                <button @click="toggleBurgerFavorite(burger.id, burger.is_favorite)" class="bb-saved-btn bb-saved-btn-fav" :class="{ 'bb-saved-btn-fav--active': burger.is_favorite }" :title="burger.is_favorite ? 'Eemalda lemmikutest' : 'Lisa kiirtellimusse'">{{ burger.is_favorite ? '♥' : '♡' }}</button>
                <button @click="loadBurger(burger)" class="bb-saved-btn">Muuda</button>
                <button v-if="burger.status === 'approved'" @click="quickOrder(burger)" class="bb-saved-btn bb-saved-btn-green">🛒 Telli</button>
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
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Ingredient, SelectedIngredient, CustomBurger } from '@/types/burger-types';

interface Props {
  ingredients: Record<string, Ingredient[]> | any;
  customBurgers: CustomBurger[];
  canCreateMore: boolean;
  maxBurgers: number;
}
const props = defineProps<Props>();
const success = (msg: string) => console.log('✅', msg);
const error   = (msg: string) => console.error('❌', msg);

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

const burgerName = ref('');
const selectedIngredients = ref<Record<string, SelectedIngredient[]>>({
  'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [],
});

const isSelected = (cat: string, id: number) =>
  selectedIngredients.value[cat]?.some(i => i.id === id) ?? false;

const togglePatty = (id: number) => {
  const arr = selectedIngredients.value['pitav'] ?? [];
  const idx = arr.findIndex(i => i.id === id);
  if (idx >= 0) selectedIngredients.value['pitav'] = arr.filter(i => i.id !== id);
  else if (arr.length < 2) selectedIngredients.value['pitav'] = [...arr, { id, quantity: 1 }];
};
const toggleMulti = (cat: string, id: number) => {
  const arr = selectedIngredients.value[cat] ?? [];
  const idx = arr.findIndex(i => i.id === id);
  if (idx >= 0) selectedIngredients.value[cat] = arr.filter(i => i.id !== id);
  else selectedIngredients.value[cat] = [...arr, { id, quantity: 1 }];
};

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
const totalPrice  = computed(() => allSelectedFlat.value.reduce((t, i) => t + i.price * i.quantity, 0));
const favoriteBurgers = computed(() => (props.customBurgers ?? []).filter(b => b.is_favorite));
const canSave     = computed(() => burgerName.value.trim() !== '' && totalLayers.value > 0);

const categoryColor = (cat: string) => {
  const map: Record<string, string> = {
    'vöi': '#D4822E', 'pitav': '#8B4513', 'salat': '#4CAF50', 'lisand': '#FDD835', 'juust': '#F9A825',
  };
  return map[cat] ?? '#666';
};

const getAllSelected = (): SelectedIngredient[] => {
  const all: SelectedIngredient[] = [];
  Object.values(selectedIngredients.value).forEach(i => all.push(...i));
  return all;
};

const saveBurger = (fav: boolean) => {
  if (!props.canCreateMore) { alert(`Limit: ${props.maxBurgers}`); return; }
  router.post('/burger-builder', { name: burgerName.value, ingredients: getAllSelected(), is_favorite: fav } as any, {
    preserveScroll: true,
    onSuccess: () => {
      success('Burger salvestatud! 🍔');
      burgerName.value = '';
      selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
      setTimeout(() => scrollToSaved(), 400);
    },
    onError: () => error('Salvestamine ebaõnnestus'),
  });
};

const saveAndSubmit = () => {
  if (!canSave.value) return;
  if (!props.canCreateMore) { alert(`Limit: ${props.maxBurgers}`); return; }
  router.post('/burger-builder', {
    name: burgerName.value, ingredients: getAllSelected(), is_favorite: false, submit_for_review: true,
  } as any, {
    preserveScroll: true,
    onSuccess: () => {
      burgerName.value = '';
      selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
    }
  });
};

const submitForReviewById = (id: number) => router.post(`/burger-builder/${id}/submit`, {}, { preserveScroll: true });
const statusLabel = (status: string) => ({ draft:'Mustand', pending:'Ootab kinnitust', approved:'Kinnitatud', rejected:'Tagasi lükatud' }[status] ?? status);

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
const deleteBurger = (id: number) => router.delete(`/burger-builder/${id}`, { preserveScroll: true });
const loadBurger = (burger: CustomBurger) => {
  burgerName.value = burger.name;
  selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
  burger.ingredients.forEach(ing => {
    const cat = ing.category;
    if (selectedIngredients.value[cat]) selectedIngredients.value[cat].push({ id: ing.id, quantity: ing.pivot.quantity });
  });
};
const scrollToSaved = () => {
  document.getElementById('my-burgers')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

// ═══ SVG BURGER ═══
const BUN_DOME = 62
const BOT_H    = 22
const RIM_H    = 10
const START_Y  = 8
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
  let y = START_Y + BUN_DOME + RIM_H + GAP
  for(const l of out){ l.y=y; y+=l.h+GAP }
  return out
}
function gn(id:number):string{ return getAllIngredients().find(i=>i.id===id)?.name?.toLowerCase()??'' }
function pC(item:{id:number}):string{
  const n=gn(item.id)
  if(n.includes('kana'))  return '#A87040'
  if(n.includes('vegan')) return '#607830'
  if(n.includes('grill')) return '#5E1E08'
  if(n.includes('veise')) return '#8A3418'
  return '#8C3C16'
}
function pS():string{return '#1C0402'}
function cC(n:string):string{if(n.includes('mozzarella'))return '#F5EEE0';if(n.includes('blue'))return '#A8B8C8';return '#E8A40E'}
function cS(n:string):string{if(n.includes('mozzarella'))return '#CCBCA0';if(n.includes('blue'))return '#687899';return '#A07208'}
function vD(n:string):[string,number,string,string]{
  if(n.includes('tomat'))return ['t',13,'#C01008','#700404']
  if(n.includes('kurk')) return ['k',9,'#559018','#185808']
  if(n.includes('sibul')||n.includes('kastrull'))return ['o',11,'transparent','transparent']
  if(n.includes('avocado'))return ['a',10,'#6A9818','#2E4E08']
  return ['l',13,'#389A0C','#145C08']
}
function sC(n:string):string{
  if(n.includes('ketšup'))  return '#BE1808'
  if(n.includes('majonees'))return '#F2F2E4'
  if(n.includes('bbq'))     return '#5C1208'
  if(n.includes('chipotle'))return '#9C2608'
  if(n.includes('peekon'))  return '#A41C1C'
  if(n.includes('muna'))    return '#D8B218'
  return '#C49008'
}
function sS(n:string):string{
  if(n.includes('ketšup'))  return '#600606'
  if(n.includes('majonees'))return '#C0C098'
  return '#8C6808'
}
function lp(y:number, back:boolean):string{
  const b = back ? y+5 : y
  return `M14,${b+8} Q34,${b} 54,${b+7} Q74,${b+15} 94,${b+3} Q114,${b-8} 134,${b+3} Q154,${b+14} 174,${b+1} Q194,${b-9} 214,${b-1} Q234,${b+9} 254,${b+1} L254,${b+13} Q234,${b+21} 214,${b+11} Q194,${b+23} 174,${b+13} Q154,${b+26} 134,${b+13} Q114,${b+1} 94,${b+15} Q74,${b+27} 54,${b+15} Q34,${b+5} 14,${b+16} Z`
}

const layers3   = computed(()=>mkL())
const svgTopRim = computed(()=> START_Y + BUN_DOME)
const svgBotY   = computed(()=>{
  const ls=layers3.value
  if(!ls.length) return svgTopRim.value + RIM_H + GAP + 4
  const last=ls[ls.length-1]
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

onMounted(async () => {});
</script>

<style scoped>
* { box-sizing: border-box; }
/* ── Root ── */
.bb-root { background:#080808; color:#fff; min-height:100vh; }

/* ── Header ── */
.bb-page-header { background:#080808; border-bottom:1px solid #161616; padding:5rem 3rem 4rem; position:relative; overflow:hidden; }
.bb-page-header::before { content:''; position:absolute; top:-80px; left:-80px; width:500px; height:500px; background:radial-gradient(circle, rgba(210,105,30,.07) 0%, transparent 65%); pointer-events:none; }
.bb-page-header::after { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.015'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); pointer-events:none; }
.bb-page-header-inner { max-width:1300px; margin:0 auto; position:relative; z-index:1; }
.bb-eyebrow { font-size:.7rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:#D2691E; margin-bottom:.9rem; display:flex; align-items:center; gap:.6rem; }
.bb-eyebrow::before { content:''; display:block; width:24px; height:1px; background:#D2691E; opacity:.6; }
.bb-page-title { font-size:clamp(2.2rem,5vw,4rem); font-weight:900; color:#fff; letter-spacing:-.03em; margin-bottom:.6rem; line-height:1; }
.bb-page-sub { font-size:1rem; color:#444; }

/* ── Builder grid ── */
.bb-builder { display:grid; grid-template-columns:440px 1fr; align-items:start; border-bottom:1px solid #141414; }
@media (max-width:960px) { .bb-builder { grid-template-columns:1fr; } }

/* ── Left panel ── */
.bb-left { border-right:1px solid #141414; position:sticky; top:0; height:100vh; overflow:hidden; background:#0a0a0a; }
@media (max-width:960px) { .bb-left { position:static; height:auto; border-right:none; border-bottom:1px solid #141414; } }
.bb-burger-panel { height:100%; overflow-y:auto; padding:2.5rem 2rem; display:flex; flex-direction:column; gap:1.5rem; scrollbar-width:none; }
.bb-burger-panel::-webkit-scrollbar { display:none; }
@media (max-width:960px) { .bb-burger-panel { padding:1.5rem 1rem; height:auto; overflow:visible; } }
@media (max-width:960px) { .bb-burger-visual { min-height:140px; } }
@media (max-width:960px) { .bb-burger-visual svg { width:160px !important; } }
.bb-burger-title { display:flex; align-items:center; justify-content:space-between; }
.bb-burger-name-display { font-size:.8rem; color:#D2691E; font-weight:700; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; background:rgba(210,105,30,.08); border:1px solid rgba(210,105,30,.2); padding:3px 10px; border-radius:99px; }
.bb-burger-visual { position:relative; width:100%; display:flex; align-items:center; justify-content:center; min-height:220px; padding:16px 0; background:radial-gradient(ellipse at 50% 80%, rgba(180,80,10,.13) 0%, transparent 65%); border-radius:16px; }

/* ── Ingredient list (left panel) ── */
.bb-ingredient-list { display:flex; flex-direction:column; gap:1px; border-top:1px solid #141414; padding-top:1rem; }
.bb-ing-row { display:flex; align-items:center; gap:9px; padding:6px 0; border-bottom:1px solid #0d0d0d; }
.bb-ing-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; box-shadow:0 0 5px currentColor; }
.bb-ing-name { flex:1; font-size:.84rem; color:#999; }
.bb-ing-qty { font-size:.74rem; color:#444; }
.bb-ing-price { font-size:.84rem; color:#D2691E; font-weight:700; min-width:46px; text-align:right; }
.bb-ing-empty { font-size:.84rem; color:#2a2a2a; padding:.6rem 0; font-style:italic; }
.bb-price-row { display:flex; justify-content:space-between; align-items:baseline; padding:1rem 0 0; border-top:1px solid #161616; font-size:.7rem; color:#444; letter-spacing:.12em; text-transform:uppercase; }
.bb-price-total { font-size:2rem; font-weight:900; color:#D2691E; line-height:1; }
.bb-my-burgers-btn { display:flex; align-items:center; gap:.6rem; background:rgba(210,105,30,.06); border:1px solid rgba(210,105,30,.2); color:#D2691E; padding:.8rem 1.1rem; border-radius:10px; cursor:pointer; font-size:.85rem; font-weight:700; transition:all .2s; width:100%; justify-content:center; }
.bb-my-burgers-btn:hover { background:rgba(210,105,30,.12); border-color:rgba(210,105,30,.4); }
.bb-my-burgers-count { background:#D2691E; color:#fff; border-radius:99px; padding:1px 8px; font-size:.7rem; font-weight:800; margin-left:auto; }

/* ── Right panel ── */
.bb-right { padding:3rem 3.5rem 7rem; display:flex; flex-direction:column; gap:3.5rem; }
@media (max-width:960px) { .bb-right { padding:2rem 1.5rem 5rem; } }

/* ── Section headers ── */
.bb-section-header { display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #141414; }
.bb-section-num { width:30px; height:30px; border-radius:50%; background:rgba(210,105,30,.1); border:1px solid rgba(210,105,30,.25); display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:800; color:#D2691E; flex-shrink:0; }
.bb-section-title { font-size:1.2rem; font-weight:800; color:#fff; flex:1; letter-spacing:-.01em; }
.bb-section-hint { font-size:.68rem; color:#444; letter-spacing:.06em; text-transform:uppercase; background:#0e0e0e; border:1px solid #1e1e1e; padding:3px 10px; border-radius:99px; }

/* ── Ingredient cards ── */
.bb-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px; }
.bb-card { background:#0d0d0d; border:1px solid #1a1a1a; border-radius:12px; padding:1.1rem 1rem; cursor:pointer; transition:all .2s cubic-bezier(.2,0,0,1); position:relative; user-select:none; overflow:hidden; }
.bb-card::after { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg, transparent, rgba(210,105,30,.6), transparent); opacity:0; transition:opacity .2s; }
.bb-card:hover { border-color:#282828; background:#121212; transform:translateY(-3px); box-shadow:0 10px 30px rgba(0,0,0,.5); }
.bb-card:hover::after { opacity:1; }
.bb-card--active { border-color:#D2691E !important; background:rgba(210,105,30,.07) !important; box-shadow:0 0 0 1px rgba(210,105,30,.2), 0 8px 24px rgba(210,105,30,.12) !important; }
.bb-card--active::after { opacity:1 !important; }
.bb-card-icon { font-size:1.6rem; margin-bottom:.6rem; display:block; }
.bb-card-name { font-size:.9rem; font-weight:600; color:#ddd; margin-bottom:.4rem; line-height:1.3; }
.bb-card-price { font-size:.82rem; color:#D2691E; font-weight:700; }
.bb-card-check { position:absolute; top:8px; right:8px; width:20px; height:20px; border-radius:50%; background:linear-gradient(135deg,#D2691E,#B8571A); display:flex; align-items:center; justify-content:center; font-size:.65rem; color:#fff; font-weight:800; box-shadow:0 2px 8px rgba(210,105,30,.5); }

/* ── Name input ── */
.bb-name-input { width:100%; background:#0d0d0d; border:1px solid #1a1a1a; border-radius:10px; padding:1rem 1.2rem; color:#fff; font-size:1rem; font-weight:500; outline:none; transition:all .2s; }
.bb-name-input:focus { border-color:#D2691E; box-shadow:0 0 0 3px rgba(210,105,30,.1); }
.bb-name-input::placeholder { color:#2e2e2e; }

/* ── Actions ── */
.bb-actions { display:flex; flex-direction:column; gap:1.2rem; padding-top:2rem; border-top:1px solid #141414; }
.bb-actions-price { display:flex; justify-content:space-between; align-items:baseline; }
.bb-actions-price-label { font-size:.7rem; color:#444; text-transform:uppercase; letter-spacing:.12em; }
.bb-actions-price-val { font-size:2.2rem; font-weight:900; color:#D2691E; line-height:1; }
.bb-actions-btns { display:flex; gap:.75rem; }
.bb-btn-secondary { background:transparent; color:#555; border:1px solid #1e1e1e; padding:.9rem 1.4rem; border-radius:10px; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .2s; }
.bb-btn-secondary:hover { border-color:#2e2e2e; color:#999; background:#0e0e0e; }
.bb-btn-secondary:disabled { opacity:.2; cursor:not-allowed; }
.bb-btn-review { background:linear-gradient(135deg,#D2691E,#B8571A); color:#fff; border:none; padding:.9rem 1.8rem; border-radius:10px; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; flex:1; box-shadow:0 4px 20px rgba(210,105,30,.3); }
.bb-btn-review:hover { background:linear-gradient(135deg,#E07A2E,#C8671A); transform:translateY(-1px); box-shadow:0 8px 28px rgba(210,105,30,.4); }
.bb-btn-review:disabled { opacity:.25; cursor:not-allowed; transform:none; box-shadow:none; }

/* ── Quick order bar ── */
.bb-quick { background:#060606; border-bottom:1px solid #141414; padding:.9rem 3rem; }
@media (max-width:960px) { .bb-quick { padding:.9rem 1.5rem; } }
.bb-quick-inner { max-width:1300px; margin:0 auto; display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; }
.bb-quick-label { font-size:.68rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:#D2691E; white-space:nowrap; flex-shrink:0; display:flex; align-items:center; gap:.5rem; }
.bb-quick-scroll { display:flex; gap:.6rem; flex-wrap:wrap; flex:1; }
.bb-quick-card { display:flex; align-items:center; gap:.6rem; background:#0e0e0e; border:1px solid #1c1c1c; border-radius:10px; padding:.5rem .9rem; transition:all .15s; }
.bb-quick-card:hover { border-color:#2a2a2a; background:#121212; }
.bb-quick-card-name { font-size:.84rem; font-weight:600; color:#ccc; }
.bb-quick-card-price { font-size:.76rem; color:#D2691E; font-weight:700; }
.bb-quick-btn { background:linear-gradient(135deg,#D2691E,#B8571A); color:#fff; border:none; padding:.32rem .8rem; border-radius:6px; font-size:.76rem; font-weight:700; cursor:pointer; transition:all .15s; white-space:nowrap; }
.bb-quick-btn:hover { background:linear-gradient(135deg,#E07A2E,#C8671A); }
.bb-quick-pending { font-size:.72rem; color:#444; font-style:italic; }

/* ── Saved burgers ── */
.bb-saved { padding:5rem 0 8rem; border-top:1px solid #141414; background:#080808; }
.bb-saved-inner { max-width:1200px; margin:0 auto; padding:0 3rem; }
@media (max-width:960px) { .bb-saved-inner { padding:0 1.5rem; } }
.bb-saved-title { font-size:clamp(2rem,5vw,3.5rem); font-weight:900; color:#fff; margin:.4rem 0 .3rem; letter-spacing:-.03em; }
.bb-saved-count { font-size:.8rem; color:#333; margin-bottom:3rem; }
.bb-saved-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.5rem; }
.bb-saved-card { background:#0d0d0d; border:1px solid #1a1a1a; border-radius:16px; padding:1.5rem; display:flex; flex-direction:column; gap:1rem; transition:border-color .2s,transform .2s,box-shadow .2s; }
.bb-saved-card:hover { border-color:#242424; transform:translateY(-2px); box-shadow:0 12px 32px rgba(0,0,0,.5); }
.bb-saved-card-header { display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:wrap; }
.bb-saved-card-name { font-size:1.05rem; font-weight:800; color:#fff; }
.bb-saved-card-price { font-size:1.3rem; font-weight:900; color:#D2691E; }
.bb-status-badge { font-size:.66rem; font-weight:700; padding:3px 9px; border-radius:99px; text-transform:uppercase; letter-spacing:.06em; }
.bb-status-draft    { background:rgba(80,80,80,.15); color:#666; border:1px solid #282828; }
.bb-status-pending  { background:rgba(250,200,0,.08); color:#E8B000; border:1px solid rgba(250,200,0,.2); }
.bb-status-approved { background:rgba(34,197,94,.08); color:#22c55e; border:1px solid rgba(34,197,94,.2); }
.bb-status-rejected { background:rgba(239,68,68,.08); color:#ef4444; border:1px solid rgba(239,68,68,.2); }
.bb-rejected-note { font-size:.8rem; color:#777; background:#0a0a0a; border:1px solid #181818; border-radius:8px; padding:.6rem .85rem; }
.bb-saved-card-btns { display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
.bb-saved-pending-hint { font-size:.76rem; color:#E8B000; font-style:italic; }
.bb-saved-btn { background:transparent; border:1px solid #1e1e1e; color:#555; padding:.5rem 1rem; border-radius:8px; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .15s; }
.bb-saved-btn:hover { border-color:#2e2e2e; color:#999; background:#0e0e0e; }
.bb-saved-btn-fav { min-width:38px; font-size:1rem; }
.bb-saved-btn-fav--active { border-color:rgba(210,105,30,.5) !important; color:#D2691E !important; background:rgba(210,105,30,.06) !important; }
.bb-saved-btn-green  { border-color:rgba(34,197,94,.35) !important; color:#22c55e !important; }
.bb-saved-btn-green:hover  { background:rgba(34,197,94,.07) !important; }
.bb-saved-btn-orange { border-color:rgba(210,105,30,.35) !important; color:#D2691E !important; }
.bb-saved-btn-orange:hover { background:rgba(210,105,30,.07) !important; }
.bb-saved-btn-red    { border-color:rgba(239,68,68,.3) !important; color:#ef4444 !important; }
.bb-saved-btn-red:hover    { background:rgba(239,68,68,.07) !important; }
</style>