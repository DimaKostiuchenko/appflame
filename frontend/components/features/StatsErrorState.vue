<template>
  <div class="p-4 rounded bg-red-50 border border-red-200 text-red-800">
    <strong>Error:</strong>
    <p class="mt-2">{{ message }}</p>
  </div>
</template>

<script setup lang="ts">
interface Props {
  message: string
}

defineProps<Props>()
</script>
