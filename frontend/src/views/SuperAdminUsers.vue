<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800">👥 User Approvals & Management</h1>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-400">Loading users...</div>

    <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left text-sm text-gray-500">
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">ATBI & Contact</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="u in users" :key="u.user_id" class="hover:bg-gray-50">
              <td class="px-4 py-4">
                <p class="font-medium text-gray-800">{{ u.full_name }}</p>
                <p class="text-xs text-gray-500">{{ u.email }}</p>
                <p class="text-xs text-gray-400">@{{ u.username }}</p>
              </td>
              <td class="px-4 py-4 text-sm">
                <p>{{ u.atbi_affiliation }}</p>
                <p class="text-xs text-gray-500">{{ u.phone_number }}</p>
              </td>
              <td class="px-4 py-4">
                <span :class="['px-2 py-1 rounded text-xs font-semibold uppercase', getStatusClass(u.status)]">
                  {{ u.status }}
                </span>
              </td>
              <td class="px-4 py-4">
                <span class="text-sm font-medium">{{ formatRole(u.role) }}</span>
              </td>
              <td class="px-4 py-4 text-center">
                <div v-if="u.status === 'pending'" class="flex justify-center gap-2">
                  <button @click="promptApprove(u)" class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700">Approve</button>
                  <button @click="promptReject(u)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                </div>
                
                <div v-else-if="u.status === 'approved' && u.user_id !== auth.user?.user_id" class="flex justify-center gap-1">
                  <button v-if="u.role !== 'admin'" @click="promptChangeRole(u, 'admin')" class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">Make Admin</button>
                  <button v-if="u.role === 'admin'" @click="promptChangeRole(u, 'user')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Remove Admin</button>
                </div>

                <span v-else class="text-xs text-gray-400 italic">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <ConfirmationModal 
      :show="showModal"
      :title="modalConfig.title"
      :message="modalConfig.message"
      :confirmText="modalConfig.confirmText"
      :type="modalConfig.type"
      @confirm="executeAction"
      @cancel="closeModal"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue'

const auth = useAuthStore()
const users = ref<any[]>([])
const loading = ref(true)

// Modal State
const showModal = ref(false)
const modalConfig = reactive({
  title: '',
  message: '',
  confirmText: 'Confirm',
  type: 'warning',
  action: null as (() => void) | null
})

function getStatusClass(status: string) {
  if (status === 'approved') return 'bg-emerald-100 text-emerald-700'
  if (status === 'rejected') return 'bg-red-100 text-red-700'
  return 'bg-amber-100 text-amber-700'
}

function formatRole(role: string) {
  return role.charAt(0).toUpperCase() + role.slice(1)
}

async function loadUsers() {
  loading.value = true
  try {
    const { data } = await axiosClient.get('/users', { params: { requester_id: auth.user?.user_id } })
    if (data.success) users.value = data.data
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

function closeModal() {
  showModal.value = false
}

function executeAction() {
  if (modalConfig.action) modalConfig.action()
  closeModal()
}

// --- Trigger Modal Functions ---
function promptApprove(u: any) {
  modalConfig.title = 'Approve User'
  modalConfig.message = `Are you sure you want to approve ${u.full_name}? An email will be sent to them.`
  modalConfig.confirmText = 'Approve'
  modalConfig.type = 'success'
  modalConfig.action = async () => {
    await axiosClient.post('/users/approve', { requester_id: auth.user?.user_id, user_id: u.user_id })
    await loadUsers()
  }
  showModal.value = true
}

function promptReject(u: any) {
  modalConfig.title = 'Reject User'
  modalConfig.message = `Are you sure you want to reject ${u.full_name}?`
  modalConfig.confirmText = 'Reject'
  modalConfig.type = 'danger'
  modalConfig.action = async () => {
    await axiosClient.post('/users/reject', { requester_id: auth.user?.user_id, user_id: u.user_id })
    await loadUsers()
  }
  showModal.value = true
}

function promptChangeRole(u: any, newRole: string) {
  modalConfig.title = 'Change Role'
  modalConfig.message = `Change ${u.full_name}'s role to ${newRole}?`
  modalConfig.confirmText = 'Confirm'
  modalConfig.type = 'warning'
  modalConfig.action = async () => {
    await axiosClient.post('/users/assign-role', { requester_id: auth.user?.user_id, user_id: u.user_id, role: newRole })
    await loadUsers()
  }
  showModal.value = true
}

onMounted(loadUsers)
</script>