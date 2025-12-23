<template>
  <UiCard variant="outlined" padding="md" class="mb-6">
    <template #header>
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Session Information</h2>
    </template>
    <div class="space-y-2">
      <p class="font-mono text-sm">
        <strong class="text-gray-700">Session ID:</strong>
        <span class="text-gray-900">{{ sessionId || 'Not available' }}</span>
      </p>
      <p class="font-mono text-sm">
        <strong class="text-gray-700">API Base URL:</strong>
        <span class="text-gray-900">{{ apiBase }}</span>
      </p>
      <p class="font-mono text-sm">
        <strong class="text-gray-700">API Token:</strong>
        <span class="text-gray-900">{{ apiToken ? '***' + apiToken.slice(-4) : 'Not set' }}</span>
      </p>
    </div>
  </UiCard>
</template>

<script setup lang="ts">
interface Props {
  sessionId?: string
  apiBase: string
  apiToken?: string
}

defineProps<Props>()
</script>
