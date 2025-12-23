<template>
  <UiCard variant="default" padding="md" class="mb-6">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Event Tracking</h2>
    <div class="flex flex-wrap gap-4">
      <UiButton @click="$emit('track', 'page_view')" :disabled="loading"> Page View </UiButton>
      <UiButton @click="$emit('track', 'cta_click')" :disabled="loading"> CTA Click </UiButton>
      <UiButton @click="$emit('track', 'form_submit')" :disabled="loading"> Form Submit </UiButton>
    </div>
  </UiCard>
</template>

<script setup lang="ts">
interface Props {
  loading?: boolean
}

defineProps<Props>()

defineEmits<{
  track: [type: 'page_view' | 'cta_click' | 'form_submit']
}>()
</script>
