<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">👑 User Management</h1>
        <p class="text-gray-500 text-sm mt-1">Assign roles to users</p>
      </div>
      <button @click="openAddUser" 
        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white 
               rounded-lg font-semibold text-sm">
        + Create User
      </button>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading users...</div>

    <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[700px]">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left text-sm text-gray-500">
              <th class="px-4 md:px-6 py-3">Name</th>
              <th class="px-4 md:px-6 py-3">Username</th>
              <th class="px-4 md:px-6 py-3">Email</th>
              <th class="px-4 md:px-6 py-3">Role</th>
              <th class="px-4 md:px-6 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="u in users" :key="u.user_id" class="hover:bg-gray-50">
              <td class="px-4 md:px-6 py-4 font-medium">{{ u.full_name }}</td>
              <td class="px-4 md:px-6 py-4 text-sm text-gray-600">{{ u.username }}</td>
              <td class="px-4 md:px-6 py-4 text-sm text-gray-600">{{ u.email }}</td>
              <td class="px-4 md:px-6 py-4">
                <span :class="['px-2 py-1 rounded text-xs font-semibold', getRoleClass(u.role)]">
                  {{ formatRole(u.role) }}
                </span>
              </td>
              <td class="px-4 md:px-6 py-4 text-center">
                <div v-if="u.role !== 'super_admin'" class="flex justify-center gap-1 flex-wrap">
                  <button v-if="u.role !== 'admin'" @click="changeRole(u, 'admin')"
                    class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                    Make Admin
                  </button>
                  <button v-if="u.role === 'admin'" @click="changeRole(u, 'user')"
                    class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    Remove Admin
                  </button>
                </div>
                <span v-else class="text-xs text-amber-600 font-medium">— Protected —</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showAddUser" 
      class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
      @click.self="showAddUser = false">
      <div class="bg-white rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Create New User</h3>
        <form @submit.prevent="createUser" class="space-y-3">
          <input v-model="newUser.full_name" placeholder="Full name" required
            class="w-full px-4 py-2.5 border rounded-lg" />
          <input v-model="newUser.username" placeholder="Username" required
            class="w-full px-4 py-2.5 border rounded-lg" />
          <input v-model="newUser.email" type="email" placeholder="Email" required
            class="w-full px-4 py-2.5 border rounded-lg" />
          <input v-model="newUser.password" type="password" placeholder="Password (6+ chars)" required
            class="w-full px-4 py-2.5 border rounded-lg" />
          <select v-model="newUser.role" class="w-full px-4 py-2.5 border rounded-lg">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
          <div v-if="createError" class="p-3 bg-red-50 border border-red-200 
            rounded-lg text-red-700 text-sm">{{ createError }}</div>
          <div class="flex gap-2 pt-2">
            <button type="button" @click="showAddUser = false"
              class="flex-1 py-2.5 bg-gray-100 rounded-lg font-medium">Cancel</button>
            <button type="submit" :disabled="creating"
              class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg font-medium 
                     disabled:opacity-50">
              {{ creating ? 'Creating...' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const users = ref<any[]>([])
const loading = ref(true)
const showAddUser = ref(false)
const creating = ref(false)
const createError = ref('')

const newUser = reactive({
  full_name: '', username: '', email: '', password: '', role: 'user'
})

function formatRole(role: string) {
  return role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

function getRoleClass(role: string) {
  if (role === 'super_admin') return 'bg-amber-100 text-amber-700'
  if (role === 'admin') return 'bg-blue-100 text-blue-700'
  return 'bg-gray-100 text-gray-700'
}

async function loadUsers() {
  loading.value = true
  try {
    const { data } = await axiosClient.get('/users', {
      params: { requester_id: auth.user?.user_id }
    })
    if (data.success) users.value = data.data
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

async function changeRole(u: any, newRole: string) {
  if (!confirm(`Change ${u.full_name}'s role to ${newRole}?`)) return
  try {
    const { data } = await axiosClient.post('/users/assign-role', {
      requester_id: auth.user?.user_id,
      user_id: u.user_id,
      role: newRole
    })
    if (data.success) {
      alert(data.message)
      await loadUsers()
    } else {
      alert(data.message)
    }
  } catch (e: any) {
    alert(e.response?.data?.message || 'Failed to change role')
  }
}

function openAddUser() {
  newUser.full_name = ''
  newUser.username = ''
  newUser.email = ''
  newUser.password = ''
  newUser.role = 'user'
  createError.value = ''
  showAddUser.value = true
}

async function createUser() {
  creating.value = true
  createError.value = ''
  try {
    const { data } = await axiosClient.post('/users/create', {
      ...newUser,
      requester_id: auth.user?.user_id
    })
    if (data.success) {
      alert(data.message)
      showAddUser.value = false
      await loadUsers()
    } else {
      createError.value = data.message || 'Failed to create user'
    }
  } catch (e: any) {
    createError.value = e.response?.data?.message || 'Failed to create user'
  } finally {
    creating.value = false
  }
}

onMounted(loadUsers)
</script>