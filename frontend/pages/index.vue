<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 border-b-2 border-blue-600 pb-2 mb-8">
      Event Tracking Dashboard
    </h1>

    <FeaturesSessionCard :session-id="sessionId" :api-base="apiBase" :api-token="apiToken" />

    <FeaturesEventControls :loading="loading" @track="handleEvent" />

    <UiCard variant="default" padding="md" class="mb-6">
      <template #header>
        <FeaturesStatsHeader :is-polling="isPolling" :last-updated="lastUpdated" />
      </template>

      <FeaturesStatsLoadingState v-if="statsLoading && !stats" />
      <FeaturesStatsErrorState
        v-else-if="statsError && !stats"
        :message="statsError || 'Unknown error'"
      />

      <div v-else-if="stats" class="space-y-6">
        <FeaturesStatsGrid :counts="stats.counts" :total="stats.total" />

        <div class="text-center text-sm text-gray-600">
          Date: <span class="font-semibold">{{ formatDate(stats.date) }}</span>
        </div>
      </div>
    </UiCard>
  </div>
</template>

<script setup lang="ts">
import { formatDate } from '~/utils/date'

definePageMeta({
  layout: 'default',
})

const { sessionId, handleEvent, loading } = useEventActions()
const {
  stats,
  loading: statsLoading,
  error: statsError,
  isPolling,
  lastUpdated,
} = useStatsPolling()
const config = useRuntimeConfig()

const apiBase = config.public.apiBase
const apiToken = config.public.apiToken
</script>
