<template>
  <div :class="cardClasses">
    <template v-if="$slots.header">
      <div
        :class="[paddingClasses, { 'border-b border-gray-200': $slots.default || $slots.footer }]"
      >
        <slot name="header" />
      </div>
    </template>
    <div v-if="$slots.default" :class="paddingClasses">
      <slot />
    </div>
    <template v-if="$slots.footer">
      <div
        :class="[paddingClasses, { 'border-t border-gray-200': $slots.header || $slots.default }]"
      >
        <slot name="footer" />
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
interface Props {
  variant?: 'default' | 'outlined' | 'elevated'
  padding?: 'none' | 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  padding: 'md',
})

const cardClasses = computed(() => {
  const base = 'rounded-lg'
  const variants: Record<'default' | 'outlined' | 'elevated', string> = {
    default: 'bg-white',
    outlined: 'border border-gray-200 bg-white',
    elevated: 'bg-white shadow-md',
  }

  const variant: 'default' | 'outlined' | 'elevated' = props.variant ?? 'default'
  return `${base} ${variants[variant]}`.trim()
})

const paddingClasses = computed(() => {
  const paddings: Record<'none' | 'sm' | 'md' | 'lg', string> = {
    none: '',
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6',
  }

  const padding: 'none' | 'sm' | 'md' | 'lg' = props.padding ?? 'md'
  return paddings[padding]
})
</script>
