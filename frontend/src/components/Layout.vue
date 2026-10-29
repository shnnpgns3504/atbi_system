<template>
  <div class="min-h-screen flex">
    <aside class="w-64 bg-emerald-700 text-white p-4 flex flex-col">
      <h1 class="text-2xl font-bold mb-8">🌱 ATBI System</h1>
      <nav class="flex-1 space-y-2">
        <RouterLink to="/home" class="block px-4 py-3 rounded-lg hover:bg-emerald-600"
          :class="$route.path === '/home' ? 'bg-emerald-600' : ''">🏠 Home</RouterLink>
        <RouterLink to="/calendar" class="block px-4 py-3 rounded-lg hover:bg-emerald-600"
          :class="$route.path === '/calendar' ? 'bg-emerald-600' : ''">📅 Calendar</RouterLink>
        <RouterLink to="/cart" class="block px-4 py-3 rounded-lg hover:bg-emerald-600 flex justify-between"
          :class="$route.path === '/cart' ? 'bg-emerald-600' : ''">
          <span>🛒 Cart</span>
          <span v-if="auth.cartCount" class="bg-red-500 px-2 rounded-full text-xs">{{ auth.cartCount }}</span>
        </RouterLink>

        <div v-if="auth.user?.role === 'admin'" class="pt-4 border-t border-emerald-600 space-y-2">
          <p class="text-xs text-emerald-200 px-4">ADMIN</p>
          <RouterLink to="/admin/products" class="block px-4 py-3 rounded-lg hover:bg-emerald-600"
            :class="$route.path === '/admin/products' ? 'bg-emerald-600' : ''">📦 Manage Products</RouterLink>
          <RouterLink to="/admin/calendar" class="block px-4 py-3 rounded-lg hover:bg-emerald-600"
            :class="$route.path === '/admin/calendar' ? 'bg-emerald-600' : ''">🗓️ Manage Events</RouterLink>
        </div>
      </nav>

      <div class="border-t border-emerald-600 pt-4">
        <p class="text-sm">{{ auth.user?.full_name }}</p>
        <p class="text-xs text-emerald-200">{{ auth.user?.role }}</p>
        <button @click="logout" class="mt-2 w-full px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-sm">Logout</button>
      </div>
    </aside>

    <main class="flex-1 p-6 overflow-auto bg-gray-50">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

function logout() {
  auth.logout()
  router.push('/login')
}
</script>