<template>
  <div class="min-h-screen flex flex-col lg:flex-row">
    <!-- LEFT: Brand Logos Panel -->
    <div class="relative bg-gradient-to-br from-emerald-700 via-green-700 to-emerald-800 
                flex items-center justify-center p-6 lg:p-12 overflow-hidden
                min-h-[40vh] lg:min-h-screen lg:w-1/2">
      <!-- Decorative circles -->
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-green-400/20 rounded-full blur-3xl"></div>

      <div class="relative z-10 text-center max-w-lg">
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-3 tracking-tight">
          ATBI System
        </h1>
        <p class="text-emerald-100 text-sm md:text-base mb-8 lg:mb-12">
          Agricultural Technology Business Incubator
        </p>

        <!-- ATBI Logos Grid -->
        <div class="grid grid-cols-3 gap-3 md:gap-4">
          <div v-for="(atbi, i) in atbiLogos" :key="i" 
               class="bg-white/95 backdrop-blur rounded-xl p-3 md:p-4 shadow-lg 
                      hover:scale-105 transition-transform duration-300 flex items-center justify-center
                      aspect-square">
            <img :src="atbi.logo" :alt="atbi.name" 
                 class="w-full h-full object-contain" 
                 @error="handleImgError($event, i)" />
          </div>
        </div>

        <p class="mt-6 lg:mt-10 text-emerald-200 text-xs md:text-sm">
          Benguet State University
        </p>
      </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="flex-1 flex items-center justify-center bg-gray-50 p-4 md:p-8 
                lg:w-1/2 min-h-[60vh] lg:min-h-screen">
      <div class="w-full max-w-md">
        <div class="text-center mb-8">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Welcome Back</h2>
          <p class="text-gray-500 mt-2 text-sm md:text-base">Sign in to your account</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4 md:space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
            <input v-model="username" type="text" required autocomplete="username"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-emerald-500 
                     focus:border-transparent transition text-base" 
              placeholder="Enter your username" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <div class="relative">
              <input v-model="password" :type="showPassword ? 'text' : 'password'" 
                required autocomplete="current-password"
                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg 
                       focus:outline-none focus:ring-2 focus:ring-emerald-500 
                       focus:border-transparent transition text-base" 
                placeholder="Enter your password" />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ error }}
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white 
                   rounded-lg font-semibold text-base disabled:opacity-50 
                   transition-colors shadow-md hover:shadow-lg">
            {{ loading ? 'Signing in...' : 'Sign In' }}
          </button>

          <div class="text-center pt-2">
            <button type="button" @click="openForgotModal"
              class="text-sm text-emerald-600 hover:text-emerald-700 hover:underline font-medium">
              Forgot Password?
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Forgot Password Modal -->
    <transition name="modal-fade">
      <div v-if="showForgotModal" 
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
        @click.self="closeForgot">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
          <!-- Step 1: Request -->
          <div v-if="forgotStep === 'request'">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Forgot Password</h3>
            <p class="text-sm text-gray-500 mb-5">
              Enter your email or username to receive a reset OTP.
            </p>
            <input v-model="forgotIdentifier" type="text" 
              placeholder="Email or username"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-4" />
            <div v-if="forgotError" class="p-3 bg-red-50 border border-red-200 
                 rounded-lg text-red-700 text-sm mb-4">{{ forgotError }}</div>
            <div class="flex gap-2">
              <button @click="closeForgot" 
                class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium">
                Cancel
              </button>
              <button @click="requestOtp" :disabled="forgotLoading"
                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white 
                       rounded-lg font-medium disabled:opacity-50">
                {{ forgotLoading ? 'Sending...' : 'Send OTP' }}
              </button>
            </div>
          </div>

          <!-- Step 2: Verify -->
          <div v-if="forgotStep === 'verify'">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Enter OTP</h3>
            <p class="text-sm text-gray-500 mb-5">
              Enter the 6-digit OTP sent to your email.
            </p>
            <input v-model="forgotOtp" type="text" maxlength="6" 
              placeholder="6-digit OTP" inputmode="numeric"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                     text-center text-xl tracking-widest font-mono
                     focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-4" />
            <div v-if="forgotDebugOtp" class="text-xs text-emerald-600 bg-emerald-50 
                 p-2 rounded mb-4 text-center">
              🔑 Test OTP: <strong>{{ forgotDebugOtp }}</strong>
            </div>
            <div v-if="forgotError" class="p-3 bg-red-50 border border-red-200 
                 rounded-lg text-red-700 text-sm mb-4">{{ forgotError }}</div>
            <div class="flex gap-2">
              <button @click="forgotStep = 'request'" 
                class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium">
                Back
              </button>
              <button @click="verifyOtp" :disabled="forgotLoading"
                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white 
                       rounded-lg font-medium disabled:opacity-50">
                {{ forgotLoading ? 'Verifying...' : 'Verify' }}
              </button>
            </div>
          </div>

          <!-- Step 3: Reset -->
          <div v-if="forgotStep === 'reset'">
            <h3 class="text-xl font-bold text-gray-800 mb-2">New Password</h3>
            <p class="text-sm text-gray-500 mb-5">Choose a new password.</p>
            <input v-model="forgotNewPassword" type="password" 
              placeholder="New password (6+ chars)"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3" />
            <input v-model="forgotConfirmPassword" type="password" 
              placeholder="Confirm password"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                     focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-4" />
            <div v-if="forgotError" class="p-3 bg-red-50 border border-red-200 
                 rounded-lg text-red-700 text-sm mb-4">{{ forgotError }}</div>
            <div class="flex gap-2">
              <button @click="closeForgot" 
                class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium">
                Cancel
              </button>
              <button @click="resetPassword" :disabled="forgotLoading"
                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white 
                       rounded-lg font-medium disabled:opacity-50">
                {{ forgotLoading ? 'Resetting...' : 'Reset Password' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axiosClient from '@/axios'

const router = useRouter()
const auth = useAuthStore()

const username = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

// Forgot Password State
const showForgotModal = ref(false)
const forgotStep = ref<'request' | 'verify' | 'reset'>('request')
const forgotIdentifier = ref('')
const forgotOtp = ref('')
const forgotNewPassword = ref('')
const forgotConfirmPassword = ref('')
const forgotUserId = ref(0)
const forgotLoading = ref(false)
const forgotError = ref('')
const forgotDebugOtp = ref('')

// ATBI Logos — replace paths with your actual image paths
const atbiLogos = [
  { name: 'ATBI 1', logo: '/logos/atbi1.png' },
  { name: 'ATBI 2', logo: '/logos/atbi2.png' },
  { name: 'ATBI 3', logo: '/logos/atbi3.png' },
  { name: 'ATBI 4', logo: '/logos/atbi4.png' },
  { name: 'ATBI 5', logo: '/logos/atbi5.png' },
  { name: 'ATBI 6', logo: '/logos/atbi6.png' }
]

function handleImgError(e: Event, index: number) {
  // Fallback: show text if logo missing
  const img = e.target as HTMLImageElement
  img.style.display = 'none'
  const parent = img.parentElement
  if (parent && !parent.querySelector('.fallback')) {
    const span = document.createElement('span')
    span.className = 'fallback text-emerald-700 font-bold text-xs md:text-sm'
    span.textContent = `ATBI ${index + 1}`
    parent.appendChild(span)
  }
}

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

function openForgotModal() {
  showForgotModal.value = true
  forgotStep.value = 'request'
  forgotIdentifier.value = ''
  forgotOtp.value = ''
  forgotNewPassword.value = ''
  forgotConfirmPassword.value = ''
  forgotError.value = ''
  forgotDebugOtp.value = ''
}

function closeForgot() {
  showForgotModal.value = false
}

async function requestOtp() {
  if (!forgotIdentifier.value) {
    forgotError.value = 'Please enter your email or username'
    return
  }
  forgotLoading.value = true
  forgotError.value = ''
  try {
    const { data } = await axiosClient.post('/forgot/request-otp', {
      identifier: forgotIdentifier.value
    })
    if (data.success) {
      forgotUserId.value = data.user_id
      forgotDebugOtp.value = data.debug_otp || ''
      forgotStep.value = 'verify'
    } else {
      forgotError.value = data.message
    }
  } catch (e: any) {
    forgotError.value = e.response?.data?.message || 'Failed to send OTP'
  } finally {
    forgotLoading.value = false
  }
}

async function verifyOtp() {
  if (!forgotOtp.value || forgotOtp.value.length !== 6) {
    forgotError.value = 'Please enter the 6-digit OTP'
    return
  }
  forgotLoading.value = true
  forgotError.value = ''
  try {
    const { data } = await axiosClient.post('/forgot/verify-otp', {
      user_id: forgotUserId.value,
      otp: forgotOtp.value
    })
    if (data.success) {
      forgotStep.value = 'reset'
    } else {
      forgotError.value = data.message
    }
  } catch (e: any) {
    forgotError.value = e.response?.data?.message || 'Invalid OTP'
  } finally {
    forgotLoading.value = false
  }
}

async function resetPassword() {
  if (!forgotNewPassword.value || forgotNewPassword.value.length < 6) {
    forgotError.value = 'Password must be at least 6 characters'
    return
  }
  if (forgotNewPassword.value !== forgotConfirmPassword.value) {
    forgotError.value = 'Passwords do not match'
    return
  }
  forgotLoading.value = true
  forgotError.value = ''
  try {
    const { data } = await axiosClient.post('/forgot/reset-password', {
      user_id: forgotUserId.value,
      otp: forgotOtp.value,
      new_password: forgotNewPassword.value
    })
    if (data.success) {
      alert('Password reset successfully! Please login.')
      closeForgot()
    } else {
      forgotError.value = data.message
    }
  } catch (e: any) {
    forgotError.value = e.response?.data?.message || 'Failed to reset'
  } finally {
    forgotLoading.value = false
  }
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>