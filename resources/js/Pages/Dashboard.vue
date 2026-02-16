<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
  year: Number,
  kpis: Object,
  topDebtors: Array,
});

const page = usePage();
const name = page.props.auth?.user?.name ?? 'Usuario';

function money(v) {
  return Number(v ?? 0).toFixed(2);
}
</script>

<template>
  <Head title="Inicio" />
  <AuthenticatedLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-start justify-between gap-3 flex-wrap">
        <div>
          <h1 class="text-2xl font-semibold">SIGESCC</h1>
          <p class="text-gray-600 mt-1">
            Bienvenido/a, <span class="font-medium">{{ name }}</span>. Resumen {{ year }}.
          </p>
        </div>

        <div class="flex gap-2">
          <Link class="px-3 py-2 bg-black text-white rounded" :href="route('orders.create')">
            Nueva venta
          </Link>
          <Link class="px-3 py-2 border rounded" :href="route('reports.index')">
            Ver reportes
          </Link>
        </div>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl shadow p-4 border">
        <div class="text-xs text-gray-500">Ventas (BS)</div>
        <div class="text-2xl font-semibold mt-1">{{ money(kpis.sales_bs) }}</div>
        <div class="text-xs text-gray-500 mt-2">Acumulado {{ year }}</div>
      </div>

      <div class="bg-white rounded-xl shadow p-4 border">
        <div class="text-xs text-gray-500">Ventas (USD)</div>
        <div class="text-2xl font-semibold mt-1">{{ money(kpis.sales_usd) }}</div>
        <div class="text-xs text-gray-500 mt-2">Acumulado {{ year }}</div>
      </div>

      <div class="bg-white rounded-xl shadow p-4 border">
        <div class="text-xs text-gray-500">Pendiente (BS)</div>
        <div class="text-2xl font-semibold mt-1 text-red-700">{{ money(kpis.pending_bs) }}</div>
        <div class="text-xs text-gray-500 mt-2">Órdenes PENDING</div>
      </div>

      <div class="bg-white rounded-xl shadow p-4 border">
        <div class="text-xs text-gray-500">Pendiente (USD)</div>
        <div class="text-2xl font-semibold mt-1 text-red-700">{{ money(kpis.pending_usd) }}</div>
        <div class="text-xs text-gray-500 mt-2">Órdenes PENDING</div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-4">
      <!-- Accesos -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow border p-4">
        <h2 class="font-semibold mb-3">Accesos rápidos</h2>

        <div class="grid sm:grid-cols-2 gap-3">
          <Link class="p-4 border rounded-xl hover:bg-gray-50 transition" :href="route('products.index')">
            <div class="font-semibold">Productos</div>
            <div class="text-sm text-gray-600">Inventario y precios</div>
          </Link>

          <Link class="p-4 border rounded-xl hover:bg-gray-50 transition" :href="route('neighbors.index')">
            <div class="font-semibold">Vecinos</div>
            <div class="text-sm text-gray-600">Clientes y límites</div>
          </Link>

          <Link class="p-4 border rounded-xl hover:bg-gray-50 transition" :href="route('orders.index')">
            <div class="font-semibold">Ventas</div>
            <div class="text-sm text-gray-600">Pagadas y pendientes</div>
          </Link>

          <Link class="p-4 border rounded-xl hover:bg-gray-50 transition" :href="route('reports.index')">
            <div class="font-semibold">Reportes</div>
            <div class="text-sm text-gray-600">Mensual + deudores</div>
          </Link>
        </div>
      </div>

      <!-- Top deudores -->
      <div class="bg-white rounded-xl shadow border p-4">
        <h2 class="font-semibold mb-3">Top deudores</h2>

        <div v-if="topDebtors.length === 0" class="text-sm text-gray-600">
          No hay deudores.
        </div>

        <div v-else class="space-y-2">
          <div v-for="d in topDebtors" :key="d.id" class="border rounded-xl p-3">
            <div class="font-medium leading-tight">{{ d.full_name }}</div>
            <div class="text-xs text-gray-600">{{ d.cedula }}</div>

            <div class="mt-2 text-sm flex justify-between">
              <span class="text-gray-600">BS</span>
              <span class="text-red-700 font-semibold">{{ money(d.pending_bs) }}</span>
            </div>

            <div class="text-sm flex justify-between">
              <span class="text-gray-600">USD</span>
              <span class="text-red-700 font-semibold">{{ money(d.pending_usd) }}</span>
            </div>
          </div>
        </div>

        <Link class="inline-block mt-3 text-sm underline" :href="route('reports.index')">
          Ver todos en reportes
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
