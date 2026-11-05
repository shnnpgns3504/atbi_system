<template>
  <teleport to="body">
    <transition name="fade">
      <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl animate-scale-in">
          <div class="text-center">
            <div :class="['mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4', iconBgClass]">
              <span class="text-2xl">{{ icon }}</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ title }}</h3>
            <p class="text-sm text-gray-500 mb-6">{{ message }}</p>
            
            <div class="flex gap-3">
              <button @click="$emit('cancel')" 
                class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                Cancel
              </button>
              <button @click="$emit('confirm')" 
                :class="['flex-1 py-2.5 text-white rounded-lg font-medium transition-colors', btnClass]">
                {{ confirmText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  show: Boolean,
  title: { type: String, default: 'Are you sure?' },
  message: { type: String, default: 'This action cannot be undone.' },
  confirmText: { type: String, default: 'Confirm' },
  type: { type: String, default: 'warning' } // warning, danger, success
})

defineEmits(['confirm', 'cancel'])

const icon = computed(() => {
  if (props.type === 'danger') return '🚨'
  if (props.type === 'success') return '✅'
  return '⚠️'
})

const iconBgClass = computed(() => {
  if (props.type === 'danger') return 'bg-red-100 text-red-600'
  if (props.type === 'success') return 'bg-emerald-100 text-emerald-600'
  return 'bg-amber-100 text-amber-600'
})

const btnClass = computed(() => {
  if (props.type === 'danger') return 'bg-red-600 hover:bg-red-700'
  if (props.type === 'success') return 'bg-emerald-600 hover:bg-emerald-700'
  return 'bg-amber-600 hover:bg-amber-700'
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.animate-scale-in { animation: scaleIn 0.2s ease-out; }
@keyframes scaleIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>