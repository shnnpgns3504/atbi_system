<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-800">🗓️ Manage Calendar</h1>
      <button @click="openAdd" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">
        + Add Event
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b">
          <tr class="text-left text-sm text-gray-500">
            <th class="px-6 py-4">Date</th>
            <th class="px-6 py-4">Title</th>
            <th class="px-6 py-4">Type</th>
            <th class="px-6 py-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="e in events" :key="e.event_id">
            <td class="px-6 py-4 text-sm">{{ formatDate(e.event_date) }}</td>
            <td class="px-6 py-4 font-medium">{{ e.title }}</td>
            <td class="px-6 py-4">
              <span :class="['px-2 py-1 rounded text-xs font-semibold', getTypeClass(e)]">
                {{ e.is_holiday ? 'Holiday' : e.event_type }}
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button @click="openEdit(e)" class="text-blue-600 mr-3">Edit</button>
              <button @click="deleteEvent(e)" class="text-red-600">Delete</button>
            </td>
          </tr>
          <tr v-if="!events.length">
            <td colspan="4" class="text-center py-12 text-gray-400">No events yet</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
      @click.self="closeModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">{{ editing ? 'Edit' : 'Add' }} Event</h3>
        <form @submit.prevent="saveEvent" class="space-y-3">
          <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input v-model="form.title" required class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium mb-1">Start Date</label>
              <input v-model="form.event_date" type="date" required class="w-full px-4 py-2 border rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">End Date</label>
              <input v-model="form.event_end_date" type="date" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <select v-model="form.event_type" class="w-full px-4 py-2 border rounded-lg">
              <option value="event">Event</option>
              <option value="announcement">Announcement</option>
              <option value="holiday">Holiday</option>
            </select>
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
import { ref, onMounted } from 'vue'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const events = ref<any[]>([])
const showModal = ref(false)
const editing = ref(false)
const editingId = ref<number | null>(null)

const form = ref({
  title: '', description: '', event_date: '', event_end_date: '',
  event_type: 'event', is_holiday: 0
})

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function getTypeClass(e: any) {
  if (e.is_holiday || e.event_type === 'holiday') return 'bg-red-100 text-red-700'
  if (e.event_type === 'announcement') return 'bg-amber-100 text-amber-700'
  return 'bg-emerald-100 text-emerald-700'
}

async function loadEvents() {
  try {
    const { data } = await axiosClient.get('/calendar')
    if (data.success) events.value = data.data
  } catch (e) { console.error(e) }
}

function openAdd() {
  editing.value = false
  editingId.value = null
  form.value = { title: '', description: '', event_date: '', event_end_date: '', event_type: 'event', is_holiday: 0 }
  showModal.value = true
}

function openEdit(e: any) {
  editing.value = true
  editingId.value = e.event_id
  form.value = {
    title: e.title,
    description: e.description || '',
    event_date: e.event_date,
    event_end_date: e.event_end_date || '',
    event_type: e.event_type,
    is_holiday: e.is_holiday ? 1 : 0
  }
  showModal.value = true
}

function closeModal() { showModal.value = false }

async function saveEvent() {
  try {
    const payload = { ...form.value, user_id: auth.user?.user_id }
    const url = editing.value ? `/calendar/update/${editingId.value}` : '/calendar/create'
    const { data } = await axiosClient.post(url, payload)
    if (data.success) {
      alert(data.message)
      closeModal()
      await loadEvents()
    }
  } catch (e) { console.error(e) }
}

async function deleteEvent(e: any) {
  if (!confirm(`Delete "${e.title}"?`)) return
  try {
    const { data } = await axiosClient.post(`/calendar/delete/${e.event_id}`)
    if (data.success) {
      alert(data.message)
      await loadEvents()
    }
  } catch (e) { console.error(e) }
}

onMounted(loadEvents)
</script>