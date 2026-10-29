<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-600 to-green-800 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
      <h1 class="text-3xl font-bold text-center text-emerald-700 mb-2">🌱 ATBI System</h1>
      <p class="text-center text-gray-500 mb-8">Agricultural Technology Business Incubator</p>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
          <input v-model="username" type="text" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input v-model="password" type="password" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
          {{ error }}
        </div>

        <button type="submit" :disabled="loading"
          class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold disabled:opacity-50">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <p class="text-center text-gray-500 mt-6 text-sm">
        Don't have an account?
        <RouterLink to="/register" class="text-emerald-600 hover:underline">Register</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const username = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    const data = await auth.login(username.value, password.value)
    if (data.success) {
      await auth.loadCartCount()
      router.push('/home')
    } else {
      error.value = data.message || 'Invalid credentials'
    }
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>