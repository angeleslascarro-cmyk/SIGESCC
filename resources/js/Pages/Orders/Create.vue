<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  neighbors: Array,
  products: Array,
});

const form = useForm({
  neighbor_id: '',
  currency: 'BS',
  status: 'PAID',
  items: [],
});

function productById(id) {
  return props.products.find(p => p.id === id);
}

const selectedNeighbor = computed(() =>
  props.neighbors.find(n => n.id === Number(form.neighbor_id)) || null
);

function unitPrice(p) {
  if (!p) return 0;
  return form.currency === 'BS' ? Number(p.price_bs) : Number(p.price_usd);
}

const total = computed(() => {
  const t = form.items.reduce((sum, it) => {
    const p = productById(it.product_id);
    if (!p) return sum;
    return sum + unitPrice(p) * Number(it.qty);
  }, 0);
  return Number(t.toFixed(2));
});

const limitForCurrency = computed(() => {
  if (!selectedNeighbor.value) return 0;
  return form.currency === 'BS'
    ? Number(selectedNeighbor.value.credit_limit_bs ?? 0)
    : Number(selectedNeighbor.value.credit_limit_usd ?? 0);
});

const pendingDebt = computed(() => {
  if (!selectedNeighbor.value) return 0;
  return form.currency === 'BS'
    ? Number(selectedNeighbor.value.pending_total_bs ?? 0)
    : Number(selectedNeighbor.value.pending_total_usd ?? 0);
});

const availableCredit = computed(() => {
  if (!selectedNeighbor.value) return 0;
  return form.currency === 'BS'
    ? Number(selectedNeighbor.value.available_bs ?? 0)
    : Number(selectedNeighbor.value.available_usd ?? 0);
});

const insufficientCredit = computed(() => {
  if (form.status !== 'PENDING') return false;
  if (!selectedNeighbor.value) return false;
  return total.value > availableCredit.value;
});

const insufficientStock = computed(() => {
  return form.items.some(it => {
    const p = productById(it.product_id);
    if (!p) return false;
    return Number(it.qty) > Number(p.stock);
  });
});

const canSubmit = computed(() => {
  if (!form.neighbor_id) return false;
  if (form.items.length === 0) return false;
  if (insufficientStock.value) return false;
  if (insufficientCredit.value) return false;
  return true;
});

function addItem(product) {
  if (!product || product.stock <= 0) return;

  const found = form.items.find(i => i.product_id === product.id);
  if (found) {
    if (Number(found.qty) >= Number(product.stock)) return;
    found.qty++;
  } else {
    form.items.push({ product_id: product.id, qty: 1 });
  }
}

function removeItem(productId) {
  form.items = form.items.filter(i => i.product_id !== productId);
}

function incQty(productId) {
  const it = form.items.find(i => i.product_id === productId);
  if (!it) return;

  const p = productById(productId);
  if (!p) return;

  if (Number(it.qty) >= Number(p.stock)) return;
  it.qty++;
}

function decQty(productId) {
  const it = form.items.find(i => i.product_id === productId);
  if (!it) return;
  it.qty = Math.max(1, Number(it.qty) - 1);
}

function clampQty(productId) {
  const it = form.items.find(i => i.product_id === productId);
  const p = productById(productId);
  if (!it || !p) return;

  const max = Number(p.stock);
  let q = Number(it.qty || 1);

  if (q < 1) q = 1;
  if (q > max) q = max;

  it.qty = q;
}

function submit() {
  if (!canSubmit.value) return;

  form.post(route('orders.store'), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Nueva Venta" />

  <AuthenticatedLayout>
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold">Nueva Venta</h1>
      <Link class="underline" :href="route('orders.index')">Volver</Link>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
      <!-- Panel Izquierdo -->
      <div class="bg-white p-4 rounded shadow space-y-4">
        <div>
          <label class="block text-sm font-medium">Vecino</label>

          <select v-model="form.neighbor_id" class="w-full border rounded p-2">
            <option value="" disabled>Seleccione...</option>
            <option v-for="n in neighbors" :key="n.id" :value="n.id">
              {{ n.full_name }} ({{ n.cedula }})
              - Cupo BS: {{ Number(n.available_bs ?? 0).toFixed(2) }}
              - Cupo USD: {{ Number(n.available_usd ?? 0).toFixed(2) }}
            </option>
          </select>

          <div v-if="form.errors.neighbor_id" class="text-red-600 text-sm mt-1">
            {{ form.errors.neighbor_id }}
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium">Moneda</label>
            <select v-model="form.currency" class="w-full border rounded p-2">
              <option value="BS">BS</option>
              <option value="USD">USD</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium">Estado</label>
            <select v-model="form.status" class="w-full border rounded p-2">
              <option value="PAID">Pagado</option>
              <option value="PENDING">Pendiente</option>
            </select>
          </div>
        </div>

        <div v-if="selectedNeighbor" class="border rounded p-3 bg-gray-50 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600">Límite ({{ form.currency }}):</span>
            <span class="font-semibold">{{ limitForCurrency.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Pendiente ({{ form.currency }}):</span>
            <span class="font-semibold">{{ pendingDebt.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Cupo disponible ({{ form.currency }}):</span>
            <span class="font-semibold">{{ availableCredit.toFixed(2) }}</span>
          </div>
        </div>

        <div
          v-if="form.status === 'PENDING' && selectedNeighbor && insufficientCredit"
          class="p-3 rounded bg-red-50 text-red-700 text-sm"
        >
          Cupo insuficiente. Total: <b>{{ total.toFixed(2) }}</b> | Cupo:
          <b>{{ availableCredit.toFixed(2) }}</b>
        </div>

        <div v-if="insufficientStock" class="p-3 rounded bg-red-50 text-red-700 text-sm">
          Hay productos con cantidad mayor al stock disponible.
        </div>

        <div class="border-t pt-4">
          <div class="flex items-center justify-between">
            <div class="font-semibold">Total</div>
            <div class="font-semibold">{{ total.toFixed(2) }} {{ form.currency }}</div>
          </div>

          <div v-if="form.errors.items" class="text-red-600 text-sm mt-2">
            {{ form.errors.items }}
          </div>

          <button
            class="mt-3 px-3 py-2 bg-black text-white rounded disabled:opacity-50"
            @click="submit"
            :disabled="form.processing || !canSubmit"
          >
            Registrar venta
          </button>

          <div class="text-xs text-gray-600 mt-2">
            * Stock y crédito se validan definitivamente en el servidor al guardar.
          </div>
        </div>
      </div>

      <!-- Panel Derecho -->
      <div class="space-y-4">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-3">Productos</h2>

          <div class="space-y-2 max-h-[380px] overflow-auto pr-1">
            <div
              v-for="p in products"
              :key="p.id"
              class="flex items-center justify-between border rounded p-2"
            >
              <div class="min-w-0">
                <div class="font-medium truncate">{{ p.name }}</div>
                <div class="text-xs text-gray-600">
                  Stock: {{ p.stock }} | Bs: {{ Number(p.price_bs).toFixed(2) }} | USD:
                  {{ Number(p.price_usd).toFixed(2) }}
                </div>
              </div>

              <button
                class="px-2 py-1 bg-gray-900 text-white rounded disabled:opacity-50"
                @click="addItem(p)"
                :disabled="p.stock <= 0"
              >
                + Agregar
              </button>
            </div>
          </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-3">Carrito</h2>

          <div v-if="form.items.length === 0" class="text-sm text-gray-600">
            No hay productos agregados.
          </div>

          <div v-for="it in form.items" :key="it.product_id" class="border rounded p-2 mb-2">
            <div class="flex items-center justify-between">
              <div class="font-medium">{{ productById(it.product_id)?.name }}</div>
              <button class="text-red-600 underline text-sm" @click="removeItem(it.product_id)">
                Quitar
              </button>
            </div>

            <div class="flex flex-wrap items-center gap-2 mt-2">
              <div class="flex items-center gap-2">
                <button class="px-2 py-1 border rounded" @click="decQty(it.product_id)">-</button>

                <input
                  type="number"
                  min="1"
                  :max="productById(it.product_id)?.stock ?? 1"
                  v-model.number="it.qty"
                  @change="clampQty(it.product_id)"
                  @blur="clampQty(it.product_id)"
                  class="border rounded p-1 w-20"
                />

                <button class="px-2 py-1 border rounded" @click="incQty(it.product_id)">+</button>
              </div>

              <div class="text-sm ml-auto">
                <span class="text-gray-600">Precio:</span>
                {{ unitPrice(productById(it.product_id)).toFixed(2) }} {{ form.currency }}
                <span class="mx-2">|</span>
                <span class="text-gray-600">Subtotal:</span>
                {{ (unitPrice(productById(it.product_id)) * Number(it.qty)).toFixed(2) }}
                {{ form.currency }}
              </div>
            </div>

            <div class="text-xs text-gray-600 mt-1">
              Stock disponible: {{ productById(it.product_id)?.stock ?? 0 }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
