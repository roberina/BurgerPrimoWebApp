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

    <!-- ───── JÄLGIMINE ───── -->
    <template v-else>

      <!-- Header -->
      <div class="flex items-center justify-between px-5 pt-6 pb-3 flex-shrink-0">
        <div class="flex items-center gap-2">
          <span class="text-2xl">🍔</span>
          <div>
            <p class="font-bold text-sm leading-tight">Burger Primo</p>
            <p class="text-xs text-gray-500">Kulleri jälgimine</p>
          </div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold"
             :class="statusBadgeClass">
          <div class="w-1.5 h-1.5 rounded-full" :class="statusDotClass"></div>
          {{ statusLabel }}
        </div>
      </div>

      <!-- Map -->
      <div ref="mapContainer" class="flex-1 w-full" style="min-height: 0;"></div>

      <!-- Bottom sheet -->
      <div class="flex-shrink-0 bg-[#111111] border-t border-white/10 px-5 py-4 space-y-3">

        <!-- Käsitsi juhis -->
        <div v-if="manualMode && !stopped" class="bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-3 flex items-start gap-3">
          <span class="text-lg mt-0.5">🛵</span>
          <div class="flex-1">
            <p class="text-sm font-semibold text-white mb-0.5">Lohista oma asukoht kaardile</p>
            <p class="text-xs text-gray-500">GPS pole saadaval. Kliki kaardil oma asukohal või lohista 🛵 ikooni.</p>
          </div>
        </div>

        <!-- ETA riba -->
        <div v-if="etaLabel" class="flex items-center justify-between bg-cyan-950/40 border border-cyan-900/40 rounded-xl px-4 py-3">
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></div>
            <span class="text-sm text-cyan-300 font-medium">Sihtkohani</span>
          </div>
          <div class="flex items-center gap-3">
            <span v-if="etaDistance" class="text-xs text-gray-400">{{ etaDistance }}</span>
            <span class="text-lg font-bold text-cyan-400">{{ etaLabel }}</span>
          </div>
        </div>

        <!-- Tellimus + sihtkoht -->
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-0.5">Tellimus</p>
            <p class="font-mono font-bold text-[#D2691E]">{{ order.order_number }}</p>
            <div v-if="order.delivery_address" class="mt-2">
              <p class="text-xs text-gray-500 uppercase tracking-widest mb-0.5">Sihtkoht</p>
              <p class="text-sm text-white font-medium leading-snug">{{ order.delivery_address }}</p>
            </div>
          </div>
          <div v-if="lastUpdate && !manualMode" class="text-right shrink-0">
            <p class="text-xs text-gray-500 mb-0.5">GPS täpsus</p>
            <p class="text-sm font-semibold text-white">±{{ Math.round(lastUpdate.accuracy) }}m</p>
          </div>
        </div>

        <button
          v-if="manualMode && !stopped"
          @click="retryGps"
          class="w-full py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 font-semibold transition text-sm"
        >
          🔄 Proovi GPS-i uuesti
        </button>

        <button
          v-if="(isTracking || manualMode) && !stopped"
          @click="stopTracking"
          class="w-full py-3.5 rounded-xl bg-red-600/15 hover:bg-red-600 border border-red-800/50 hover:border-red-600 text-red-400 hover:text-white font-bold transition text-sm"
        >
          Peata jälgimine
        </button>
        <div v-else-if="stopped" class="text-center py-2">
          <p class="text-sm text-gray-400">Jälgimine peatatud ✓</p>
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

const openNavigation = () => {
  const destLat = props.order.delivery_lat;
  const destLng = props.order.delivery_lng;
  const address = props.order.delivery_address;

  if (destLat && destLng) {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    if (isIOS) {
      // Proovi Waze, kui pole installitud, ava Apple Maps
      window.location.href = `waze://?ll=${destLat},${destLng}&navigate=yes`;
      setTimeout(() => {
        window.location.href = `maps://maps.apple.com/?daddr=${destLat},${destLng}&dirflg=d`;
      }, 1500);
    } else {
      // Android: proovi Waze, fallback Google Maps
      window.location.href = `waze://?ll=${destLat},${destLng}&navigate=yes`;
      setTimeout(() => {
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${destLat},${destLng}&travelmode=driving`, '_blank');
      }, 1500);
    }
  } else if (address) {
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}&travelmode=driving`, '_blank');
  }
};

const acceptOrder = async () => {
  deciding.value = true;
  decideError.value = '';
  try {
    await fetch(props.acceptUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' } });
    openNavigation();
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
      style: { color: '#0ea5e9', weight: 5, opacity: 0.85, lineCap: 'round', lineJoin: 'round' },
    }).addTo(map);

    etaMinutes.value = Math.max(1, Math.ceil(route.duration / 60));
    etaDistance.value = route.distance >= 1000
      ? `${(route.distance / 1000).toFixed(1)} km`
      : `${Math.round(route.distance)} m`;
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
      const { latitude: lat, longitude: lng, accuracy } = pos.coords;
      lastUpdate.value = { lat, lng, accuracy };
      if (!map) await initMap(lat, lng, false);
      else updateMapPosition(lat, lng);
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
