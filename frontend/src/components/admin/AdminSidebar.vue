<template>
  <aside class="w-60 shrink-0 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 fixed top-0 left-0 h-full z-20 transition-colors duration-300">

    <!-- Logo -->
    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
      <div class="w-8 h-8 rounded-xl bg-emerald-500 flex items-center justify-center shadow-sm">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <span class="text-base font-semibold text-gray-900 dark:text-white tracking-tight">Glow Admin</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

      <!-- Main section -->
      <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-600 px-3 mb-2">Main</p>

      <button v-for="item in navMain" :key="item.key"
        @click="$emit('navigate', item.key)"
        :class="[
          'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
          activePage === item.key
            ? 'bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-950/60 dark:to-green-950/60 text-emerald-700 dark:text-emerald-300'
            : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-white'
        ]">
        <!-- Icon -->
        <span class="shrink-0 w-4 h-4 transition-transform duration-200 group-hover:scale-110" v-html="item.svg"></span>
        <!-- Label -->
        <span class="flex-1 text-left">{{ item.label }}</span>
        <!-- Badge count -->
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 scale-75"
          leave-active-class="transition-all duration-200"
          leave-to-class="opacity-0 scale-75">
          <span v-if="item.count !== undefined"
            :class="[
              'text-[11px] px-1.5 py-0.5 rounded-full font-semibold tabular-nums transition-colors',
              activePage === item.key
                ? 'bg-emerald-200 dark:bg-emerald-800 text-emerald-800 dark:text-emerald-200'
                : 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300'
            ]">
            {{ item.count }}
          </span>
        </Transition>
      </button>

      <!-- Manage section -->
      <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-600 px-3 mt-5 mb-2">Manage</p>

      <button v-for="item in navManage" :key="item.key"
        @click="$emit('navigate', item.key)"
        :class="[
          'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
          activePage === item.key
            ? 'bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-950/60 dark:to-green-950/60 text-emerald-700 dark:text-emerald-300'
            : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-white'
        ]">
        <span class="shrink-0 w-4 h-4 transition-transform duration-200 group-hover:scale-110" v-html="item.svg"></span>
        <span class="flex-1 text-left">{{ item.label }}</span>
        <!-- Active indicator dot -->
        <span v-if="activePage === item.key"
          class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse">
        </span>
      </button>

    </nav>

    <!-- User / Logout -->
    <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3">
      <!-- Avatar -->
      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-sm">
        {{ userInitials }}
      </div>
      <!-- Name + role -->
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ currentUser?.name || 'Admin' }}</p>
        <p class="text-xs text-gray-400 truncate">{{ currentUser?.role || 'admin' }}</p>
      </div>
      <!-- Logout button -->
      <button @click="$emit('logout')" title="Logout"
        class="p-1.5 rounded-lg text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 hover:scale-110 active:scale-95">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
      </button>
    </div>

  </aside>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  isDark:     { type: Boolean, default: false },
  activePage: { type: String,  default: 'dashboard' },
  products:   { type: Array,   default: () => [] },
  orders:     { type: Array,   default: () => [] },
  customers:  { type: Array,   default: () => [] },
  currentUser:{ type: Object,  default: null },
})

defineEmits(['navigate', 'logout'])

const userInitials = computed(() =>
  (props.currentUser?.name || 'AD').slice(0, 2).toUpperCase()
)

// ── SVG icons ──────────────────────────────────────────────────────
const svgDashboard = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`
const svgProducts  = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>`
const svgOrders    = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>`
const svgCustomers = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`
const svgCats      = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`
const svgAnalytics = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`
const svgSettings  = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>`

// ── Nav items ──────────────────────────────────────────────────────
const navMain = computed(() => [
  { key: 'dashboard', label: 'Dashboard', svg: svgDashboard },
  { key: 'products',  label: 'Products',  svg: svgProducts,  count: props.products.length },
  { key: 'orders',    label: 'Orders',    svg: svgOrders,    count: props.orders.filter(o => o.status === 'pending').length },
  { key: 'customers', label: 'Customers', svg: svgCustomers, count: props.customers.length },
])

const navManage = [
  { key: 'categories', label: 'Categories', svg: svgCats },
  { key: 'analytics',  label: 'Analytics',  svg: svgAnalytics },
  { key: 'settings',   label: 'Settings',   svg: svgSettings },
]
</script>