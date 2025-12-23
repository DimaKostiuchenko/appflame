export const useStatsPolling = () => {
  const { stats, loading, error, lastUpdated, isPolling, startPolling, stopPolling } = useStats()

  onMounted(() => {
    startPolling(10000)
  })

  onUnmounted(() => {
    stopPolling()
  })

  return {
    stats,
    loading,
    error,
    lastUpdated,
    isPolling,
  }
}
