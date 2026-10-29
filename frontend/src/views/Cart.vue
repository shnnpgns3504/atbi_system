<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-800">🛒 Your Cart</h1>

    <div v-if="loading" class="text-center py-16 text-gray-400">Loading...</div>

    <div v-else-if="items.length" class="bg-white rounded-xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b">
          <tr class="text-left text-sm text-gray-500">
            <th class="px-6 py-4">Product</th>
            <th class="px-6 py-4 text-right">Price</th>
            <th class="px-6 py-4 text-center">Quantity</th>
            <th class="px-6 py-4 text-right">Subtotal</th>
            <th class="px-6 py-4 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="item in items" :key="item.cart_id">
            <td class="px-6 py-4">
              <p class="font-semibold">{{ item.name }}</p>
              <p class="text-xs text-gray-500">{{ item.atbi_name }}</p>
            </td>
            <td class="px-6 py-4 text-right">₱{{ formatNumber(item.price) }}</td>
            <td class="px-6 py-4 text-center">
              <input type="number" :value="item.quantity" min="1"
                @change="updateQty(item, ($event.target as HTMLInputElement).valueAsNumber)"
                class="w-20 px-2 py-1 border rounded text-center" />
            </td>
            <td class="px-6 py-4 text-right font-semibold">₱{{ formatNumber(item.subtotal) }}</td>
            <td class="px-6 py-4 text-center">
              <button @click="removeItem(item)" class="text-red-600 hover:text-red-700 text-sm">
                Remove
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50 border-t">
          <tr>
            <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
            <td class="px-6 py-4 text-right font-bold text-emerald-700 text-lg">₱{{ formatNumber(total) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
      <div class="p-4 flex justify-end gap-2">
        <button @click="clearCart" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Clear Cart</button>
        <button @click="checkout" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">
          Checkout (Demo)
        </button>
      </div>
    </div>

    <div v-else class="text-center py-16 bg-white rounded-xl text-gray-400">
      Your cart is empty. <RouterLink to="/home" class="text-emerald-600">Browse products →</RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const items = ref<any[]>([])
const total = ref(0)
const loading = ref(true)

function formatNumber(n: any) {
  return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

async function loadCart() {
  loading.value = true
  try {
    const { data } = await axiosClient.get('/cart', { params: { user_id: auth.user?.user_id } })
    if (data.success) {
      items.value = data.data
      total.value = data.total
    }
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

async function updateQty(item: any, qty: number) {
  try {
    await axiosClient.post(`/cart/update/${item.cart_id}`, { quantity: qty })
    await loadCart()
    await auth.loadCartCount()
  } catch (e) { console.error(e) }
}

async function removeItem(item: any) {
  try {
    await axiosClient.post(`/cart/remove/${item.cart_id}`)
    await loadCart()
    await auth.loadCartCount()
  } catch (e) { console.error(e) }
}

async function clearCart() {
  try {
    await axiosClient.post('/cart/clear', null, { params: { user_id: auth.user?.user_id } })
    await loadCart()
    await auth.loadCartCount()
  } catch (e) { console.error(e) }
}

function checkout() {
  alert('Checkout demo — total: ₱' + formatNumber(total.value))
}

onMounted(loadCart)
</script>