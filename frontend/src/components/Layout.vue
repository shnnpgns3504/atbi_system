<template>
  <div class="flex h-screen w-full bg-gray-50 overflow-hidden font-sans">
    
    <!-- MOBILE OVERLAY -->
    <div 
      v-if="isSidebarOpen" 
      @click="isSidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-40 md:hidden"
    ></div>

    <!-- SIDEBAR -->
    <aside 
      class="fixed inset-y-0 left-0 z-50 w-64 bg-[#00563F] text-white flex flex-col transition-transform duration-300 transform md:relative md:translate-x-0"
      :class="{ '-translate-x-full': !isSidebarOpen, 'translate-x-0': isSidebarOpen }"
    >
      <!-- Logo Area -->
      <div class="p-6 flex items-center gap-3 border-b border-white/10">
        <img src="/favicon.svg" alt="ATBI Logo" class="w-8 h-8" />
        <span class="text-2xl font-bold tracking-wider">ATBI</span>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <router-link to="/home" @click="isSidebarOpen = false" 
          class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-white/10 transition-colors" 
          active-class="bg-[#007A59]">
          <span>🏠</span> Home
        </router-link>
        <router-link to="/calendar" @click="isSidebarOpen = false" 
          class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-white/10 transition-colors" 
          active-class="bg-[#007A59]">
          <span>📅</span> Calendar
        </router-link>
        <router-link to="/cart" @click="isSidebarOpen = false" 
          class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-white/10 transition-colors" 
          active-class="bg-[#007A59]">
          <span>🛒</span> Cart
        </router-link>

        <!-- Admin Section (only visible to admin) -->
        <template v-if="auth.user?.role === 'admin'">
          <div class="pt-6 pb-2">
            <p class="px-3 text-xs font-semibold text-white/50 uppercase tracking-wider">Admin</p>
          </div>
          <router-link to="/admin/products" @click="isSidebarOpen = false" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-white/10 transition-colors" 
            active-class="bg-[#007A59]">
            <span>📦</span> Manage Products
          </router-link>
          <router-link to="/admin/calendar" @click="isSidebarOpen = false" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-white/10 transition-colors" 
            active-class="bg-[#007A59]">
            <span>🗓️</span> Manage Events
          </router-link>
          <router-link to="/admin/users" @click="isSidebarOpen = false" 
            class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-white/10 transition-colors" 
            active-class="bg-[#007A59]">
            <span>👥</span> Manage Users
          </router-link>
        </template>
      </nav>

      <!-- User Profile / Logout -->
      <div class="p-4 border-t border-white/10 bg-[#004a36]">
        <div class="mb-3 text-center md:text-left">
          <p class="font-bold text-sm truncate">{{ auth.user?.full_name || 'Guest' }}</p>
          <p class="text-xs text-white/70 capitalize">{{ auth.user?.role?.replace('_', ' ') || 'User' }}</p>
        </div>
        <button @click="handleLogout" 
          class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md font-medium transition-colors">
          Logout
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full w-full overflow-hidden relative">
      
      <!-- Mobile Header -->
      <header class="md:hidden bg-white shadow-sm p-4 flex items-center justify-between z-30">
        <div class="flex items-center gap-2">
          <img src="/favicon.svg" alt="Logo" class="w-6 h-6" />
          <span class="font-bold text-[#00563F]">ATBI System</span>
        </div>
        <button @click="isSidebarOpen = !isSidebarOpen" class="text-gray-600 focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </header>

      <!-- Pages Load Here -->
      <div class="flex-1 overflow-y-auto p-4 md:p-8">
        <router-view />
      </div>

    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const isSidebarOpen = ref(false)

const handleLogout = () => {
  auth.logout()
  router.push('/login')
}
</script>