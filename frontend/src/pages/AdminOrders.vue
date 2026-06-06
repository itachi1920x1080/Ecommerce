<template>
  <div class="p-6 sm:p-8 space-y-8 bg-white dark:bg-zinc-950 min-h-[calc(100vh-64px)] transition-colors">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-white">Orders</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">Manage and update customer orders</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 6" :key="i" class="h-16 skeleton rounded-xl"></div>
    </div>

    <!-- Orders Table -->
    <div v-else class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
      <!-- Search -->
      <div class="px-8 py-6 border-b border-zinc-200 dark:border-zinc-800">
        <div class="relative max-w-xs">
          <SearchIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
          <input v-model="search" type="text" placeholder="Search orders..."
            class="w-full pl-11 pr-5 py-3 rounded-xl text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 text-zinc-900 dark:text-white placeholder:text-zinc-400 transition-all"
            id="admin-orders-search" />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50">
              <th class="text-left px-8 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Order #</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Customer</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Total</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Payment</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Status</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Date</th>
              <th class="text-right px-8 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
            <tr v-for="order in filtered" :key="order.id"
              class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
              <td class="px-8 py-5 font-medium text-zinc-900 dark:text-zinc-50">#{{ order.order_number || order.id }}</td>
              <td class="px-4 py-5 font-medium text-zinc-500 dark:text-zinc-400">{{ order.user?.name || 'Unknown' }}</td>
              <td class="px-4 py-5 font-medium text-zinc-900 dark:text-zinc-50">
                ${{ Number(order.total_amount || order.total_price || 0).toFixed(2) }}
                <span v-if="order.discount_amount > 0" class="block text-[9px] font-semibold uppercase tracking-widest text-emerald-500 mt-1">
                  - ${{ Number(order.discount_amount).toFixed(2) }} discount
                </span>
              </td>
              <td class="px-4 py-5 text-[10px] font-semibold tracking-widest uppercase text-zinc-500 dark:text-zinc-400">{{ order.payment_method || '—' }}</td>
              <td class="px-4 py-5">
                <span :class="statusBadge(order.status)" class="text-[9px] font-semibold uppercase tracking-widest px-3 py-1 rounded-full border inline-block">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-4 py-5 text-[10px] font-semibold tracking-widest uppercase text-zinc-500 dark:text-zinc-400">{{ formatDate(order.created_at) }}</td>
              <td class="px-8 py-5">
                <div class="flex justify-end gap-3 items-center">
                  <a v-if="order.full_delivery_photo_url" :href="order.full_delivery_photo_url" target="_blank" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 p-2.5 rounded-full transition-colors" title="View Delivery Photo">
                    <ImageIcon class="w-4 h-4 stroke-[2]" />
                  </a>
                  <router-link :to="`/invoice/${order.id}`" class="text-zinc-900 dark:text-white font-semibold uppercase tracking-widest text-[9px] bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 px-4 py-2.5 rounded-full transition-colors">
                    Invoice
                  </router-link>
                  <select
                    :value="order.status"
                    @change="updateStatus(order, $event.target.value)"
                    class="px-4 py-2.5 rounded-full text-[9px] font-semibold uppercase tracking-widest bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 cursor-pointer transition-all appearance-none"
                    :id="`order-status-${order.id}`"
                  >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="finding_driver">Finding Driver</option>
                    <option value="driver_assigned">Driver Assigned</option>
                    <option value="at_restaurant">At Restaurant</option>
                    <option value="delivering">Delivering</option>
                    <option value="paid">Paid</option>
                    <option value="completed">Completed</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
              </td>
            </tr>
            <tr v-if="filtered.length === 0">
              <td colspan="7" class="px-8 py-20 text-center text-xs font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">No orders found</td>
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
import { Search as SearchIcon, Image as ImageIcon } from '@lucide/vue'

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
  const map = {
    completed:  'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-zinc-900 dark:border-zinc-100',
    paid:       'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
    delivered:  'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
    processing: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
    pending:    'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-700',
    cancelled:  'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800',
    finding_driver: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
    driver_assigned: 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-800',
    at_restaurant: 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800',
    delivering: 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 border-cyan-200 dark:border-cyan-800',
  }
  return map[status] || 'bg-white dark:bg-zinc-900 text-zinc-500 border-zinc-200 dark:border-zinc-700'
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
