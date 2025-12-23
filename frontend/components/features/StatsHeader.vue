<template>
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold text-gray-700">Today's Statistics</h2>
    <div class="flex items-center gap-2">
      <div v-if="isPolling" class="flex items-center gap-2 text-sm text-gray-600">
        <span class="relative flex h-2 w-2">
          <span
            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"
          ></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
        </span>
        <span>Live</span>
      </div>
      <div v-if="lastUpdated" class="text-xs text-gray-500">
        Updated: {{ formatTime(lastUpdated) }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { formatTime } from '~/utils/date'

interface Props {
  isPolling?: boolean
  lastUpdated?: Date | null
}

defineProps<Props>()
</script>
