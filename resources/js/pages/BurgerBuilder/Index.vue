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

      <!-- ── HEADER ── -->
      <header class="bb-header">
        <div class="bb-header-inner">
          <p class="bb-eyebrow">{{ t('bb.eyebrow') }}</p>
          <h1 class="bb-title">{{ t('bb.title') }}</h1>
          <p class="bb-sub">{{ t('bb.sub') }}</p>
        </div>
        <div class="bb-header-line" aria-hidden="true"></div>
      </header>

      <!-- ── LAYOUT ── -->
      <div id="bb-builder" class="bb-layout">

        <!-- LEFT: sticky preview -->
        <aside class="bb-aside">
          <div class="bb-aside-inner">

            <!-- Name pill -->
            <div class="bb-preview-top">
              <span v-if="burgerName" class="bb-name-pill">{{ burgerName }}</span>
              <span v-else class="bb-name-pill bb-name-pill--ghost">{{ t('bb.name.your') }}</span>
            </div>

            <!-- Burger SVG -->
            <div class="bb-visual">
              <svg :viewBox="`0 0 260 ${svgH}`" width="230" :height="svgH*230/260"
                xmlns="http://www.w3.org/2000/svg" style="display:block;margin:0 auto;overflow:visible">
                <defs>
                  <radialGradient id="gTB" cx="32%" cy="14%" r="80%">
                    <stop offset="0%"   stop-color="#FFC84A"/>
                    <stop offset="22%"  stop-color="#E07820"/>
                    <stop offset="55%"  stop-color="#A84808"/>
                    <stop offset="100%" stop-color="#501800"/>
                  </radialGradient>
                  <linearGradient id="gTBRim" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#6A2808"/>
                    <stop offset="100%" stop-color="#2A0C00"/>
                  </linearGradient>
                  <radialGradient id="gBB" cx="30%" cy="16%" r="74%">
                    <stop offset="0%"   stop-color="#EEA040"/>
                    <stop offset="38%"  stop-color="#C06820"/>
                    <stop offset="100%" stop-color="#481800"/>
                  </radialGradient>
                  <radialGradient id="gSd" cx="24%" cy="20%" r="72%">
                    <stop offset="0%"   stop-color="#FFF4CC"/>
                    <stop offset="52%"  stop-color="#D4BC40"/>
                    <stop offset="100%" stop-color="#806010"/>
                  </radialGradient>
                  <filter id="fds" x="-12%" y="-12%" width="124%" height="150%">
                    <feDropShadow dx="0" dy="5" stdDeviation="5" flood-color="rgba(0,0,0,0.55)"/>
                  </filter>
                  <filter id="fds2" x="-8%" y="-8%" width="116%" height="140%">
                    <feDropShadow dx="0" dy="3" stdDeviation="3.5" flood-color="rgba(0,0,0,0.40)"/>
                  </filter>
                  <filter id="fGrill" x="-20%" y="-5%" width="140%" height="110%">
                    <feGaussianBlur stdDeviation="1.6"/>
                  </filter>
                  <filter id="fOnion" x="-18%" y="-40%" width="136%" height="180%">
                    <feGaussianBlur stdDeviation="2.4"/>
                  </filter>
                  <radialGradient id="gGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="rgba(210,105,30,0.35)"/>
                    <stop offset="100%" stop-color="rgba(210,105,30,0)"/>
                  </radialGradient>
                </defs>

                <ellipse cx="130" :cy="svgH-2" rx="100" ry="12" fill="url(#gGlow)"/>
                <ellipse cx="130" :cy="svgH+2" rx="90" ry="5" fill="rgba(0,0,0,0.45)"/>

                <g filter="url(#fds)">
                  <rect x="26" :y="svgTopRim" width="208" height="14" rx="7" fill="url(#gTBRim)"/>
                  <path :d="`M26,${svgTopRim+2} C26,${svgTopRim-BUN_DOME+4} 234,${svgTopRim-BUN_DOME+4} 234,${svgTopRim+2} Z`" fill="url(#gTB)"/>
                  <ellipse cx="98" :cy="svgTopRim-BUN_DOME*0.50" rx="56" :ry="BUN_DOME*0.20" fill="rgba(255,235,150,0.20)"/>
                  <ellipse cx="82" :cy="svgTopRim-BUN_DOME*0.64" rx="22" :ry="BUN_DOME*0.10" fill="rgba(255,252,210,0.18)"/>
                  <rect x="26" :y="svgTopRim+6" width="208" height="8" rx="4" fill="rgba(0,0,0,0.20)"/>
                  <g v-for="s in seeds" :key="s.x">
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d+2" :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="rgba(10,2,0,0.35)"/>
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d"   :rx="s.w" :ry="s.h" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="url(#gSd)"/>
                    <ellipse :cx="s.x-s.w*0.3" :cy="svgTopRim-s.d-s.h*0.3" :rx="s.w*0.35" :ry="s.h*0.3" fill="rgba(255,248,200,0.45)" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`"/>
                    <ellipse :cx="s.x" :cy="svgTopRim-s.d" :rx="s.w*0.68" :ry="s.h*0.19" :transform="`rotate(${s.r},${s.x},${svgTopRim-s.d})`" fill="rgba(55,24,2,0.26)"/>
                  </g>
                </g>

                <g v-for="(L,i) in layers3" :key="i">
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
                  <g v-else-if="L.t==='c'">
                    <ellipse v-for="d in [22,55,90,124,160,195,225]" :key="d" :cx="d" :cy="L.y+L.h+6" rx="8" ry="9" :fill="L.c"/>
                    <rect x="2" :y="L.y" width="256" :height="L.h" rx="6" :fill="L.c"/>
                    <rect x="2" :y="L.y+L.h*0.65" width="256" :height="L.h*0.35" rx="5" fill="rgba(0,0,0,0.12)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.28" rx="115" :ry="L.h*0.32" fill="rgba(255,255,220,0.24)"/>
                  </g>
                  <g v-else-if="L.t==='l'">
                    <path :d="lp(L.y,true)"  :fill="L.s"/>
                    <path :d="lp(L.y,false)" :fill="L.c"/>
                    <ellipse cx="80"  :cy="L.y+5" rx="28" :ry="L.h*0.28" fill="rgba(200,255,180,0.18)"/>
                    <ellipse cx="174" :cy="L.y+4" rx="26" :ry="L.h*0.24" fill="rgba(200,255,180,0.14)"/>
                  </g>
                  <g v-else-if="L.t==='t'">
                    <rect x="14" :y="L.y" width="232" :height="L.h" rx="8" :fill="L.c"/>
                    <rect x="14" :y="L.y+L.h*0.65" width="232" :height="L.h*0.35" rx="6" fill="rgba(0,0,0,0.14)"/>
                    <ellipse v-for="x in [44,84,126,168,208]" :key="x" :cx="x" :cy="L.y+L.h*0.44" rx="10" ry="6" fill="rgba(255,210,180,0.48)"/>
                    <ellipse v-for="x in [44,84,126,168,208]" :key="`s${x}`" :cx="x" :cy="L.y+L.h*0.44" rx="3.5" ry="2" fill="rgba(195,115,55,0.70)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.26" rx="100" :ry="L.h*0.30" fill="rgba(255,200,180,0.22)"/>
                  </g>
                  <g v-else-if="L.t==='k'">
                    <rect x="12" :y="L.y" width="236" :height="L.h" rx="6" :fill="L.c"/>
                    <rect x="12" :y="L.y+L.h*0.65" width="236" :height="L.h*0.35" rx="5" fill="rgba(0,0,0,0.12)"/>
                    <ellipse v-for="x in [42,82,124,164,204]" :key="x" :cx="x" :cy="L.y+L.h*0.44" rx="7" ry="5" fill="rgba(215,255,195,0.38)"/>
                    <ellipse v-for="x in [42,82,124,164,204]" :key="`c${x}`" :cx="x" :cy="L.y+L.h*0.44" rx="3" ry="2" fill="rgba(180,240,155,0.28)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.24" rx="106" :ry="L.h*0.28" fill="rgba(180,255,160,0.18)"/>
                  </g>
                  <g v-else-if="L.t==='o'">
                    <rect x="12" :y="L.y" width="236" :height="L.h" rx="7" fill="rgba(195,95,225,0.75)"/>
                    <rect x="12" :y="L.y+L.h*0.65" width="236" :height="L.h*0.35" rx="6" fill="rgba(0,0,0,0.12)"/>
                    <ellipse cx="75"  :cy="L.y+L.h*0.42" rx="42" :ry="L.h*0.44" fill="rgba(230,155,255,0.20)"/>
                    <ellipse cx="75"  :cy="L.y+L.h*0.42" rx="24" :ry="L.h*0.28" fill="rgba(80,12,105,0.24)"/>
                    <ellipse cx="188" :cy="L.y+L.h*0.42" rx="42" :ry="L.h*0.44" fill="rgba(230,155,255,0.20)"/>
                    <ellipse cx="188" :cy="L.y+L.h*0.42" rx="24" :ry="L.h*0.28" fill="rgba(80,12,105,0.24)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.24" rx="108" :ry="L.h*0.30" fill="rgba(255,218,255,0.20)"/>
                  </g>
                  <g v-else-if="L.t==='a'">
                    <rect x="12" :y="L.y" width="236" :height="L.h" rx="6" :fill="L.c"/>
                    <rect x="12" :y="L.y+L.h*0.65" width="236" :height="L.h*0.35" rx="5" fill="rgba(0,0,0,0.14)"/>
                    <ellipse v-for="(e,ei) in [{x:58,rx:26,ry:7},{x:128,rx:30,ry:8},{x:196,rx:24,ry:6}]" :key="ei"
                      :cx="e.x" :cy="L.y+L.h*0.40" :rx="e.rx" :ry="e.ry" fill="rgba(155,215,95,0.28)"/>
                    <ellipse cx="128" :cy="L.y+L.h*0.24" rx="108" :ry="L.h*0.30" fill="rgba(195,255,140,0.18)"/>
                  </g>
                  <g v-else-if="L.t==='s'">
                    <path :d="`M18,${L.y+L.h} C55,${L.y+L.h+3} 100,${L.y+L.h+1} 140,${L.y+L.h+3} C180,${L.y+L.h+5} 220,${L.y+L.h+2} 242,${L.y+L.h+4} L242,${L.y+L.h+9} C210,${L.y+L.h+8} 170,${L.y+L.h+7} 130,${L.y+L.h+8} C90,${L.y+L.h+9} 50,${L.y+L.h+7} 18,${L.y+L.h+8} Z`" fill="rgba(0,0,0,0.25)"/>
                    <path :d="`M18,${L.y+L.h*0.55} C60,${L.y+L.h*0.52} 100,${L.y+L.h*0.57} 140,${L.y+L.h*0.53} C180,${L.y+L.h*0.49} 215,${L.y+L.h*0.56} 242,${L.y+L.h*0.52} L242,${L.y+L.h+1} C210,${L.y+L.h+3} 170,${L.y+L.h+1} 130,${L.y+L.h+2} C90,${L.y+L.h+3} 50,${L.y+L.h+1} 18,${L.y+L.h+2} Z`" :fill="L.s"/>
                    <path :d="`M18,${L.y+3} C50,${L.y-1} 88,${L.y+5} 126,${L.y+1} C164,${L.y-3} 205,${L.y+4} 242,${L.y+1} L242,${L.y+L.h} C210,${L.y+L.h+3} 170,${L.y+L.h+1} 132,${L.y+L.h+2} C94,${L.y+L.h+3} 55,${L.y+L.h} 18,${L.y+L.h+1} Z`" :fill="L.c"/>
                    <ellipse cx="80"  :cy="L.y+L.h*0.35" rx="32" :ry="L.h*0.28" fill="rgba(255,255,255,0.14)"/>
                    <ellipse cx="175" :cy="L.y+L.h*0.32" rx="28" :ry="L.h*0.24" fill="rgba(255,255,255,0.10)"/>
                  </g>
                </g>

                <g filter="url(#fds)">
                  <rect x="26" :y="svgBotY+20" width="208" height="8" rx="4" fill="#280A00"/>
                  <rect x="26" :y="svgBotY" width="208" height="24" rx="10" fill="url(#gBB)"/>
                  <ellipse cx="100" :cy="svgBotY+9" rx="48" ry="7" fill="rgba(255,210,120,0.20)"/>
                  <ellipse cx="82"  :cy="svgBotY+6" rx="20" ry="3.5" fill="rgba(255,240,180,0.16)"/>
                  <rect x="26" :y="svgBotY+17" width="208" height="7" rx="4" fill="rgba(0,0,0,0.22)"/>
                </g>
              </svg>
            </div>

            <!-- Selected ingredient list -->
            <div v-if="totalLayers > 0" class="bb-ing-list">
              <div v-for="item in allSelectedFlat" :key="item.id" class="bb-ing-row">
                <span class="bb-ing-dot" :style="{ background: categoryColor(item.category) }"></span>
                <span class="bb-ing-name">{{ item.name }}</span>
                <span v-if="item.quantity > 1" class="bb-ing-qty">×{{ item.quantity }}</span>
                <span class="bb-ing-price">{{ (item.price * item.quantity).toFixed(2) }}€</span>
              </div>
            </div>
            <p v-else class="bb-ing-empty">{{ t('bb.empty') }}</p>

            <!-- Total -->
            <div v-if="totalLayers > 0" class="bb-total-bar">
              <span class="bb-total-lbl">{{ t('bb.total') }}</span>
              <span class="bb-total-val">{{ totalPrice.toFixed(2) }}€</span>
            </div>

            <!-- Saved burgers -->
            <div v-if="customBurgers?.length > 0" class="bb-saved">
              <p class="bb-saved-lbl">{{ t('bb.saved') }}</p>
              <div class="bb-saved-list">
                <div v-for="burger in customBurgers" :key="burger.id" class="bb-saved-item">
                  <div class="bb-saved-meta">
                    <div class="bb-saved-row">
                      <span class="bb-saved-name">{{ burger.name }}</span>
                      <span class="bb-status-badge" :class="'bb-status--'+(burger.status||'draft')">{{ statusLabel(burger.status) }}</span>
                    </div>
                    <span class="bb-saved-price">{{ Number(burger.total_price).toFixed(2) }}€</span>
                  </div>
                  <div class="bb-saved-actions">
                    <button @click="loadBurger(burger)" class="bb-sb" :title="t('bb.edit.btn')">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button v-if="burger.status === 'approved'" @click="quickOrder(burger)" class="bb-sb bb-sb--green" :title="t('bb.order.btn')">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    </button>
                    <button v-if="burger.status === 'draft' || burger.status === 'rejected'" @click="submitForReviewById(burger.id)" class="bb-sb bb-sb--orange" :title="t('bb.submit.btn')">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9l20-7z"/></svg>
                    </button>
                    <button @click="deleteBurger(burger.id)" class="bb-sb bb-sb--red" :title="t('bb.delete.btn')">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </aside>

        <!-- RIGHT: ingredient picker -->
        <main class="bb-main">

          <!-- Patties -->
          <div class="bb-section" v-if="normalizedIngredients['pitav']?.length" style="--step-color:rgba(139,69,19,0.6)">
            <div class="bb-sect-head">
              <span class="bb-step-num">01</span>
              <div class="bb-sect-text">
                <h2 class="bb-sect-title">{{ t('bb.sec.patties') }}</h2>
                <span class="bb-sect-hint">{{ t('bb.sec.patties.hint') }}</span>
              </div>
            </div>
            <div class="bb-cards">
              <button v-for="ing in normalizedIngredients['pitav']" :key="ing.id"
                class="bb-card" :class="{ 'bb-card--on': isSelected('pitav', ing.id) }"
                @click="togglePatty(ing.id)">
                <span class="bb-card-ico">🥩</span>
                <span class="bb-card-body">
                  <span class="bb-card-name">{{ ing.name }}</span>
                  <span class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</span>
                </span>
                <span v-if="isSelected('pitav', ing.id)" class="bb-card-check">
                  <svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>
                </span>
              </button>
            </div>
          </div>

          <!-- Vegetables -->
          <div class="bb-section" v-if="normalizedIngredients['salat']?.length">
            <div class="bb-sect-head">
              <span class="bb-step-num">02</span>
              <div class="bb-sect-text">
                <h2 class="bb-sect-title">{{ t('bb.sec.veg') }}</h2>
                <span class="bb-sect-hint">{{ t('bb.sec.veg.hint') }}</span>
              </div>
            </div>
            <div class="bb-cards">
              <button v-for="ing in normalizedIngredients['salat']" :key="ing.id"
                class="bb-card" :class="{ 'bb-card--on': isSelected('salat', ing.id) }"
                @click="toggleMulti('salat', ing.id)">
                <span class="bb-card-ico">🥬</span>
                <span class="bb-card-body">
                  <span class="bb-card-name">{{ ing.name }}</span>
                  <span class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</span>
                </span>
                <span v-if="isSelected('salat', ing.id)" class="bb-card-check">
                  <svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>
                </span>
              </button>
            </div>
          </div>

          <!-- Sauces -->
          <div class="bb-section" v-if="normalizedIngredients['lisand']?.length">
            <div class="bb-sect-head">
              <span class="bb-step-num">03</span>
              <div class="bb-sect-text">
                <h2 class="bb-sect-title">{{ t('bb.sec.sauces') }}</h2>
                <span class="bb-sect-hint">{{ t('bb.sec.sauces.hint') }}</span>
              </div>
            </div>
            <div class="bb-cards">
              <button v-for="ing in normalizedIngredients['lisand']" :key="ing.id"
                class="bb-card" :class="{ 'bb-card--on': isSelected('lisand', ing.id) }"
                @click="toggleMulti('lisand', ing.id)">
                <span class="bb-card-ico">🫙</span>
                <span class="bb-card-body">
                  <span class="bb-card-name">{{ ing.name }}</span>
                  <span class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</span>
                </span>
                <span v-if="isSelected('lisand', ing.id)" class="bb-card-check">
                  <svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>
                </span>
              </button>
            </div>
          </div>

          <!-- Cheese -->
          <div class="bb-section" v-if="normalizedIngredients['juust']?.length">
            <div class="bb-sect-head">
              <span class="bb-step-num">04</span>
              <div class="bb-sect-text">
                <h2 class="bb-sect-title">{{ t('bb.sec.cheese') }}</h2>
                <span class="bb-sect-hint">{{ t('bb.sec.cheese.hint') }}</span>
              </div>
            </div>
            <div class="bb-cards">
              <button v-for="ing in normalizedIngredients['juust']" :key="ing.id"
                class="bb-card" :class="{ 'bb-card--on': isSelected('juust', ing.id) }"
                @click="toggleMulti('juust', ing.id)">
                <span class="bb-card-ico">🧀</span>
                <span class="bb-card-body">
                  <span class="bb-card-name">{{ ing.name }}</span>
                  <span class="bb-card-price">{{ Number(ing.price).toFixed(2) }}€</span>
                </span>
                <span v-if="isSelected('juust', ing.id)" class="bb-card-check">
                  <svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>
                </span>
              </button>
            </div>
          </div>

          <!-- Step 05: Save / Submit -->
          <div class="bb-save-card" :class="{ 'bb-save-card--editing': isEditing }">
            <div class="bb-save-head">
              <span class="bb-step-num">05</span>
              <div class="bb-sect-text">
                <h2 class="bb-sect-title">{{ isEditing ? t('bb.edit.title') : t('bb.save.title') }}</h2>
              </div>
              <button v-if="isEditing" @click="resetForm" class="bb-cancel-btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                {{ t('bb.cancel') }}
              </button>
            </div>

            <div class="bb-name-field">
              <label class="bb-name-lbl">{{ t('bb.name.label') }}</label>
              <div class="bb-name-wrap">
                <input v-model="burgerName" type="text" :placeholder="t('bb.name.ph')" class="bb-name-input" maxlength="60" />
                <span class="bb-name-count">{{ burgerName.length }}/60</span>
              </div>
            </div>

            <div v-if="totalLayers > 0" class="bb-summary-strip">
              <div class="bb-summary-left">
                <span class="bb-summary-count">{{ totalLayers }} {{ t('bb.ingredient') }}</span>
                <div class="bb-tag-list">
                  <span v-for="item in allSelectedFlat.slice(0,4)" :key="item.id" class="bb-tag">{{ item.name }}</span>
                  <span v-if="allSelectedFlat.length > 4" class="bb-tag bb-tag--more">+{{ allSelectedFlat.length - 4 }}</span>
                </div>
              </div>
              <span class="bb-summary-price">{{ totalPrice.toFixed(2) }}€</span>
            </div>

            <div v-if="!canCreateMore && !isEditing" class="bb-limit-warn">
              <span>⚠</span> {{ t('bb.limit.pre') }} {{ maxBurgers }} {{ t('bb.limit.warn') }}
            </div>

            <p class="bb-validation" v-if="!burgerName.trim() && totalLayers > 0">{{ t('bb.valid.name') }}</p>
            <p class="bb-validation" v-else-if="burgerName.trim() && totalLayers === 0">{{ t('bb.valid.ing') }}</p>

            <div class="bb-save-btns">
              <button @click="saveBurger(false)" :disabled="!canAct" class="bb-btn-ghost">
                {{ isEditing ? t('bb.btn.update') : t('bb.btn.draft') }}
              </button>
              <button @click="saveAndSubmit()" :disabled="!canAct" class="bb-btn-primary">
                {{ isEditing ? t('bb.btn.update.submit') : t('bb.btn.submit') }}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </button>
            </div>
          </div>

        </main>
      </div>
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
const { t, locale } = useI18n();
const ln = (et: string, en: string | null | undefined) => (locale.value === 'en' && en) ? en : et;

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
  const result: { id: number; name: string; name_en?: string | null; price: number; quantity: number; category: string }[] = [];
  Object.entries(selectedIngredients.value).forEach(([cat, items]) => {
    items.forEach(item => {
      const ing = getAllIngredients().find(i => i.id === item.id);
      if (ing) result.push({ id: item.id, name: ing.name, name_en: ing.name_en, price: Number(ing.price), quantity: item.quantity, category: cat });
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
.bb-root { background: #080808; color: #e0e0e0; min-height: 100vh; }

/* ── Header ── */
.bb-header {
  padding: 5rem 2.5rem 3.5rem;
  position: relative;
  background: linear-gradient(to bottom, rgba(210,105,30,.04) 0%, transparent 100%);
}
@media (max-width: 768px) { .bb-header { padding: 3rem 1.25rem 2.5rem; } }
.bb-header-inner { max-width: 1320px; margin: 0 auto; }
.bb-eyebrow {
  display: inline-flex; align-items: center; gap: .5rem;
  font-size: .62rem; font-weight: 800; letter-spacing: .2em; text-transform: uppercase;
  color: #D2691E; background: rgba(210,105,30,.08); border: 1px solid rgba(210,105,30,.18);
  padding: .3rem .875rem; border-radius: 99px; margin-bottom: 1.25rem;
}
.bb-title {
  font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900;
  letter-spacing: -.03em; line-height: 1.05;
  color: #fff; margin-bottom: .6rem;
}
.bb-sub { font-size: .9rem; color: #404040; }
.bb-header-line {
  position: absolute; bottom: 0; left: 0; right: 0; height: 1px;
  background: linear-gradient(to right, transparent, rgba(255,255,255,.06) 20%, rgba(255,255,255,.06) 80%, transparent);
}

/* ── Layout ── */
.bb-layout { display: grid; grid-template-columns: 360px 1fr; min-height: calc(100vh - 280px); }
@media (max-width: 960px) { .bb-layout { grid-template-columns: 1fr; } }

/* ── Aside (left sticky panel) ── */
.bb-aside {
  background: linear-gradient(to bottom, #0d0d0d 0%, #0a0a0a 100%);
  border-right: 1px solid rgba(255,255,255,.05);
  position: sticky; top: 0;
  height: 100vh; overflow: hidden;
  box-shadow: inset -1px 0 0 rgba(210,105,30,.04);
}
@media (max-width: 960px) { .bb-aside { position: static; height: auto; border-right: none; border-bottom: 1px solid rgba(255,255,255,.05); } }

.bb-aside-inner {
  height: 100%; overflow-y: auto;
  padding: 2rem 1.75rem;
  display: flex; flex-direction: column; gap: 1.25rem;
  scrollbar-width: none;
}
.bb-aside-inner::-webkit-scrollbar { display: none; }
@media (max-width: 960px) { .bb-aside-inner { height: auto; overflow: visible; padding: 1.5rem 1.25rem; } }

/* Name pill */
.bb-preview-top { display: flex; }
.bb-name-pill {
  font-size: .72rem; font-weight: 700; color: #D2691E;
  background: rgba(210,105,30,.08); border: 1px solid rgba(210,105,30,.2);
  padding: .3rem 1rem; border-radius: 99px;
  max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.bb-name-pill--ghost { color: #2a2a2a; background: transparent; border-color: rgba(255,255,255,.05); }

/* Visual (burger SVG) */
.bb-visual {
  display: flex; align-items: center; justify-content: center;
  padding: 1rem 0;
  background: radial-gradient(ellipse at 50% 85%, rgba(210,105,30,.07) 0%, transparent 65%);
  border-radius: 16px; min-height: 180px;
}
@media (max-width: 960px) { .bb-visual svg { width: 160px !important; } }

/* Ingredient list */
.bb-ing-list { display: flex; flex-direction: column; border-top: 1px solid rgba(255,255,255,.05); padding-top: .875rem; }
.bb-ing-row {
  display: flex; align-items: center; gap: 8px;
  padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,.03);
}
.bb-ing-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.bb-ing-name { flex: 1; font-size: .8rem; color: #606060; }
.bb-ing-qty { font-size: .68rem; color: #383838; }
.bb-ing-price { font-size: .8rem; color: #D2691E; font-weight: 700; min-width: 40px; text-align: right; }
.bb-ing-empty { font-size: .78rem; color: #282828; font-style: italic; padding: .25rem 0; }

/* Total bar */
.bb-total-bar {
  display: flex; justify-content: space-between; align-items: baseline;
  border-top: 1px solid rgba(255,255,255,.05); padding-top: 1rem;
}
.bb-total-lbl { font-size: .6rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: #323232; }
.bb-total-val { font-size: 2rem; font-weight: 900; color: #D2691E; line-height: 1; letter-spacing: -.02em; }

/* Saved burgers panel */
.bb-saved { border-top: 1px solid rgba(255,255,255,.05); padding-top: .875rem; display: flex; flex-direction: column; gap: .5rem; }
.bb-saved-lbl { font-size: .58rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #303030; }
.bb-saved-list { display: flex; flex-direction: column; gap: .3rem; }
.bb-saved-item {
  display: flex; align-items: center; gap: .4rem;
  background: rgba(255,255,255,.02); border: 1px solid rgba(255,255,255,.05); border-radius: 10px;
  padding: .5rem .5rem .5rem .75rem;
  transition: border-color .15s;
}
.bb-saved-item:hover { border-color: rgba(255,255,255,.08); }
.bb-saved-meta { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: .1rem; }
.bb-saved-row { display: flex; align-items: center; gap: .35rem; }
.bb-saved-name { font-size: .78rem; font-weight: 600; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; min-width: 0; }
.bb-saved-price { font-size: .7rem; color: #D2691E; font-weight: 600; }
.bb-saved-actions { display: flex; gap: .2rem; flex-shrink: 0; }
.bb-status-badge {
  font-size: .52rem; font-weight: 800; padding: 1px 5px;
  border-radius: 4px; text-transform: uppercase; letter-spacing: .04em; flex-shrink: 0;
}
.bb-status--draft    { background: rgba(255,255,255,.04); color: #3a3a3a; border: 1px solid rgba(255,255,255,.06); }
.bb-status--pending  { background: rgba(250,200,0,.06); color: #A87A00; border: 1px solid rgba(250,200,0,.14); }
.bb-status--approved { background: rgba(34,197,94,.06); color: #16a34a; border: 1px solid rgba(34,197,94,.15); }
.bb-status--rejected { background: rgba(239,68,68,.05); color: #dc2626; border: 1px solid rgba(239,68,68,.14); }

/* Saved action buttons */
.bb-sb {
  display: flex; align-items: center; justify-content: center;
  width: 24px; height: 24px; background: transparent;
  border: 1px solid rgba(255,255,255,.07); color: #363636;
  border-radius: 6px; cursor: pointer; transition: all .12s; flex-shrink: 0;
}
.bb-sb:hover { border-color: rgba(255,255,255,.12); color: #707070; background: rgba(255,255,255,.03); }
.bb-sb--green { border-color: rgba(34,197,94,.18) !important; color: #16a34a !important; }
.bb-sb--green:hover { background: rgba(34,197,94,.06) !important; border-color: rgba(34,197,94,.32) !important; }
.bb-sb--orange { border-color: rgba(210,105,30,.18) !important; color: #D2691E !important; }
.bb-sb--orange:hover { background: rgba(210,105,30,.06) !important; border-color: rgba(210,105,30,.32) !important; }
.bb-sb--red { border-color: rgba(239,68,68,.12) !important; color: #363636 !important; }
.bb-sb--red:hover { color: #dc2626 !important; background: rgba(239,68,68,.05) !important; border-color: rgba(239,68,68,.28) !important; }

/* ── Main (right content) ── */
.bb-main {
  padding: 3rem 3.5rem 6rem;
  display: flex; flex-direction: column; gap: 3rem;
}
@media (max-width: 1100px) { .bb-main { padding: 2.5rem 2rem 5rem; } }
@media (max-width: 768px)  { .bb-main { padding: 2rem 1.25rem 4rem; gap: 2.5rem; } }

/* ── Section ── */
.bb-section {
  background: rgba(255,255,255,.015);
  border: 1px solid rgba(255,255,255,.05);
  border-radius: 16px;
  padding: 1.75rem;
}
@media (max-width: 768px) { .bb-section { padding: 1.25rem; } }

/* ── Section header ── */
.bb-sect-head {
  display: flex; align-items: flex-start; gap: 1rem;
  margin-bottom: 1.25rem; padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255,255,255,.05);
}
.bb-step-num {
  font-size: 2rem; font-weight: 900; color: rgba(210,105,30,.2);
  letter-spacing: -.04em; line-height: 1; flex-shrink: 0;
}
.bb-sect-text { display: flex; flex-direction: column; gap: .2rem; padding-top: .3rem; flex: 1; }
.bb-sect-title { font-size: 1rem; font-weight: 700; color: #d0d0d0; letter-spacing: -.01em; }
.bb-sect-hint {
  font-size: .63rem; color: #303030; letter-spacing: .06em; text-transform: uppercase;
}

/* ── Ingredient cards ── */
.bb-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 8px;
}
.bb-card {
  display: flex; align-items: center; gap: 12px;
  background: rgba(255,255,255,.02); border: 1px solid rgba(255,255,255,.06); border-radius: 12px;
  padding: 12px 14px; cursor: pointer; text-align: left; width: 100%;
  transition: border-color .15s, background .15s, transform .15s, box-shadow .15s;
  user-select: none;
}
.bb-card:hover {
  background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.10);
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(0,0,0,.3);
}
.bb-card--on {
  border-color: rgba(210,105,30,.45) !important;
  background: rgba(210,105,30,.06) !important;
  box-shadow: 0 0 0 1px rgba(210,105,30,.12), 0 4px 16px rgba(210,105,30,.08) !important;
}
.bb-card-ico {
  width: 40px; height: 40px; border-radius: 10px;
  background: rgba(255,255,255,.04); display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; flex-shrink: 0; transition: background .15s;
}
.bb-card--on .bb-card-ico { background: rgba(210,105,30,.12); }
.bb-card-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.bb-card-name { font-size: .87rem; font-weight: 600; color: #aaa; line-height: 1.3; }
.bb-card--on .bb-card-name { color: #e0e0e0; }
.bb-card-price { font-size: .74rem; color: #D2691E; font-weight: 700; }
.bb-card-check {
  width: 22px; height: 22px; border-radius: 50%;
  background: #D2691E; color: #fff;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(210,105,30,.4);
}

/* ── Save / submit card ── */
.bb-save-card {
  border: 1px solid rgba(255,255,255,.06); border-radius: 16px;
  padding: 1.75rem; background: rgba(255,255,255,.015);
  display: flex; flex-direction: column; gap: 1.25rem;
}
.bb-save-card--editing {
  border-color: rgba(210,105,30,.25);
  background: rgba(210,105,30,.02);
}
.bb-save-head {
  display: flex; align-items: flex-start; gap: 1rem;
  padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,.05);
}
.bb-save-head .bb-step-num { color: rgba(210,105,30,.2); }
.bb-save-card--editing .bb-save-head .bb-step-num { color: rgba(210,105,30,.45); }
.bb-cancel-btn {
  display: flex; align-items: center; gap: .3rem;
  background: transparent; border: 1px solid rgba(255,255,255,.07); color: #3a3a3a;
  padding: .3rem .7rem; border-radius: 7px;
  font-size: .7rem; font-weight: 600; cursor: pointer;
  transition: all .14s; margin-left: auto; flex-shrink: 0; margin-top: .3rem;
}
.bb-cancel-btn:hover { border-color: rgba(255,255,255,.12); color: #707070; }

/* Name field */
.bb-name-field { display: flex; flex-direction: column; gap: .4rem; }
.bb-name-lbl { font-size: .6rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #2e2e2e; }
.bb-name-wrap { position: relative; }
.bb-name-input {
  width: 100%; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); border-radius: 12px;
  padding: .9rem 3.5rem .9rem 1.1rem; color: #e0e0e0; font-size: .95rem; font-weight: 500;
  outline: none; transition: border-color .2s, box-shadow .2s, background .2s;
}
.bb-name-input:focus { border-color: rgba(210,105,30,.4); box-shadow: 0 0 0 3px rgba(210,105,30,.07); background: rgba(255,255,255,.04); }
.bb-name-input::placeholder { color: #282828; }
.bb-name-count { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); font-size: .6rem; color: #282828; pointer-events: none; }

/* Summary strip */
.bb-summary-strip {
  display: flex; align-items: flex-start; justify-content: space-between;
  background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06); border-radius: 12px;
  padding: 1rem 1.125rem; gap: 1rem;
}
.bb-summary-left { display: flex; flex-direction: column; gap: .4rem; flex: 1; min-width: 0; }
.bb-summary-count { font-size: .6rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #2e2e2e; }
.bb-tag-list { display: flex; flex-wrap: wrap; gap: .25rem; }
.bb-tag { font-size: .67rem; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07); color: #484848; padding: 2px 7px; border-radius: 5px; }
.bb-tag--more { color: #D2691E; border-color: rgba(210,105,30,.2); background: rgba(210,105,30,.05); }
.bb-summary-price { font-size: 1.5rem; font-weight: 900; color: #D2691E; line-height: 1; letter-spacing: -.02em; flex-shrink: 0; }

/* Limit warning */
.bb-limit-warn {
  display: flex; align-items: center; gap: .4rem;
  background: rgba(239,68,68,.04); border: 1px solid rgba(239,68,68,.15);
  border-radius: 10px; padding: .65rem .9rem; font-size: .78rem; color: #b91c1c;
}

/* Validation message */
.bb-validation { font-size: .72rem; color: #383838; font-style: italic; }

/* Buttons */
.bb-save-btns { display: grid; grid-template-columns: 1fr 1.6fr; gap: .6rem; }
.bb-btn-ghost {
  background: transparent; border: 1px solid rgba(255,255,255,.08); color: #3a3a3a;
  padding: .9rem 1rem; border-radius: 12px; font-size: .83rem; font-weight: 600;
  cursor: pointer; transition: all .15s;
}
.bb-btn-ghost:hover:not(:disabled) { border-color: rgba(255,255,255,.14); color: #787878; background: rgba(255,255,255,.03); }
.bb-btn-ghost:disabled { opacity: .2; cursor: not-allowed; }
.bb-btn-primary {
  background: linear-gradient(135deg, #C85A14, #D97020); color: #fff; border: none;
  padding: .9rem 1.1rem; border-radius: 12px; font-size: .87rem; font-weight: 800;
  cursor: pointer; transition: all .18s;
  display: flex; align-items: center; justify-content: center; gap: .45rem;
  box-shadow: 0 4px 20px rgba(210,105,30,.25);
}
.bb-btn-primary:hover:not(:disabled) { background: linear-gradient(135deg, #D86520, #E07830); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(210,105,30,.35); }
.bb-btn-primary:disabled { opacity: .2; cursor: not-allowed; transform: none; box-shadow: none; }

/* ── Toast notifications ── */
.bb-toasts { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: .4rem; pointer-events: none; }
.bb-toast {
  display: flex; align-items: center; gap: .6rem;
  background: #111; border: 1px solid rgba(255,255,255,.08); border-radius: 12px;
  padding: .7rem 1rem; font-size: .82rem; font-weight: 500; color: #bbb;
  box-shadow: 0 8px 32px rgba(0,0,0,.5); pointer-events: auto; min-width: 220px;
}
.bb-toast--ok  { border-color: rgba(34,197,94,.2); }
.bb-toast--err { border-color: rgba(239,68,68,.24); color: #ef4444; }
.bb-toast-icon {
  width: 18px; height: 18px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .65rem; font-weight: 800; flex-shrink: 0;
}
.bb-toast--ok  .bb-toast-icon { background: rgba(34,197,94,.1);  color: #22c55e; }
.bb-toast--err .bb-toast-icon { background: rgba(239,68,68,.1); color: #ef4444; }
.toast-enter-active, .toast-leave-active { transition: all .2s ease; }
.toast-enter-from { opacity: 0; transform: translateX(16px); }
.toast-leave-to   { opacity: 0; transform: translateX(16px); }
</style>
