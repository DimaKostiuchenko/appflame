<template>
  <Teleport to="body">
    <div
      class="fixed top-4 right-4 z-[9999] pointer-events-none"
      aria-live="polite"
      aria-atomic="true"
    >
      <div class="pointer-events-auto">
        <UiToast
          v-for="toast in toasts"
          :key="toast.id"
          :id="toast.id"
          :type="toast.type"
          :title="toast.title"
          :message="toast.message"
          :duration="toast.duration"
          @dismiss="(id?: string) => dismiss(id || toast.id)"
        />
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
const { toasts, dismiss } = useToast()
</script>
