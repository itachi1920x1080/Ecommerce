<template>
  <header class="sticky top-0 z-40 flex items-center justify-between px-8 py-5 bg-surface/80 dark:bg-surface-dark/80 backdrop-blur-md border-b border-zinc-200/50 dark:border-zinc-800/50 transition-colors">

    <!-- Left: Title + date -->
    <div>
      <h1 class="text-2xl font-display font-medium text-zinc-900 dark:text-zinc-50">{{ pageTitle }}</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ todayStr }}</p>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-3">

      <!-- API status badge -->
      <Transition name="fade">
        <span :class="[
          'flex items-center gap-2 text-xs font-medium px-3 py-1.5 rounded-full border transition-colors',
          apiOk
            ? 'bg-green-50 dark:bg-green-500/10 border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400'
            : 'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400'
        ]">
          <span :class="['w-1.5 h-1.5 rounded-full transition-colors', apiOk ? 'bg-green-500 animate-pulse' : 'bg-red-500']"></span>
          {{ apiOk ? 'Connected' : 'Offline' }}
        </span>
      </Transition>

      <div class="w-px h-6 bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

      <!-- Dark mode toggle -->
      <button @click="$emit('toggleDark')"
        class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
        <MoonIcon v-if="!isDark" class="w-5 h-5" />
        <SunIcon v-else class="w-5 h-5" />
      </button>

      <!-- Refresh button -->
      <button @click="$emit('refresh')" :disabled="loading"
        class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors disabled:opacity-50">
        <RefreshCwIcon class="w-5 h-5" :class="loading ? 'animate-spin' : ''" />
      </button>

      <!-- Export button -->
      <button @click="$emit('export')"
        class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
        <DownloadIcon class="w-5 h-5" />
      </button>

      <!-- Add Action button -->
      <button v-if="showAdd" @click="$emit('add')"
        class="btn-primary ml-2 px-6 py-2.5 text-sm flex items-center gap-2">
        <PlusIcon class="w-4 h-4" />
        {{ addLabel }}
      </button>

    </div>
  </header>
</template>

<script setup>
import { Moon as MoonIcon, Sun as SunIcon, RefreshCw as RefreshCwIcon, Download as DownloadIcon, Plus as PlusIcon } from '@lucide/vue'

const props = defineProps({
  isDark:    { type: Boolean, default: false },
  loading:   { type: Boolean, default: false },
  apiOk:     { type: Boolean, default: false },
  pageTitle: { type: String,  default: 'Dashboard' },
  showAdd:   { type: Boolean, default: false },
  addLabel:  { type: String,  default: 'Add New' }
})

defineEmits(['toggleDark', 'refresh', 'export', 'add'])

const todayStr = new Date().toLocaleDateString('en-US', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
})
</script>