<template>
  <teleport to="body">
    <transition name="fade">
      <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
          <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
          
          <h3 class="text-2xl font-bold text-gray-800 mb-6">Add New Product</h3>

          <form @submit.prevent="submitProduct" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">ATBI <span class="text-red-500">*</span></label>
              <select v-model="form.atbi_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="" disabled>Select ATBI</option>
                <option v-for="a in atbis" :key="a.atbi_id" :value="a.atbi_id">{{ a.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="e.g. Fresh Broccoli" />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="Brief product details..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Price (₱) <span class="text-red-500">*</span></label>
                <input v-model.number="form.price" type="number" step="0.01" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                <input v-model.number="form.stock" type="number" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" />
              </div>
            </div>

            <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">{{ error }}</div>

            <div class="flex gap-3 pt-4">
              <button type="button" @click="$emit('close')" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl font-bold text-gray-700">Cancel</button>
              <button type="submit" :disabled="loading" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold disabled:opacity-50">
                {{ loading ? 'Saving...' : 'Save Product' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import axiosClient from '@/axios'

const props = defineProps({
  show: Boolean,
  initialAtbiId: { type: Number, default: 0 }
})

const emit = defineEmits(['close', 'added'])

const atbis = ref<any[]>([])
const loading = ref(false)
const error = ref('')

const form = ref({
  atbi_id: props.initialAtbiId || '',
  name: '',
  description: '',
  price: 0,
  stock: 0
})

// Load ATBIs for the dropdown
onMounted(async () => {
  try {
    const { data } = await axiosClient.get('/atbis')
    if (data.success) atbis.value = data.data
  } catch (e) { console.error(e) }
})

// Watch for when the modal opens to set the default ATBI
watch(() => props.show, (newVal) => {
  if (newVal) {
    form.value = {
      atbi_id: props.initialAtbiId || '',
      name: '',
      description: '',
      price: 0,
      stock: 0
    }
    error.value = ''
  }
})

async function submitProduct() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await axiosClient.post('/products/create', form.value)
    if (data.success) {
      emit('added')
      emit('close')
    } else {
      error.value = data.message || 'Failed to save product'
    }
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Error saving product'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>