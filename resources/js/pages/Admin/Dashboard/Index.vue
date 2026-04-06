<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

interface RecentItem {
  id: number;
  name: string;
  price: number;
  is_active: boolean;
  is_featured: boolean;
  is_available: boolean;
  category?: { name: string };
  image_url?: string | null;
}

interface DailySale  { day: number; date: string; total: number }
interface MonthlyOrder { month: number; year: number; count: number }
interface PopularProduct { name: string; orders: number; sold: number; price: number }

interface Props {
  stats: {
    totalItems: number;
    totalCategories: number;
    featuredItems: number;
    unavailableItems: number;
    totalRevenue: number;
    totalOrders: number;
    growthPercentage: number;
    pendingOrders: number;
  };
  recentItems: RecentItem[];
  dailySales: DailySale[];
  monthlyOrders: MonthlyOrder[];
  popularProducts: PopularProduct[];
}

const props = defineProps<Props>();
const fmt = (n: number) => `€${Number(n).toFixed(2)}`;

const monthNames = ['Jan','Veb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dets'];
const dayNames   = ['E','T','K','N','R','L','P'];

// Chart bar heights
const maxDaily   = Math.max(...(props.dailySales?.map(d => d.total) ?? [1]), 1);
const maxMonthly = Math.max(...(props.monthlyOrders?.map(m => m.count) ?? [1]), 1);
</script>

<template>
  <AdminLayout>
    <div class="p-4 lg:p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Armatuurlaud</h1>
        <Link
          v-if="stats.pendingOrders > 0"
          href="/admin/orders"
          class="flex items-center gap-2 px-4 py-2 bg-yellow-500/15 border border-yellow-500/30 text-yellow-400 rounded-xl text-sm font-semibold hover:bg-yellow-500/25 transition-all"
        >
          <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
          {{ stats.pendingOrders }} ootel tellimust
        </Link>
      </div>

      <!-- Revenue + menu stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl p-5 col-span-2 lg:col-span-1">
          <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Kuu tulu</p>
          <p class="text-3xl font-bold text-[#D2691E]">{{ fmt(stats.totalRevenue) }}</p>
          <p class="text-xs mt-1" :class="stats.growthPercentage >= 0 ? 'text-green-400' : 'text-red-400'">
            {{ stats.growthPercentage >= 0 ? '▲' : '▼' }} {{ Math.abs(stats.growthPercentage) }}% eelmisest kuust
          </p>
        </div>
        <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl p-5">
          <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Kuu tellimused</p>
          <p class="text-3xl font-bold text-white">{{ stats.totalOrders }}</p>
        </div>
        <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl p-5">
          <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Menüü tooteid</p>
          <p class="text-3xl font-bold text-white">{{ stats.totalItems }}</p>
          <p class="text-xs text-gray-600 mt-1">{{ stats.totalCategories }} kategooriat</p>
        </div>
        <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl p-5">
          <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Populaarsed</p>
          <p class="text-3xl font-bold text-yellow-400">{{ stats.featuredItems }}</p>
          <p class="text-xs text-red-400 mt-1">{{ stats.unavailableItems }} pole saadaval</p>
        </div>
      </div>

      <!-- Charts row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <!-- Daily sales bar chart -->
        <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl p-5">
          <h2 class="text-sm font-bold text-white mb-4 uppercase tracking-wide">Viimased 7 päeva müük</h2>
          <div class="flex items-end gap-2 h-32">
            <div
              v-for="day in dailySales"
              :key="day.date"
              class="flex-1 flex flex-col items-center gap-1"
            >
              <span class="text-[10px] text-gray-600">{{ day.total > 0 ? fmt(day.total) : '' }}</span>
              <div
                class="w-full rounded-t-md bg-gradient-to-t from-[#B8571A] to-[#D2691E] transition-all min-h-[4px]"
                :style="{ height: `${Math.max((day.total / maxDaily) * 96, 4)}px` }"
              ></div>
              <span class="text-[10px] text-gray-500">{{ dayNames[(day.day - 1) % 7] }}</span>
            </div>
          </div>
        </div>

        <!-- Monthly orders line-ish chart -->
        <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl p-5">
          <h2 class="text-sm font-bold text-white mb-4 uppercase tracking-wide">Viimased 7 kuud tellimused</h2>
          <div class="flex items-end gap-2 h-32">
            <div
              v-for="m in monthlyOrders"
              :key="`${m.year}-${m.month}`"
              class="flex-1 flex flex-col items-center gap-1"
            >
              <span class="text-[10px] text-gray-600">{{ m.count > 0 ? m.count : '' }}</span>
              <div
                class="w-full rounded-t-md bg-gradient-to-t from-blue-900 to-blue-500 transition-all min-h-[4px]"
                :style="{ height: `${Math.max((m.count / maxMonthly) * 96, 4)}px` }"
              ></div>
              <span class="text-[10px] text-gray-500">{{ monthNames[m.month - 1] }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <Link href="/admin/menu/items/create" class="bg-[#D2691E] hover:bg-[#E07A2E] text-white rounded-xl p-4 flex items-center gap-3 transition-colors">
          <span class="text-2xl">➕</span>
          <div>
            <p class="font-bold">Lisa menüü toode</p>
            <p class="text-xs text-orange-200">Loo uus toode</p>
          </div>
        </Link>
        <Link href="/admin/orders" class="bg-[#121212] border border-[#1a1a1a] hover:border-[#D2691E]/40 text-white rounded-xl p-4 flex items-center gap-3 transition-colors">
          <span class="text-2xl">📋</span>
          <div>
            <p class="font-bold">Tellimused</p>
            <p class="text-xs text-gray-400">Halda tellimusi</p>
          </div>
        </Link>
        <Link href="/admin/addons" class="bg-[#121212] border border-[#1a1a1a] hover:border-[#D2691E]/40 text-white rounded-xl p-4 flex items-center gap-3 transition-colors">
          <span class="text-2xl">🥤</span>
          <div>
            <p class="font-bold">Lisandid</p>
            <p class="text-xs text-gray-400">Joogid, kastmed, suurused</p>
          </div>
        </Link>
      </div>

      <!-- Popular products -->
      <div v-if="popularProducts?.length > 0" class="bg-[#121212] border border-[#1a1a1a] rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#1a1a1a]">
          <h2 class="text-sm font-bold text-white uppercase tracking-wide">Populaarsemad tooted</h2>
        </div>
        <div class="divide-y divide-[#1a1a1a]">
          <div v-for="(product, i) in popularProducts" :key="product.name" class="px-6 py-3 flex items-center gap-4">
            <span class="text-gray-700 font-mono text-sm w-5">{{ i + 1 }}</span>
            <div class="flex-1 min-w-0">
              <p class="text-white text-sm font-medium truncate">{{ product.name }}</p>
              <p class="text-gray-600 text-xs">{{ product.sold }} müüdud · {{ product.orders }} tellimust</p>
            </div>
            <span class="text-[#D2691E] font-bold text-sm">{{ fmt(product.price) }}</span>
          </div>
        </div>
      </div>

      <!-- Recent items -->
      <div class="bg-[#121212] border border-[#1a1a1a] rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#1a1a1a] flex items-center justify-between">
          <h2 class="text-sm font-bold text-white uppercase tracking-wide">Viimati lisatud tooted</h2>
          <Link href="/admin/menu/items" class="text-sm text-[#D2691E] hover:underline">Vaata kõiki</Link>
        </div>
        <div class="divide-y divide-[#1a1a1a]">
          <div v-for="item in recentItems" :key="item.id" class="px-6 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-[#0B0B0B] flex items-center justify-center flex-shrink-0 overflow-hidden">
              <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover" />
              <span v-else class="text-xl">🍔</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-white font-medium truncate text-sm">{{ item.name }}</p>
              <p class="text-gray-500 text-xs">{{ item.category?.name ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span :class="['text-xs px-2 py-0.5 rounded-full font-semibold', item.is_available ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400']">
                {{ item.is_available ? 'Saadaval' : 'Pole' }}
              </span>
              <span class="text-white font-bold text-sm">{{ fmt(item.price) }}</span>
              <Link :href="`/admin/menu/items/${item.id}/edit`" class="text-gray-500 hover:text-[#D2691E] transition-colors text-sm">Muuda</Link>
            </div>
          </div>
          <div v-if="!recentItems?.length" class="px-6 py-10 text-center text-gray-500 text-sm">
            Ühtegi toodet pole veel lisatud.
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>