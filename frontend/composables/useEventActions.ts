import type { ToastMessage } from './useToast'

export const useEventActions = () => {
  const { sessionId, trackEvent: baseTrackEvent } = useAnalytics()
  const { success: showSuccess, error: showError } = useToast()
  const loading = ref(false)

  const handleEvent = async (type: 'page_view' | 'cta_click' | 'form_submit') => {
    loading.value = true

    try {
      const result = await baseTrackEvent(type)
      console.log('✅ Event tracked successfully:', result)

      showSuccess(
        {
          eventType: type,
          text: 'Event tracked successfully',
          data: result,
        } as ToastMessage,
        {
          title: 'Event Tracked Successfully',
        }
      )

      return result
    } catch (error: any) {
      console.error('❌ Event tracking failed:', error)

      showError(
        {
          eventType: type,
          text: error.message || 'Failed to track event',
          data: {
            statusCode: error.statusCode,
            statusMessage: error.statusMessage,
            data: error.data,
          },
        } as ToastMessage,
        {
          title: 'Event Tracking Failed',
        }
      )

      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    sessionId,
    handleEvent,
    loading: readonly(loading),
  }
}
