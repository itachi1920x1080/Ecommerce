<template>
  <div class="p-6 space-y-5">
    <div>
      <h2 class="text-lg font-bold text-gray-900 dark:text-white">Analytics</h2>
      <p class="text-xs text-gray-400 mt-0.5">Performance overview from your store data</p>
    </div>

    <!-- Loading -->
    <div v-if="!analytics && orders.length === 0" class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="h-28 rounded-2xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
    </div>

    <!-- KPI Row — derived from real data -->
    <div v-else class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <div v-for="(kpi, i) in kpis" :key="kpi.label"
        class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800
               shadow-sm p-4 animate-slide-up"
        :style="{ animationDelay: i * 70 + 'ms' }">
        <p class="text-xs text-gray-400 font-medium mb-1">{{ kpi.label }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ kpi.value }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ kpi.sub }}</p>
      </div>
    </div>

    <!-- Orders by status -->
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
      <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Orders by Status</h3>
      <div class="space-y-3.5">
        <div v-for="s in statusBreakdown" :key="s.label">
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs text-gray-600 dark:text-gray-400 font-medium capitalize">{{ s.label }}</span>
            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
              {{ s.count }} ({{ s.pct }}%)
            </span>
          </div>
          <div class="h-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700"
                 :style="{ width: s.pct + '%', background: s.color }"/>
          </div>
        </div>
      </div>
    </div>

    <!-- Two panels row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

      <!-- Top Products by stock -->
      <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Top Products</h3>
        <div v-if="topProducts.length === 0" class="py-8 text-center text-sm text-gray-400">No products yet</div>
        <div v-else class="space-y-3.5">
          <div v-for="(p, i) in topProducts" :key="p.id" class="space-y-1.5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 w-4 tabular-nums">{{ i + 1 }}</span>
                <span class="text-sm text-gray-700 dark:text-gray-200 font-medium truncate max-w-[160px]">{{ p.name }}</span>
              </div>
              <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                ${{ Number(p.price).toFixed(2) }}
              </span>
            </div>
            <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-700"
                   :style="{ width: productPct(p) + '%', background: productColor(i) }"/>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue summary from analytics prop -->
      <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Revenue Summary</h3>

        <div v-if="!analytics" class="space-y-3">
          <div v-for="i in 4" :key="i" class="h-10 rounded-xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
        </div>

        <div v-else class="space-y-3">
          <div v-for="item in revenueItems" :key="item.label"
            class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ item.label }}</span>
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ item.value }}</span>
          </div>
        </div>

        <!-- Computed from orders if analytics missing -->
        <div v-if="!analytics" class="mt-4 space-y-3">
          <div v-for="item in computedRevenue" :key="item.label"
            class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ item.label }}</span>
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ item.value }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// ALL data from AdminView — no dummy data
const props = defineProps({
  analytics: { type: Object, default: null },
  orders:    { type: Array,  default: () => [] },
  products:  { type: Array,  default: () => [] },
  isDark:    { type: Boolean, default: false },
})

// KPIs — prefer analytics prop, fallback to computed from orders/products
const totalRevenue = computed(() =>
  props.analytics?.total_revenue ??
  props.orders.reduce((s, o) => s + Number(o.total_price || 0), 0)
)
const totalOrders = computed(() =>
  props.analytics?.total_orders ?? props.orders.length
)
const totalProducts = computed(() =>
  props.analytics?.total_products ?? props.products.length
)
const avgOrderValue = computed(() =>
  totalOrders.value ? (totalRevenue.value / totalOrders.value) : 0
)

const kpis = computed(() => [
  { label: 'Total Revenue',   value: '$' + Number(totalRevenue.value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }), sub: 'All time' },
  { label: 'Total Orders',    value: totalOrders.value.toLocaleString(),   sub: 'All time' },
  { label: 'Total Products',  value: totalProducts.value.toLocaleString(), sub: 'In catalog' },
  { label: 'Avg Order Value', value: '$' + avgOrderValue.value.toFixed(2), sub: 'Per order' },
])

// Status breakdown from real orders
const statusColors = {
  pending:    '#f59e0b',
  processing: '#0ea5e9',
  paid:       '#10b981',
  completed:  '#10b981',
  cancelled:  '#ef4444',
}

const statusBreakdown = computed(() => {
  const total = props.orders.length || 1
  const counts = {}
  props.orders.forEach(o => {
    counts[o.status] = (counts[o.status] || 0) + 1
  })
  return Object.entries(counts).map(([label, count]) => ({
    label,
    count,
    pct: Math.round((count / total) * 100),
    color: statusColors[label] ?? '#6366f1',
  })).sort((a, b) => b.count - a.count)
})

// Top 5 products by price
const topProducts = computed(() =>
  [...props.products].sort((a, b) => Number(b.price) - Number(a.price)).slice(0, 5)
)

const maxPrice = computed(() =>
  Math.max(...props.products.map(p => Number(p.price)), 1)
)

function productPct(p) {
  return Math.round((Number(p.price) / maxPrice.value) * 100)
}

const productColors = ['#6366f1','#8b5cf6','#0ea5e9','#10b981','#f59e0b']
function productColor(i) { return productColors[i % productColors.length] }

// Revenue items from analytics prop
const revenueItems = computed(() => {
  if (!props.analytics) return []
  const a = props.analytics
  return Object.entries(a)
    .filter(([k, v]) => v != null && typeof v !== 'object')
    .map(([key, val]) => ({
      label: key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
      value: typeof val === 'number' && key.includes('revenue')
        ? '$' + Number(val).toLocaleString()
        : String(val),
    }))
})

// Fallback computed stats when analytics endpoint not available
const computedRevenue = computed(() => [
  { label: 'Total Revenue',       value: '$' + totalRevenue.value.toFixed(2) },
  { label: 'Total Orders',        value: totalOrders.value },
  { label: 'Pending Orders',      value: props.orders.filter(o => o.status === 'pending').length },
  { label: 'Completed Orders',    value: props.orders.filter(o => ['completed','paid'].includes(o.status)).length },
  { label: 'Avg Order Value',     value: '$' + avgOrderValue.value.toFixed(2) },
  { label: 'Products in Catalog', value: totalProducts.value },
])
</script>

<style scoped>
@keyframes slide-up {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.4s ease both; }
</style>