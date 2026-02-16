<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

function roleBadgeClass(role) {
  if (role === 'admin') return 'bg-red-100 text-red-700'
  if (role === 'user') return 'bg-blue-100 text-blue-700'
  if (role === 'supervisor') return 'bg-green-100 text-green-700'
  return 'bg-gray-200 text-gray-700'
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- NAV -->
    <nav class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">

          <!-- Logo + Nombre -->
          <Link :href="route('dashboard')" class="flex items-center gap-2">
            <img
              src="/images/logo-sigescc2.png"
              alt="SIGESCC"
              class="h-8 w-auto"
            />
            <span class="font-bold text-lg">
              SIGESCC
            </span>
          </Link>

          <!-- Links -->
          <div class="flex flex-wrap items-center gap-3">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
              Inicio
            </NavLink>

            <NavLink :href="route('products.index')" :active="route().current('products.*')">
              Productos
            </NavLink>

            <NavLink :href="route('neighbors.index')" :active="route().current('neighbors.*')">
              Vecinos
            </NavLink>

            <NavLink :href="route('orders.index')" :active="route().current('orders.*')">
              Ventas
            </NavLink>

            <NavLink :href="route('reports.index')" :active="route().current('reports.*')">
              Reportes
            </NavLink>
          </div>

          <!-- Usuario + Logout -->
          <div class="ml-auto flex items-center gap-3">
            <div class="flex items-center gap-2 text-sm">
              <span class="font-semibold text-gray-800">
                {{ user?.name }}
              </span>

              <span
                class="px-2 py-0.5 text-xs font-medium rounded-full"
                :class="roleBadgeClass(user?.role)"
              >
                {{ user?.role }}
              </span>
            </div>

            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="px-3 py-2 rounded bg-red-600 text-white text-sm hover:bg-red-700"
            >
              Cerrar sesión
            </Link>
          </div>

        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4">
      <slot />
    </main>
  </div>
</template>
