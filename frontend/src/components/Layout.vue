<template>
  <div class="min-h-screen flex flex-col lg:flex-row">
    <!-- Mobile Header -->
    <header class="lg:hidden bg-emerald-700 text-white px-4 py-3 flex items-center 
                   justify-between sticky top-0 z-40 shadow-md">
      <h1 class="text-lg font-bold">🌱 ATBI System</h1>
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2">
        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </header>

    <!-- Overlay for mobile -->
    <div v-if="mobileMenuOpen" 
      @click="mobileMenuOpen = false"
      class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="[
      'bg-emerald-700 text-white p-4 flex flex-col z-50',
      'fixed lg:static inset-y-0 left-0 w-64 transform transition-transform duration-300',
      'lg:translate-x-0 lg:w-64 lg:h-screen lg:sticky lg:top-0',
      mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
      <div class="flex items-center justify-between mb-6 lg:mb-8">
        <h1 class="text-xl lg:text-2xl font-bold">🌱 ATBI</h1>
        <button @click="mobileMenuOpen = false" class="lg:hidden p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 space-y-1 overflow-y-auto">
        <RouterLink to="/home" @click="mobileMenuOpen = false"
          class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-emerald-600 text-sm"
          :class="$route.path === '/home' ? 'bg-emerald-600' : ''">
          🏠 Home
        </RouterLink>
        <RouterLink to="/calendar" @click="mobileMenuOpen = false"
          class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-emerald-600 text-sm"
          :class="$route.path === '/calendar' ? 'bg-emerald-600' : ''">
          📅 Calendar
        </RouterLink>
        <RouterLink to="/cart" @click="mobileMenuOpen = false"
          class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-emerald-600 text-sm"
          :class="$route.path === '/cart' ? 'bg-emerald-600' : ''">
          <span>🛒 Cart</span>
          <span v-if="auth.cartCount" class="bg-red-500 px-2 rounded-full text-xs">
            {{ auth.cartCount }}
          </span>
        </RouterLink>

        <!-- Admin Section -->
        <div v-if="['admin','super_admin'].includes(auth.user?.role)" 
          class="pt-3 mt-3 border-t border-emerald-600 space-y-1">
          <p class="text-xs text-emerald-200 px-3 py-1">ADMIN</p>
          <RouterLink to="/admin/products" @click="mobileMenuOpen = false"
            class="block px-3 py-2.5 rounded-lg hover:bg-emerald-600 text-sm"
            :class="$route.path === '/admin/products' ? 'bg-emerald-600' : ''">
            📦 Manage Products
          </RouterLink>
          <RouterLink to="/admin/calendar" @click="mobileMenuOpen = false"
            class="block px-3 py-2.5 rounded-lg hover:bg-emerald-600 text-sm"
            :class="$route.path === '/admin/calendar' ? 'bg-emerald-600' : ''">
            🗓️ Manage Events
          </RouterLink>
        </div>

        <!-- Super Admin Section -->
        <div v-if="auth.user?.role === 'super_admin'" 
          class="pt-3 mt-3 border-t border-emerald-600 space-y-1">
          <p class="text-xs text-amber-200 px-3 py-1">👑 SUPER ADMIN</p>
          <RouterLink to="/superadmin/users" @click="mobileMenuOpen = false"
            class="block px-3 py-2.5 rounded-lg hover:bg-emerald-600 text-sm"
            :class="$route.path === '/superadmin/users' ? 'bg-emerald-600' : ''">
            👥 Manage Users
          </RouterLink>
        </div>
      </nav>

      <div class="border-t border-emerald-600 pt-4 mt-4">
        <p class="text-sm font-medium truncate">{{ auth.user?.full_name }}</p>
        <p class="text-xs text-emerald-200 capitalize">{{ auth.user?.role?.replace('_',' ') }}</p>
        <button @click="logout" 
          class="mt-2 w-full px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-sm">
          Logout
        </button>
      </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-4 md:p-6 overflow-auto bg-gray-50 w-full">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const mobileMenuOpen = ref(false)

function logout() {
  auth.logout()
  router.push('/login')
}
</script>