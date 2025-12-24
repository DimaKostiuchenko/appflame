<template>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <div class="text-sm text-blue-700 font-medium mb-1">Page Views</div>
      <div class="text-2xl font-bold text-blue-900">{{ counts.page_view }}</div>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
      <div class="text-sm text-green-700 font-medium mb-1">CTA Clicks</div>
      <div class="text-2xl font-bold text-green-900">{{ counts.cta_click }}</div>
    </div>
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
      <div class="text-sm text-orange-700 font-medium mb-1">Form Submits</div>
      <div class="text-2xl font-bold text-orange-900">{{ counts.form_submit }}</div>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
      <div class="text-sm text-purple-700 font-medium mb-1">Total</div>
      <div class="text-2xl font-bold text-purple-900">{{ total }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  counts?: {
    page_view: number
    cta_click: number
    form_submit: number
  }
  total?: number
}

const props = withDefaults(defineProps<Props>(), {
  counts: () => ({
    page_view: 0,
    cta_click: 0,
    form_submit: 0,
  }),
  total: 0,
})
</script>
