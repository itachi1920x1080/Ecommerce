<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">My Orders</h1>
        <p class="text-sm text-slate-400 mt-1">Track your order history</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-3">
        <div v-for="i in 4" :key="i" class="h-20 skeleton rounded-2xl"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="orders.length === 0" class="bg-white rounded-2xl border border-slate-200/60 p-16 text-center">
        <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-slate-100 flex items-center justify-center">
          <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-slate-600 mb-2">No orders yet</h2>
        <p class="text-sm text-slate-400 mb-6">Your order history will appear here after your first purchase</p>
        <router-link to="/" class="inline-block px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
          Start Shopping
        </router-link>
      </div>

      <!-- Orders Table -->
      <div v-else class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/50">
                <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Order #</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Payment</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</th>
                <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders" :key="order.id"
                class="border-b border-slate-50 hover:bg-blue-50/30 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-800">
                  #{{ order.order_number || order.id }}
                </td>
                <td class="px-4 py-4">
                  <span :class="statusClass(order.status)"
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold capitalize">
                    {{ order.status }}
                  </span>
                </td>
                <td class="px-4 py-4 text-slate-600">
                  {{ order.payment_method || '—' }}
                </td>
                <td class="px-4 py-4 font-semibold text-blue-600">
                  ${{ Number(order.total_amount || order.total_price || order.total || 0).toFixed(2) }}
                </td>
                <td class="px-6 py-4 text-slate-400">
                  {{ formatDate(order.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <router-link :to="`/invoice/${order.id}`" class="text-blue-600 hover:text-blue-700 font-medium text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                    View Invoice
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'

const toast   = inject('toast')
const orders  = ref([])
const loading = ref(true)

async function fetchOrders() {
  loading.value = true
  try {
    const res = await api.get('/my-orders')
    orders.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load orders', 'error')
  } finally {
    loading.value = false
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric'
  })
}

function statusClass(status) {
  const map = {
    completed:  'bg-emerald-100 text-emerald-700',
    paid:       'bg-emerald-100 text-emerald-700',
    delivered:  'bg-emerald-100 text-emerald-700',
    processing: 'bg-blue-100 text-blue-700',
    pending:    'bg-amber-100 text-amber-700',
    cancelled:  'bg-red-100 text-red-700',
    failed:     'bg-red-100 text-red-700',
  }
  return map[status] || 'bg-slate-100 text-slate-600'
}

onMounted(fetchOrders)
</script>
