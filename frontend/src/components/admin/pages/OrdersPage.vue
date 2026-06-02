<template>
  <div class="p-6 space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Orders</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ filtered.length }} total orders</p>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex gap-1.5 flex-wrap">
      <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
        :class="activeTab === tab.value
          ? 'bg-emerald-600 text-white shadow-sm'
          : 'bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'"
        class="px-3.5 py-1.5 rounded-xl text-xs font-medium transition-all duration-150">
        {{ tab.label }}
        <span class="ml-1 opacity-70">({{ countByStatus(tab.value) }})</span>
      </button>
    </div>

    <!-- Search -->
    <div class="relative max-w-sm">
      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input v-model="search" type="text" placeholder="Search orders, customers..."
        class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm bg-white dark:bg-gray-900
               border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
               placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

      <!-- Loading -->
      <div v-if="orders.length === 0" class="p-8 space-y-3">
        <div v-for="i in 5" :key="i" class="h-14 rounded-xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Order</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Customer</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Items</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
              <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in paginated" :key="order.id"
              class="border-b border-gray-50 dark:border-gray-800/60
                     hover:bg-emerald-50/40 dark:hover:bg-emerald-900/10 transition-colors duration-150">

              <!-- ID -->
              <td class="px-5 py-3.5 font-mono text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                #{{ order.id }}
              </td>

              <!-- Customer -->
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500
                              flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ (order.user?.name || 'U').slice(0,2).toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-gray-800 dark:text-gray-100 font-medium text-sm">
                      {{ order.user?.name || 'Unknown' }}
                    </p>
                    <p class="text-xs text-gray-400">{{ order.user?.email || '' }}</p>
                  </div>
                </div>
              </td>

              <!-- Items count -->
              <td class="px-4 py-3.5 text-gray-500 dark:text-gray-400 text-xs">
                {{ order.items?.length ?? order.order_items?.length ?? '—' }} item(s)
              </td>

              <!-- Total -->
              <td class="px-4 py-3.5 font-semibold text-gray-800 dark:text-gray-100">
                ${{ Number(order.total_price || 0).toFixed(2) }}
              </td>

              <!-- Date -->
              <td class="px-4 py-3.5 text-gray-400 text-xs">
                {{ order.created_at ? new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—' }}
              </td>

              <!-- Status -->
              <td class="px-4 py-3.5">
                <span :class="statusClass(order.status)"
                  class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                  {{ order.status }}
                </span>
              </td>

              <!-- Update status — emits to AdminView which calls api -->
              <td class="px-5 py-3.5">
                <select
                  :value="order.status"
                  @change="$emit('updateStatus', order, $event.target.value)"
                  class="text-xs px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700
                         bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                         focus:outline-none focus:ring-2 focus:ring-emerald-500/50 cursor-pointer">
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="paid">Paid</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </td>
            </tr>

            <tr v-if="paginated.length === 0">
              <td colspan="7" class="px-5 py-16 text-center text-sm text-gray-400">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1"
        class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs text-gray-400">Page {{ page }} of {{ totalPages }}</p>
        <div class="flex gap-1.5">
          <button v-for="p in totalPages" :key="p" @click="page = p"
            :class="p === page
              ? 'bg-emerald-600 text-white'
              : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700'"
            class="w-7 h-7 rounded-lg text-xs font-medium transition-all">
            {{ p }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// ALL data from AdminView — no dummy data
const props = defineProps({
  orders: { type: Array, default: () => [] },
})

// emit updateStatus to AdminView which calls api.put
defineEmits(['updateStatus'])

const search    = ref('')
const activeTab = ref('all')
const page      = ref(1)
const perPage   = 10

const tabs = [
  { label: 'All',        value: 'all' },
  { label: 'Pending',    value: 'pending' },
  { label: 'Processing', value: 'processing' },
  { label: 'Paid',       value: 'paid' },
  { label: 'Completed',  value: 'completed' },
  { label: 'Cancelled',  value: 'cancelled' },
]

function countByStatus(val) {
  if (val === 'all') return props.orders.length
  return props.orders.filter(o => o.status === val).length
}

const filtered = computed(() => {
  page.value = 1
  return props.orders.filter(o => {
    const matchTab    = activeTab.value === 'all' || o.status === activeTab.value
    const q           = search.value.toLowerCase()
    const matchSearch = !q ||
      (o.user?.name || '').toLowerCase().includes(q) ||
      (o.user?.email || '').toLowerCase().includes(q) ||
      String(o.id).includes(q)
    return matchTab && matchSearch
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((page.value - 1) * perPage, page.value * perPage))

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