<template>
  <div class="min-h-screen w-full flex flex-col md:flex-row overflow-hidden bg-gray-50">
    
    <!-- LEFT: Brand Logos Panel (Animated Background) -->
    <div
      class="relative animated-gradient flex flex-col items-center justify-center p-8 md:p-12 overflow-hidden min-h-[40vh] md:min-h-screen md:w-1/2 shadow-2xl z-10"
    >
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>

      <div class="relative z-10 text-center max-w-md w-full flex flex-col items-center">
        <img 
          src="/logos/main.png" 
          alt="Intertrade Logo" 
          class="w-48 h-48 md:w-64 md:h-64 object-cover rounded-full bg-white p-2 mb-8 drop-shadow-2xl border-4 border-white/20" 
        />
        <h1 class="font-playfair text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-3 tracking-tight drop-shadow-md text-center">
          ATBI Intertrade System
        </h1>
        <p class="text-white/90 text-sm md:text-base mb-10 drop-shadow text-center">
          Agribased Technology Business Incubator
        </p>

        <div class="grid grid-cols-3 gap-3 md:gap-5 w-full">
          <div 
            v-for="(atbi, i) in atbiLogos" 
            :key="i" 
            class="bg-white/95 backdrop-blur rounded-2xl p-3 md:p-4 shadow-xl flex items-center justify-center aspect-square hover:scale-105 transition-transform duration-300"
          >
            <img :src="atbi.logo" :alt="atbi.name" class="w-full h-full object-contain" />
          </div>
        </div>
        
        <p class="mt-12 text-white/70 text-xs md:text-sm">
          &copy; 2026 ATBI Intertrade System. All rights reserved.
        </p>
      </div>
    </div>

    <!-- RIGHT: Forms Container -->
    <div class="flex-1 flex items-center justify-center p-4 md:p-8 md:w-1/2 relative bg-gray-50">
      
      <!-- THE FORM BOX WITH GRADIENT BORDER -->
      <div class="w-full max-w-md bg-gradient-to-br from-emerald-400 via-teal-500 to-blue-600 p-[2px] rounded-2xl shadow-2xl">
        <div class="bg-white rounded-2xl p-8 h-full relative overflow-hidden">
          
          <transition name="slide-fade" mode="out-in">
            
            <!-- LOGIN FORM -->
            <div v-if="!isRegistering" key="login" class="w-full">
              <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gray-800">Welcome Back</h2>
                <p class="text-gray-500 mt-2">Sign in to continue to your account</p>
              </div>

              <form @submit.prevent="handleLogin" class="space-y-5">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                  <input 
                    v-model="username" 
                    type="text" 
                    required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
                    placeholder="Enter your username" 
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <input 
                      v-model="password" 
                      :type="showPassword ? 'text' : 'password'" 
                      required 
                      class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
                      placeholder="Enter your password" 
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                      <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                  </div>
                </div>

                <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm font-medium">
                  {{ error }}
                </div>

                <button type="submit" :disabled="loading" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 mt-4">
                  {{ loading ? 'Signing in...' : 'Sign In' }}
                </button>
                
                <div class="flex justify-between items-center pt-2">
                  <button type="button" @click="openForgotModal" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:underline">Forgot Password?</button>
                  <button type="button" @click="isRegistering = true" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline">Create Account →</button>
                </div>
              </form>
            </div>

            <!-- REGISTER FORM -->
            <div v-else key="register" class="w-full">
              <div class="text-center mb-6">
                <h2 class="text-2xl font-extrabold text-gray-800">Create Account</h2>
                <p class="text-gray-500 mt-1 text-xs">Wait for admin approval after registering</p>
              </div>

              <form @submit.prevent="handleRegister" class="space-y-4">
                
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                  <input v-model="regForm.username" type="text" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" placeholder="e.g. juan123" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input v-model="regForm.first_name" type="text" required @input="regForm.first_name = $event.target.value.replace(/[^a-zA-Z\s.]/g, '')" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Juan" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
                    <input v-model="regForm.middle_name" type="text" @input="regForm.middle_name = $event.target.value.replace(/[^a-zA-Z\s.]/g, '')" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Santos" />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input v-model="regForm.last_name" type="text" required @input="regForm.last_name = $event.target.value.replace(/[^a-zA-Z\s.]/g, '')" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Dela Cruz" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ext Name</label>
                    <input v-model="regForm.extension_name" type="text" @input="regForm.extension_name = $event.target.value.replace(/[^a-zA-Z\s.]/g, '')" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Jr., III" />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1">Email (Gmail) <span class="text-red-500">*</span></label>
                  <input 
                    v-model="regForm.email" 
                    type="email" 
                    required 
                    class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all"
                    :class="emailError ? 'border-red-400 focus:border-red-400' : 'border-gray-300'"
                    placeholder="juan@gmail.com" 
                  />
                  <p v-if="emailError" class="text-xs text-red-500 mt-1 font-medium">{{ emailError }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ATBI Affiliation <span class="text-red-500">*</span></label>
                    <input v-model="regForm.atbi_affiliation" type="text" required @input="regForm.atbi_affiliation = $event.target.value.replace(/[^a-zA-Z\s.]/g, '')" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="ATBI 1" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input 
                      v-model="regForm.phone_number" 
                      type="text" 
                      required 
                      @input="regForm.phone_number = $event.target.value.replace(/\D/g, '').slice(0, 11)" 
                      class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all"
                      :class="phoneError ? 'border-red-400 focus:border-red-400' : 'border-gray-300'"
                      placeholder="09123456789" 
                    />
                    <p v-if="phoneError" class="text-xs text-red-500 mt-1 font-medium">{{ phoneError }}</p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <input v-model="regForm.password" :type="showRegPassword ? 'text' : 'password'" required class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Create a password" />
                    <button type="button" @click="showRegPassword = !showRegPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                      <svg v-if="!showRegPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                  </div>
                  <div class="mt-2 space-y-1">
                    <p class="text-xs font-medium" :class="hasLength ? 'text-emerald-600' : 'text-gray-400'">✓ At least 8 characters</p>
                    <p class="text-xs font-medium" :class="hasUppercase ? 'text-emerald-600' : 'text-gray-400'">✓ At least 1 uppercase letter</p>
                    <p class="text-xs font-medium" :class="hasLowercase ? 'text-emerald-600' : 'text-gray-400'">✓ At least 1 lowercase letter</p>
                    <p class="text-xs font-medium" :class="hasNumber ? 'text-emerald-600' : 'text-gray-400'">✓ At least 1 number</p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <input v-model="regForm.confirm_password" :type="showRegConfirm ? 'text' : 'password'" required class="w-full px-4 py-2.5 pr-10 border rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all" :class="passwordsMismatch ? 'border-red-400' : 'border-gray-300'" placeholder="Re-type your password" />
                    <button type="button" @click="showRegConfirm = !showRegConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                      <svg v-if="!showRegConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                  </div>
                  <p v-if="passwordsMismatch" class="text-xs text-red-500 mt-1 font-medium">Passwords do not match</p>
                </div>

                <div v-if="regError" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm font-medium">
                  {{ regError }}
                </div>

                <button type="submit" :disabled="regLoading || passwordsMismatch || !!emailError || !!phoneError" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all mt-2 disabled:opacity-50">
                  {{ regLoading ? 'Submitting...' : 'Register' }}
                </button>

                <div class="text-center pt-2 pb-2">
                  <button type="button" @click="isRegistering = false" class="text-sm font-bold text-gray-600 hover:text-gray-800 hover:underline">← Back to Login</button>
                </div>
              </form>
            </div>

          </transition>
        </div>
      </div>
    </div>

    <!-- Registration Success Modal -->
    <transition name="fade">
      <div v-if="showSuccessModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-8 shadow-2xl text-center">
          <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 mb-5">
            <span class="text-4xl">✅</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Registration Submitted!</h3>
          <p class="text-sm text-gray-500 mb-8 leading-relaxed">Your account is now pending. Please wait for the admin to approve your registration.</p>
          <button @click="showSuccessModal = false; isRegistering = false" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md transition-colors">Got it</button>
        </div>
      </div>
    </transition>

    <!-- Login Success Modal -->
    <transition name="fade">
      <div v-if="showLoginSuccessModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-8 shadow-2xl text-center">
          <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 mb-5 animate-bounce-slow">
            <span class="text-4xl">👋</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Welcome Back!</h3>
          <p class="text-sm text-gray-500 mb-8 leading-relaxed">
            You have successfully signed in as <span class="font-bold text-emerald-600">{{ loggedInName }}</span>. Redirecting to your dashboard...
          </p>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-emerald-500 h-2 rounded-full animate-progress"></div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axiosClient from '@/axios'

const router = useRouter()
const auth = useAuthStore()

const isRegistering = ref(false)
const showPassword = ref(false)
const showRegPassword = ref(false)
const showRegConfirm = ref(false)
const loading = ref(false)
const error = ref('')

// LOGIN
const username = ref('')
const password = ref('')

// REGISTER
const regLoading = ref(false)
const regError = ref('')
const showSuccessModal = ref(false)

// LOGIN SUCCESS MODAL
const showLoginSuccessModal = ref(false)
const loggedInName = ref('')

const regForm = reactive({
  username: '', first_name: '', middle_name: '', last_name: '', extension_name: '',
  email: '', atbi_affiliation: '', phone_number: '', password: '', confirm_password: ''
})

// Live Password Rules
const hasLength = computed(() => regForm.password.length >= 8)
const hasUppercase = computed(() => /[A-Z]/.test(regForm.password))
const hasLowercase = computed(() => /[a-z]/.test(regForm.password))
const hasNumber = computed(() => /\d/.test(regForm.password))
const passwordsMismatch = computed(() => regForm.confirm_password !== '' && regForm.password !== regForm.confirm_password)

// Live Validation
const emailError = computed(() => {
  if (!regForm.email) return ''
  if (!regForm.email.toLowerCase().endsWith('@gmail.com')) return 'Must be a valid @gmail.com address.'
  return ''
})

const phoneError = computed(() => {
  if (!regForm.phone_number) return ''
  if (regForm.phone_number.length !== 11) return 'Must be exactly 11 digits.'
  return ''
})

const atbiLogos = [
  { name: 'BSU', logo: '/logos/bsu.png' },
  { name: 'CLSU', logo: '/logos/clsu.png' },
  { name: 'DMMMSU', logo: '/logos/dmmmsu.png' },
  { name: 'ISU', logo: '/logos/isu.png' },
  { name: 'MMSU', logo: '/logos/mmsu.png' },
  { name: 'PSU', logo: '/logos/psu.png' }
]

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    const data = await auth.login(username.value, password.value)
    if (data.success) {
      await auth.loadCartCount()
      
      // Extract first name for the welcome message
      loggedInName.value = data.user.full_name.split(' ')[0] || data.user.username
      
      // Show the success modal
      showLoginSuccessModal.value = true
      
      // Wait 1.5 seconds before redirecting
      setTimeout(() => {
        showLoginSuccessModal.value = false
        router.push('/home')
      }, 1500)

    } else {
      error.value = data.message || 'Invalid credentials'
    }
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}

async function handleRegister() {
  regLoading.value = true
  regError.value = ''
  
  if (emailError.value || phoneError.value) {
    regLoading.value = false
    return
  }

  try {
    const { data } = await axiosClient.post('/auth/register', regForm)
    if (data.success) {
      showSuccessModal.value = true
      Object.assign(regForm, { username: '', first_name: '', middle_name: '', last_name: '', extension_name: '', email: '', atbi_affiliation: '', phone_number: '', password: '', confirm_password: '' })
    } else {
      regError.value = Object.values(data.errors || {})[0] || data.message
    }
  } catch (e: any) {
    regError.value = Object.values(e.response?.data?.errors || {})[0] || e.response?.data?.message || 'Registration failed'
  } finally {
    regLoading.value = false
  }
}

const showForgotModal = ref(false)
function openForgotModal() { showForgotModal.value = true }
</script>

<style scoped>
/* Slide Fade Transition for Forms */
.slide-fade-enter-active {
  transition: all 0.4s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter-from {
  transform: translateX(20px);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}

.animated-gradient { 
  background: linear-gradient(-45deg, #064e3b, #047857, #0ea5e9, #0369a1); 
  background-size: 400% 400%; 
  animation: gradientShift 12s ease infinite; 
}
@keyframes gradientShift { 
  0% { background-position: 0% 50%; } 
  50% { background-position: 100% 50%; } 
  100% { background-position: 0% 50%; } 
}

.font-playfair { font-family: 'Playfair Display', serif; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Animations for the Login Success Modal */
.animate-bounce-slow {
  animation: bounce 1.5s infinite;
}
@keyframes bounce {
  0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
  50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}

.animate-progress {
  width: 0%;
  animation: load 1.5s ease-in-out forwards;
}
@keyframes load {
  0% { width: 0%; }
  100% { width: 100%; }
}
</style>