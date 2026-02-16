<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  price_bs: 0,
  price_usd: 0,
  stock: 0,
  active: true,
});

function submit() {
  form.post(route('products.store'));
}
</script>

<template>
  <Head title="Nuevo Producto" />
  <AuthenticatedLayout>
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold">Nuevo Producto</h1>
      <Link class="underline" :href="route('products.index')">Volver</Link>
    </div>

    <form @submit.prevent="submit" class="bg-white p-4 rounded shadow space-y-3 max-w-xl">
      <div>
        <label class="block text-sm font-medium">Nombre</label>
        <input v-model="form.name" class="w-full border rounded p-2" />
        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium">Precio Bs</label>
          <input type="number" step="0.01" v-model="form.price_bs" class="w-full border rounded p-2" />
          <div v-if="form.errors.price_bs" class="text-red-600 text-sm mt-1">{{ form.errors.price_bs }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Precio USD</label>
          <input type="number" step="0.01" v-model="form.price_usd" class="w-full border rounded p-2" />
          <div v-if="form.errors.price_usd" class="text-red-600 text-sm mt-1">{{ form.errors.price_usd }}</div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium">Stock</label>
        <input type="number" min="0" v-model="form.stock" class="w-full border rounded p-2" />
        <div v-if="form.errors.stock" class="text-red-600 text-sm mt-1">{{ form.errors.stock }}</div>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" v-model="form.active" />
        <span class="text-sm">Activo</span>
      </div>

      <button class="px-3 py-2 bg-black text-white rounded disabled:opacity-50" :disabled="form.processing">
        Guardar
      </button>
    </form>
  </AuthenticatedLayout>
</template>
