<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ orders: Array });

function fmtDate(d) {
  return new Date(d).toLocaleString();
}
</script>

<template>
  <Head title="Ventas" />
  <AuthenticatedLayout>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Ventas</h1>
      <Link class="px-3 py-2 bg-black text-white rounded" :href="route('orders.create')">Nueva venta</Link>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-2 text-left">#</th>
            <th class="p-2 text-left">Vecino</th>
            <th class="p-2 text-left">Usuario</th>
            <th class="p-2 text-left">Estado</th>
            <th class="p-2 text-left">Moneda</th>
            <th class="p-2 text-right">Total</th>
            <th class="p-2 text-left">Fecha</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="o in orders" :key="o.id" class="border-t">
            <td class="p-2">{{ o.id }}</td>
            <td class="p-2">{{ o.neighbor?.full_name }}</td>
            <td class="p-2">{{ o.user?.name }}</td>
            <td class="p-2">
              <span :class="o.status==='PENDING' ? 'text-orange-700 font-semibold' : 'text-green-700 font-semibold'">
                {{ o.status === 'PENDING' ? 'Pendiente' : 'Pagado' }}
              </span>
            </td>
            <td class="p-2">{{ o.currency }}</td>
            <td class="p-2 text-right">{{ Number(o.total).toFixed(2) }}</td>
            <td class="p-2">{{ fmtDate(o.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
