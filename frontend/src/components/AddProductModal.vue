<template>
  <teleport to="body">
    <transition name="fade">
      <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative my-8">
          <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
          
          <h3 class="text-2xl font-bold text-gray-800 mb-6">Add New Product</h3>

          <form @submit.prevent="submitProduct" class="space-y-4">
            
            <!-- LOCKED ATBI -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">ATBI</label>
              <div class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-xl text-gray-600 font-medium cursor-not-allowed">
                {{ atbiName || 'Loading...' }}
              </div>
            </div>

            <!-- PRODUCT NAME -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="e.g. Fresh Broccoli" />
            </div>

            <!-- IMAGE UPLOAD -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Product Image <span class="text-red-500">*</span></label>
              <div class="flex items-center gap-4">
                <!-- Preview -->
                <div class="w-20 h-20 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                  <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                  <span v-else class="text-3xl text-gray-300">📷</span>
                </div>
                <!-- Input -->
                <div class="flex-1">
                  <input 
                    type="file" 
                    accept="image/*" 
                    @change="handleFileUpload" 
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all cursor-pointer"
                  />
                  <p class="text-xs text-gray-400 mt-1">JPEG, PNG (Max 2MB)</p>
                </div>
              </div>
            </div>

            <!-- DESCRIPTION -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="Brief product details..."></textarea>
            </div>

            <!-- PRICE, WEIGHT, STOCK -->
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Price (₱) <span class="text-red-500">*</span></label>
                <input v-model.number="form.price" type="number" step="0.01" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                <!-- MIN 1 -->
                <input v-model.number="form.weight_kg" type="number" step="1" min="1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Stock <span class="text-red-500">*</span></label>
                <!-- MIN 0 (if they have no stock yet) -->
                <input v-model.number="form.stock" type="number" step="1" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500" />
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
import { ref, watch } from 'vue'
import axiosClient from '@/axios'

const props = defineProps({
  show: Boolean,
  initialAtbiId: { type: Number, default: 0 },
  atbiName: { type: String, default: '' } // NEW PROP: Pass the name from AtbiDetail
})

const emit = defineEmits(['close', 'added'])

const loading = ref(false)
const error = ref('')
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)

const form = ref({
  atbi_id: props.initialAtbiId || '',
  name: '',
  description: '',
  price: 0,
  weight_kg: 1, // Default 1kg
  stock: 0
})

// Watch for when the modal opens to reset the form
watch(() => props.show, (newVal) => {
  if (newVal) {
    form.value = {
      atbi_id: props.initialAtbiId || '',
      name: '',
      description: '',
      price: 0,
      weight_kg: 1,
      stock: 0
    }
    imageFile.value = null
    imagePreview.value = null
    error.value = ''
  }
})

// Handle Image Selection & Preview
function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      error.value = "Image must be less than 2MB"
      return
    }
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
    error.value = ''
  }
}

async function submitProduct() {
  if (!imageFile.value) {
    error.value = 'Product image is required.'
    return
  }

  loading.value = true
  error.value = ''

  // Use FormData because we are uploading a file
  const formData = new FormData()
  formData.append('atbi_id', String(form.value.atbi_id))
  formData.append('name', form.value.name)
  formData.append('description', form.value.description)
  formData.append('price', String(form.value.price))
  formData.append('weight_kg', String(form.value.weight_kg))
  formData.append('stock', String(form.value.stock))
  formData.append('image', imageFile.value)

  try {
    const { data } = await axiosClient.post('/products/create', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
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