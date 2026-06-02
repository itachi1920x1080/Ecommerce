<template>
  <!-- Loading Skeleton -->
  <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div v-for="i in 4" :key="i" class="h-28 rounded-2xl skeleton"></div>
  </div>

  <!-- Cards Grid -->
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div
      v-for="(card, i) in cards"
      :key="card.label"
      class="rounded-2xl bg-white border border-slate-200/60 p-5 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300 animate-slide-up"
      :style="{ animationDelay: i * 100 + 'ms' }"
    >
      <div class="flex items-center justify-between mb-3">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ card.label }}</p>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="card.iconBg">
          <span class="w-5 h-5" :class="card.iconColor" v-html="card.icon"></span>
        </div>
      </div>
      <p class="text-2xl font-bold text-slate-800 tracking-tight">{{ card.value }}</p>
      <p class="text-xs text-slate-400 mt-1">{{ card.sub }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  analytics: { type: Object,  default: null },
  loading:   { type: Boolean, default: false }
})

// SVG icons
const dollarIcon  = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
const cartIcon    = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>`
const usersIcon   = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`
const boxIcon     = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>`

const cards = computed(() => {
  const data = props.analytics?.analytics || props.analytics?.data?.analytics || {}
  return [
    {
      label: 'Total Revenue',
      value: '$' + Number(data.total_revenue || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }),
      sub: 'Lifetime earnings',
      icon: dollarIcon,
      iconBg: 'bg-blue-100',
      iconColor: 'text-blue-600',
    },
    {
      label: 'Total Orders',
      value: Number(data.total_orders || 0).toLocaleString(),
      sub: 'All time orders',
      icon: cartIcon,
      iconBg: 'bg-indigo-100',
      iconColor: 'text-indigo-600',
    },
    {
      label: 'Total Users',
      value: Number(data.total_users || 0).toLocaleString(),
      sub: 'Registered accounts',
      icon: usersIcon,
      iconBg: 'bg-violet-100',
      iconColor: 'text-violet-600',
    },
    {
      label: 'Total Products',
      value: Number(data.total_products || 0).toLocaleString(),
      sub: 'Active listings',
      icon: boxIcon,
      iconBg: 'bg-cyan-100',
      iconColor: 'text-cyan-600',
    },
  ]
})
</script>
