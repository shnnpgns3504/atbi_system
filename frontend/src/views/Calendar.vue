<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-800">📅 Calendar</h1>

    <div class="bg-white rounded-xl shadow-md p-6">
      <vue-cal
        :events="calendarEvents"
        :disable-views="['years', 'year']"
        active-view="month"
        class="vuecal-custom"
        @event-click="onEventClick"
      />
    </div>

    <div v-if="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="selectedEvent = null">
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-2">{{ selectedEvent.title }}</h3>
        <p class="text-sm text-gray-500 mb-2">
          {{ formatDate(selectedEvent.start) }}
          <span v-if="selectedEvent.end && selectedEvent.start !== selectedEvent.end">
            → {{ formatDate(selectedEvent.end) }}
          </span>
        </p>
        <p class="text-gray-700">{{ selectedEvent.description || 'No description' }}</p>
        <button @click="selectedEvent = null" class="mt-4 w-full py-2 bg-gray-200 rounded-lg">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'
import axiosClient from '@/axios'

const events = ref<any[]>([])
const selectedEvent = ref<any>(null)

const calendarEvents = computed(() =>
  events.value.map(e => ({
    start: e.event_date,
    end: e.event_end_date || e.event_date,
    title: e.title,
    description: e.description,
    class: e.is_holiday ? 'event-holiday' : (e.event_type === 'announcement' ? 'event-announcement' : 'event-default')
  }))
)

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
}

function onEventClick(e: any) {
  selectedEvent.value = e
}

onMounted(async () => {
  try {
    const { data } = await axiosClient.get('/calendar')
    if (data.success) events.value = data.data
  } catch (e) { console.error(e) }
})
</script>

<style>
.vuecal-custom { font-family: inherit; }
.event-holiday { background: #ef4444 !important; color: white !important; border-radius: 4px; }
.event-announcement { background: #f59e0b !important; color: white !important; border-radius: 4px; }
.event-default { background: #10b981 !important; color: white !important; border-radius: 4px; }
</style>