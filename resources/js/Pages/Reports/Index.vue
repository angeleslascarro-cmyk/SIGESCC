<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  year: Number,
  monthly: Array,
  debtors: Array,
});

function monthName(m) {
  const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
  return months[(Number(m) || 1) - 1] || m;
}

function money(v) {
  return Number(v ?? 0).toFixed(2);
}
</script>

<template>
  <Head title="Reportes" />
  <AuthenticatedLayout>
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
      <h1 class="text-xl font-semibold">Reportes ({{ year }})</h1>

      <div class="flex gap-2 flex-wrap">
        <a
          class="px-3 py-2 bg-black text-white rounded"
          :href="route('reports.debtors.csv')"
        >
          Exportar deudores CSV
        </a>
        <a
          class="px-3 py-2 bg-blue-700 text-white rounded"
          :href="route('reports.monthly.csv', { year })"
        >
          Exportar ventas mensuales CSV
        </a>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
      <!-- Ventas mensuales -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Ventas mensuales</h2>
        <p class="text-sm text-gray-600 mb-3">
          Totales agrupados por mes y moneda.
        </p>

        <div v-if="monthly.length === 0" class="text-sm text-gray-600">
          No hay ventas registradas para este año.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="p-2 text-left">Mes</th>
                <th class="p-2 text-left">Moneda</th>
                <th class="p-2 text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in monthly" :key="String(r.month) + r.currency" class="border-t">
                <td class="p-2">{{ monthName(r.month) }}</td>
                <td class="p-2">{{ r.currency }}</td>
                <td class="p-2 text-right">{{ money(r.total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Deudores -->
      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Cobranza / Deudores</h2>
        <p class="text-sm text-gray-600 mb-3">
          Vecinos con saldo pendiente (órdenes PENDING).
        </p>

        <div v-if="debtors.length === 0" class="text-sm text-gray-600">
          No hay deudores.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="p-2 text-left">Vecino</th>
                <th class="p-2 text-left">Cédula</th>
                <th class="p-2 text-left">Teléfono</th>

                
                <th class="p-2 text-right">Pendiente BS</th>
                <th class="p-2 text-right">Pendiente USD</th>

                <th class="p-2 text-right">Límite BS</th>
                <th class="p-2 text-right">Límite USD</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="d in debtors" :key="d.id" class="border-t">
                <td class="p-2">{{ d.full_name }}</td>
                <td class="p-2">{{ d.cedula }}</td>
                <td class="p-2">{{ d.phone || '-' }}</td>

                <td class="p-2 text-right text-red-600">{{ money(d.pending_bs) }}</td>
                <td class="p-2 text-right text-red-600">{{ money(d.pending_usd) }}</td>

                <td class="p-2 text-right">{{ money(d.credit_limit_bs) }}</td>
                <td class="p-2 text-right">{{ money(d.credit_limit_usd) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="text-xs text-gray-600 mt-3">
          Consejo: usa “Exportar deudores CSV” para imprimir o trabajar en Excel.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
