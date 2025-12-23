<template>
  <Teleport to="body">
    <Transition
      name="toast"
      enter-active-class="transition ease-out duration-300"
      enter-from-class="transform translate-x-full opacity-0"
      enter-to-class="transform translate-x-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="transform translate-x-0 opacity-100"
      leave-to-class="transform translate-x-full opacity-0"
    >
      <div
        v-if="visible"
        role="alert"
        :class="[toastClasses, 'fixed top-4 right-4 z-[9999]']"
        class="min-w-[320px] max-w-md shadow-2xl rounded-lg p-4 mb-3 flex items-start gap-3 overflow-hidden"
        @mouseenter="pauseTimer"
        @mouseleave="resumeTimer"
      >
        <div :class="iconClasses" class="flex-shrink-0 mt-0.5">
          <svg
            v-if="props.type === 'success'"
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
          <svg
            v-else-if="props.type === 'error'"
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </div>

        <div class="flex-1 min-w-0">
          <h4
            v-if="props.title"
            :class="titleClasses"
            class="font-semibold text-sm mb-1 leading-tight"
          >
            {{ props.title }}
          </h4>
          <p v-if="messageText" :class="messageClasses" class="text-sm leading-relaxed">
            {{ messageText }}
          </p>
        </div>

        <button
          @click="dismiss"
          class="opacity-50 hover:opacity-100 transition-opacity flex-shrink-0"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>

        <div
          v-if="duration > 0"
          class="absolute bottom-0 left-0 h-1 bg-current opacity-20 transition-all ease-linear"
          :style="{ width: `${progress}%` }"
        />
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
interface ToastMessage {
  eventType?: string
  text?: string
  data?: any
}

interface Props {
  id?: string
  duration?: number
  type?: 'success' | 'error'
  title?: string
  message?: string | ToastMessage
}

const props = withDefaults(defineProps<Props>(), { duration: 5000 })
const emit = defineEmits<{
  dismiss: [id?: string]
}>()

const toastClasses = computed(() => ({
  'bg-green-50 border border-green-200 text-green-800': props.type === 'success',
  'bg-red-50 border border-red-200 text-red-800': props.type === 'error',
}))

const iconClasses = computed(() => ({
  'text-green-600': props.type === 'success',
  'text-red-600': props.type === 'error',
}))

const titleClasses = computed(() => ({
  'text-green-900': props.type === 'success',
  'text-red-900': props.type === 'error',
}))

const messageClasses = computed(() => ({
  'text-green-700': props.type === 'success',
  'text-red-700': props.type === 'error',
}))

const messageText = computed(() => {
  if (!props.message) return ''
  if (typeof props.message === 'string') return props.message
  return props.message.text || props.message.eventType || ''
})

const visible = ref(true)
const progress = ref(100)
let startTime: number = 0
let remaining: number = props.duration
let intervalId: ReturnType<typeof setInterval> | null = null

const startTimer = () => {
  if (intervalId) return
  startTime = Date.now()
  intervalId = setInterval(() => {
    const elapsed = Date.now() - startTime
    remaining = Math.max(0, props.duration - elapsed)
    progress.value = (remaining / props.duration) * 100
    if (remaining <= 0) {
      clearInterval(intervalId!)
      intervalId = null
      dismiss()
    }
  }, 100)
}

const pauseTimer = () => {
  if (intervalId) {
    clearInterval(intervalId)
    intervalId = null
  }
}

const resumeTimer = () => {
  if (!intervalId && remaining > 0) {
    startTimer()
  }
}

const dismiss = () => {
  visible.value = false
  if (intervalId) {
    clearInterval(intervalId)
    intervalId = null
  }
  setTimeout(() => emit('dismiss', props.id), 300)
}

onMounted(() => props.duration > 0 && startTimer())
onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>
