<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ product: Object });

const form = useForm({
  name: props.product.name,
  price_bs: props.product.price_bs,
  price_usd: props.product.price_usd,
  stock: props.product.stock,
  active: !!props.product.active,
});

function submit() {
  form.put(route('products.update', props.product.id));
}
</script>

<template>
  <Head title="Editar Producto" />
  <AuthenticatedLayout>
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold">Editar Producto</h1>
      <Link class="underline" :href="route('products.index')">Volver</Link>
    </div>

    <form @submit.prevent="submit" class="bg-white p-4 rounded shadow space-y-3 max-w-xl">
      <div>
        <label class="block text-sm">Nombre</label>
        <input v-model="form.name" class="w-full border rounded p-2" />
        <div v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm">Precio Bs</label>
          <input type="number" step="0.01" v-model="form.price_bs" class="w-full border rounded p-2" />
        </div>
        <div>
          <label class="block text-sm">Precio USD</label>
          <input type="number" step="0.01" v-model="form.price_usd" class="w-full border rounded p-2" />
        </div>
      </div>

      <div>
        <label class="block text-sm">Stock</label>
        <input type="number" v-model="form.stock" class="w-full border rounded p-2" />
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" v-model="form.active" />
        <span class="text-sm">Activo</span>
      </div>

      <button class="px-3 py-2 bg-black text-white rounded" :disabled="form.processing">
        Guardar cambios
      </button>
    </form>
  </AuthenticatedLayout>
</template>
