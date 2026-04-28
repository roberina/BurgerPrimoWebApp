<template>
  <!-- ── Horizontal card ── -->
  <div
    v-if="horizontal"
    class="group relative rounded-xl overflow-hidden transition-all duration-200 flex items-stretch min-h-[110px] cursor-pointer
           bg-[#121212] hover:bg-[#161616] border border-[#1a1a1a] hover:border-[#D2691E]/30"
    :class="highlighted ? 'ring-2 ring-[#D2691E]' : ''"
  >
    <div class="flex-1 p-4 flex flex-col justify-between min-w-0">
      <div>
        <div class="flex items-center gap-2 mb-1.5">
          <span v-if="item.is_featured" class="text-xs bg-yellow-500/15 text-yellow-400 px-2 py-0.5 rounded-full font-semibold">{{ t('menu.item.popular') }}</span>
          <span v-if="item.discount_percentage" class="text-xs bg-[#D2691E]/15 text-[#D2691E] px-2 py-0.5 rounded-full font-semibold">-{{ item.discount_percentage }}%</span>
        </div>
        <h3 class="font-bold text-sm leading-snug text-white">{{ item.name }}</h3>
        <p class="text-xs mt-1 line-clamp-2 leading-relaxed text-gray-500">{{ item.description }}</p>
      </div>
      <div class="flex items-center gap-2 mt-3">
        <span class="font-bold text-sm text-white">€{{ Number(item.price).toFixed(2) }}</span>
        <span v-if="item.original_price && Number(item.original_price) > Number(item.price)" class="text-xs line-through text-gray-600">€{{ Number(item.original_price).toFixed(2) }}</span>
      </div>
    </div>
    <div class="relative flex-shrink-0 w-28">
      <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover" />
      <div v-else class="w-full h-full flex items-center justify-center text-3xl bg-[#0d0d0d]">🍔</div>
      <button @click.stop="toggleFavorite" class="absolute top-2 left-2 w-7 h-7 rounded-lg flex items-center justify-center transition-all z-10 cursor-pointer bg-black/50 hover:bg-black/70">
        <svg xmlns="http://www.w3.org/2000/svg" :class="['h-4 w-4', item.is_favorited ? 'fill-red-500 text-red-500' : 'fill-none text-white']" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
      </button>
      <button @click.stop="openAddonModal" class="absolute bottom-2 right-2 w-8 h-8 bg-[#D2691E] hover:bg-[#E07A2E] text-white rounded-lg flex items-center justify-center shadow-lg transition-all duration-200 opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
      </button>
    </div>
    <AddonModal v-if="showAddonModal" :item="item" :addons="addons" :loading="addonsLoading" @close="closeAddonModal" @added="onAdded" />
  </div>

  <!-- ── Vertical card ── -->
  <div v-else class="rounded-2xl overflow-hidden hover:ring-2 hover:ring-[#D2691E] transition-all duration-300 group cursor-pointer bg-[#121212]">
    <div class="relative aspect-[4/3] overflow-hidden bg-[#0B0B0B]">
      <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
      <div v-else class="w-full h-full flex items-center justify-center text-6xl">🍔</div>
      <div class="absolute top-3 left-3 flex gap-2">
        <span v-if="item.is_featured" class="bg-yellow-500 text-black px-3 py-1 rounded-full text-xs font-bold">{{ t('menu.item.popular') }}</span>
        <span v-if="item.discount_percentage" class="bg-[#D2691E] text-white px-3 py-1 rounded-full text-xs font-bold">-{{ item.discount_percentage }}%</span>
      </div>
      <button @click.stop="toggleFavorite" class="absolute top-3 right-3 w-10 h-10 bg-black/50 hover:bg-black/70 rounded-lg flex items-center justify-center transition-all duration-200 z-10 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" :class="['h-6 w-6 transition-all', item.is_favorited ? 'fill-red-500 text-red-500' : 'fill-none text-white']" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
      </button>
      <button @click="openAddonModal" class="absolute bottom-3 right-3 w-12 h-12 bg-[#D2691E] hover:bg-[#E07A2E] text-white rounded-xl flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-[#D2691E]/50 hover:scale-110 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
      </button>
    </div>
    <div class="p-4">
      <h3 class="text-lg font-bold mb-2 text-white">{{ item.name }}</h3>
      <p class="text-sm mb-4 line-clamp-2 min-h-[40px] text-gray-400">{{ item.description }}</p>
      <div class="flex items-center gap-2">
        <span class="text-2xl font-bold text-[#D2691E]">€{{ Number(item.price).toFixed(2) }}</span>
        <span v-if="item.original_price && Number(item.original_price) > Number(item.price)" class="text-sm line-through text-gray-500">€{{ Number(item.original_price).toFixed(2) }}</span>
      </div>
    </div>
    <AddonModal v-if="showAddonModal" :item="item" :addons="addons" :loading="addonsLoading" @close="closeAddonModal" @added="onAdded" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import AddonModal from '@/components/Menu/AddonModal.vue';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

interface MenuItemData {
  id: number; name: string; description: string; price: number;
  original_price: number | null; image_url: string | null;
  is_featured: boolean; discount_percentage: number | null;
  size: string | null; has_discount: boolean; is_favorited?: boolean;
}

const props = defineProps<{ item: MenuItemData; horizontal?: boolean; highlighted?: boolean }>();
const { success, error } = useToast();

// Default fallback addons — used when /api/addons route doesn't exist yet
const FALLBACK_ADDONS = {
  size: [
    { id: 1, name: 'Väike',   price: 0,    slug: 'small'  },
    { id: 2, name: 'Keskmine', price: 1.50, slug: 'medium' },
    { id: 3, name: 'Suur',    price: 3.00, slug: 'large'  },
  ],
  drink: [
    { id: 1, name: 'Coca-Cola 0.5L',      price: 2.50, slug: 'coca-cola'      },
    { id: 2, name: 'Coca-Cola Zero 0.5L', price: 2.50, slug: 'coca-cola-zero' },
    { id: 3, name: 'Fanta 0.5L',          price: 2.50, slug: 'fanta'          },
    { id: 4, name: 'Sprite 0.5L',         price: 2.50, slug: 'sprite'         },
    { id: 5, name: 'Vesi 0.5L',           price: 1.50, slug: 'vesi'           },
  ],
  sauce: [
    { id: 1, name: 'Ketchup',                 price: 0,    slug: 'ketchup'     },
    { id: 2, name: 'Majonees',                price: 0,    slug: 'majonees'    },
    { id: 3, name: 'BBQ kaste',               price: 0.50, slug: 'bbq'         },
    { id: 4, name: 'Sinihallitusjuustu kaste', price: 0.50, slug: 'blue-cheese' },
    { id: 5, name: 'Küüslaugukaste',          price: 0.50, slug: 'garlic'      },
    { id: 6, name: 'Chili kaste',             price: 0.50, slug: 'chili'       },
  ],
  fries: [
    { id: 1, name: 'Ei soovi',        price: 0,    slug: 'none'  },
    { id: 2, name: 'Väike friikartul', price: 2.00, slug: 'small' },
    { id: 3, name: 'Suur friikartul',  price: 3.50, slug: 'large' },
  ],
};

const toggleFavorite = () => {
  router.post(`/menu/${props.item.id}/favorite`, {}, { preserveScroll: true });
};

const showAddonModal = ref(false);
const addons = ref<any>(null);
const addonsLoading = ref(false);

const openAddonModal = async () => {
  showAddonModal.value = true;
  if (!addons.value) {
    addonsLoading.value = true;
    try {
      const res = await fetch('/api/addons');
      if (!res.ok) throw new Error('not found');
      addons.value = await res.json();
    } catch {
      // Route not set up yet — use hardcoded fallback so modal still works
      addons.value = FALLBACK_ADDONS;
    } finally {
      addonsLoading.value = false;
    }
  }
};

const closeAddonModal = () => { showAddonModal.value = false; };
const onAdded = () => { closeAddonModal(); success(`${props.item.name} ${t('toast.cart.added')}`); };
</script>

<style scoped>
.line-clamp-2 { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
</style>