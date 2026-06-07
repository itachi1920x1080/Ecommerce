<template>
  <div class="p-6 sm:p-8 space-y-8 bg-white dark:bg-zinc-950 min-h-[calc(100vh-64px)] transition-colors">
    <!-- Page Header -->
    <div>
      <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-white">Dashboard</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">Welcome back, Admin. Here's your store overview.</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-if="loading" v-for="i in 4" :key="i" class="h-32 skeleton rounded-2xl"></div>
      <template v-else>
        <div v-for="card in dashCards" :key="card.label" class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
          <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 dark:text-zinc-400">{{ card.label }}</p>
          <p class="text-3xl font-display font-medium text-zinc-900 dark:text-white mt-3">{{ card.value }}</p>
        </div>
      </template>
    </div>

    <!-- Recent Orders & Quick Actions -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

      <!-- Recent Orders -->
      <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-xl font-display font-medium text-zinc-900 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center shrink-0">
              <ShoppingBagIcon class="w-5 h-5 text-zinc-900 dark:text-zinc-100 stroke-[1.5]" />
            </div>
            Recent Orders
          </h3>
          <router-link to="/admin/orders" class="text-xs font-semibold uppercase tracking-widest text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">View all</router-link>
        </div>

        <div v-if="loading" class="space-y-4">
          <div v-for="i in 5" :key="i" class="h-16 skeleton rounded-xl"></div>
        </div>

        <div v-else-if="recentOrders.length === 0" class="py-12 text-center text-xs font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
          No orders yet
        </div>

        <div v-else class="space-y-4">
          <div v-for="order in recentOrders" :key="order.id"
            class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
            <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-900 dark:text-white text-sm font-display font-medium shrink-0">
              {{ (order.user?.name || 'U').slice(0,2).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ order.user?.name || 'Unknown' }}</p>
              <p class="text-[10px] font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mt-1">#{{ order.order_number || order.id }}</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-sm font-medium text-zinc-900 dark:text-white">${{ Number(order.total_amount || order.total_price || 0).toFixed(2) }}</p>
              <span :class="statusClass(order.status)"
                class="text-[9px] font-semibold uppercase tracking-widest px-3 py-1 rounded-full mt-1.5 inline-block border">
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Status Breakdown -->
      <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm">
        <h3 class="text-xl font-display font-medium text-zinc-900 dark:text-white mb-8 flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center shrink-0">
            <BarChart2Icon class="w-5 h-5 text-zinc-900 dark:text-zinc-100 stroke-[1.5]" />
          </div>
          Order Status
        </h3>

        <div v-if="loading" class="space-y-6">
          <div v-for="i in 4" :key="i" class="h-12 skeleton rounded-xl"></div>
        </div>

        <div v-else class="space-y-6">
          <div v-for="stat in orderStatusBreakdown" :key="stat.label" class="space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-widest">
              <span class="text-zinc-500 dark:text-zinc-400">{{ stat.label }}</span>
              <span class="text-zinc-900 dark:text-white">{{ stat.count }}</span>
            </div>
            <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-1000 ease-out"
                :class="stat.color"
                :style="{ width: stat.pct + '%' }"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { ShoppingBag as ShoppingBagIcon, BarChart2 as BarChart2Icon } from '@lucide/vue'

const dashCards = computed(() => {
  const d = analytics.value?.analytics || analytics.value?.data || analytics.value || {}
  return [
    { label: 'Total Revenue', value: '$' + Number(d.total_revenue || 0).toFixed(2) },
    { label: 'Total Orders', value: d.total_orders || 0 },
    { label: 'Total Products', value: d.total_products || 0 },
    { label: 'Total Users', value: d.total_users || 0 },
  ]
})

const toast     = inject('toast')
const analytics = ref(null)
const orders    = ref([])
const loading   = ref(true)

// Recent orders — last 5
const recentOrders = computed(() =>
  [...orders.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5)
)

// Order status breakdown for bar chart
const orderStatusBreakdown = computed(() => {
  const statusData = analytics.value?.data?.order_status || analytics.value?.order_status || {}
  const total = Object.values(statusData).reduce((sum, v) => sum + v, 0) || 1

  const colorMap = {
    pending:    'bg-zinc-500',
    processing: 'bg-amber-500',
    paid:       'bg-emerald-500',
    completed:  'bg-zinc-900 dark:bg-zinc-100',
    delivered:  'bg-blue-500',
    cancelled:  'bg-red-500',
  }

  return Object.entries(statusData).map(([label, count]) => ({
    label,
    count,
    pct: Math.round((count / total) * 100),
    color: colorMap[label] || 'bg-zinc-300 dark:bg-zinc-700'
  }))
})

function statusClass(status) {
  const map = {
    completed:  'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-zinc-900 dark:border-zinc-100',
    paid:       'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
    processing: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
    pending:    'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-700',
    cancelled:  'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800',
  }
  return map[status] || 'bg-white dark:bg-zinc-900 text-zinc-500 border-zinc-200 dark:border-zinc-700'
}

async function loadData() {
  loading.value = true
  try {
    const [analyticsRes, ordersRes] = await Promise.all([
      api.get('/dashboard/analytics'),
      api.get('/admin/orders').catch(() => api.get('/orders'))
    ])
    analytics.value = analyticsRes.data
    orders.value = ordersRes.data?.data ?? ordersRes.data ?? []
  } catch (e) {
    toast('Failed to load dashboard data', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>
