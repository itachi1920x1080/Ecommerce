<template>
  <div class="p-6 space-y-6">

    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 p-6 text-white shadow-lg">
      <div class="relative z-10">
        <p class="text-emerald-100 text-sm font-medium">Good {{ timeGreeting }},</p>
        <h1 class="text-2xl font-bold mt-0.5">{{ adminName }} 👋</h1>
        <p class="text-emerald-100 text-sm mt-1">Here's what's happening with your store today.</p>
      </div>
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10" />
      <div class="absolute -right-4 -bottom-10 w-28 h-28 rounded-full bg-white/10" />
      <div class="absolute right-24 -top-4 w-16 h-16 rounded-full bg-white/10" />
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i"
        class="h-28 rounded-2xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
    </div>

    <!-- Stat Cards -->
    <div v-else class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <div v-for="(stat, i) in stats" :key="stat.label"
        class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800
               shadow-sm p-4 animate-slide-up hover:shadow-md transition-shadow"
        :style="{ animationDelay: i * 80 + 'ms' }">
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ stat.label }}</p>
          <div class="w-8 h-8 rounded-xl flex items-center justify-center"
               :class="stat.iconBg">
            <span v-html="stat.icon" class="w-4 h-4" :class="stat.iconColor"/>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ stat.value }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ stat.sub }}</p>
      </div>
    </div>

    <!-- Recent Orders + Top Products -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

      <!-- Recent Orders (from real orders prop) -->
      <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Recent Orders</h3>
          <button @click="$emit('navigate', 'orders')"
            class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
            View all
          </button>
        </div>

        <div v-if="loading" class="space-y-3">
          <div v-for="i in 4" :key="i" class="h-12 rounded-xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
        </div>

        <div v-else-if="recentOrders.length === 0"
          class="py-8 text-center text-sm text-gray-400">No orders yet</div>

        <div v-else class="space-y-2">
          <div v-for="order in recentOrders" :key="order.id"
            class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500
                        flex items-center justify-center text-white text-xs font-bold shrink-0">
              {{ (order.user?.name || 'U').slice(0,2).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                {{ order.user?.name || 'Unknown' }}
              </p>
              <p class="text-xs text-gray-400 truncate">
                {{ order.items?.[0]?.product?.name || order.notes || '—' }}
              </p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                ${{ Number(order.total_price || 0).toFixed(2) }}
              </p>
              <span :class="statusClass(order.status)"
                class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold capitalize">
                {{ order.status }}
              </span>
            </div>
            <button @click="$emit('markPaid', order)"
              v-if="order.status === 'pending'"
              class="opacity-0 group-hover:opacity-100 text-xs px-2 py-1 rounded-lg
                     bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300
                     hover:bg-emerald-200 transition-all shrink-0">
              Mark paid
            </button>
          </div>
        </div>
      </div>

      <!-- Top Products (from real products prop) -->
      <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Products Overview</h3>
          <button @click="$emit('navigate', 'products')"
            class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
            Manage
          </button>
        </div>

        <div v-if="loading" class="space-y-3">
          <div v-for="i in 5" :key="i" class="h-10 rounded-xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
        </div>

        <div v-else-if="topProducts.length === 0"
          class="py-8 text-center text-sm text-gray-400">No products yet</div>

        <div v-else class="space-y-3.5">
          <div v-for="(product, i) in topProducts" :key="product.id" class="space-y-1.5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 w-4 tabular-nums">{{ i + 1 }}</span>
                <div class="w-6 h-6 rounded bg-gray-100 dark:bg-gray-800 overflow-hidden shrink-0">
                  <img v-if="product.image" :src="product.image" class="w-full h-full object-cover" @error="product.image=''" :alt="product.name"/>
                </div>
                <span class="text-sm text-gray-700 dark:text-gray-200 font-medium truncate max-w-[130px]">
                  {{ product.name }}
                </span>
              </div>
              <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                ${{ Number(product.price).toFixed(2) }}
              </span>
            </div>
            <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
              <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700"
                   :style="{ width: stockPct(product) + '%' }"/>
            </div>
            <p class="text-[10px] text-gray-400">{{ product.stock ?? 0 }} in stock</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Analytics summary row (from analytics prop) -->
    <div v-if="analytics" class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <div v-for="(kpi, i) in analyticsKpis" :key="kpi.label"
        class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800
               shadow-sm p-4 animate-slide-up"
        :style="{ animationDelay: i * 60 + 'ms' }">
        <p class="text-xs text-gray-400 font-medium mb-1">{{ kpi.label }}</p>
        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ kpi.value }}</p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  loading:   { type: Boolean, default: false },
  analytics: { type: Object,  default: null },
  orders:    { type: Array,   default: () => [] },
  products:  { type: Array,   default: () => [] },
  isDark:    { type: Boolean, default: false },
})

defineEmits(['navigate', 'markPaid'])

// Greeting
const hour = new Date().getHours()
const timeGreeting = hour < 12 ? 'morning' : hour < 17 ? 'afternoon' : 'evening'
const adminName = 'Admin'

// Derived stats from real backend data
const totalRevenue = computed(() =>
  props.orders.reduce((sum, o) => sum + Number(o.total_price || 0), 0)
)
const pendingCount = computed(() =>
  props.orders.filter(o => o.status === 'pending').length
)
const lowStockCount = computed(() =>
  props.products.filter(p => (p.stock ?? 0) <= 5).length
)

const stats = computed(() => [
  {
    label: 'Total Revenue',
    value: '$' + totalRevenue.value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
    sub: `${props.orders.length} orders total`,
    icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    iconBg: 'bg-emerald-100 dark:bg-emerald-900/40',
    iconColor: 'text-emerald-600 dark:text-emerald-400',
  },
  {
    label: 'Total Orders',
    value: props.orders.length.toLocaleString(),
    sub: `${pendingCount.value} pending`,
    icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>`,
    iconBg: 'bg-blue-100 dark:bg-blue-900/40',
    iconColor: 'text-blue-600 dark:text-blue-400',
  },
  {
    label: 'Total Products',
    value: props.products.length.toLocaleString(),
    sub: `${lowStockCount.value} low stock`,
    icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>`,
    iconBg: 'bg-violet-100 dark:bg-violet-900/40',
    iconColor: 'text-violet-600 dark:text-violet-400',
  },
  {
    label: 'Avg Order Value',
    value: props.orders.length
      ? '$' + (totalRevenue.value / props.orders.length).toFixed(2)
      : '$0.00',
    sub: 'per order',
    icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`,
    iconBg: 'bg-amber-100 dark:bg-amber-900/40',
    iconColor: 'text-amber-600 dark:text-amber-400',
  },
])

// Last 5 orders
const recentOrders = computed(() =>
  [...props.orders].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5)
)

// Top 5 products by stock desc (or you could sort by sales if available)
const topProducts = computed(() =>
  [...props.products].slice(0, 5)
)

const maxStock = computed(() =>
  Math.max(...props.products.map(p => p.stock ?? 0), 1)
)

function stockPct(product) {
  return Math.round(((product.stock ?? 0) / maxStock.value) * 100)
}

// Analytics KPIs from backend analytics prop
const analyticsKpis = computed(() => {
  if (!props.analytics) return []
  const a = props.analytics
  return [
    { label: 'Total Revenue',   value: a.total_revenue   != null ? '$' + Number(a.total_revenue).toLocaleString()  : '—' },
    { label: 'Total Orders',    value: a.total_orders    != null ? a.total_orders    : '—' },
    { label: 'Total Customers', value: a.total_customers != null ? a.total_customers : '—' },
    { label: 'Total Products',  value: a.total_products  != null ? a.total_products  : '—' },
  ].filter(k => k.value !== '—')
})

function statusClass(status) {
  return {
    completed:  'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
    paid:       'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
    processing: 'bg-blue-100   dark:bg-blue-900/40    text-blue-700   dark:text-blue-300',
    pending:    'bg-amber-100  dark:bg-amber-900/40   text-amber-700  dark:text-amber-300',
    cancelled:  'bg-red-100    dark:bg-red-900/40     text-red-700    dark:text-red-300',
  }[status] ?? 'bg-gray-100 text-gray-600'
}
</script>

<style scoped>
@keyframes slide-up {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.4s ease both; }
</style>