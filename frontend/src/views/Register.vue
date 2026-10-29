<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-600 to-green-800 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
      <h1 class="text-3xl font-bold text-center text-emerald-700 mb-2">🌱 ATBI System</h1>
      <p class="text-center text-gray-500 mb-8">Create your account</p>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
          <input v-model="form.full_name" type="text" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
          <input v-model="form.username" type="text" required minlength="3"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input v-model="form.email" type="email" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input v-model="form.password" type="password" required minlength="6"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
          {{ error }}
        </div>

        <button type="submit" :disabled="loading"
          class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold disabled:opacity-50">
          {{ loading ? 'Creating...' : 'Create Account' }}
        </button>
      </form>

      <p class="text-center text-gray-500 mt-6 text-sm">
        Already have an account?
        <RouterLink to="/login" class="text-emerald-600 hover:underline">Sign in</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const loading = ref(false)
const error = ref('')

const form = reactive({ full_name: '', username: '', email: '', password: '' })

async function handleRegister() {
  loading.value = true
  error.value = ''
  try {
    const data = await auth.register(form)
    if (data.success) {
      router.push('/login')
    } else {
      error.value = data.message || 'Registration failed'
    }
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>