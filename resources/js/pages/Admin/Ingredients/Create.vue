<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <a href="/admin/ingredients" class="p-2 hover:bg-gray-800 rounded-lg transition">
          <ArrowLeft :size="20" />
        </a>
        <div>
          <h2 class="text-xl lg:text-2xl font-bold">Lisa Koostisosa</h2>
          <p class="text-sm text-gray-400 mt-1">Loo uus burgeri koostisosa</p>
        </div>
      </div>
    </template>

    <div class="max-w-2xl">
      <form @submit.prevent="submit" class="bg-[#111111] border border-gray-800 rounded-xl p-6 space-y-6">
        <div>
          <label for="name" class="block text-sm font-semibold text-white mb-2">Nimi</label>
          <input id="name" v-model="form.name" type="text" required class="w-full bg-[#0a0a0a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-600 transition" placeholder="nt. Juustuburgeri kotlet" />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
          <label for="category" class="block text-sm font-semibold text-white mb-2">Kategooria</label>
          <select id="category" v-model="form.category" required class="w-full bg-[#0a0a0a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-600 transition">
            <option v-for="cat in categories" :key="cat.value" :value="cat.value">{{ cat.label }}</option>
          </select>
          <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</p>
        </div>

        <div>
          <label for="price" class="block text-sm font-semibold text-white mb-2">Hind</label>
          <input id="price" v-model.number="form.price" type="number" step="0.01" min="0" max="99.99" required class="w-full bg-[#0a0a0a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-600 transition" placeholder="0.00" />
          <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
        </div>

        <div class="flex items-center gap-3">
          <input id="is_available" v-model="form.is_available" type="checkbox" class="w-5 h-5 rounded bg-[#0a0a0a] border-gray-700 text-orange-600 focus:ring-orange-600" />
          <label for="is_available" class="text-sm font-semibold text-white">Saadaval klientidele</label>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" :disabled="form.processing" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold transition disabled:opacity-50">
            {{ form.processing ? 'Salvestan...' : 'Salvesta' }}
          </button>
          <a href="/admin/ingredients" class="px-6 py-3 bg-[#0a0a0a] hover:bg-gray-800 text-white rounded-lg font-semibold transition">Tühista</a>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
  name: '',
  category: 'patties',
  price: 0,
  is_available: true,
});

const submit = () => {
  form.post('/admin/ingredients');
};

const categories = [
  { value: 'buns', label: '🍞 Saiakesed' },
  { value: 'patties', label: '🍖 Lihakotletid' },
  { value: 'vegetables', label: '🥬 Köögiviljad' },
  { value: 'sauces', label: '🧴 Kastmed' },
  { value: 'extras', label: '🧀 Lisandid' },
];
</script>