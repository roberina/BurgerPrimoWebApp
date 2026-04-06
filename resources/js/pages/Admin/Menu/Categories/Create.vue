<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <h2 class="text-xl lg:text-2xl font-bold">{{ isEdit ? 'Muuda kategooriat' : 'Lisa kategooria' }}</h2>
        <Link href="/admin/menu/categories" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1">
          ← Tagasi
        </Link>
      </div>
    </template>

    <div class="max-w-4xl">
      <form @submit.prevent="submit" class="bg-[#121212] rounded-lg border border-[#1e1e1e] p-8">
        <!-- Name -->
        <div class="mb-6">
          <label class="block text-sm font-semibold mb-2">Nimi *</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg px-4 py-3 text-white focus:border-[#D2691E] focus:ring-2 focus:ring-[#D2691E]/20 transition"
            placeholder="nt. BURGERID"
          />
          <p v-if="errors.name" class="text-red-400 text-sm mt-1">{{ errors.name }}</p>
        </div>

        <!-- Slug -->
        <div class="mb-6">
          <label class="block text-sm font-semibold mb-2">
            Slug <span class="text-gray-500">(jäta tühjaks automaatseks genereerimiseks)</span>
          </label>
          <input
            v-model="form.slug"
            type="text"
            class="w-full bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg px-4 py-3 text-white focus:border-[#D2691E] focus:ring-2 focus:ring-[#D2691E]/20 transition"
            placeholder="nt. burgerid"
          />
          <p class="text-gray-500 text-sm mt-1">URL-i sõbralik nimi (kasutab automaatselt nime)</p>
          <p v-if="errors.slug" class="text-red-400 text-sm mt-1">{{ errors.slug }}</p>
        </div>

        <!-- Description -->
        <div class="mb-6">
          <label class="block text-sm font-semibold mb-2">Kirjeldus</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg px-4 py-3 text-white focus:border-[#D2691E] focus:ring-2 focus:ring-[#D2691E]/20 transition"
            placeholder="Kategooria lühikirjeldus..."
          ></textarea>
          <p v-if="errors.description" class="text-red-400 text-sm mt-1">{{ errors.description }}</p>
        </div>

        <!-- Sort Order -->
        <div class="mb-6">
          <label class="block text-sm font-semibold mb-2">Järjekord</label>
          <input
            v-model.number="form.sort_order"
            type="number"
            min="0"
            class="w-full bg-[#0B0B0B] border border-[#2a2a2a] rounded-lg px-4 py-3 text-white focus:border-[#D2691E] focus:ring-2 focus:ring-[#D2691E]/20 transition"
            placeholder="0"
          />
          <p class="text-gray-500 text-sm mt-1">Väiksem number = kõrgem positsioon menüüs</p>
          <p v-if="errors.sort_order" class="text-red-400 text-sm mt-1">{{ errors.sort_order }}</p>
        </div>

        <!-- Is Active -->
        <div class="mb-8">
          <label class="flex items-center cursor-pointer">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="w-5 h-5 rounded border-[#333] bg-[#1a1a1a] text-[#D2691E] focus:ring-[#D2691E]/30 focus:ring-offset-gray-900 cursor-pointer"
            />
            <span class="ml-3 font-semibold">Kategooria on aktiivne</span>
          </label>
          <p class="text-gray-500 text-sm mt-1 ml-8">Mitteaktiivseid kategooriaid ei näidata klientidele</p>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
          <button
            type="submit"
            :disabled="processing"
            class="flex-1 bg-[#D2691E] hover:bg-[#D2691E] disabled:opacity-40 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-semibold transition"
          >
            {{ processing ? 'Salvestamine...' : (isEdit ? 'Salvesta muudatused' : 'Lisa kategooria') }}
          </button>
          <Link
            href="/admin/menu/categories"
            class="flex-1 bg-[#121212] border border-[#1e1e1e] hover:bg-[#1a1a1a] hover:border-[#D2691E]/30 text-gray-300 hover:text-white px-6 py-3 rounded-lg font-semibold transition text-center"
          >
            Tühista
          </Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">import AdminLayout from '@/layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';


interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  sort_order: number;
  is_active: boolean;
}
interface Props {
  category?: Category;
  errors?: Record<string, string>;
}

const props = withDefaults(defineProps<Props>(), {
  errors: () => ({}),
});

const isEdit = computed(() => !!props.category);

const form = useForm({
  name: props.category?.name || '',
  slug: props.category?.slug || '',
  description: props.category?.description || '',
  sort_order: props.category?.sort_order || 0,
  is_active: props.category?.is_active ?? true,
});

const processing = computed(() => form.processing);

const submit = () => {
  if (isEdit.value) {
    form.put(`/admin/menu/categories/${props.category!.id}` as any, {
      preserveScroll: true,
    });
  } else {
    form.post('/admin/menu/categories' as any, {
      preserveScroll: true,
    });
  }
};
</script>