<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-800">📦 Manage Products</h1>
      <button @click="openAdd" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">
        + Add Product
      </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-md p-4">
      <label class="text-sm font-medium text-gray-700 mr-2">Filter by ATBI:</label>
      <select v-model="filterAtbi" class="px-4 py-2 border rounded-lg">
        <option :value="0">All ATBIs</option>
        <option v-for="a in atbis" :key="a.atbi_id" :value="a.atbi_id">{{ a.name }}</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b">
          <tr class="text-left text-sm text-gray-500">
            <th class="px-6 py-4">Product Name</th>
            <th class="px-6 py-4">ATBI</th>
            <th class="px-6 py-4 text-right">Price</th>
            <th class="px-6 py-4 text-center">Stock</th>
            <th class="px-6 py-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="p in filteredProducts" :key="p.product_id">
            <td class="px-6 py-4 font-medium">{{ p.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ getAtbiName(p.atbi_id) }}</td>
            <td class="px-6 py-4 text-right">₱{{ formatNumber(p.price) }}</td>
            <td class="px-6 py-4 text-center">{{ p.stock }}</td>
            <td class="px-6 py-4 text-center">
              <button @click="openEdit(p)" class="text-blue-600 hover:text-blue-700 mr-3">Edit</button>
              <button @click="deleteProduct(p)" class="text-red-600 hover:text-red-700">Delete</button>
            </td>
          </tr>
          <tr v-if="!filteredProducts.length">
            <td colspan="5" class="text-center py-12 text-gray-400">No products found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
      @click.self="closeModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">{{ editing ? 'Edit' : 'Add' }} Product</h3>
        <form @submit.prevent="saveProduct" class="space-y-3">
          <div>
            <label class="block text-sm font-medium mb-1">ATBI</label>
            <select v-model="form.atbi_id" required class="w-full px-4 py-2 border rounded-lg">
              <option v-for="a in atbis" :key="a.atbi_id" :value="a.atbi_id">{{ a.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Product Name</label>
            <input v-model="form.name" required class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium mb-1">Price (₱)</label>
              <input v-model.number="form.price" type="number" step="0.01" required class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Stock</label>
              <input v-model.number="form.stock" type="number" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div class="flex gap-2 pt-2">
            <button type="button" @click="closeModal" class="flex-1 py-2 bg-gray-200 rounded-lg">Cancel</button>
            <button type="submit" class="flex-1 py-2 bg-emerald-600 text-white rounded-lg">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axiosClient from '@/axios'

const products = ref<any[]>([])
const atbis = ref<any[]>([])
const filterAtbi = ref(0)
const showModal = ref(false)
const editing = ref(false)
const editingId = ref<number | null>(null)

const form = ref({ atbi_id: 0, name: '', description: '', price: 0, stock: 0 })

const filteredProducts = computed(() =>
  filterAtbi.value === 0 ? products.value : products.value.filter(p => p.atbi_id === filterAtbi.value)
)

function formatNumber(n: any) {
  return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function getAtbiName(id: number) {
  return atbis.value.find(a => a.atbi_id === id)?.name || 'Unknown'
}

async function loadData() {
  try {
    const [p, a] = await Promise.all([
      axiosClient.get('/products'),
      axiosClient.get('/atbis')
    ])
    if (p.data.success) products.value = p.data.data
    if (a.data.success) atbis.value = a.data.data
  } catch (e) { console.error(e) }
}

function openAdd() {
  editing.value = false
  editingId.value = null
  form.value = { atbi_id: atbis.value[0]?.atbi_id || 0, name: '', description: '', price: 0, stock: 0 }
  showModal.value = true
}

function openEdit(p: any) {
  editing.value = true
  editingId.value = p.product_id
  form.value = { atbi_id: p.atbi_id, name: p.name, description: p.description, price: p.price, stock: p.stock }
  showModal.value = true
}

function closeModal() { showModal.value = false }

async function saveProduct() {
  try {
    const url = editing.value ? `/products/update/${editingId.value}` : '/products/create'
    const { data } = await axiosClient.post(url, form.value)
    if (data.success) {
      alert(data.message)
      closeModal()
      await loadData()
    }
  } catch (e) { console.error(e) }
}

async function deleteProduct(p: any) {
  if (!confirm(`Delete "${p.name}"?`)) return
  try {
    const { data } = await axiosClient.post(`/products/delete/${p.product_id}`)
    if (data.success) {
      alert(data.message)
      await loadData()
    }
  } catch (e) { console.error(e) }
}

onMounted(loadData)
</script>