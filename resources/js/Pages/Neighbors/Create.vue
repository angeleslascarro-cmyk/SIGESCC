<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
  full_name: '',
  cedula: '',
  phone: '',
  address: '',
  credit_limit_bs: 0,
  credit_limit_usd: 0,
});

function submit() {
  form.post(route('neighbors.store'));
}
</script>

<template>
  <Head title="Nuevo Vecino" />
  <AuthenticatedLayout>
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold">Nuevo Vecino</h1>
      <Link class="underline" :href="route('neighbors.index')">Volver</Link>
    </div>

    <form @submit.prevent="submit" class="bg-white p-4 rounded shadow space-y-3 max-w-xl">
      <div>
        <label class="block text-sm">Nombre completo</label>
        <input v-model="form.full_name" class="w-full border rounded p-2" />
        <div v-if="form.errors.full_name" class="text-red-600 text-sm">{{ form.errors.full_name }}</div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm">Cédula</label>
          <input v-model="form.cedula" class="w-full border rounded p-2" />
          <div v-if="form.errors.cedula" class="text-red-600 text-sm">{{ form.errors.cedula }}</div>
        </div>
        <div>
          <label class="block text-sm">Teléfono</label>
          <input v-model="form.phone" class="w-full border rounded p-2" />
          <div v-if="form.errors.phone" class="text-red-600 text-sm">{{ form.errors.phone }}</div>
        </div>
      </div>

      <div>
        <label class="block text-sm">Dirección</label>
        <input v-model="form.address" class="w-full border rounded p-2" />
        <div v-if="form.errors.address" class="text-red-600 text-sm">{{ form.errors.address }}</div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm">Límite de crédito (BS)</label>
          <input type="number" step="0.01" min="0" v-model.number="form.credit_limit_bs" class="w-full border rounded p-2" />
          <div v-if="form.errors.credit_limit_bs" class="text-red-600 text-sm">{{ form.errors.credit_limit_bs }}</div>
        </div>

        <div>
          <label class="block text-sm">Límite de crédito (USD)</label>
          <input type="number" step="0.01" min="0" v-model.number="form.credit_limit_usd" class="w-full border rounded p-2" />
          <div v-if="form.errors.credit_limit_usd" class="text-red-600 text-sm">{{ form.errors.credit_limit_usd }}</div>
        </div>
      </div>

      <button type="submit" class="px-3 py-2 bg-black text-white rounded disabled:opacity-50" :disabled="form.processing">
        Guardar
      </button>
    </form>
  </AuthenticatedLayout>
</template>
