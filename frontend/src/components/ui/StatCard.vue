<template>
  <div class="rounded-2xl bg-white dark:bg-gray-900
              border border-gray-100 dark:border-gray-800
              p-4 shadow-sm hover:shadow-md
              hover:-translate-y-0.5 transition-all duration-200 cursor-default">
    <div class="flex items-start justify-between mb-3">
      <!-- Icon -->
      <div class="w-10 h-10 rounded-xl flex items-center justify-center"
           :class="iconBg">
        <!-- Currency -->
        <svg v-if="icon === 'currency'" class="w-5 h-5" :class="iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <!-- Shopping -->
        <svg v-if="icon === 'shopping'" class="w-5 h-5" :class="iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <!-- Users -->
        <svg v-if="icon === 'users'" class="w-5 h-5" :class="iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <!-- Box -->
        <svg v-if="icon === 'box'" class="w-5 h-5" :class="iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
        </svg>
      </div>

      <!-- Trend badge -->
      <span class="flex items-center gap-0.5 text-xs font-semibold px-2 py-0.5 rounded-full"
            :class="trend === 'up'
              ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400'
              : 'text-red-500 bg-red-50 dark:bg-red-900/30 dark:text-red-400'">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
            :d="trend === 'up' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"/>
        </svg>
        {{ change }}
      </span>
    </div>

    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ value }}</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-medium">{{ label }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: String,
  value: String,
  change: String,
  trend: { type: String, default: 'up' },
  icon: String,
  color: { type: String, default: 'indigo' }
})

const palettes = {
  indigo:  { bg: 'bg-indigo-50 dark:bg-indigo-900/30',  text: 'text-indigo-600 dark:text-indigo-400' },
  violet:  { bg: 'bg-violet-50 dark:bg-violet-900/30',  text: 'text-violet-600 dark:text-violet-400' },
  sky:     { bg: 'bg-sky-50 dark:bg-sky-900/30',        text: 'text-sky-600 dark:text-sky-400' },
  emerald: { bg: 'bg-emerald-50 dark:bg-emerald-900/30',text: 'text-emerald-600 dark:text-emerald-400' }
}

const iconBg   = computed(() => palettes[props.color]?.bg   || palettes.indigo.bg)
const iconColor = computed(() => palettes[props.color]?.text || palettes.indigo.text)
</script>