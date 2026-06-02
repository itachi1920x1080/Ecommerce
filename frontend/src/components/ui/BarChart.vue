<template>
  <div class="flex items-end gap-2 h-40 w-full">
    <div
      v-for="(bar, i) in data"
      :key="bar.label"
      class="flex-1 flex flex-col items-center gap-1.5 group"
    >
      <!-- Tooltip -->
      <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-150
                  text-xs font-semibold text-white bg-gray-800 dark:bg-gray-700
                  px-2 py-1 rounded-lg pointer-events-none whitespace-nowrap">
        ${{ (bar.value / 1000).toFixed(1) }}k
      </div>
      <!-- Bar -->
      <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden flex flex-col justify-end"
           style="height: 120px">
        <div
          class="w-full rounded-lg transition-all duration-700 ease-out
                 bg-gradient-to-t from-indigo-600 to-indigo-400
                 group-hover:from-violet-600 group-hover:to-violet-400"
          :style="{ height: pct(bar.value) + '%', transitionDelay: i * 60 + 'ms' }"
        />
      </div>
      <!-- Label -->
      <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ bar.label }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: { type: Array, default: () => [] }
})

const max = computed(() => Math.max(...props.data.map(d => d.value)) || 1)
const pct = val => Math.round((val / max.value) * 90)
</script>