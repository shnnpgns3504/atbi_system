<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-3xl font-bold text-gray-800">Welcome to ATBI System</h1>
      <p class="text-gray-500 mt-1">Explore our 6 Agricultural Technology Business Incubators</p>
    </div>

    <div v-if="loading" class="text-center py-16 text-gray-400">Loading ATBIs...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <RouterLink v-for="atbi in atbis" :key="atbi.atbi_id" :to="`/atbi/${atbi.slug}`"
        class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-100">
        <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center text-2xl font-bold text-emerald-700 mb-4">
          {{ atbi.name.charAt(5) }}
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">{{ atbi.name }}</h2>
        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ atbi.description }}</p>
        <p class="text-xs text-gray-500">📍 {{ atbi.location }}</p>
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axiosClient from '@/axios'

const atbis = ref<any[]>([])
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await axiosClient.get('/atbis')
    if (data.success) atbis.value = data.data
  } catch (e) { console.error(e) }
  finally { loading.value = false }
})
</script>