<template>
  <div>
    <!-- Address search -->
    <div class="relative mb-3">
      <div class="flex items-center gap-2 bg-[#0B0B0B] border border-gray-700 rounded-xl px-4 focus-within:border-[#D2691E] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          @input="debouncedSearch"
          @keydown.escape="suggestions = []"
          @keydown.enter.prevent="suggestions.length && selectSuggestion(suggestions[0])"
          type="text"
          placeholder="Otsi aadressi Saaremaal..."
          class="flex-1 bg-transparent py-3 text-white text-sm focus:outline-none placeholder-gray-600"
        />
        <button v-if="searchQuery" @click="clearSearch" class="text-gray-600 hover:text-gray-400 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Suggestions dropdown -->
      <div v-if="suggestions.length" class="absolute top-full left-0 right-0 z-[2000] mt-1 bg-[#1a1a1a] border border-gray-700 rounded-xl overflow-hidden shadow-2xl">
        <button
          v-for="s in suggestions"
          :key="s.place_id"
          @click="selectSuggestion(s)"
          class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:bg-white/6 hover:text-white transition-colors border-b border-gray-800/60 last:border-0 flex items-start gap-2"
        >
          <span class="text-base leading-none mt-0.5 flex-shrink-0">📍</span>
          <span class="leading-snug">{{ formatAddress(s) }}</span>
        </button>
      </div>
    </div>

    <!-- Map -->
    <div
      ref="mapContainer"
      class="w-full rounded-xl overflow-hidden border border-gray-800"
      style="height: 280px; isolation: isolate; z-index: 0;"
    ></div>

    <!-- Selected address -->
    <div class="mt-2.5 min-h-[20px]">
      <div v-if="modelAddress" class="flex items-start gap-2">
        <span class="text-green-400 text-sm mt-0.5">✓</span>
        <span class="text-sm text-gray-300 leading-snug">{{ modelAddress }}</span>
      </div>
      <p v-else class="text-xs text-gray-600">Kliki kaardil või lohista maja ikooni oma aadressile</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{
  modelLat: number | null;
  modelLng: number | null;
  modelAddress: string;
}>();

const emit = defineEmits<{
  'update:modelLat': [value: number];
  'update:modelLng': [value: number];
  'update:modelAddress': [value: string];
}>();

// Saaremaa center (Kuressaare) & bounds
const CENTER: [number, number] = [58.2517, 22.4935];
const BOUNDS = { sw: [57.85, 21.50] as [number, number], ne: [58.75, 23.20] as [number, number] };

const mapContainer = ref<HTMLElement | null>(null);
const searchQuery = ref('');
const suggestions = ref<any[]>([]);
const isSearching = ref(false);

let map: any = null;
let homeMarker: any = null;
let L: any = null;
let searchTimer: ReturnType<typeof setTimeout> | null = null;

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

const isInSaaremaa = (lat: number, lng: number): boolean =>
  lat >= BOUNDS.sw[0] && lat <= BOUNDS.ne[0] &&
  lng >= BOUNDS.sw[1] && lng <= BOUNDS.ne[1];

const formatAddress = (s: any): string => {
  const a = s.address || {};
  const parts = [
    a.road && a.house_number ? `${a.road} ${a.house_number}` : (a.road || null),
    a.village || a.town || a.city || a.municipality || null,
  ].filter(Boolean);
  return parts.length ? parts.join(', ') : s.display_name;
};

const reverseGeocode = async (lat: number, lng: number) => {
  try {
    const resp = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=17&addressdetails=1`,
      { headers: { 'Accept-Language': 'et' } },
    );
    const data = await resp.json();
    if (data.display_name) {
      const a = data.address || {};
      const parts = [
        a.road && a.house_number ? `${a.road} ${a.house_number}` : (a.road || null),
        a.village || a.town || a.city || a.municipality || null,
        a.county || null,
      ].filter(Boolean);
      emit('update:modelAddress', parts.length ? parts.join(', ') : data.display_name);
    }
  } catch { /* silent */ }
};

const placeMarker = (lat: number, lng: number) => {
  if (!homeMarker || !map) return;
  homeMarker.setLatLng([lat, lng]);
  emit('update:modelLat', lat);
  emit('update:modelLng', lng);
  reverseGeocode(lat, lng);
};

const debouncedSearch = () => {
  if (searchTimer) clearTimeout(searchTimer);
  searchTimer = setTimeout(doSearch, 400);
};

const doSearch = async () => {
  const q = searchQuery.value.trim();
  if (q.length < 3) { suggestions.value = []; return; }
  isSearching.value = true;
  try {
    const viewbox = `${BOUNDS.sw[1]},${BOUNDS.sw[0]},${BOUNDS.ne[1]},${BOUNDS.ne[0]}`;
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&viewbox=${viewbox}&bounded=1&countrycodes=ee&limit=5&addressdetails=1`;
    const resp = await fetch(url, { headers: { 'Accept-Language': 'et' } });
    suggestions.value = await resp.json();
  } catch { /* silent */ }
  finally { isSearching.value = false; }
};

const selectSuggestion = (s: any) => {
  const lat = parseFloat(s.lat);
  const lng = parseFloat(s.lon);
  suggestions.value = [];
  searchQuery.value = formatAddress(s);
  homeMarker?.setLatLng([lat, lng]);
  map?.setView([lat, lng], 16, { animate: true });
  emit('update:modelLat', lat);
  emit('update:modelLng', lng);
  emit('update:modelAddress', formatAddress(s));
};

const clearSearch = () => {
  searchQuery.value = '';
  suggestions.value = [];
};

onMounted(async () => {
  await import('leaflet/dist/leaflet.css');
  const leaflet = await import('leaflet');
  L = leaflet.default;
  if (!mapContainer.value) return;

  const startLat = props.modelLat ?? CENTER[0];
  const startLng = props.modelLng ?? CENTER[1];

  map = L.map(mapContainer.value, {
    zoomControl: true,
    scrollWheelZoom: true,
    attributionControl: false,
  }).setView([startLat, startLng], 12);

  // Lock map to Saaremaa
  const leafletBounds = L.latLngBounds(BOUNDS.sw, BOUNDS.ne);
  map.setMaxBounds(leafletBounds.pad(0.05));
  map.options.minZoom = 10;

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    subdomains: 'abcd',
  }).addTo(map);

  // Draggable home marker
  homeMarker = L.marker([startLat, startLng], {
    icon: makeHomeIcon(),
    draggable: true,
  }).addTo(map).bindPopup('Sinu sihtkoht', { offset: [0, -16] });

  homeMarker.on('dragend', () => {
    const { lat, lng } = homeMarker.getLatLng();
    if (!isInSaaremaa(lat, lng)) {
      homeMarker.setLatLng([startLat, startLng]);
      return;
    }
    emit('update:modelLat', lat);
    emit('update:modelLng', lng);
    reverseGeocode(lat, lng);
  });

  // Click on map to move marker
  map.on('click', (e: any) => {
    const { lat, lng } = e.latlng;
    if (!isInSaaremaa(lat, lng)) return;
    placeMarker(lat, lng);
  });
});

onUnmounted(() => {
  if (searchTimer) clearTimeout(searchTimer);
  if (map) { map.remove(); map = null; }
});
</script>