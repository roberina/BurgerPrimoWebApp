<template>
  <div class="min-h-screen bg-[#0B0B0B] text-white flex flex-col">

    <!-- ───── OTSUSTAMINE — kaart taustal + popup all ───── -->
    <template v-if="phase === 'deciding'">
      <!-- Taustakaart (staatilne, ainult preview) -->
      <div ref="previewMapContainer" class="flex-1 w-full" style="min-height: 0;"></div>

      <!-- Popup — tuleb alt üles -->
      <div class="flex-shrink-0 relative">
        <!-- Gradient fade kaardist popupisse -->
        <div class="absolute -top-12 left-0 right-0 h-12 pointer-events-none"
             style="background: linear-gradient(to bottom, transparent, #0B0B0B)"></div>

        <div class="bg-[#0B0B0B] px-5 pt-4 pb-6 space-y-4">

          <!-- Drag handle -->
          <div class="flex justify-center mb-1">
            <div class="w-10 h-1 bg-white/20 rounded-full"></div>
          </div>

          <!-- Header -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-[#D2691E]/15 rounded-xl flex items-center justify-center text-xl">🍔</div>
              <div>
                <p class="text-xs text-gray-500 uppercase tracking-widest">Uus tellimus</p>
                <p class="font-mono font-bold text-[#D2691E] text-base">{{ order.order_number }}</p>
              </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ Number(order.total_amount).toFixed(2) }}€</p>
          </div>

          <!-- Items -->
          <div class="bg-[#111111] rounded-2xl border border-white/6 divide-y divide-white/5 overflow-hidden">
            <div
              v-for="item in order.items"
              :key="item.burger_name"
              class="flex items-center justify-between px-4 py-3"
            >
              <div>
                <p class="font-semibold text-sm">{{ item.burger_name }}</p>
                <p class="text-xs text-gray-500">{{ item.quantity }}x</p>
              </div>
              <p class="font-bold text-[#D2691E] text-sm">{{ Number(item.price * item.quantity).toFixed(2) }}€</p>
            </div>
          </div>

          <!-- Sihtkoht -->
          <div class="flex items-center gap-3 bg-[#111111] rounded-2xl border border-white/6 px-4 py-3">
            <span class="text-xl">🏠</span>
            <p class="text-sm text-gray-300 leading-snug flex-1">
              {{ order.delivery_address ?? 'Sihtkoht täpsustamata' }}
            </p>
          </div>

          <!-- Viga -->
          <p v-if="decideError" class="text-sm text-red-400 text-center">{{ decideError }}</p>

          <!-- Nupud -->
          <div class="grid grid-cols-2 gap-3 pt-1">
            <button
              @click="declineOrder"
              :disabled="deciding"
              class="py-4 rounded-2xl font-bold text-base transition bg-white/6 hover:bg-white/12 border border-white/10 text-gray-300 disabled:opacity-50"
            >✕ Keeldun</button>
            <button
              @click="acceptOrder"
              :disabled="deciding"
              class="py-4 rounded-2xl font-bold text-base transition disabled:opacity-50"
              style="background: linear-gradient(135deg, #16a34a, #15803d); color: white;"
            >
              {{ deciding ? '...' : '✓ Võtan vastu' }}
            </button>
          </div>

        </div>
      </div>
    </template>

    <!-- ───── KEELDUTUD ───── -->
    <template v-else-if="phase === 'declined'">
      <div class="flex-1 flex flex-col items-center justify-center px-5 text-center gap-4">
        <div class="text-6xl mb-2">👋</div>
        <h2 class="text-2xl font-bold">Tellimus edastatud</h2>
        <p class="text-gray-400 text-sm">Keeldusid tellimuse vastuvõtmisest.<br>Restoran leiab teise kulleri.</p>
      </div>
    </template>

    <!-- ───── JÄLGIMINE (navigatsiooni reziim) ───── -->
    <template v-else>
      <div class="relative w-full h-screen overflow-hidden bg-[#0a1628]">

        <!-- Ülemine navigatsiooniriba -->
        <div class="absolute top-0 left-0 right-0 z-20 shadow-2xl">

          <!-- Peamine juhis -->
          <div class="flex items-stretch bg-[#1a2b3c]" style="min-height:80px">
            <!-- Pöörde ikoon -->
            <div class="w-20 bg-[#0f1e2e] flex items-center justify-center flex-shrink-0">
              <div v-html="currentManeuverSvg" class="text-white w-12 h-12"></div>
            </div>
            <!-- Kaugus + tänav -->
            <div class="flex-1 flex flex-col justify-center px-4 py-2 min-w-0">
              <div class="text-3xl font-black text-white leading-none tracking-tight">{{ distanceToNextFormatted }}</div>
              <div class="text-sm text-gray-300 mt-1 truncate">{{ currentStepStreet }}</div>
            </div>
          </div>

          <!-- Järgmine juhis -->
          <div v-if="nextNavStep" class="flex items-center gap-2 px-4 py-1.5 bg-[#0f1e2e]/90 backdrop-blur-sm">
            <span class="text-[10px] text-gray-500 uppercase tracking-widest flex-shrink-0">ja siis</span>
            <div v-html="nextManeuverSvg" class="text-gray-300 w-4 h-4 flex-shrink-0"></div>
            <span class="text-xs text-gray-300 truncate">{{ nextNavStep.name || 'Jätka' }}</span>
          </div>

          <!-- Käsitsi režiim hoiatus -->
          <div v-if="manualMode && !stopped" class="flex items-center gap-2 px-4 py-2 bg-orange-900/40 border-t border-orange-700/30">
            <span class="text-sm">🛵</span>
            <p class="text-xs text-orange-300">GPS pole saadaval — kliki kaardil oma asukohale</p>
          </div>
        </div>

        <!-- Kaart (täisekraan) -->
        <div ref="mapContainer" class="absolute inset-0 w-full h-full"></div>

        <!-- Alumine infopaneel -->
        <div class="absolute bottom-0 left-0 right-0 z-20" style="background: linear-gradient(to top, #0a1628 80%, transparent)">

          <div class="px-5 pt-6 pb-4">
            <!-- Kiirus + ETA + peata -->
            <div class="flex items-center justify-between mb-3">
              <!-- Kiirus -->
              <div class="bg-[#0f1e2e]/80 rounded-2xl px-4 py-2 text-center min-w-[72px] border border-white/8">
                <div class="text-2xl font-black text-white leading-none">{{ currentSpeedValue }}</div>
                <div class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">km/h</div>
              </div>

              <!-- ETA -->
              <div v-if="etaLabel" class="text-center flex-1 px-3">
                <div class="text-xl font-black text-white">{{ etaLabel }}</div>
                <div class="text-xs text-gray-400">{{ etaDistance }}</div>
              </div>

              <!-- Nupud -->
              <div class="flex flex-col gap-2">
                <button v-if="manualMode && !stopped" @click="retryGps"
                  class="w-10 h-10 rounded-full bg-blue-600/20 border border-blue-700/50 flex items-center justify-center text-blue-400 text-sm">
                  🔄
                </button>
                <button v-if="!stopped" @click="stopTracking"
                  class="w-10 h-10 rounded-full bg-red-600/20 border border-red-800/50 flex items-center justify-center text-red-400 font-bold text-sm">
                  ✕
                </button>
              </div>
            </div>

            <!-- Tellimus info -->
            <div class="flex items-center gap-2 bg-[#0f1e2e]/60 rounded-xl px-4 py-2.5 border border-white/6">
              <span class="text-base">🏠</span>
              <div class="flex-1 min-w-0">
                <span class="font-mono text-xs text-[#D2691E] font-bold mr-2">{{ order.order_number }}</span>
                <span v-if="order.delivery_address" class="text-xs text-gray-400 truncate">{{ order.delivery_address }}</span>
              </div>
              <div v-if="lastUpdate && !manualMode" class="text-right shrink-0">
                <span class="text-[10px] text-gray-600">±{{ Math.round(lastUpdate.accuracy) }}m</span>
              </div>
            </div>

            <div v-if="stopped" class="text-center mt-3">
              <p class="text-sm text-gray-400">Jälgimine peatatud ✓</p>
            </div>
          </div>
        </div>

      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface OrderItem {
  burger_name: string;
  quantity: number;
  price: number;
}

const props = defineProps<{
  order: {
    id: number;
    order_number: string;
    status: string;
    total_amount: number;
    delivery_lat?: number | null;
    delivery_lng?: number | null;
    delivery_address?: string | null;
    items: OrderItem[];
  };
  token: string;
  updateUrl: string;
  acceptUrl: string;
  declineUrl: string;
}>();

// ─── Phase ────────────────────────────────────────────────
type Phase = 'deciding' | 'tracking' | 'declined';
const phase = ref<Phase>('deciding');
const deciding = ref(false);
const decideError = ref('');
const previewMapContainer = ref<HTMLElement | null>(null);
let previewMap: any = null;

const acceptOrder = async () => {
  deciding.value = true;
  decideError.value = '';
  try {
    await fetch(props.acceptUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' } });
    phase.value = 'tracking';
    // GPS käivitub pärast faasi muutumist (mapContainer on siis DOM-is)
    setTimeout(() => startTracking(), 100);
  } catch {
    decideError.value = 'Viga. Proovi uuesti.';
  }
  deciding.value = false;
};

const declineOrder = async () => {
  deciding.value = true;
  decideError.value = '';
  try {
    await fetch(props.declineUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' } });
    phase.value = 'declined';
  } catch {
    decideError.value = 'Viga. Proovi uuesti.';
  }
  deciding.value = false;
};

// ─── Tracking ─────────────────────────────────────────────
const mapContainer = ref<HTMLElement | null>(null);
const isTracking = ref(false);
const manualMode = ref(false);
const stopped = ref(false);
const lastUpdate = ref<{ lat: number; lng: number; accuracy: number } | null>(null);

let map: any = null;
let courierMarker: any = null;
let routeLayer: any = null;
let L: any = null;
let watchId: number | null = null;
let gpsTimeout: ReturnType<typeof setTimeout> | null = null;
let manualInterval: ReturnType<typeof setInterval> | null = null;

const etaMinutes = ref<number | null>(null);
const etaDistance = ref<string | null>(null);
let lastRouteFetchLat = 0;
let lastRouteFetchLng = 0;
let lastRouteFetchTime = 0;

// Navigatsiooni olek
const routeSteps = ref<any[]>([]);
const currentStepIndex = ref(0);
const distanceToNextManeuver = ref<number | null>(null);
const currentSpeed = ref<number | null>(null);

const haversineDist = (lat1: number, lng1: number, lat2: number, lng2: number): number => {
  const R = 6371000;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLng = (lng2 - lng1) * Math.PI / 180;
  const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
};

const updateNavStep = (lat: number, lng: number) => {
  if (!routeSteps.value.length) return;
  let idx = currentStepIndex.value;
  while (idx < routeSteps.value.length - 1) {
    const loc = routeSteps.value[idx]?.maneuver?.location;
    if (!loc) break;
    const dist = haversineDist(lat, lng, loc[1], loc[0]);
    if (dist < 25) { idx++; currentStepIndex.value = idx; }
    else break;
  }
  const cur = routeSteps.value[idx];
  if (cur?.maneuver?.location) {
    const [mLng, mLat] = cur.maneuver.location;
    distanceToNextManeuver.value = haversineDist(lat, lng, mLat, mLng);
  }
};

const arrowSvg = (modifier: string, size: number): string => {
  const s = `stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"`;
  const arrows: Record<string, string> = {
    straight:      `<path d="M20 32V10M12 18l8-9 8 9" ${s}/>`,
    right:         `<path d="M13 28V20a7 7 0 0 1 7-7h2M22 8l6 5-6 5" ${s}/>`,
    left:          `<path d="M27 28V20a7 7 0 0 0-7-7h-2M18 8l-6 5 6 5" ${s}/>`,
    'slight right':`<path d="M14 30V22c0-5 3-9 9-12M23 8l6 3-3 6" ${s}/>`,
    'slight left': `<path d="M26 30V22c0-5-3-9-9-12M17 8l-6 3 3 6" ${s}/>`,
    'sharp right': `<path d="M12 30v-6a8 8 0 0 1 8-8h6M26 11l3 7-7 1" ${s}/>`,
    'sharp left':  `<path d="M28 30v-6a8 8 0 0 0-8-8h-6M14 11l-3 7 7 1" ${s}/>`,
    uturn:         `<path d="M14 30V22a8 8 0 0 1 16 0v2M30 20l-4 4-4-4" ${s}/>`,
    arrive:        `<circle cx="20" cy="20" r="8" ${s}/><path d="M20 10v10M20 30v-2" ${s}/>`,
  };
  const path = arrows[modifier] || arrows['straight'];
  return `<svg width="${size}" height="${size}" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">${path}</svg>`;
};

const getManeuverSvg = (step: any, size = 40): string => {
  if (!step) return arrowSvg('straight', size);
  const type = step.maneuver?.type;
  const mod = step.maneuver?.modifier;
  if (type === 'arrive') return arrowSvg('arrive', size);
  return arrowSvg(mod || 'straight', size);
};

const currentNavStep = computed(() => routeSteps.value[currentStepIndex.value] ?? null);
const nextNavStep = computed(() => routeSteps.value[currentStepIndex.value + 1] ?? null);
const currentManeuverSvg = computed(() => getManeuverSvg(currentNavStep.value, 40));
const nextManeuverSvg = computed(() => getManeuverSvg(nextNavStep.value, 16));
const currentStepStreet = computed(() => currentNavStep.value?.name || 'Jätka otse');
const currentSpeedValue = computed(() => currentSpeed.value !== null ? Math.round(currentSpeed.value) : 0);
const distanceToNextFormatted = computed(() => {
  const d = distanceToNextManeuver.value;
  if (d === null) return etaDistance.value ?? '--';
  if (d < 50) return `${Math.round(d)} m`;
  if (d < 1000) return `${Math.round(d / 10) * 10} m`;
  return `${(d / 1000).toFixed(1)} km`;
});

const etaLabel = computed(() => {
  if (etaMinutes.value === null) return null;
  if (etaMinutes.value < 60) return `~${etaMinutes.value} min`;
  const h = Math.floor(etaMinutes.value / 60);
  const m = etaMinutes.value % 60;
  return m > 0 ? `~${h}h ${m}min` : `~${h}h`;
});

const fetchRoute = async (lat: number, lng: number) => {
  const destLat = props.order.delivery_lat;
  const destLng = props.order.delivery_lng;
  if (!destLat || !destLng || !map || !L) return;

  // Ainult kui liigutud >100m või >60s möödunud (välja arvatud esimene kord)
  const now = Date.now();
  if (routeLayer) {
    const R = 6371000;
    const dLat = (lat - lastRouteFetchLat) * Math.PI / 180;
    const dLng = (lng - lastRouteFetchLng) * Math.PI / 180;
    const moved = R * Math.sqrt(dLat ** 2 + (Math.cos(lat * Math.PI / 180) * dLng) ** 2);
    if (now - lastRouteFetchTime < 60000 && moved < 100) return;
  }
  lastRouteFetchLat = lat;
  lastRouteFetchLng = lng;
  lastRouteFetchTime = now;

  try {
    const res = await fetch(
      `https://router.project-osrm.org/route/v1/driving/${lng},${lat};${destLng},${destLat}?geometries=geojson&overview=full`,
    );
    const data = await res.json();
    if (!data.routes?.[0]) return;
    const route = data.routes[0];

    if (routeLayer) { map.removeLayer(routeLayer); routeLayer = null; }
    routeLayer = L.geoJSON(route.geometry, {
      style: { color: '#0ea5e9', weight: 6, opacity: 0.9, lineCap: 'round', lineJoin: 'round' },
    }).addTo(map);

    etaMinutes.value = Math.max(1, Math.ceil(route.duration / 60));
    etaDistance.value = route.distance >= 1000
      ? `${(route.distance / 1000).toFixed(1)} km`
      : `${Math.round(route.distance)} m`;

    // Salvesta pöördejuhised
    if (data.routes[0].legs?.[0]?.steps) {
      routeSteps.value = data.routes[0].legs[0].steps;
      currentStepIndex.value = 0;
      distanceToNextManeuver.value = null;
    }
  } catch { /* vaikne */ }
};

const RESTAURANT_LAT = 58.2517;
const RESTAURANT_LNG = 22.4935;
const SAAREMAA = { sw: [57.85, 21.50] as [number, number], ne: [58.75, 23.20] as [number, number] };

const statusBadgeClass = computed(() => {
  if (stopped.value)    return 'bg-gray-800 text-gray-400';
  if (isTracking.value) return 'bg-green-900/40 text-green-400';
  if (manualMode.value) return 'bg-orange-900/40 text-orange-400';
  return 'bg-gray-800 text-gray-400';
});
const statusDotClass = computed(() => {
  if (stopped.value)    return 'bg-gray-500';
  if (isTracking.value) return 'bg-green-400 animate-pulse';
  if (manualMode.value) return 'bg-orange-400 animate-pulse';
  return 'bg-gray-500';
});
const statusLabel = computed(() => {
  if (stopped.value)    return 'Peatatud';
  if (isTracking.value) return 'GPS aktiivne';
  if (manualMode.value) return 'Käsitsi';
  return 'Ühendan...';
});

const makeCourierIcon = (draggable = false) =>
  L.divIcon({
    html: `
      <div style="position:relative;width:48px;height:48px">
        <div style="position:absolute;inset:0;background:rgba(56,189,248,0.25);border-radius:50%;animation:ping 1.5s cubic-bezier(0,0,0.2,1) infinite"></div>
        <div style="position:absolute;inset:6px;background:#0ea5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 12px rgba(14,165,233,0.6)${draggable ? ';cursor:grab' : ''}">🛵</div>
      </div>
      <style>@keyframes ping{75%,100%{transform:scale(1.8);opacity:0}}</style>
    `,
    iconSize: [48, 48], iconAnchor: [24, 24], className: '',
  });

const makeRestaurantIcon = () =>
  L.divIcon({
    html: `<div style="font-size:28px;line-height:1;filter:drop-shadow(0 2px 6px rgba(0,0,0,.8))">🍔</div>`,
    iconSize: [32, 32], iconAnchor: [16, 16], className: '',
  });

const makeHomeIcon = () =>
  L.divIcon({
    html: `
      <div style="position:relative;width:44px;height:44px">
        <div style="position:absolute;inset:0;background:rgba(210,105,30,0.2);border-radius:50%;animation:ping2 1.8s cubic-bezier(0,0,0.2,1) infinite"></div>
        <div style="position:absolute;inset:6px;background:#D2691E;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 12px rgba(210,105,30,0.5)">🏠</div>
      </div>
      <style>@keyframes ping2{75%,100%{transform:scale(1.8);opacity:0}}</style>
    `,
    iconSize: [44, 44], iconAnchor: [22, 22], className: '',
  });

const initMap = async (lat: number, lng: number, isDraggable = false) => {
  if (map || !mapContainer.value) return;

  await import('leaflet/dist/leaflet.css');
  const leaflet = await import('leaflet');
  L = leaflet.default;

  map = L.map(mapContainer.value, { zoomControl: false, attributionControl: false })
    .setView([lat, lng], 14);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19, subdomains: 'abcd',
  }).addTo(map);

  L.marker([RESTAURANT_LAT, RESTAURANT_LNG], { icon: makeRestaurantIcon() })
    .addTo(map).bindPopup('<strong>Burger Primo</strong>');

  const destLat = props.order.delivery_lat;
  const destLng = props.order.delivery_lng;
  if (destLat && destLng) {
    L.marker([destLat, destLng], { icon: makeHomeIcon() })
      .addTo(map).bindPopup(props.order.delivery_address ?? 'Sihtkoht');
    map.fitBounds(
      [[RESTAURANT_LAT, RESTAURANT_LNG], [destLat, destLng], [lat, lng]],
      { padding: [50, 50] },
    );
  }

  courierMarker = L.marker([lat, lng], { icon: makeCourierIcon(isDraggable), draggable: isDraggable })
    .addTo(map).bindPopup(isDraggable ? 'Lohista siia oma asukohale' : 'Sina oled siin');

  if (isDraggable) {
    courierMarker.on('dragend', () => {
      const { lat: la, lng: ln } = courierMarker.getLatLng();
      const cLat = Math.max(SAAREMAA.sw[0], Math.min(SAAREMAA.ne[0], la));
      const cLng = Math.max(SAAREMAA.sw[1], Math.min(SAAREMAA.ne[1], ln));
      courierMarker.setLatLng([cLat, cLng]);
      sendLocation(cLat, cLng);
      fetchRoute(cLat, cLng);
    });
    map.on('click', (e: any) => {
      if (stopped.value) return;
      const { lat: cLat, lng: cLng } = e.latlng;
      if (cLat < SAAREMAA.sw[0] || cLat > SAAREMAA.ne[0] || cLng < SAAREMAA.sw[1] || cLng > SAAREMAA.ne[1]) return;
      courierMarker.setLatLng([cLat, cLng]);
      map.panTo([cLat, cLng], { animate: true, duration: 0.5 });
      sendLocation(cLat, cLng);
      fetchRoute(cLat, cLng);
    });
    courierMarker.openPopup();
  }

  // Joonista marsruut kohe kaardi laadimisel
  fetchRoute(lat, lng);
};

const updateMapPosition = (lat: number, lng: number) => {
  if (!map || !courierMarker) return;
  courierMarker.setLatLng([lat, lng]);
  map.panTo([lat, lng], { animate: true, duration: 1 });
};

const startTracking = () => {
  if (!navigator.geolocation) { switchToManual(); return; }

  gpsTimeout = setTimeout(() => {
    if (!isTracking.value) switchToManual();
  }, 8000);

  watchId = navigator.geolocation.watchPosition(
    async (pos) => {
      if (gpsTimeout) { clearTimeout(gpsTimeout); gpsTimeout = null; }
      if (manualMode.value) {
        manualMode.value = false;
        if (manualInterval) { clearInterval(manualInterval); manualInterval = null; }
        courierMarker?.dragging?.disable();
      }
      isTracking.value = true;
      const { latitude: lat, longitude: lng, accuracy, speed } = pos.coords;
      currentSpeed.value = speed !== null ? speed * 3.6 : null;
      lastUpdate.value = { lat, lng, accuracy };
      if (!map) await initMap(lat, lng, false);
      else updateMapPosition(lat, lng);
      updateNavStep(lat, lng);
      sendLocation(lat, lng);
      fetchRoute(lat, lng);
    },
    () => {
      if (gpsTimeout) { clearTimeout(gpsTimeout); gpsTimeout = null; }
      if (!isTracking.value) switchToManual();
    },
    { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 },
  );
};

const switchToManual = async () => {
  manualMode.value = true;
  isTracking.value = false;
  const startLat = RESTAURANT_LAT;
  const startLng = RESTAURANT_LNG;
  if (!map) await initMap(startLat, startLng, true);
  else {
    courierMarker?.dragging?.enable();
    fetchRoute(startLat, startLng);
  }
  manualInterval = setInterval(() => {
    if (stopped.value || !courierMarker) return;
    const { lat, lng } = courierMarker.getLatLng();
    sendLocation(lat, lng);
  }, 30000);
};

const retryGps = () => {
  if (manualInterval) { clearInterval(manualInterval); manualInterval = null; }
  manualMode.value = false;
  isTracking.value = false;
  if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
  startTracking();
};

const stopTracking = () => {
  if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
  if (gpsTimeout) { clearTimeout(gpsTimeout); gpsTimeout = null; }
  if (manualInterval) { clearInterval(manualInterval); manualInterval = null; }
  isTracking.value = false;
  manualMode.value = false;
  stopped.value = true;
};

const sendLocation = async (lat: number, lng: number) => {
  try {
    await fetch(props.updateUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ lat, lng }),
    });
  } catch { /* vaikne */ }
};

// Initsialiseeri preview kaart otsustamise ekraanil
const initPreviewMap = async () => {
  if (!previewMapContainer.value || previewMap) return;
  const destLat = props.order.delivery_lat;
  const destLng = props.order.delivery_lng;
  if (!destLat || !destLng) return;

  await import('leaflet/dist/leaflet.css');
  const leaflet = await import('leaflet');
  const Lp = leaflet.default;

  previewMap = Lp.map(previewMapContainer.value, {
    zoomControl: false,
    scrollWheelZoom: false,
    dragging: false,
    doubleClickZoom: false,
    attributionControl: false,
    keyboard: false,
  }).setView([destLat, destLng], 14);

  Lp.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19, subdomains: 'abcd',
  }).addTo(previewMap);

  // Restorani marker
  Lp.marker([RESTAURANT_LAT, RESTAURANT_LNG], {
    icon: Lp.divIcon({
      html: `<div style="font-size:22px;line-height:1;filter:drop-shadow(0 2px 4px rgba(0,0,0,.8))">🍔</div>`,
      iconSize: [26, 26], iconAnchor: [13, 13], className: '',
    }),
  }).addTo(previewMap);

  // Sihtkoha marker
  Lp.marker([destLat, destLng], {
    icon: Lp.divIcon({
      html: `
        <div style="position:relative;width:38px;height:38px">
          <div style="position:absolute;inset:0;background:rgba(210,105,30,0.25);border-radius:50%;animation:pingp 1.8s cubic-bezier(0,0,0.2,1) infinite"></div>
          <div style="position:absolute;inset:5px;background:#D2691E;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;box-shadow:0 3px 8px rgba(210,105,30,0.6)">🏠</div>
        </div>
        <style>@keyframes pingp{75%,100%{transform:scale(1.8);opacity:0}}</style>
      `,
      iconSize: [38, 38], iconAnchor: [19, 19], className: '',
    }),
  }).addTo(previewMap);

  previewMap.fitBounds(
    [[RESTAURANT_LAT, RESTAURANT_LNG], [destLat, destLng]],
    { padding: [30, 30] },
  );
};

onMounted(() => {
  // Initsialiseeri preview kaart kui sihtkoord on olemas
  if (props.order.delivery_lat && props.order.delivery_lng) {
    setTimeout(() => initPreviewMap(), 50);
  }
});

onUnmounted(() => {
  if (watchId !== null) navigator.geolocation.clearWatch(watchId);
  if (gpsTimeout) clearTimeout(gpsTimeout);
  if (manualInterval) clearInterval(manualInterval);
  if (routeLayer) { routeLayer = null; }
  if (map) { map.remove(); map = null; }
  if (previewMap) { previewMap.remove(); previewMap = null; }
});
</script>
