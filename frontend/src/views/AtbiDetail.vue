<template>
  <div class="space-y-6">
    <button @click="$router.push('/home')" class="text-emerald-600 hover:text-emerald-700 text-sm">
      ← Back to Home
    </button>

    <div v-if="loading" class="text-center py-16 text-gray-400">Loading...</div>

    <div v-else-if="atbi" class="space-y-6">
      <!-- ATBI Header -->
      <div class="bg-gradient-to-r from-emerald-600 to-green-700 rounded-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">{{ atbi.name }}</h1>
        <p class="text-emerald-100 mb-4">{{ atbi.description }}</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>📍 {{ atbi.location }}</div>
          <div>👤 {{ atbi.contact_person }}</div>
          <div>📞 {{ atbi.contact_phone }}</div>
        </div>
      </div>

      <!-- Products -->
      <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Products</h2>
        <div v-if="atbi.products?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="product in atbi.products" :key="product.product_id"
            class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
            <div class="w-full h-40 bg-emerald-50 rounded-lg mb-3 flex items-center justify-center text-5xl">
              🛒
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ product.name }}</h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ product.description }}</p>
            <div class="flex items-center justify-between">
              <span class="text-xl font-bold text-emerald-700">₱{{ formatNumber(product.price) }}</span>
              <button @click="addToCart(product)"
                :disabled="adding === product.product_id"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold disabled:opacity-50">
                {{ adding === product.product_id ? 'Adding...' : '+ Cart' }}
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-16 text-gray-400 bg-white rounded-xl">
          No products available yet.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const auth = useAuthStore()
const atbi = ref<any>(null)
const loading = ref(true)
const adding = ref<number | null>(null)

function formatNumber(n: any) {
  return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

async function addToCart(product: any) {
  adding.value = product.product_id
  try {
    const { data } = await axiosClient.post('/cart/add', {
      user_id: auth.user?.user_id,
      product_id: product.product_id,
      quantity: 1
    })
    if (data.success) {
      await auth.loadCartCount()
      alert(`Added ${product.name} to cart!`)
    }
  } catch (e) { console.error(e) }
  finally { adding.value = null }
}

onMounted(async () => {
  try {
    const { data } = await axiosClient.get(`/atbis/${route.params.slug}`)
    if (data.success) atbi.value = data.data
  } catch (e) { console.error(e) }
  finally { loading.value = false }
})
</script>