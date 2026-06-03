<template>
  <div class="group bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden border border-zinc-200/60 dark:border-zinc-800/60 shadow-sm flex flex-col h-full animate-pulse">
    <!-- Image Skeleton -->
    <div class="relative aspect-[4/5] bg-zinc-200 dark:bg-zinc-800 w-full overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 dark:via-white/5 to-transparent -translate-x-full animate-[shimmer_1.5s_infinite]"></div>
    </div>
    
    <!-- Content Skeleton -->
    <div class="p-5 flex flex-col flex-1 gap-4">
      <div class="flex justify-between items-start gap-4">
        <div class="flex-1 space-y-2">
          <!-- Title -->
          <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded-full w-3/4"></div>
          <!-- Category -->
          <div class="h-3 bg-zinc-100 dark:bg-zinc-800/60 rounded-full w-1/2"></div>
        </div>
        <!-- Price -->
        <div class="h-5 bg-zinc-200 dark:bg-zinc-800 rounded-full w-16"></div>
      </div>
      
      <!-- Footer Button Skeleton -->
      <div class="mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-800/50 flex justify-between items-center">
        <div class="h-8 w-24 bg-zinc-200 dark:bg-zinc-800 rounded-full"></div>
        <div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-800 rounded-full"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Skeleton Card requires no props
</script>
