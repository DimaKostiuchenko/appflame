export interface ToastMessage {
  eventType?: string
  text?: string
  data?: any
}

export interface Toast {
  id: string
  type: 'success' | 'error'
  title?: string
  message: string | ToastMessage
  duration?: number
}

const toasts = ref<Toast[]>([])

export const useToast = () => {
  const show = (
    type: 'success' | 'error',
    message: string | ToastMessage,
    options?: {
      title?: string
      duration?: number
    }
  ) => {
    const id = crypto.randomUUID()
    const toast: Toast = {
      id,
      type,
      message,
      title: options?.title,
      duration: options?.duration ?? 5000,
    }

    toasts.value.push(toast)

    // Limit to maximum 5 toasts
    if (toasts.value.length > 5) {
      toasts.value.shift()
    }

    return id
  }

  const success = (
    message: string | ToastMessage,
    options?: {
      title?: string
      duration?: number
    }
  ) => {
    return show('success', message, {
      title: options?.title ?? 'Success',
      ...options,
    })
  }

  const error = (
    message: string | ToastMessage,
    options?: {
      title?: string
      duration?: number
    }
  ) => {
    return show('error', message, {
      title: options?.title ?? 'Error',
      ...options,
    })
  }

  const dismiss = (id: string) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const dismissAll = () => {
    toasts.value = []
  }

  return {
    toasts: readonly(toasts),
    show,
    success,
    error,
    dismiss,
    dismissAll,
  }
}
