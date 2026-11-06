<template>
  <div class="space-y-6">
    <!-- Back Button -->
    <button @click="$router.push('/home')" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium flex items-center gap-1">
      ← Back to Home
    </button>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16 text-gray-400">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mb-2"></div>
      <p>Loading ATBI details...</p>
    </div>

    <!-- ATBI Content -->
    <div v-else-if="atbi" class="space-y-6">
      
      <!-- ATBI Header Banner -->
      <div class="bg-gradient-to-r from-emerald-600 to-green-700 rounded-2xl p-8 text-white relative shadow-lg">
        <h1 class="text-4xl font-bold text-white">{{ atbi.full_name || atbi.name }}</h1>
        <p class="text-emerald-100 mb-6 max-w-2xl">{{ atbi.description }}</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm bg-black/10 p-4 rounded-xl">
          <div class="flex items-center gap-2">
            <span>📍</span> {{ atbi.location || 'Location not set' }}
          </div>
          <div class="flex items-center gap-2">
            <span>👤</span> {{ atbi.contact_person || 'Contact not set' }}
          </div>
          <div class="flex items-center gap-2">
            <span>📞</span> {{ atbi.contact_phone || 'Phone not set' }}
          </div>
        </div>

        <!-- Add Product Button (Only visible if user has permission) -->
        <button 
          v-if="canAddProduct" 
          @click="showAddModal = true" 
          class="absolute top-8 right-8 bg-white text-emerald-700 px-5 py-2.5 rounded-xl font-bold hover:bg-gray-100 shadow-md transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Add Product
        </button>
      </div>

      <!-- Products List -->
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold text-gray-800">Products</h2>
          <span class="text-sm text-gray-500">{{ displayedProducts.length }} items available</span>
        </div>

        <!-- SEARCH & EXPORT COMPONENT -->
        <SearchExportBar 
          :data="atbi.products || []" 
          :atbiName="atbi.name"
          :atbiFullName="atbi.full_name || atbi.name"
          :atbiSlug="atbi.slug"
          @update="displayedProducts = $event"
        />

        <!-- Product Grid -->
        <div v-if="displayedProducts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="product in displayedProducts" 
            :key="product.product_id"
            class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-5 border border-gray-100 flex flex-col"
          >
            <!-- Product Image FIXED HERE -->
            <div class="w-full h-40 bg-emerald-50 rounded-xl mb-4 flex items-center justify-center overflow-hidden">
              <img v-if="product.image_url" :src="getImageUrl(product.image_url)" class="w-full h-full object-cover" />
              <span v-else class="text-5xl">🛒</span>
            </div>
            
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-800 mb-1">{{ product.name }}</h3>
              <p class="text-gray-600 text-sm mb-4 line-clamp-2" :title="product.description">
                {{ product.description || 'No description available.' }}
              </p>
            </div>
            
            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
              <span class="text-xl font-extrabold text-emerald-700">₱{{ formatNumber(product.price) }}</span>
              
              <button 
                @click="addToCart(product)"
                :disabled="adding === product.product_id"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold disabled:opacity-50 transition-colors flex items-center gap-2"
              >
                <span v-if="adding === product.product_id" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                <span v-else>+ Cart</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20 text-gray-400 bg-white rounded-2xl border border-gray-100">
          <div class="text-6xl mb-4">📦</div>
          <p class="text-lg font-medium">No products found.</p>
          <p class="text-sm mt-1">Try adjusting your search.</p>
        </div>
      </div>
    </div>

    <!-- ADD PRODUCT MODAL -->
    <AddProductModal 
      :show="showAddModal"
      :initial-atbi-id="atbi?.atbi_id || 0"
      :atbiName="atbi?.name" 
      @close="showAddModal = false"
      @added="handleProductAdded"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'
import AddProductModal from '@/components/AddProductModal.vue'
import SearchExportBar from '@/components/SearchExportBar.vue'

const route = useRoute()
const auth = useAuthStore()
const atbi = ref<any>(null)
const loading = ref(true)
const adding = ref<number | null>(null)
const showAddModal = ref(false)

const displayedProducts = ref<any[]>([])

const canAddProduct = computed(() => {
  if (!auth.user) return false;
  if (auth.user.role === 'admin') return true;
  if (auth.user.atbi_affiliation && atbi.value) {
    return atbi.value.name.includes(auth.user.atbi_affiliation);
  }
  return false;
});

function formatNumber(n: any) {
  return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// --- NEW: FIX IMAGE URL FOR CODECIGNITER BACKEND ---
function getImageUrl(path: string) {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  
  // Adjust this to match your CodeIgniter base URL!
  // If you are running CI on localhost:8080, use that.
  // If you have VITE_API_URL in your .env, it will use that.
  const backendUrl = import.meta.env.VITE_API_URL || 'http://localhost:8080'; 
  return `${backendUrl}${path}`;
}

async function addToCart(product: any) {
  if (!auth.user) {
    alert('Please log in to add items to cart.')
    return
  }
  
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
  } catch (e) { 
    console.error(e) 
    alert('Failed to add item to cart.')
  } finally { 
    adding.value = null 
  }
}

async function loadAtbi() {
  loading.value = true
  try {
    const { data } = await axiosClient.get(`/atbis/${route.params.id}`)
    if (data.success) {
      atbi.value = data.data
      displayedProducts.value = data.data.products || []
    }
  } catch (e) { 
    console.error(e) 
  } finally { 
    loading.value = false 
  }
}

function handleProductAdded() {
  loadAtbi()
}

onMounted(loadAtbi)

watch(() => route.params.id, () => {
  loadAtbi()
})
</script>