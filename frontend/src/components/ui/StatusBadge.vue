<template>
  <span class="inline-flex items-center gap-1 font-medium rounded-full capitalize"
        :class="[sizeClass, colorClass]">
    <span class="w-1.5 h-1.5 rounded-full" :class="dotClass" />
    {{ status }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, default: 'pending' },
  size: { type: String, default: 'sm' }
})

const sizeClass = computed(() =>
  props.size === 'xs' ? 'px-1.5 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs'
)

const map = {
  completed:  { badge: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', dot: 'bg-emerald-500' },
  processing: { badge: 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',               dot: 'bg-sky-500 animate-pulse' },
  pending:    { badge: 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',        dot: 'bg-amber-500' },
  cancelled:  { badge: 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',               dot: 'bg-red-500' },
  active:     { badge: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',dot: 'bg-emerald-500' },
  inactive:   { badge: 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',             dot: 'bg-gray-400' },
  draft:      { badge: 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',   dot: 'bg-violet-500' }
}

const colorClass = computed(() => map[props.status]?.badge || map.pending.badge)
const dotClass   = computed(() => map[props.status]?.dot   || 'bg-gray-400')
</script>