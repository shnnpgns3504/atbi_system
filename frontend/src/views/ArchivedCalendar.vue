<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📁 Calendar Archive</h1>
        <p class="text-gray-500 text-sm mt-1">View and manage archived events</p>
      </div>
      <RouterLink to="/admin/calendar" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm transition-colors">
        ← Back to Calendar
      </RouterLink>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left text-sm text-gray-500">
              <th class="px-4 md:px-6 py-4">Date</th>
              <th class="px-4 md:px-6 py-4">Title</th>
              <th class="px-4 md:px-6 py-4">Type</th>
              <th class="px-4 md:px-6 py-4">Archived Date</th>
              <th class="px-4 md:px-6 py-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="e in events" :key="e.event_id" class="hover:bg-gray-50">
              <td class="px-4 md:px-6 py-4 text-sm whitespace-nowrap">{{ formatDate(e.event_date) }}</td>
              <td class="px-4 md:px-6 py-4 font-medium text-gray-800">{{ e.title }}</td>
              <td class="px-4 md:px-6 py-4 text-sm text-gray-600">{{ e.event_type }}</td>
              <td class="px-4 md:px-6 py-4 text-xs text-gray-400">-</td>
              <td class="px-4 md:px-6 py-4 text-center whitespace-nowrap">
                <button @click="restoreEvent(e)" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium mr-3">Restore</button>
                <button @click="deletePermanent(e)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete Permanently</button>
              </td>
            </tr>
            <tr v-if="!events.length">
              <td colspan="5" class="text-center py-12 text-gray-400">Archive is empty</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axiosClient from '@/axios'

const events = ref<any[]>([])

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function loadArchived() {
  try {
    const { data } = await axiosClient.get('/calendar/archived')
    if (data.success) events.value = data.data
  } catch (e) { console.error(e) }
}

async function restoreEvent(e: any) {
  if (!confirm(`Restore "${e.title}" back to the calendar?`)) return
  try {
    const { data } = await axiosClient.post(`/calendar/restore/${e.event_id}`)
    if (data.success) {
      await loadArchived()
    }
  } catch (e) { console.error(e) }
}

async function deletePermanent(e: any) {
  if (!confirm(`⚠️ WARNING: Permanently delete "${e.title}"? This action CANNOT be undone.`)) return
  try {
    const { data } = await axiosClient.post(`/calendar/delete/${e.event_id}`)
    if (data.success) {
      await loadArchived()
    }
  } catch (e) { console.error(e) }
}

onMounted(loadArchived)
</script>