<template>
  <div>
    <section class="py-16 px-6 bg-white min-h-[70vh]">
      <div class="max-w-5xl mx-auto">
        <div class="mb-10">
          <h1 class="text-2xl font-bold text-slate-800">My Orders</h1>
          <p class="text-sm text-slate-400 mt-1">Track and manage your order history</p>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
          <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
            :class="activeTab === tab.value
              ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25'
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
            class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200">
            {{ tab.label }}
          </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-28 skeleton rounded-2xl"></div>
        </div>

        <!-- Order Cards -->
        <div v-else-if="filteredOrders.length" class="space-y-4">
          <div v-for="order in filteredOrders" :key="order.id"
            class="bg-white rounded-2xl border border-slate-100 p-5 hover:border-primary-200/50 hover:shadow-md transition-all duration-300 animate-slide-up">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                  <svg class="w-6 h-6 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">Order #{{ order.id }}</h3>
                  <p class="text-xs text-slate-400 mt-0.5">{{ formatDate(order.created_at) }}</p>
                </div>
              </div>

              <div class="flex items-center gap-4">
                <span :class="statusClass(order.status)"
                  class="px-3 py-1 rounded-full text-xs font-semibold capitalize">
                  {{ order.status?.replace(/_/g, ' ') }}
                </span>
                <span class="text-lg font-bold text-slate-800">${{ Number(order.total_price || 0).toFixed(2) }}</span>
                <router-link :to="`/invoice/${order.id}`"
                  class="px-4 py-2 bg-slate-100 hover:bg-primary-50 hover:text-primary-600 text-slate-600 text-xs font-semibold rounded-xl transition-all">
                  View
                </router-link>
              </div>
            </div>

            <!-- Delivery Timeline (for active deliveries) -->
            <div v-if="isDeliveryOrder(order)" class="mt-5 pt-5 border-t border-slate-100">
              <div class="flex items-center gap-0 mb-4">
                <div v-for="(step, idx) in deliverySteps" :key="step.key" class="flex items-center" :class="idx < deliverySteps.length - 1 ? 'flex-1' : ''">
                  <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                      :class="isStepDone(order.status, step.key)
                        ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25'
                        : 'bg-slate-100 text-slate-400'">
                      <svg v-if="isStepDone(order.status, step.key)" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                      </svg>
                      <span v-else>{{ idx + 1 }}</span>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 whitespace-nowrap hidden sm:block">{{ step.label }}</span>
                  </div>
                  <div v-if="idx < deliverySteps.length - 1" class="flex-1 h-0.5 mx-1 transition-all duration-500"
                    :class="isStepDone(order.status, deliverySteps[idx + 1].key) ? 'bg-primary-500' : 'bg-slate-200'">
                  </div>
                </div>
              </div>
              
              <!-- Proof of Delivery -->
              <div v-if="order.status === 'delivered' && order.full_delivery_photo_url" class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800 mb-2">Proof of Delivery</p>
                  <a :href="order.full_delivery_photo_url" target="_blank" class="block rounded-lg overflow-hidden border border-slate-200 hover:opacity-90 transition-opacity">
                    <img :src="order.full_delivery_photo_url" alt="Delivery Photo" class="w-32 h-32 object-cover" />
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty -->
        <div v-else class="text-center py-24">
          <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-700 mb-2">No orders yet</h3>
          <p class="text-sm text-slate-400 mb-6">Start shopping and your orders will appear here</p>
          <router-link to="/" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
            Start Shopping
          </router-link>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import Footer from '@/components/shop/Footer.vue'

const toast = inject('toast')
const orders = ref([])
const loading = ref(true)
const activeTab = ref('all')

const tabs = [
  { label: 'All', value: 'all' },
  { label: 'Pending', value: 'pending' },
  { label: 'Preparing', value: 'preparing' },
  { label: 'Delivering', value: 'delivering' },
  { label: 'Delivered', value: 'delivered' },
  { label: 'Cancelled', value: 'cancelled' },
]

const deliverySteps = [
  { key: 'pending', label: 'Ordered' },
  { key: 'preparing', label: 'Preparing' },
  { key: 'finding_driver', label: 'Finding Driver' },
  { key: 'driver_assigned', label: 'Driver Found' },
  { key: 'delivering', label: 'Delivering' },
  { key: 'delivered', label: 'Delivered' },
]

const statusOrder = ['pending', 'preparing', 'finding_driver', 'driver_assigned', 'at_restaurant', 'delivering', 'delivered']

function isStepDone(orderStatus, stepKey) {
  return statusOrder.indexOf(orderStatus) >= statusOrder.indexOf(stepKey)
}

function isDeliveryOrder(order) {
  return ['finding_driver', 'driver_assigned', 'at_restaurant', 'delivering', 'delivered'].includes(order.status)
}

const filteredOrders = computed(() => {
  if (activeTab.value === 'all') return orders.value
  return orders.value.filter(o => o.status === activeTab.value)
})

function statusClass(status) {
  const map = {
    pending:        'bg-amber-100 text-amber-700',
    preparing:      'bg-blue-100 text-blue-700',
    finding_driver: 'bg-indigo-100 text-indigo-700',
    driver_assigned:'bg-cyan-100 text-cyan-700',
    at_restaurant:  'bg-teal-100 text-teal-700',
    delivering:     'bg-violet-100 text-violet-700',
    delivered:      'bg-emerald-100 text-emerald-700',
    cancelled:      'bg-red-100 text-red-700',
  }
  return map[status] || 'bg-slate-100 text-slate-600'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

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

onMounted(fetchOrders)
</script>
