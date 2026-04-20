<template>
  <div class="relative">
    <div ref="mapContainer" class="w-full rounded-xl overflow-hidden" :style="{ height }"></div>

    <!-- Bolt-stiil info overlay -->
    <div class="absolute top-3 left-3 right-3 z-[1000] pointer-events-none">
      <div v-if="courierLat" class="bg-black/80 backdrop-blur-sm rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse flex-shrink-0"></div>
        <div class="flex-1 min-w-0">
          <p class="text-white text-sm font-semibold">Kuller on teel 🛵</p>
          <p v-if="courierUpdatedAt" class="text-gray-400 text-xs truncate">
            Uuendatud {{ formatTime(courierUpdatedAt) }}
          </p>
        </div>
        <div v-if="etaLabel" class="shrink-0 text-right">
          <p class="text-[10px] text-gray-500 uppercase tracking-wider">ETA</p>
          <p class="text-base font-bold text-cyan-400 leading-tight">{{ etaLabel }}</p>
        </div>
      </div>
      <div v-else class="bg-black/80 backdrop-blur-sm rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse flex-shrink-0"></div>
        <p class="text-gray-300 text-sm">Ootame kulleri GPS signaali...</p>
      </div>
    </div>

    <!-- Sihtkoha label -->
    <div class="absolute bottom-3 left-3 right-3 z-[1000] pointer-events-none">
      <div class="bg-black/80 backdrop-blur-sm rounded-xl px-4 py-2.5 flex items-center gap-2">
        <span class="text-base">🏠</span>
        <p class="text-white text-xs font-medium truncate">{{ deliveryAddress || 'Sihtkoht määramata' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, watch, computed } from 'vue';
import { ref } from 'vue';

const haversineKm = (lat1: number, lng1: number, lat2: number, lng2: number): number => {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLng = (lng2 - lng1) * Math.PI / 180;
  const a = Math.sin(dLat / 2) ** 2
    + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(a));
};

const props = withDefaults(
  defineProps<{
    courierLat: number | null;
    courierLng: number | null;
    courierUpdatedAt?: string | null;
    deliveryLat?: number | null;
    deliveryLng?: number | null;
    deliveryAddress?: string | null;
    height?: string;
  }>(),
  {
    deliveryLat: null,
    deliveryLng: null,
    deliveryAddress: null,
    height: '380px',
  },
);

const mapContainer = ref<HTMLElement | null>(null);
let map: any = null;
let courierMarker: any = null;
let L: any = null;

// ETA — Haversine kaugus * 1.35 (tee tegur) / 25 km/h
const etaMinutes = computed(() => {
  if (!props.courierLat || !props.courierLng || !props.deliveryLat || !props.deliveryLng) return null;
  const dist = haversineKm(props.courierLat, props.courierLng, props.deliveryLat, props.deliveryLng);
  return Math.max(1, Math.ceil(dist * 1.35 / 25 * 60));
});

const etaLabel = computed(() => {
  if (etaMinutes.value === null) return null;
  if (etaMinutes.value < 60) return `~${etaMinutes.value} min`;
  const h = Math.floor(etaMinutes.value / 60);
  const m = etaMinutes.value % 60;
  return m > 0 ? `~${h}h ${m}min` : `~${h}h`;
});

const formatTime = (d: string) =>
  new Date(d).toLocaleTimeString('et-EE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

// SVG markerid — Bolt-stiil
const makeCourierIcon = () =>
  L.divIcon({
    html: `
      <div style="position:relative;width:48px;height:48px">
        <div style="position:absolute;inset:0;background:rgba(56,189,248,0.25);border-radius:50%;animation:ping 1.5s cubic-bezier(0,0,0.2,1) infinite"></div>
        <div style="position:absolute;inset:6px;background:#0ea5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 12px rgba(14,165,233,0.6)">🛵</div>
      </div>
      <style>@keyframes ping{75%,100%{transform:scale(1.8);opacity:0}}</style>
    `,
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    className: '',
  });

const makeHomeIcon = () =>
  L.divIcon({
    html: `
      <div style="position:relative;width:44px;height:44px">
        <div style="position:absolute;inset:0;background:rgba(210,105,30,0.2);border-radius:50%;animation:ping 1.8s cubic-bezier(0,0,0.2,1) infinite"></div>
        <div style="position:absolute;inset:6px;background:#D2691E;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 12px rgba(210,105,30,0.5)">🏠</div>
      </div>
      <style>@keyframes ping{75%,100%{transform:scale(1.8);opacity:0}}</style>
    `,
    iconSize: [44, 44],
    iconAnchor: [22, 22],
    className: '',
  });

onMounted(async () => {
  await import('leaflet/dist/leaflet.css');
  const leaflet = await import('leaflet');
  L = leaflet.default;

  if (!mapContainer.value) return;

  // Kuressaare keskpunkt — fallback kui pole muud
  const KURESSAARE: [number, number] = [58.2517, 22.4935];
  const center: [number, number] =
    props.deliveryLat && props.deliveryLng
      ? [props.deliveryLat, props.deliveryLng]
      : props.courierLat && props.courierLng
        ? [props.courierLat, props.courierLng]
        : KURESSAARE;

  map = L.map(mapContainer.value, {
    zoomControl: false,
    scrollWheelZoom: false,
    attributionControl: false,
  }).setView(center, 14);

  // CartoDB Dark Matter — Bolt-laadne tume kaart
  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    subdomains: 'abcd',
  }).addTo(map);

  // Kodu marker (sihtkoht)
  if (props.deliveryLat && props.deliveryLng) {
    L.marker([props.deliveryLat, props.deliveryLng], { icon: makeHomeIcon() })
      .addTo(map)
      .bindPopup(props.deliveryAddress ?? 'Sihtkoht');
  }

  // Kulleri marker (kui asukoht on teada)
  if (props.courierLat && props.courierLng) {
    courierMarker = L.marker([props.courierLat, props.courierLng], { icon: makeCourierIcon() })
      .addTo(map)
      .bindPopup('Kuller');
  }

  // Fit bounds — näita 🏠 ja 🛵
  const points: [number, number][] = [];
  if (props.deliveryLat && props.deliveryLng) points.push([props.deliveryLat, props.deliveryLng]);
  if (props.courierLat && props.courierLng) points.push([props.courierLat, props.courierLng]);
  if (points.length > 1) {
    map.fitBounds(points, { padding: [60, 60] });
  }
});

// Kulleri markeri uuendus (polling tõmbab uued koordinaadid)
watch(
  () => [props.courierLat, props.courierLng] as [number | null, number | null],
  ([lat, lng]) => {
    if (!map || !L || !lat || !lng) return;
    if (courierMarker) {
      courierMarker.setLatLng([lat, lng]);
      map.setView([lat, lng], map.getZoom(), { animate: true, duration: 0.8 });
    } else {
      courierMarker = L.marker([lat, lng], { icon: makeCourierIcon() }).addTo(map).bindPopup('Kuller');
      const fitPoints: [number, number][] = [[lat, lng]];
      if (props.deliveryLat && props.deliveryLng) fitPoints.push([props.deliveryLat, props.deliveryLng]);
      if (fitPoints.length > 1) map.fitBounds(fitPoints, { padding: [60, 60] });
    }
  },
);

onUnmounted(() => {
  if (map) { map.remove(); map = null; }
});
</script>
