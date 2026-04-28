<template>
  <Head>
    <meta name="description" content="Burger Primo pakub käsitsi valmistatud burgereid, krõbedaid snäkke ja maitsvaid jooke Kuressaares. Telli kohale või tule kohale!" head-key="description" />
    <meta name="robots" content="index, follow" head-key="robots" />
    <meta property="og:title" content="Burger Primo — Parimad burgerid Kuressaares" head-key="og:title" />
    <meta property="og:description" content="Burger Primo pakub käsitsi valmistatud burgereid, krõbedaid snäkke ja maitsvaid jooke Kuressaares. Telli kohale või tule kohale!" head-key="og:description" />
    <meta property="og:type" content="website" head-key="og:type" />
    <meta property="og:url" content="https://burgerprimo.ee" head-key="og:url" />
    <meta property="og:image" content="/img/Logo45.png" head-key="og:image" />   
  </Head>  
  <MainLayout>

    <Teleport to="body">
      <div class="bb-toasts">
        <TransitionGroup name="toast">
          <div v-for="t in toasts" :key="t.id" class="bb-toast" :class="'bb-toast--'+t.type">
            <span class="bb-toast-icon">{{ t.type==='ok' ? '✓' : '✕' }}</span>
            {{ t.msg }}
          </div>
        </TransitionGroup>
      </div>
    </Teleport>

    <div class="bb-root">
      <div class="bb-page-header">
        <div class="bb-page-header-inner">
          <p class="bb-eyebrow">{{ t('bb.eyebrow') }}</p>
          <h1 class="bb-page-title">{{ t('bb.title') }}</h1>
          <p class="bb-page-sub">{{ t('bb.sub') }}</p>
        </div>
      </div>

      <section id="bb-builder" class="bb-builder">
        <div class="bb-left">
          <div class="bb-burger-panel">
            <div class="bb-burger-title">
              <span class="bb-eyebrow">{{ t('bb.name.your') }}</span>
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
                  <!-- Grill mark blur -->
                  <filter id="fGrill" x="-20%" y="-5%" width="140%" height="110%">
                    <feGaussianBlur stdDeviation="1.6"/>
                  </filter>
                  <!-- Onion ring soft blur -->
                  <filter id="fOnion" x="-18%" y="-40%" width="136%" height="180%">
                    <feGaussianBlur stdDeviation="2.4"/>
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
                    <!-- Subtle seed crease (thin dark ellipse, no hard line) -->
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d" :rx="s.w*0.68" :ry="s.h*0.19" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="rgba(55,24,2,0.26)"/>
                  </g>
                </g>

                <!-- INGREDIENT LAYERS — no outer drop-shadow filter (it creates dark stripes between layers) -->
                <g v-for="(L,i) in layers3" :key="i">

                  <!-- PATTY -->
                  <g v-if="L.t==='p'">
                    <rect x="20" :y="L.y" width="220" :height="L.h" rx="11" :fill="L.c"/>
                    <clipPath :id="`cp${i}`"><rect x="20" :y="L.y" width="220" :height="L.h" rx="11"/></clipPath>
                    <g :clip-path="`url(#cp${i})`">
                      <g filter="url(#fGrill)">
                        <path v-for="g in [32,62,94,126,158,188]" :key="g"
                          :d="`M${g},${L.y} L${g+10},${L.y} L${g+18},${L.y+L.h} L${g+8},${L.y+L.h} Z`"
                          fill="rgba(3,0,0,0.55)"/>
                      </g>
                      <ellipse cx="68"  :cy="L.y+L.h*0.55" rx="13" :ry="L.h*0.30" fill="rgba(18,2,0,0.25)"/>
                      <ellipse cx="128" :cy="L.y+L.h*0.38" rx="10" :ry="L.h*0.22" fill="rgba(18,2,0,0.20)"/>
                      <ellipse cx="178" :cy="L.y+L.h*0.60" rx="15" :ry="L.h*0.26" fill="rgba(18,2,0,0.22)"/>
                      <rect x="20" :y="L.y+L.h*0.70" width="220" :height="L.h*0.30" rx="8" fill="rgba(0,0,0,0.18)"/>
                      <ellipse cx="130" :cy="L.y+L.h*0.25" rx="90" :ry="L.h*0.28" fill="rgba(255,120,30,0.12)"/>
                    </g>
                  </g>

                  <!-- CHEESE -->
                  <g v-else-if="L.t==='c'">
                    <ellipse v-for="d in [22,55,90,124,160,195,225]" :key="d" :cx="d" :cy="L.y+L.h+6" rx="8" ry="9" :fill="L.c"/>
                    <rect x="2" :y="L.y" width="256" :height="L.h" rx="6" :fill="L.c"/>
                    <rect x="2" :y="L.y+L.h*0.65" width="256" :height="L.h*0.35" rx="5" fill="rgba(0,0,0,0.12)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.28" rx="115" :ry="L.h*0.32" fill="rgba(255,255,220,0.24)"/>
                  </g>

                  <!-- LETTUCE -->
                  <g v-else-if="L.t==='l'">
                    <path :d="lp(L.y,true)"  :fill="L.s"/>
                    <path :d="lp(L.y,false)" :fill="L.c"/>
                    <ellipse cx="80"  :cy="L.y+5" rx="28" :ry="L.h*0.28" fill="rgba(200,255,180,0.18)"/>
                    <ellipse cx="174" :cy="L.y+4" rx="26" :ry="L.h*0.24" fill="rgba(200,255,180,0.14)"/>
                  </g>

                  <!-- TOMATO -->
                  <g v-else-if="L.t==='t'">
                    <rect x="14" :y="L.y" width="232" :height="L.h" rx="8" :fill="L.c"/>
                    <rect x="14" :y="L.y+L.h*0.65" width="232" :height="L.h*0.35" rx="6" fill="rgba(0,0,0,0.14)"/>
                    <ellipse v-for="x in [44,84,126,168,208]" :key="x" :cx="x" :cy="L.y+L.h*0.44" rx="10" ry="6" fill="rgba(255,210,180,0.48)"/>
                    <ellipse v-for="x in [44,84,126,168,208]" :key="`s${x}`" :cx="x" :cy="L.y+L.h*0.44" rx="3.5" ry="2" fill="rgba(195,115,55,0.70)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.26" rx="100" :ry="L.h*0.30" fill="rgba(255,200,180,0.22)"/>
                  </g>

                  <!-- CUCUMBER -->
                  <g v-else-if="L.t==='k'">
                    <rect x="12" :y="L.y" width="236" :height="L.h" rx="6" :fill="L.c"/>
                    <rect x="12" :y="L.y+L.h*0.65" width="236" :height="L.h*0.35" rx="5" fill="rgba(0,0,0,0.12)"/>
                    <ellipse v-for="x in [42,82,124,164,204]" :key="x" :cx="x" :cy="L.y+L.h*0.44" rx="7" ry="5" fill="rgba(215,255,195,0.38)"/>
                    <ellipse v-for="x in [42,82,124,164,204]" :key="`c${x}`" :cx="x" :cy="L.y+L.h*0.44" rx="3" ry="2" fill="rgba(180,240,155,0.28)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.24" rx="106" :ry="L.h*0.28" fill="rgba(180,255,160,0.18)"/>
                  </g>

                  <!-- ONION -->
                  <g v-else-if="L.t==='o'">
                    <rect x="12" :y="L.y" width="236" :height="L.h" rx="7" fill="rgba(195,95,225,0.75)"/>
                    <rect x="12" :y="L.y+L.h*0.65" width="236" :height="L.h*0.35" rx="6" fill="rgba(0,0,0,0.12)"/>
                    <ellipse cx="75"  :cy="L.y+L.h*0.42" rx="42" :ry="L.h*0.44" fill="rgba(230,155,255,0.20)"/>
                    <ellipse cx="75"  :cy="L.y+L.h*0.42" rx="24" :ry="L.h*0.28" fill="rgba(80,12,105,0.24)"/>
                    <ellipse cx="188" :cy="L.y+L.h*0.42" rx="42" :ry="L.h*0.44" fill="rgba(230,155,255,0.20)"/>
                    <ellipse cx="188" :cy="L.y+L.h*0.42" rx="24" :ry="L.h*0.28" fill="rgba(80,12,105,0.24)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.24" rx="108" :ry="L.h*0.30" fill="rgba(255,218,255,0.20)"/>
                  </g>

                  <!-- AVOCADO -->
                  <g v-else-if="L.t==='a'">
                    <rect x="12" :y="L.y" width="236" :height="L.h" rx="6" :fill="L.c"/>
                    <rect x="12" :y="L.y+L.h*0.65" width="236" :height="L.h*0.35" rx="5" fill="rgba(0,0,0,0.14)"/>
                    <ellipse v-for="(e,ei) in [{x:58,rx:26,ry:7},{x:128,rx:30,ry:8},{x:196,rx:24,ry:6}]" :key="ei"
                      :cx="e.x" :cy="L.y+L.h*0.40" :rx="e.rx" :ry="e.ry" fill="rgba(155,215,95,0.28)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.24" rx="108" :ry="L.h*0.30" fill="rgba(195,255,140,0.18)"/>
                  </g>

                  <!-- SAUCE -->
                  <g v-else-if="L.t==='s'">
                    <!-- Shadow smear -->
                    <path :d="`M18,${L.y+L.h} C55,${L.y+L.h+3} 100,${L.y+L.h+1} 140,${L.y+L.h+3} C180,${L.y+L.h+5} 220,${L.y+L.h+2} 242,${L.y+L.h+4} L242,${L.y+L.h+9} C210,${L.y+L.h+8} 170,${L.y+L.h+7} 130,${L.y+L.h+8} C90,${L.y+L.h+9} 50,${L.y+L.h+7} 18,${L.y+L.h+8} Z`" fill="rgba(0,0,0,0.25)"/>
                    <!-- Underside darker layer -->
                    <path :d="`M18,${L.y+L.h*0.55} C60,${L.y+L.h*0.52} 100,${L.y+L.h*0.57} 140,${L.y+L.h*0.53} C180,${L.y+L.h*0.49} 215,${L.y+L.h*0.56} 242,${L.y+L.h*0.52} L242,${L.y+L.h+1} C210,${L.y+L.h+3} 170,${L.y+L.h+1} 130,${L.y+L.h+2} C90,${L.y+L.h+3} 50,${L.y+L.h+1} 18,${L.y+L.h+2} Z`" :fill="L.s"/>
                    <!-- Main sauce smear -->
                    <path :d="`M18,${L.y+3} C50,${L.y-1} 88,${L.y+5} 126,${L.y+1} C164,${L.y-3} 205,${L.y+4} 242,${L.y+1} L242,${L.y+L.h} C210,${L.y+L.h+3} 170,${L.y+L.h+1} 132,${L.y+L.h+2} C94,${L.y+L.h+3} 55,${L.y+L.h} 18,${L.y+L.h+1} Z`" :fill="L.c"/>
                    <!-- Glossy highlights -->
                    <ellipse cx="80"  :cy="L.y+L.h*0.35" rx="32" :ry="L.h*0.28" fill="rgba(255,255,255,0.14)"/>
                    <ellipse cx="175" :cy="L.y+L.h*0.32" rx="28" :ry="L.h*0.24" fill="rgba(255,255,255,0.10)"/>
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
            <div class="bb-ing-empty" v-else>{{ t('bb.empty') }}</div>

            <div class="bb-price-row" v-if="totalLayers > 0">
              <span>{{ t('bb.total') }}</span>
              <span class="bb-price-total">{{ totalPrice.toFixed(2) }}€</span>
            </div>

            <!-- SALVESTATUD BURGERID (kompaktne) -->
            <div v-if="customBurgers?.length > 0" class="bb-saved-panel">
              <p class="bb-saved-panel-label">{{ t('bb.saved') }}</p>
              <div class="bb-saved-panel-list">
                <div v-for="burger in customBurgers" :key="burger.id" class="bb-saved-panel-item">
                  <div class="bb-saved-panel-item-main">
                    <div class="bb-saved-panel-item-top">
                      <span class="bb-saved-panel-name">{{ burger.name }}</span>
                      <span class="bb-status-badge" :class="'bb-status-' + (burger.status || 'draft')">{{ statusLabel(burger.status) }}</span>
                    </div>
                    <span class="bb-saved-panel-price">{{ Number(burger.total_price).toFixed(2) }}€</span>
                  </div>
                  <div class="bb-saved-panel-actions">
                    <button @click="loadBurger(burger)" class="bb-spbtn" :title="t('bb.edit.btn')">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button v-if="burger.status === 'approved'" @click="quickOrder(burger)" class="bb-spbtn bb-spbtn--green" :title="t('bb.order.btn')">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    </button>
                    <button v-if="burger.status === 'draft' || burger.status === 'rejected'" @click="submitForReviewById(burger.id)" class="bb-spbtn bb-spbtn--orange" :title="t('bb.submit.btn')">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9l20-7z"/></svg>
                    </button>
                    <button @click="deleteBurger(burger.id)" class="bb-spbtn bb-spbtn--red" :title="t('bb.delete.btn')">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bb-right">
          <div class="bb-section" v-if="normalizedIngredients['pitav']?.length">
            <div class="bb-section-header"><span class="bb-section-num">01</span><h2 class="bb-section-title">{{ t('bb.sec.patties') }}</h2><span class="bb-section-hint">{{ t('bb.sec.patties.hint') }}</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['pitav']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('pitav', ing.id) }" @click="togglePatty(ing.id)">
                <div class="bb-card-icon">🥩</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('pitav', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['salat']?.length">
            <div class="bb-section-header"><span class="bb-section-num">02</span><h2 class="bb-section-title">{{ t('bb.sec.veg') }}</h2><span class="bb-section-hint">{{ t('bb.sec.veg.hint') }}</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['salat']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('salat', ing.id) }" @click="toggleMulti('salat', ing.id)">
                <div class="bb-card-icon">🥬</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('salat', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['lisand']?.length">
            <div class="bb-section-header"><span class="bb-section-num">03</span><h2 class="bb-section-title">{{ t('bb.sec.sauces') }}</h2><span class="bb-section-hint">{{ t('bb.sec.sauces.hint') }}</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['lisand']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('lisand', ing.id) }" @click="toggleMulti('lisand', ing.id)">
                <div class="bb-card-icon">🫙</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('lisand', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <div class="bb-section" v-if="normalizedIngredients['juust']?.length">
            <div class="bb-section-header"><span class="bb-section-num">04</span><h2 class="bb-section-title">{{ t('bb.sec.cheese') }}</h2><span class="bb-section-hint">{{ t('bb.sec.cheese.hint') }}</span></div>
            <div class="bb-grid">
              <div v-for="ing in normalizedIngredients['juust']" :key="ing.id" class="bb-card" :class="{ 'bb-card--active': isSelected('juust', ing.id) }" @click="toggleMulti('juust', ing.id)">
                <div class="bb-card-icon">🧀</div><div class="bb-card-name">{{ ing.name }}</div>
                <div class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</div>
                <div class="bb-card-check" v-if="isSelected('juust', ing.id)">✓</div>
              </div>
            </div>
          </div>
          <!-- SAVE CARD -->
          <div class="bb-save-card" :class="{ 'bb-save-card--editing': isEditing }">
            <div class="bb-save-card-header">
              <span class="bb-save-card-step">05</span>
              <h2 class="bb-save-card-title">{{ isEditing ? t('bb.edit.title') : t('bb.save.title') }}</h2>
              <button v-if="isEditing" @click="resetForm" class="bb-save-cancel" :title="t('bb.cancel')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                {{ t('bb.cancel') }}
              </button>
            </div>

            <div class="bb-save-name-row">
              <label class="bb-save-name-label">{{ t('bb.name.label') }}</label>
              <input v-model="burgerName" type="text" :placeholder="t('bb.name.ph')" class="bb-save-name-input" maxlength="60"/>
              <span class="bb-save-name-count">{{ burgerName.length }}/60</span>
            </div>

            <div class="bb-save-summary" v-if="totalLayers > 0">
              <div class="bb-save-summary-left">
                <span class="bb-save-summary-layers">{{ totalLayers }} {{ t('bb.ingredient') }}</span>
                <div class="bb-save-summary-tags">
                  <span v-for="item in allSelectedFlat.slice(0,4)" :key="item.id" class="bb-save-tag">{{ item.name }}</span>
                  <span v-if="allSelectedFlat.length > 4" class="bb-save-tag bb-save-tag--more">+{{ allSelectedFlat.length - 4 }}</span>
                </div>
              </div>
              <div class="bb-save-summary-price">{{ totalPrice.toFixed(2) }}€</div>
            </div>

            <div class="bb-save-limit-warn" v-if="!canCreateMore && !isEditing">
              <span>⚠</span> {{ t('bb.limit.pre') }} {{ maxBurgers }} {{ t('bb.limit.warn') }}
            </div>

            <p class="bb-save-validation" v-if="!burgerName.trim() && totalLayers > 0">{{ t('bb.valid.name') }}</p>
            <p class="bb-save-validation" v-else-if="burgerName.trim() && totalLayers === 0">{{ t('bb.valid.ing') }}</p>

            <div class="bb-save-btns">
              <button @click="saveBurger(false)" :disabled="!canAct" class="bb-save-btn-draft">
                {{ isEditing ? t('bb.btn.update') : t('bb.btn.draft') }}
              </button>
              <button @click="saveAndSubmit()" :disabled="!canAct" class="bb-save-btn-submit">
                {{ isEditing ? t('bb.btn.update.submit') : t('bb.btn.submit') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </button>
            </div>
          </div>
        </div>
      </section>

    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Ingredient, SelectedIngredient, CustomBurger } from '@/types/burger-types';
import { useI18n } from '@/composables/useI18n';
const { t } = useI18n();

interface Props {
  ingredients: Record<string, Ingredient[]> | any;
  customBurgers: CustomBurger[];
  canCreateMore: boolean;
  maxBurgers: number;
}
const props = defineProps<Props>();

const toasts = ref<{id:number;msg:string;type:'ok'|'err'}[]>([]);
let _tid = 0;
const showToast = (msg: string, type: 'ok'|'err' = 'ok') => {
  const id = ++_tid;
  toasts.value.push({ id, msg, type });
  setTimeout(() => { toasts.value = toasts.value.filter(t => t.id !== id); }, 3200);
};
const success = (msg: string) => showToast(msg, 'ok');
const error   = (msg: string) => showToast(msg, 'err');

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
const editingBurgerId = ref<number|null>(null);
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
const canSave     = computed(() => burgerName.value.trim() !== '' && totalLayers.value > 0);
const isEditing   = computed(() => editingBurgerId.value !== null);
const canAct      = computed(() => canSave.value && (isEditing.value || props.canCreateMore));

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

const resetForm = () => {
  burgerName.value = '';
  editingBurgerId.value = null;
  selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
};

const saveBurger = (submitForReview = false) => {
  if (!canAct.value) return;
  const payload = { name: burgerName.value, ingredients: getAllSelected(), is_favorite: false, submit_for_review: submitForReview };
  if (isEditing.value) {
    router.put(`/burger-builder/${editingBurgerId.value}`, payload as any, {
      preserveScroll: true,
      onSuccess: () => { success(submitForReview ? 'Burger saadetud kinnitamiseks! ✓' : 'Burger uuendatud! ✓'); resetForm(); },
      onError: () => error(t('bb.toast.save.err')),
    });
  } else {
    router.post('/burger-builder', payload as any, {
      preserveScroll: true,
      onSuccess: () => { success(submitForReview ? 'Burger saadetud kinnitamiseks! ✓' : 'Burger salvestatud! ✓'); resetForm(); },
      onError: () => error(t('bb.toast.save.err')),
    });
  }
};

const saveAndSubmit = () => saveBurger(true);

const submitForReviewById = (id: number) => router.post(`/burger-builder/${id}/submit`, {}, { preserveScroll: true });
const statusLabel = (status: string) => ({ draft: t('bb.status.draft'), pending: t('bb.status.pending'), approved: t('bb.status.approved'), rejected: t('bb.status.rejected') }[status] ?? status);

const quickOrder = (burger: CustomBurger) => {
  router.post('/cart/add', { burger_id: burger.id, quantity: 1 } as any, {
    onSuccess: () => router.visit('/cart'),
    onError: () => error('Korvi lisamine ebaõnnestus'),
  });
};
const deleteBurger = (id: number) => router.delete(`/burger-builder/${id}`, { preserveScroll: true });
const loadBurger = (burger: CustomBurger) => {
  burgerName.value = burger.name;
  editingBurgerId.value = burger.id;
  selectedIngredients.value = { 'vöi': [], 'pitav': [], 'salat': [], 'lisand': [], 'juust': [] };
  burger.ingredients.forEach(ing => {
    const cat = ing.category;
    if (selectedIngredients.value[cat]) selectedIngredients.value[cat].push({ id: ing.id, quantity: ing.pivot.quantity });
  });
  document.getElementById('bb-builder')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

// ═══ SVG BURGER ═══
const BUN_DOME = 62
const BOT_H    = 26
const RIM_H    = 12
const START_Y  = 8
const GAP      = 4

interface SL { t:string; y:number; h:number; c:string; s:string; }

function mkL(): SL[] {
  const out: SL[] = []
  const li = selectedIngredients.value['lisand']??[]
  const sa = selectedIngredients.value['salat']??[]
  const ju = selectedIngredients.value['juust']??[]
  const pi = selectedIngredients.value['pitav']??[]
  const push = (t:string,h:number,c:string,s:string) => out.push({t,y:0,h,c,s})
  if(pi.length>=2){
    push('p',22,pC(pi[0]),pS())
    ju.forEach(x=>{const n=gn(x.id);push('c',16,cC(n),cS(n))})
    sa.forEach(x=>{const n=gn(x.id);const[t,h,c,s]=vD(n);push(t,h,c,s)})
    li.forEach(x=>{const n=gn(x.id);push('s',12,sC(n),sS(n))})
    push('p',22,pC(pi[1]),pS())
  } else if(pi.length===1){
    push('p',22,pC(pi[0]),pS())
    ju.forEach(x=>{const n=gn(x.id);push('c',16,cC(n),cS(n))})
    sa.forEach(x=>{const n=gn(x.id);const[t,h,c,s]=vD(n);push(t,h,c,s)})
    li.forEach(x=>{const n=gn(x.id);push('s',12,sC(n),sS(n))})
  } else {
    ju.forEach(x=>{const n=gn(x.id);push('c',16,cC(n),cS(n))})
    sa.forEach(x=>{const n=gn(x.id);const[t,h,c,s]=vD(n);push(t,h,c,s)})
    li.forEach(x=>{const n=gn(x.id);push('s',12,sC(n),sS(n))})
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
  if(n.includes('tomat'))return ['t',20,'#C01008','#700404']
  if(n.includes('kurk')) return ['k',16,'#559018','#185808']
  if(n.includes('sibul')||n.includes('kastrull'))return ['o',16,'transparent','transparent']
  if(n.includes('avocado'))return ['a',16,'#6A9818','#2E4E08']
  return ['l',20,'#389A0C','#145C08']
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



/* ── Toast notifications ── */
.bb-toasts { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; pointer-events:none; }
.bb-toast { display:flex; align-items:center; gap:.6rem; background:#111; border:1px solid #222; border-radius:10px; padding:.75rem 1.1rem; font-size:.85rem; font-weight:600; color:#ddd; box-shadow:0 8px 32px rgba(0,0,0,.6); pointer-events:auto; min-width:240px; }
.bb-toast--ok { border-color:rgba(34,197,94,.3); }
.bb-toast--err { border-color:rgba(239,68,68,.3); color:#ef4444; }
.bb-toast-icon { width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:800; flex-shrink:0; }
.bb-toast--ok .bb-toast-icon { background:rgba(34,197,94,.15); color:#22c55e; }
.bb-toast--err .bb-toast-icon { background:rgba(239,68,68,.15); color:#ef4444; }
.toast-enter-active,.toast-leave-active { transition:all .25s ease; }
.toast-enter-from { opacity:0; transform:translateX(20px); }
.toast-leave-to { opacity:0; transform:translateX(20px); }

/* ── Quick order (inside left panel) ── */
.bb-quick { border-top:1px solid #141414; padding-top:1rem; display:flex; flex-direction:column; gap:.6rem; }
.bb-quick-label { font-size:.64rem; font-weight:800; letter-spacing:.16em; text-transform:uppercase; color:#D2691E; }
.bb-quick-list { display:flex; flex-direction:column; gap:.4rem; }
.bb-quick-item { display:flex; align-items:center; justify-content:space-between; gap:.6rem; background:#0d0d0d; border:1px solid #181818; border-radius:8px; padding:.5rem .6rem .5rem .85rem; }
.bb-quick-item-info { display:flex; align-items:baseline; gap:.5rem; flex:1; min-width:0; }
.bb-quick-item-name { font-size:.84rem; font-weight:600; color:#ccc; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bb-quick-item-price { font-size:.74rem; color:#555; font-weight:500; flex-shrink:0; }
.bb-quick-order-btn { display:flex; align-items:center; gap:.3rem; background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.18); color:#22c55e; padding:.3rem .65rem; border-radius:5px; font-size:.74rem; font-weight:700; cursor:pointer; transition:all .15s; white-space:nowrap; flex-shrink:0; }
.bb-quick-order-btn:hover { background:rgba(34,197,94,.16); border-color:rgba(34,197,94,.35); }
.bb-quick-item-status { font-size:.7rem; color:#333; flex-shrink:0; }

/* ── Save card ── */
.bb-save-card { border:1px solid #1a1a1a; border-radius:16px; padding:1.8rem; background:#0a0a0a; display:flex; flex-direction:column; gap:1.2rem; }
.bb-save-card--editing { border-color:rgba(210,105,30,.3); background:rgba(210,105,30,.03); }
.bb-save-card-header { display:flex; align-items:center; gap:.75rem; }
.bb-save-card-step { width:28px; height:28px; border-radius:50%; background:rgba(210,105,30,.1); border:1px solid rgba(210,105,30,.25); display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:800; color:#D2691E; flex-shrink:0; }
.bb-save-card-title { font-size:1.1rem; font-weight:800; color:#fff; letter-spacing:-.01em; flex:1; }
.bb-save-cancel { display:flex; align-items:center; gap:.3rem; background:transparent; border:1px solid #222; color:#555; padding:.3rem .7rem; border-radius:7px; font-size:.75rem; font-weight:600; cursor:pointer; transition:all .15s; }
.bb-save-cancel:hover { border-color:#333; color:#999; background:#0e0e0e; }
.bb-save-name-row { display:flex; flex-direction:column; gap:.4rem; position:relative; }
.bb-save-name-label { font-size:.68rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#444; }
.bb-save-name-input { width:100%; background:#0d0d0d; border:1px solid #1e1e1e; border-radius:10px; padding:.9rem 3.5rem .9rem 1.1rem; color:#fff; font-size:1rem; font-weight:500; outline:none; transition:all .2s; }
.bb-save-name-input:focus { border-color:#D2691E; box-shadow:0 0 0 3px rgba(210,105,30,.1); }
.bb-save-name-input::placeholder { color:#272727; }
.bb-save-name-count { position:absolute; right:.9rem; bottom:.95rem; font-size:.68rem; color:#333; pointer-events:none; }
.bb-save-summary { display:flex; align-items:flex-start; justify-content:space-between; background:#0d0d0d; border:1px solid #161616; border-radius:10px; padding:1rem 1.1rem; gap:1rem; }
.bb-save-summary-left { display:flex; flex-direction:column; gap:.4rem; flex:1; min-width:0; }
.bb-save-summary-layers { font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#444; }
.bb-save-summary-tags { display:flex; flex-wrap:wrap; gap:.3rem; }
.bb-save-tag { font-size:.72rem; background:#141414; border:1px solid #1e1e1e; color:#666; padding:2px 8px; border-radius:99px; }
.bb-save-tag--more { color:#D2691E; border-color:rgba(210,105,30,.2); background:rgba(210,105,30,.05); }
.bb-save-summary-price { font-size:1.5rem; font-weight:900; color:#D2691E; line-height:1; flex-shrink:0; }
.bb-save-limit-warn { display:flex; align-items:center; gap:.5rem; background:rgba(239,68,68,.06); border:1px solid rgba(239,68,68,.2); border-radius:8px; padding:.7rem 1rem; font-size:.82rem; color:#ef4444; }
.bb-save-validation { font-size:.76rem; color:#555; font-style:italic; margin-top:-.4rem; }
.bb-save-btns { display:grid; grid-template-columns:1fr 1.6fr; gap:.6rem; }
.bb-save-btn-draft { background:transparent; border:1px solid #222; color:#555; padding:.85rem 1rem; border-radius:10px; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .2s; }
.bb-save-btn-draft:hover:not(:disabled) { border-color:#333; color:#999; background:#0e0e0e; }
.bb-save-btn-draft:disabled { opacity:.2; cursor:not-allowed; }
.bb-save-btn-submit { background:linear-gradient(135deg,#D2691E,#B8571A); color:#fff; border:none; padding:.85rem 1.2rem; border-radius:10px; font-size:.88rem; font-weight:700; cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:.5rem; box-shadow:0 4px 20px rgba(210,105,30,.3); }
.bb-save-btn-submit:hover:not(:disabled) { background:linear-gradient(135deg,#E07A2E,#C8671A); transform:translateY(-1px); box-shadow:0 8px 28px rgba(210,105,30,.4); }
.bb-save-btn-submit:disabled { opacity:.25; cursor:not-allowed; transform:none; box-shadow:none; }

/* ── Compact saved panel (left panel) ── */
.bb-saved-panel { border-top:1px solid #141414; padding-top:1rem; display:flex; flex-direction:column; gap:.6rem; }
.bb-saved-panel-label { font-size:.64rem; font-weight:800; letter-spacing:.16em; text-transform:uppercase; color:#444; }
.bb-saved-panel-list { display:flex; flex-direction:column; gap:.35rem; }
.bb-saved-panel-item { background:#0d0d0d; border:1px solid #181818; border-radius:9px; padding:.55rem .6rem .55rem .85rem; display:flex; align-items:center; gap:.5rem; }
.bb-saved-panel-item-main { flex:1; min-width:0; display:flex; flex-direction:column; gap:.18rem; }
.bb-saved-panel-item-top { display:flex; align-items:center; gap:.4rem; }
.bb-saved-panel-name { font-size:.82rem; font-weight:600; color:#ccc; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1; min-width:0; }
.bb-saved-panel-price { font-size:.74rem; color:#D2691E; font-weight:700; }
.bb-saved-panel-actions { display:flex; gap:.25rem; flex-shrink:0; }
.bb-spbtn { display:flex; align-items:center; justify-content:center; width:26px; height:26px; background:transparent; border:1px solid #1e1e1e; color:#444; border-radius:6px; cursor:pointer; transition:all .15s; flex-shrink:0; }
.bb-spbtn:hover { border-color:#2e2e2e; color:#999; background:#0e0e0e; }
.bb-spbtn--green { border-color:rgba(34,197,94,.2) !important; color:#22c55e !important; }
.bb-spbtn--green:hover { background:rgba(34,197,94,.08) !important; border-color:rgba(34,197,94,.4) !important; }
.bb-spbtn--orange { border-color:rgba(210,105,30,.2) !important; color:#D2691E !important; }
.bb-spbtn--orange:hover { background:rgba(210,105,30,.08) !important; border-color:rgba(210,105,30,.4) !important; }
.bb-spbtn--red { border-color:rgba(239,68,68,.18) !important; color:#555 !important; }
.bb-spbtn--red:hover { color:#ef4444 !important; background:rgba(239,68,68,.06) !important; border-color:rgba(239,68,68,.35) !important; }
.bb-rejected-note { display:flex; align-items:flex-start; gap:.5rem; font-size:.78rem; color:#666; background:#0a0a0a; border:1px solid #181818; border-radius:8px; padding:.6rem .85rem; }
.bb-status-badge { font-size:.58rem; font-weight:700; padding:2px 6px; border-radius:99px; text-transform:uppercase; letter-spacing:.05em; flex-shrink:0; }
.bb-status-draft    { background:rgba(80,80,80,.12); color:#555; border:1px solid #232323; }
.bb-status-pending  { background:rgba(250,200,0,.07); color:#E8B000; border:1px solid rgba(250,200,0,.18); }
.bb-status-approved { background:rgba(34,197,94,.07); color:#22c55e; border:1px solid rgba(34,197,94,.18); }
.bb-status-rejected { background:rgba(239,68,68,.07); color:#ef4444; border:1px solid rgba(239,68,68,.18); }
</style>