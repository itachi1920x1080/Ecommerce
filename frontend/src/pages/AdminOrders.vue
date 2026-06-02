<template>
  <div class="p-6 sm:p-8 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Orders</h1>
      <p class="text-sm text-slate-400 mt-1">Manage and update customer orders</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 6" :key="i" class="h-16 skeleton rounded-2xl"></div>
    </div>

    <!-- Orders Table -->
    <div v-else class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
      <!-- Search -->
      <div class="px-6 py-4 border-b border-slate-100">
        <div class="relative max-w-xs">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="search" type="text" placeholder="Search orders..."
            class="w-full pl-10 pr-4 py-2 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
            id="admin-orders-search" />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50/50">
              <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Order #</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Payment</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
              <th class="text-right px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filtered" :key="order.id"
              class="border-b border-slate-50 hover:bg-blue-50/30 transition-colors">
              <td class="px-6 py-4 font-medium text-slate-800">#{{ order.order_number || order.id }}</td>
              <td class="px-4 py-4 text-slate-600">{{ order.user?.name || 'Unknown' }}</td>
              <td class="px-4 py-4 font-semibold text-slate-700">${{ Number(order.total_amount || order.total_price || 0).toFixed(2) }}</td>
              <td class="px-4 py-4 text-slate-500">{{ order.payment_method || '—' }}</td>
              <td class="px-4 py-4">
                <span :class="statusBadge(order.status)" class="px-2.5 py-1 rounded-lg text-xs font-semibold capitalize">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-4 py-4 text-slate-400">{{ formatDate(order.created_at) }}</td>
              <td class="px-6 py-4">
                <div class="flex justify-end gap-2 items-center">
                  <router-link :to="`/invoice/${order.id}`" class="text-blue-600 hover:text-blue-700 font-medium text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                    Invoice
                  </router-link>
                  <select
                    :value="order.status"
                    @change="updateStatus(order, $event.target.value)"
                    class="px-3 py-1.5 rounded-lg text-xs bg-slate-50 border border-slate-200 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer transition-all"
                    :id="`order-status-${order.id}`"
                  >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="paid">Paid</option>
                    <option value="completed">Completed</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
              </td>
            </tr>
            <tr v-if="filtered.length === 0">
              <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-400">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import api from '@/api/axios.js'

const toast   = inject('toast')
const orders  = ref([])
const loading = ref(true)
const search  = ref('')

const filtered = computed(() =>
  orders.value.filter(o => {
    const q = search.value.toLowerCase()
    return (o.order_number || String(o.id)).toLowerCase().includes(q)
      || (o.user?.name || '').toLowerCase().includes(q)
      || (o.status || '').toLowerCase().includes(q)
  })
)

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function statusBadge(status) {
  return {
    completed:  'bg-emerald-100 text-emerald-700',
    paid:       'bg-emerald-100 text-emerald-700',
    delivered:  'bg-indigo-100 text-indigo-700',
    processing: 'bg-blue-100 text-blue-700',
    pending:    'bg-amber-100 text-amber-700',
    cancelled:  'bg-red-100 text-red-700',
  }[status] || 'bg-slate-100 text-slate-600'
}

async function updateStatus(order, newStatus) {
  try {
    await api.put(`/orders/${order.id}/status`, { status: newStatus })
    order.status = newStatus
    toast(`Order #${order.order_number || order.id} updated to ${newStatus}`, 'success')
  } catch (e) {
    toast('Failed to update order status', 'error')
  }
}

async function loadOrders() {
  loading.value = true
  try {
    const res = await api.get('/admin/orders').catch(() => api.get('/orders'))
    orders.value = res.data?.data ?? res.data ?? []
  } catch (e) {
    toast('Failed to load orders', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(loadOrders)
</script>
