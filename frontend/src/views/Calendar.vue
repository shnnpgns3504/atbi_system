<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">📅 Academic Calendar</h1>
        <p class="text-gray-500 mt-1">
          {{ auth.user?.role === 'admin' ? 'Click on a date to add an event' : 'View upcoming events and announcements' }}
        </p>
      </div>
    </div>

    <!-- Large Calendar Container -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 min-h-[600px]">
      <vue-cal
        :events="calendarEvents"
        :disable-views="['years', 'year']"
        active-view="month"
        class="vuecal-custom large-calendar"
        :min-date="minDate"
        :max-date="maxDate"
        @event-click="onEventClick"
        @cell-click="onDayClick"
      />
    </div>

    <!-- View Event Modal -->
    <div v-if="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="selectedEvent = null">
      <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-start justify-between mb-4">
          <h3 class="text-xl font-bold text-gray-800">{{ selectedEvent.title }}</h3>
          <span :class="['px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider', getBadgeClass(selectedEvent.type)]">
            {{ selectedEvent.type }}
          </span>
        </div>
        <p class="text-sm font-medium text-emerald-600 mb-4">
          <font-awesome-icon :icon="['fas', 'calendar-day']" class="mr-2" />
          {{ formatDate(selectedEvent.start) }}
          <span v-if="selectedEvent.end && selectedEvent.start !== selectedEvent.end">
            → {{ formatDate(selectedEvent.end) }}
          </span>
        </p>
        <p class="text-gray-600 leading-relaxed">{{ selectedEvent.description || 'No description provided.' }}</p>
        
        <div v-if="selectedEvent.status !== 'upcoming'" class="mt-4 text-sm font-semibold">
          Status: <span :class="selectedEvent.status === 'finished' ? 'text-gray-500' : 'text-red-500'">{{ selectedEvent.status.toUpperCase() }}</span>
        </div>

        <button @click="selectedEvent = null" class="mt-6 w-full py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
          Close
        </button>
      </div>
    </div>

    <!-- Add Event Modal (Opens when admin clicks a date) -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="closeAddModal">
      <div class="bg-white rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Add Event for {{ formatDate(form.event_date) }}</h3>
        <form @submit.prevent="saveNewEvent" class="space-y-4">
          
          <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input v-model="form.title" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Type of Activity</label>
            <input v-model="form.event_type" required placeholder="e.g., Announcement, Meeting, Activity" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" />
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
            <button type="button" @click="closeAddModal" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium">Cancel</button>
            <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium">Save Event</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'
import axiosClient from '@/axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const events = ref<any[]>([])
const selectedEvent = ref<any>(null)
const showAddModal = ref(false)

const form = ref({
  title: '', description: '', event_date: '', event_end_date: '',
  event_type: '', status: 'upcoming'
})

// Calendar limits: Exactly 1 year ahead of today
const today = new Date()
const minDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0]
const nextYear = new Date()
nextYear.setFullYear(today.getFullYear() + 1)
const maxDate = nextYear.toISOString().split('T')[0]

const calendarEvents = computed(() =>
  events.value.map(e => ({
    start: e.event_date,
    end: e.event_end_date || e.event_date,
    title: e.title,
    description: e.description,
    type: e.event_type,
    status: e.status,
    class: getEventClass(e)
  }))
)

function getEventClass(e: any) {
  if (e.is_holiday) return 'event-holiday'
  if (e.status === 'cancelled') return 'event-cancelled'
  if (e.status === 'finished') return 'event-finished'
  if (e.event_type.toLowerCase() === 'announcement') return 'event-announcement'
  return 'event-default'
}

function getBadgeClass(type: string) {
  const t = type?.toLowerCase() || ''
  if (t === 'holiday') return 'bg-red-100 text-red-700'
  if (t === 'announcement') return 'bg-amber-100 text-amber-700'
  return 'bg-emerald-100 text-emerald-700'
}

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
}

function onEventClick(e: any) {
  selectedEvent.value = e
}

// Click a day to add event (Admin only)
function onDayClick(date: string) {
  if (auth.user?.role !== 'admin') return // Ignore for regular users
  
  form.value = {
    title: '', description: '', event_date: date, event_end_date: '',
    event_type: 'Announcement', status: 'upcoming'
  }
  showAddModal.value = true
}

function closeAddModal() {
  showAddModal.value = false
}

async function saveNewEvent() {
  try {
    const payload = { ...form.value, user_id: auth.user?.user_id }
    const { data } = await axiosClient.post('/calendar/create', payload)
    if (data.success) {
      alert(data.message)
      closeAddModal()
      await loadEvents() // Refresh calendar
    }
  } catch (e) { console.error(e) }
}

async function loadEvents() {
  try {
    const { data } = await axiosClient.get('/calendar')
    if (data.success) events.value = data.data
  } catch (e) { console.error(e) }
}

onMounted(loadEvents)
</script>

<style>
.large-calendar {
  min-height: 600px;
  font-family: inherit;
}
.large-calendar .vuecal__cell-content {
  padding: 8px;
  cursor: pointer;
}

/* Event Color Classes */
.event-holiday { background: #ef4444 !important; color: white !important; border-radius: 4px; }
.event-announcement { background: #f59e0b !important; color: white !important; border-radius: 4px; }
.event-default { background: #10b981 !important; color: white !important; border-radius: 4px; }
.event-cancelled { background: #9ca3af !important; color: white !important; border-radius: 4px; text-decoration: line-through; }
.event-finished { background: #6b7280 !important; color: white !important; border-radius: 4px; opacity: 0.7; }
</style>