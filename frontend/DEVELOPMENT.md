# Frontend Development Guidelines

This document outlines the development rules, conventions, and best practices for the Nuxt 3 frontend application.

## Component Development Rules

### Single Responsibility Principle
Each component should have one clear, well-defined purpose. If a component is doing multiple things, consider splitting it into smaller, focused components.

**Example:**
- ✅ Good: `Button.vue` - only handles button rendering and styling
- ❌ Bad: `Button.vue` - handles button rendering, form validation, and API calls

### DRY (Don't Repeat Yourself)
Extract reusable logic into composables or utility functions. Avoid duplicating code across components.

**Example:**
- ✅ Good: Create `useFormValidation.ts` composable for form logic
- ❌ Bad: Copy-paste form validation logic into multiple components

### Component Structure
Follow this order in component files:
1. Template
2. Script (with setup)
3. Style (if needed, prefer Tailwind)

### Naming Conventions
- **Components**: PascalCase for component names (e.g., `UserProfile`, `DataTable`)
- **Files**: kebab-case for component files (e.g., `user-profile.vue`, `data-table.vue`)
- **Props**: camelCase (e.g., `isLoading`, `buttonText`)
- **Events**: kebab-case (e.g., `@user-selected`, `@form-submitted`)

### Props
- Always define props with TypeScript interfaces/types
- Use `withDefaults()` for default values
- Document complex props with JSDoc comments

```vue
<script setup lang="ts">
interface Props {
  /** Button text content */
  label: string
  /** Button variant style */
  variant?: 'primary' | 'secondary'
  /** Whether button is disabled */
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  disabled: false,
})
</script>
```

### Slots
Use named slots for flexible composition when components need multiple content areas.

```vue
<template>
  <Card>
    <template #header>Header Content</template>
    Main content here
    <template #footer>Footer Content</template>
  </Card>
</template>
```

## Styling Rules

### Use Tailwind CSS
- Prefer Tailwind utility classes over custom CSS
- Use utility classes directly in templates for most styling needs
- Keep utility classes readable and organized (group related classes)

**Example:**
```vue
<template>
  <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold text-gray-900">Title</h2>
  </div>
</template>
```

### Component Classes
- Use `@apply` directive only when creating truly reusable component-level styles
- Prefer computed properties for dynamic class generation

```vue
<script setup lang="ts">
const buttonClasses = computed(() => {
  const base = 'font-medium rounded-lg transition-colors'
  const variant = props.variant === 'primary' ? 'bg-blue-600 text-white' : 'bg-gray-200'
  return `${base} ${variant}`
})
</script>
```

### Responsive Design
Use Tailwind's responsive prefixes (sm:, md:, lg:, xl:, 2xl:) for breakpoints.

```vue
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <!-- Responsive grid -->
</div>
```

### Custom Styles
Place custom styles in `assets/css/` only when necessary:
- Complex animations
- Third-party library overrides
- Global resets or base styles

Avoid using `<style scoped>` when Tailwind utilities can achieve the same result.

## File Organization

### Components Directory Structure
```
components/
├── ui/           # Reusable UI components (Button, Card, Input, Badge, etc.)
├── layout/       # Layout components (Header, Footer, Container, etc.)
└── features/     # Feature-specific components (AnalyticsWidget, etc.)
```

### Components
- **ui/**: Basic building blocks (atoms) - buttons, inputs, badges, cards
- **layout/**: Layout-related components - headers, footers, containers, grids
- **features/**: Feature-specific components that combine multiple UI components

### Composables
- Keep composables in `composables/` directory
- Nuxt auto-imports from this directory
- Use `use` prefix for composable names (e.g., `useAnalytics`, `useFormValidation`)

### Types
- Create `types/` directory for shared TypeScript interfaces
- Export types from `types/index.ts` for easy importing

### Utils
- Create `utils/` directory for helper functions
- Export utilities from `utils/index.ts` or individual files

## Code Quality

### TypeScript
- Always use TypeScript for scripts (`<script setup lang="ts">`)
- Define interfaces for props, emits, and complex data structures
- Avoid using `any` - use `unknown` or proper types instead

### Imports
- Leverage Nuxt auto-imports (components, composables, utilities)
- No need to import components from `components/` directory
- No need to import composables from `composables/` directory

### Props Validation
Use TypeScript interfaces for props validation instead of runtime validation:

```vue
<script setup lang="ts">
interface Props {
  title: string
  count?: number
}
defineProps<Props>()
</script>
```

### Comments
- Add JSDoc comments for complex components/composables
- Explain "why" not "what" in comments
- Keep comments up-to-date with code changes

```typescript
/**
 * Tracks analytics events and manages session ID
 * 
 * @returns Session ID and trackEvent function
 */
export const useAnalytics = () => {
  // Implementation
}
```

## Development Workflow

### Component Creation Process
1. Determine component responsibility (single purpose)
2. Choose appropriate directory (ui/, layout/, features/)
3. Create component file with kebab-case naming
4. Define TypeScript interfaces for props
5. Implement template using Tailwind classes
6. Test component in isolation
7. Document props and usage if complex

### Before Creating a New Component
1. Check if similar component exists in `components/ui/`
2. Check if existing component can be extended with props/slots
3. Consider if logic should be in a composable instead

### Component Checklist
- [ ] Single responsibility
- [ ] TypeScript props interface defined
- [ ] Uses Tailwind classes (not custom CSS)
- [ ] Named slots for flexible content (if needed)
- [ ] Proper event emissions with types
- [ ] Accessible (proper ARIA attributes if needed)
- [ ] Responsive design considered

## Best Practices Summary

1. **Keep it simple**: Start with simple components, refactor when needed
2. **Reuse first**: Check for existing components before creating new ones
3. **Compose over configure**: Use slots and composition over many props
4. **Type everything**: Use TypeScript for type safety
5. **Utility-first**: Prefer Tailwind utilities over custom CSS
6. **Test in isolation**: Test components independently before integration
7. **Document complex logic**: Add comments for non-obvious code

## Resources

- [Nuxt 3 Documentation](https://nuxt.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Vue 3 Documentation](https://vuejs.org/guide/)
- [Reka UI Documentation](https://github.com/reka-ui/reka-ui) (when available)

