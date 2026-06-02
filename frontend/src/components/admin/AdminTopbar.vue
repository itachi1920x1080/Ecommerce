<template>
  <header class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 transition-colors duration-300">

    <!-- Left: Title + date -->
    <div>
      <h1 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">{{ pageTitle }}</h1>
      <p class="text-xs text-gray-400 mt-0.5">{{ todayStr }}</p>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-2">

      <!-- API status badge -->
      <Transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 scale-90"
        leave-active-class="transition-all duration-200"
        leave-to-class="opacity-0 scale-90">
        <span :class="[
          'flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-xl border transition-colors duration-300',
          apiOk
            ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400'
            : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-600 dark:text-red-400'
        ]">
          <span :class="['w-1.5 h-1.5 rounded-full transition-colors', apiOk ? 'bg-emerald-500 animate-pulse' : 'bg-red-500']"></span>
          {{ apiOk ? 'Connected' : 'API Error' }}
        </span>
      </Transition>

      <!-- Dark mode toggle -->
      <button @click="$emit('toggleDark')"
        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 transition-all duration-200 hover:scale-105 active:scale-95 select-none">
        <!-- Moon icon -->
        <svg v-if="!isDark" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <!-- Sun icon -->
        <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="1" x2="12" y2="3"/>
          <line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
          <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/>
          <line x1="21" y1="12" x2="23" y2="12"/>
          <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
          <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        {{ isDark ? 'Light' : 'Dark' }}
      </button>

      <!-- Refresh button -->
      <button @click="$emit('refresh')" :disabled="loading"
        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 transition-all duration-200 hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
        <svg :class="loading ? 'animate-spin' : ''"
          width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 4 23 10 17 10"/>
          <polyline points="1 20 1 14 7 14"/>
          <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
        </svg>
        Refresh
      </button>

      <!-- Export button -->
      <button @click="$emit('export')"
        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 transition-all duration-200 hover:scale-105 active:scale-95">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export
      </button>

      <!-- Add product button -->
      <button @click="$emit('addProduct')"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-md">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add product
      </button>

    </div>
  </header>
</template>

<script setup>
const props = defineProps({
  isDark:    { type: Boolean, default: false },
  loading:   { type: Boolean, default: false },
  apiOk:     { type: Boolean, default: false },
  pageTitle: { type: String,  default: 'Dashboard' },
})

defineEmits(['toggleDark', 'refresh', 'export', 'addProduct'])

const todayStr = new Date().toLocaleDateString('en-US', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
})
</script>