export const useAnalytics = () => {
  // Helper function to generate UUID v4
  // Uses browser's built-in crypto.randomUUID() with fallback
  const generateUUID = (): string => {
    if (process.client && typeof crypto !== 'undefined' && crypto.randomUUID) {
      return crypto.randomUUID()
    }
    // Fallback for older browsers or SSR
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
      const r = (Math.random() * 16) | 0
      const v = c === 'x' ? r : (r & 0x3) | 0x8
      return v.toString(16)
    })
  }

  // 1. Create/read cookie with 1 year expiration
  const sessionId = useCookie('analytics_session_id', {
    maxAge: 60 * 60 * 24 * 365, // 1 year
    path: '/',
  })

  // 2. Generate session ID if cookie doesn't exist
  if (!sessionId.value) {
    sessionId.value = generateUUID()
  }

  const trackEvent = async (type: 'page_view' | 'cta_click' | 'form_submit') => {
    const config = useRuntimeConfig()

    // Validate session ID exists
    if (!sessionId.value) {
      console.warn('Session ID not available')
      return
    }

    // Generate new idempotency key for EACH request
    const idempotencyKey = generateUUID()
    const timestamp = new Date().toISOString()

    try {
      const response = await $fetch('/events', {
        method: 'POST',
        baseURL: config.public.apiBase,
        headers: {
          Authorization: `Bearer ${config.public.apiToken}`,
          'X-Idempotency-Key': idempotencyKey,
          'Content-Type': 'application/json',
        },
        body: {
          type,
          ts: timestamp,
          session_id: sessionId.value,
        },
      })

      console.log('Event tracked:', response)
      return response
    } catch (error: any) {
      console.error('Event tracking failed:', error)

      // Handle specific error types
      if (error.statusCode === 401) {
        console.error('Authentication failed: Check your API token')
      } else if (error.statusCode === 422) {
        console.error('Validation error:', error.data)
      } else if (error.statusCode === 400) {
        console.error('Bad request:', error.data)
      }

      throw error
    }
  }

  return {
    sessionId: readonly(sessionId),
    trackEvent,
  }
}
