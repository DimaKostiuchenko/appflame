interface StatsResponse {
  date: string
  counts: {
    page_view: number
    cta_click: number
    form_submit: number
  }
  total: number
}

export const useStats = () => {
  const config = useRuntimeConfig()

  const stats = ref<StatsResponse | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const lastUpdated = ref<Date | null>(null)
  const pollingInterval = ref<NodeJS.Timeout | null>(null)
  const isPolling = ref(false)

  const fetchStats = async (showLoading = false) => {
    if (showLoading) {
      loading.value = true
    }
    error.value = null

    try {
      const data = await $fetch<StatsResponse>('/stats/today', {
        method: 'GET',
        baseURL: config.public.apiBase,
        headers: {
          Authorization: `Bearer ${config.public.apiToken}`,
        },
      })

      stats.value = data
      lastUpdated.value = new Date()
      console.log('Stats fetched:', data)
    } catch (err: any) {
      const errorMessage = err.message || 'Failed to fetch stats'
      error.value = errorMessage
      console.error('Stats fetch error:', err)

      // Handle specific error types
      if (err.statusCode === 401) {
        console.error('Authentication failed: Check your API token')
      } else if (err.statusCode === 404) {
        console.error('Stats endpoint not found')
      } else if (err.statusCode >= 500) {
        console.error('Server error:', err.statusCode)
      }
    } finally {
      if (showLoading) {
        loading.value = false
      }
    }
  }

  const startPolling = (interval: number = 10000) => {
    if (pollingInterval.value) {
      stopPolling()
    }

    // Fetch immediately
    fetchStats(true)

    // Then poll every interval
    pollingInterval.value = setInterval(() => {
      fetchStats(false)
    }, interval)

    isPolling.value = true
  }

  const stopPolling = () => {
    if (pollingInterval.value) {
      clearInterval(pollingInterval.value)
      pollingInterval.value = null
      isPolling.value = false
    }
  }

  return {
    stats: readonly(stats),
    loading: readonly(loading),
    error: readonly(error),
    lastUpdated: readonly(lastUpdated),
    isPolling: readonly(isPolling),
    fetchStats,
    startPolling,
    stopPolling,
  }
}
