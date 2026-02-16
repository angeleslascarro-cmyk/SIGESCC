<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({ neighbors: Array });

const page = usePage();

const isAdmin = page.props.auth.user.role === 'admin';

function destroyNeighbor(id) {
  if (!confirm('¿Eliminar vecino?')) return;
  router.delete(route('neighbors.destroy', id));
}

function money(v) {
  return Number(v ?? 0).toFixed(2);
}

function availableClass(v) {
  const n = Number(v ?? 0);
  return n <= 0 ? 'text-red-700 font-semibold' : 'text-green-600';
}
</script>

<template>
  <Head title="Vecinos" />
  <AuthenticatedLayout>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Vecinos</h1>

      <!-- Crear permitido para admin y agent -->
      <Link class="px-3 py-2 bg-black text-white rounded" :href="route('neighbors.create')">
        Nuevo
      </Link>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Cédula</th>
            <th class="p-2 text-left">Teléfono</th>

            <th class="p-2 text-right">Límite BS</th>
            <th class="p-2 text-right">Pendiente BS</th>
            <th class="p-2 text-right">Disponible BS</th>

            <th class="p-2 text-right">Límite USD</th>
            <th class="p-2 text-right">Pendiente USD</th>
            <th class="p-2 text-right">Disponible USD</th>

            <th class="p-2 text-right">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="n in neighbors"
            :key="n.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="p-2">{{ n.full_name }}</td>
            <td class="p-2">{{ n.cedula }}</td>
            <td class="p-2">{{ n.phone || '-' }}</td>

            <!-- BS -->
            <td class="p-2 text-right font-medium">
              {{ money(n.credit_limit_bs) }}
            </td>
            <td class="p-2 text-right text-red-600">
              {{ money(n.pending_total_bs) }}
            </td>
            <td class="p-2 text-right" :class="availableClass(n.available_bs)">
              {{ money(n.available_bs) }}
            </td>

            <!-- USD -->
            <td class="p-2 text-right font-medium">
              {{ money(n.credit_limit_usd) }}
            </td>
            <td class="p-2 text-right text-red-600">
              {{ money(n.pending_total_usd) }}
            </td>
            <td class="p-2 text-right" :class="availableClass(n.available_usd)">
              {{ money(n.available_usd) }}
            </td>

            <td class="p-2 text-right space-x-2">
                           <Link
                v-if="isAdmin"
                class="underline"
                :href="route('neighbors.edit', n.id)"
              >
                Editar
              </Link>

              <button
                v-if="isAdmin"
                class="underline text-red-600"
                @click="destroyNeighbor(n.id)"
              >
                Eliminar
              </button>
            </td>
          </tr>

          <tr v-if="neighbors.length === 0">
            <td colspan="10" class="p-4 text-center text-gray-500">
              No hay vecinos registrados.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
