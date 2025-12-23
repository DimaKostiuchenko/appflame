<template>
  <component
    :is="as"
    :class="[
      'inline-flex items-center font-medium leading-none transition-colors',
      VARIANT_MAP[variant],
      SIZE_MAP[size],
      pill ? 'rounded-full' : 'rounded-md',
    ]"
  >
    <span v-if="dot" class="mr-1.5 h-2 w-2 rounded-full fill-current" aria-hidden="true" />

    <slot />
  </component>
</template>

<script setup lang="ts">
export type BadgeVariant = 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'outline'
export type BadgeSize = 'sm' | 'md' | 'lg'

interface Props {
  variant?: BadgeVariant
  size?: BadgeSize
  pill?: boolean
  dot?: boolean
  as?: string | object
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  pill: true,
  dot: false,
  as: 'span',
})

const VARIANT_MAP: Record<BadgeVariant, string> = {
  primary: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
  secondary: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
  success: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
  danger: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
  warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
  outline: 'border border-gray-300 text-gray-600 bg-transparent',
}

const SIZE_MAP: Record<BadgeSize, string> = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-2.5 py-0.5 text-sm',
  lg: 'px-3 py-1 text-base',
}
</script>
