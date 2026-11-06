<template>
  <div class="h-screen w-full flex overflow-hidden bg-gray-50 font-sans">
    
    <!-- SIDEBAR -->
    <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>
      
    <aside 
      class="fixed md:relative inset-y-0 left-0 z-50 w-64 bg-[#00563F] text-white flex flex-col transition-transform duration-300 transform md:translate-x-0 flex-shrink-0 h-full shadow-xl"
      :class="{ '-translate-x-full': !isSidebarOpen, 'translate-x-0': isSidebarOpen }"
    >
      <div class="h-32 flex items-center justify-center gap-3 border-b border-white/10 bg-[#004a36] flex-shrink-0">
        <img src="/logos/main.png" alt="ATBI Logo" class="w-27 h-27 rounded-full bg-white p-1" />
        <!-- <span class="text-xl font-bold tracking-wider">ATBI</span> -->
      </div>

      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <router-link to="/home" @click="isSidebarOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/10 transition-colors" active-class="bg-[#007A59] shadow-inner">
          <span>🏠</span> Home
        </router-link>
        <router-link to="/calendar" @click="isSidebarOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/10 transition-colors" active-class="bg-[#007A59] shadow-inner">
          <span>📅</span> Calendar
        </router-link>
        <router-link to="/cart" @click="isSidebarOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/10 transition-colors" active-class="bg-[#007A59] shadow-inner">
          <span>🛒</span> Cart
        </router-link>

        <template v-if="auth.user?.role === 'admin'">
          <div class="pt-6 pb-2">
            <p class="px-3 text-xs font-semibold text-white/50 uppercase tracking-wider">Admin</p>
          </div>
          <router-link to="/admin/products" @click="isSidebarOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/10 transition-colors" active-class="bg-[#007A59] shadow-inner">
            <span>📦</span> Manage Products
          </router-link>
          <router-link to="/admin/calendar" @click="isSidebarOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/10 transition-colors" active-class="bg-[#007A59] shadow-inner">
            <span>📅</span> Manage Events
          </router-link>
          <router-link to="/admin/users" @click="isSidebarOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/10 transition-colors" active-class="bg-[#007A59] shadow-inner">
            <span>👥</span> Manage Users
          </router-link>
        </template>
      </nav>

      <div class="p-4 border-t border-white/10 bg-[#004a36] flex-shrink-0">
        <button @click="handleLogout" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium transition-colors shadow-sm">
          Logout
        </button>
      </div>
    </aside>

    <!-- RIGHT SIDE CONTENT -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
      
      <!-- HEADER & NAVBAR -->
      <div class="flex-shrink-0 z-40 shadow-md">
        <header class="h-16 bg-[#00563F] text-white flex items-center justify-between px-6">
          <div class="flex items-center gap-4">
            <button @click="isSidebarOpen = !isSidebarOpen" class="md:hidden text-white focus:outline-none">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <h1 class="text-xl font-bold tracking-tight">Agribased Technology Business Incubator: Intertrade System</h1>
          </div>

          <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-bold leading-tight">{{ auth.user?.full_name }}</p>
              <p class="text-xs text-emerald-200 capitalize">{{ auth.user?.role }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/50 cursor-pointer hover:bg-white/30 transition-colors">
              <span class="text-lg font-bold">{{ auth.user?.full_name?.charAt(0) }}</span>
            </div>
          </div>
        </header>

        <!-- SECONDARY NAVIGATION BAR -->
        <nav class="h-16 bg-gradient-to-r from-[#00563F] to-blue-900 flex items-center px-6 overflow-x-auto whitespace-nowrap hide-scrollbar">
          <div class="flex items-center space-x-2">
            
            <!-- ATBI Links (Using ID to guarantee it works) -->
            <RouterLink 
              v-for="(atbi, i) in navAtbis" 
              :key="atbi.atbi_id" 
              :to="`/atbi/${atbi.atbi_id}`"
              class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:bg-white/20 transition-all group border border-transparent"
              active-class="bg-emerald-500 text-white shadow-md border-emerald-400"
            >
              <img :src="getLogo(atbi.slug, i)" :alt="atbi.name" class="w-7 h-7 rounded-full bg-white object-cover p-0.5 group-hover:scale-110 transition-transform shadow-sm" />
              <span class="hidden lg:block">{{ atbi.name }}</span>
            </RouterLink>

            <div class="w-px h-8 bg-white/30 mx-2"></div>

            <RouterLink 
              to="/about" 
              class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:bg-white/20 transition-all border border-transparent" 
              active-class="bg-emerald-500 text-white shadow-md border-emerald-400"
            >
              About Us
            </RouterLink>
          </div>
        </nav>
      </div>

      <!-- MAIN CONTENT -->
      <main class="flex-1 overflow-y-auto flex flex-col bg-gray-50">
        <div class="flex-1 p-4 md:p-8">
          <router-view v-slot="{ Component, route }">
            <component :is="Component" :key="route.fullPath" />
          </router-view>
        </div>

        <!-- FOOTER -->
        <footer class="bg-gradient-to-r from-[#00563F] to-blue-900 text-white py-4 mt-auto flex-shrink-0 shadow-inner">
          <div class="max-w-7xl mx-auto px-6">
            <!-- CHANGED: justify-center instead of justify-between (single child → center it) -->
            <div class="flex items-center justify-center">
              <div class="flex flex-col items-center text-center">
                <img
                  src="/logos/main.png"
                  alt="ATBI Main"
                  class="w-14 h-14 rounded-full bg-white p-1 mb-1 shadow-md object-contain"
                />
                <p class="text-xs font-bold text-white">ATBI Intertrade System</p>
                <p class="text-[10px] text-emerald-200 mt-0.5">&copy; 2026 All rights reserved.</p>
              </div>
            </div>
          </div>
        </footer>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axiosClient from '@/axios'

const router = useRouter()
const auth = useAuthStore()
const isSidebarOpen = ref(false)

const navAtbis = ref<any[]>([])

onMounted(async () => {
  try {
    const { data } = await axiosClient.get('/atbis')
    if (data.success) {
      navAtbis.value = data.data
    }
  } catch (e) { 
    console.error('Failed to load ATBI navigation', e) 
  }
})

function getLogo(slug: string, index: number) {
  const slugLower = slug.toLowerCase();
  if (slugLower.includes('1') || slugLower.includes('bsu')) return '/logos/bsu.png';
  if (slugLower.includes('2') || slugLower.includes('clsu')) return '/logos/clsu.png';
  if (slugLower.includes('3') || slugLower.includes('dmmmsu')) return '/logos/dmmmsu.png';
  if (slugLower.includes('4') || slugLower.includes('isu')) return '/logos/isu.png';
  if (slugLower.includes('5') || slugLower.includes('mmsu')) return '/logos/mmsu.png';
  if (slugLower.includes('6') || slugLower.includes('psu')) return '/logos/psu.png';
  const fallbacks = ['/logos/bsu.png', '/logos/clsu.png', '/logos/dmmmsu.png', '/logos/isu.png', '/logos/mmsu.png', '/logos/psu.png'];
  return fallbacks[index % 6];
}

const handleLogout = () => {
  auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>