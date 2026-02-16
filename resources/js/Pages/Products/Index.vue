<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  products: Array,
});

const page = usePage();

// Detectar si es admin
const isAdmin = page.props.auth.user.role === 'admin';

function destroyProduct(id) {
  if (!confirm('¿Eliminar producto?')) return;
  router.delete(route('products.destroy', id));
}
</script>

<template>
  <Head title="Productos" />
  <AuthenticatedLayout>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Productos</h1>

      <!-- Crear permitido para admin y agent -->
      <Link
        class="px-3 py-2 bg-black text-white rounded"
        :href="route('products.create')"
      >
        Nuevo
      </Link>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-right">Bs</th>
            <th class="p-2 text-right">USD</th>
            <th class="p-2 text-right">Stock</th>
            <th class="p-2 text-center">Activo</th>
            <th class="p-2 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in products" :key="p.id" class="border-t">
            <td class="p-2">{{ p.name }}</td>
            <td class="p-2 text-right">{{ Number(p.price_bs).toFixed(2) }}</td>
            <td class="p-2 text-right">{{ Number(p.price_usd).toFixed(2) }}</td>
            <td class="p-2 text-right">{{ p.stock }}</td>
            <td class="p-2 text-center">{{ p.active ? 'Sí' : 'No' }}</td>

            <td class="p-2 text-right space-x-2">
              <!-- SOLO ADMIN -->
              <Link
                v-if="isAdmin"
                class="underline"
                :href="route('products.edit', p.id)"
              >
                Editar
              </Link>

              <button
                v-if="isAdmin"
                class="underline text-red-600"
                @click="destroyProduct(p.id)"
              >
                Eliminar
              </button>
            </td>
          </tr>

          <tr v-if="products.length === 0">
            <td class="p-4 text-center text-gray-600" colspan="6">
              No hay productos.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
