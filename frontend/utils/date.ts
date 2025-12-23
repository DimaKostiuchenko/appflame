/**
 * Format a date string to a human-readable format
 * @param dateString - Date string in format YYYY-MM-DD
 * @returns Formatted date string (e.g., "January 1, 2024")
 */
export const formatDate = (dateString: string): string => {
  const date = new Date(dateString + 'T00:00:00')
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

/**
 * Format a Date object to a time string
 * @param date - Date object
 * @returns Formatted time string (e.g., "02:30:45 PM")
 */
export const formatTime = (date: Date): string => {
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}
