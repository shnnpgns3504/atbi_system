<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">🗓️ Manage Calendar</h1>
        <p class="text-gray-500 text-sm mt-1">Manage custom events and announcements</p>
      </div>
      <div class="flex gap-2">
        <RouterLink to="/admin/calendar/archive" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold text-sm flex items-center gap-2">
          📁 View Archive
        </RouterLink>
        <button @click="$router.push('/calendar')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm shadow-sm">
          + Add Event (on Calendar)
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm flex flex-wrap gap-4 items-center">
      <div class="flex-1 min-w-[200px]">
        <input v-model="searchQuery" type="text" placeholder="Search event title..." 
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" />
      </div>
      <select v-model="filterMonth" class="px-4 py-2 border rounded-lg bg-white">
        <option value="">All Months</option>
        <option v-for="(m, i) in months" :key="i" :value="i">{{ m }}</option>
      </select>
      <select v-model="filterYear" class="px-4 py-2 border rounded-lg bg-white">
        <option value="">All Years</option>
        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left text-sm text-gray-500">
              <th class="px-4 md:px-6 py-4">Date</th>
              <th class="px-4 md:px-6 py-4">Title</th>
              <th class="px-4 md:px-6 py-4">Type</th>
              <th class="px-4 md:px-6 py-4">Status</th>
              <th class="px-4 md:px-6 py-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="e in filteredEvents" :key="e.event_id" class="hover:bg-gray-50">
              <td class="px-4 md:px-6 py-4 text-sm whitespace-nowrap">{{ formatDate(e.event_date) }}</td>
              <td class="px-4 md:px-6 py-4 font-medium">{{ e.title }}</td>
              <td class="px-4 md:px-6 py-4">
                <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-xs font-semibold">
                  {{ e.event_type }}
                </span>
              </td>
              <td class="px-4 md:px-6 py-4">
                <span :class="['px-2 py-1 rounded text-xs font-semibold', getStatusClass(e.status)]">
                  {{ e.status }}
                </span>
              </td>
              <td class="px-4 md:px-6 py-4 text-center whitespace-nowrap">
                <button @click="openEdit(e)" class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3">Edit</button>
                <button @click="archiveEvent(e)" class="text-amber-600 hover:text-amber-800 text-sm font-medium">Archive</button>
              </td>
            </tr>
            <tr v-if="!filteredEvents.length">
              <td colspan="5" class="text-center py-12 text-gray-400">No custom events found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="closeModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Edit Event</h3>
        <form @submit.prevent="saveEvent" class="space-y-4">
          
          <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input v-model="form.title" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Type of Activity</label>
            <input v-model="form.event_type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium mb-1">Start Date</label>
              <input v-model="form.event_date" type="date" required class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">End Date (Optional)</label>
              <input v-model="form.event_end_date" type="date" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select v-model="form.status" class="w-full px-4 py-2 border rounded-lg bg-white">
              <option value="upcoming">Upcoming / Active</option>
              <option value="finished">Finished</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>

          <div class="flex gap-2 pt-2">
            <button type="button" @click="closeModal" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium">Cancel</button>
            <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium">Update Event</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const events = ref<any[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)

// Filters
const searchQuery = ref('')
const filterMonth = ref('')
const filterYear = ref('')

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
const currentYear = new Date().getFullYear()
const years = [currentYear, currentYear + 1, currentYear + 2]

const form = ref({
  title: '', description: '', event_date: '', event_end_date: '',
  event_type: 'Announcement', status: 'upcoming'
})

// Filtering Logic: EXCLUDE system holidays
const filteredEvents = computed(() => {
  return events.value.filter(e => {
    // 1. MUST NOT be a holiday
    if (e.is_holiday == 1 || e.event_type === 'holiday') return false;

    // 2. Search filter
    const matchesSearch = e.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    // 3. Date filters
    const dateObj = new Date(e.event_date)
    const matchesMonth = filterMonth.value !== '' ? dateObj.getMonth() === Number(filterMonth.value) : true
    const matchesYear = filterYear.value !== '' ? dateObj.getFullYear() === Number(filterYear.value) : true

    return matchesSearch && matchesMonth && matchesYear
  })
})

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function getStatusClass(status: string) {
  if (status === 'finished') return 'bg-gray-200 text-gray-700'
  if (status === 'cancelled') return 'bg-red-100 text-red-700'
  return 'bg-blue-100 text-blue-700' // upcoming
}

async function loadEvents() {
  try {
    const { data } = await axiosClient.get('/calendar')
    if (data.success) events.value = data.data
  } catch (e) { console.error(e) }
}

function openEdit(e: any) {
  editingId.value = e.event_id
  form.value = {
    title: e.title,
    description: e.description || '',
    event_date: e.event_date,
    event_end_date: e.event_end_date || '',
    event_type: e.event_type,
    status: e.status || 'upcoming'
  }
  showModal.value = true
}

function closeModal() { showModal.value = false }

async function saveEvent() {
  try {
    const payload = { ...form.value, user_id: auth.user?.user_id }
    const { data } = await axiosClient.post(`/calendar/update/${editingId.value}`, payload)
    if (data.success) {
      alert(data.message)
      closeModal()
      await loadEvents()
    }
  } catch (e) { console.error(e) }
}

async function archiveEvent(e: any) {
  if (!confirm(`Archive "${e.title}"? It will be moved to the Archive page.`)) return
  try {
    const { data } = await axiosClient.post(`/calendar/archive/${e.event_id}`)
    if (data.success) {
      await loadEvents()
    }
  } catch (e) { console.error(e) }
}

onMounted(loadEvents)
</script>