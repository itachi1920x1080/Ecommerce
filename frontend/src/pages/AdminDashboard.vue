<template>
  <div class="p-6 sm:p-8 space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
      <p class="text-sm text-slate-400 mt-1">Welcome back, Admin. Here's your store overview.</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-if="loading" v-for="i in 4" :key="i" class="h-28 skeleton rounded-2xl"></div>
      <template v-else>
        <div v-for="card in dashCards" :key="card.label" class="bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm">
          <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">{{ card.label }}</p>
          <p class="text-2xl font-bold text-slate-800 mt-2">{{ card.value }}</p>
        </div>
      </template>
    </div>

    <!-- Recent Orders & Quick Actions -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

      <!-- Recent Orders -->
      <div class="bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-base font-semibold text-slate-800">Recent Orders</h3>
          <router-link to="/admin/orders" class="text-xs text-blue-600 hover:underline font-medium">View all</router-link>
        </div>

        <div v-if="loading" class="space-y-3">
          <div v-for="i in 5" :key="i" class="h-14 skeleton rounded-xl"></div>
        </div>

        <div v-else-if="recentOrders.length === 0" class="py-10 text-center text-sm text-slate-400">
          No orders yet
        </div>

        <div v-else class="space-y-2">
          <div v-for="order in recentOrders" :key="order.id"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
              {{ (order.user?.name || 'U').slice(0,2).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-slate-700 truncate">{{ order.user?.name || 'Unknown' }}</p>
              <p class="text-xs text-slate-400">#{{ order.order_number || order.id }}</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-sm font-semibold text-slate-700">${{ Number(order.total_amount || order.total_price || 0).toFixed(2) }}</p>
              <span :class="statusClass(order.status)"
                class="text-[10px] px-2 py-0.5 rounded-full font-semibold capitalize">
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Status Breakdown -->
      <div class="bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm">
        <h3 class="text-base font-semibold text-slate-800 mb-5">Order Status</h3>

        <div v-if="loading" class="space-y-4">
          <div v-for="i in 4" :key="i" class="h-10 skeleton rounded-xl"></div>
        </div>

        <div v-else class="space-y-4">
          <div v-for="stat in orderStatusBreakdown" :key="stat.label" class="space-y-1.5">
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-600 capitalize">{{ stat.label }}</span>
              <span class="font-semibold text-slate-700">{{ stat.count }}</span>
            </div>
            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-700"
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

const dashCards = computed(() => {
  const d = analytics.value?.data || analytics.value || {}
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
    pending:    'bg-amber-400',
    processing: 'bg-blue-500',
    paid:       'bg-emerald-500',
    completed:  'bg-emerald-500',
    delivered:  'bg-indigo-500',
    cancelled:  'bg-red-400',
  }

  return Object.entries(statusData).map(([label, count]) => ({
    label,
    count,
    pct: Math.round((count / total) * 100),
    color: colorMap[label] || 'bg-slate-400'
  }))
})

function statusClass(status) {
  const map = {
    completed:  'bg-emerald-100 text-emerald-700',
    paid:       'bg-emerald-100 text-emerald-700',
    processing: 'bg-blue-100 text-blue-700',
    pending:    'bg-amber-100 text-amber-700',
    cancelled:  'bg-red-100 text-red-700',
  }
  return map[status] || 'bg-slate-100 text-slate-600'
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
