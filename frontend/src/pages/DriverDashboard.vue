<template>
  <div>
    <section class="py-16 px-6 bg-slate-50 min-h-screen">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Driver Dashboard</h1>
        <p class="text-sm text-slate-400 mb-10">Manage and deliver customer orders</p>

        <!-- Active Deliveries -->
        <div class="mb-12">
          <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            My Active Deliveries
          </h2>

          <div v-if="loadingActive" class="space-y-4">
            <div v-for="i in 2" :key="i" class="h-24 skeleton rounded-2xl"></div>
          </div>
          <div v-else-if="activeOrders.length" class="space-y-4">
            <div v-for="order in activeOrders" :key="order.id" class="bg-white rounded-2xl border border-emerald-100 p-5 shadow-sm shadow-emerald-500/5">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="font-bold text-slate-800">Order #{{ order.id }}</h3>
                  <p class="text-xs text-slate-500 mt-1">{{ order.address?.full_address || 'No address' }}</p>
                </div>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase tracking-wider">
                  {{ order.status.replace(/_/g, ' ') }}
                </span>
              </div>
              
              <div class="flex gap-2">
                <button v-if="order.status === 'driver_assigned'" @click="updateStatus(order, 'at_restaurant')" :disabled="updating"
                  class="flex-1 py-2.5 bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold text-sm rounded-xl transition-colors">
                  Arrived at Restaurant
                </button>
                <button v-if="order.status === 'at_restaurant'" @click="updateStatus(order, 'delivering')" :disabled="updating"
                  class="flex-1 py-2.5 bg-violet-50 text-violet-600 hover:bg-violet-100 font-semibold text-sm rounded-xl transition-colors">
                  Start Delivering
                </button>
                <label v-if="order.status === 'delivering'"
                  class="flex-1 py-2.5 bg-emerald-600 text-white hover:bg-emerald-700 font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/25 transition-all cursor-pointer text-center flex items-center justify-center gap-2"
                  :class="{ 'opacity-50 pointer-events-none': updating }">
                  <input type="file" accept="image/*" capture="environment" class="hidden" @change="uploadDeliveryPhoto($event, order)" />
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Snap & Deliver
                </label>
              </div>
            </div>
          </div>
          <div v-else class="bg-white rounded-2xl border border-slate-100 p-8 text-center text-slate-400 text-sm">
            You have no active deliveries.
          </div>
        </div>

        <!-- Available Orders -->
        <div>
          <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Available Orders
          </h2>

          <div v-if="loadingAvailable" class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-24 skeleton rounded-2xl"></div>
          </div>
          <div v-else-if="availableOrders.length" class="space-y-4">
            <div v-for="order in availableOrders" :key="order.id" class="bg-white rounded-2xl border border-slate-100 p-5 hover:border-blue-200 transition-colors">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="font-bold text-slate-800">Order #{{ order.id }}</h3>
                  <p class="text-xs text-slate-500 mt-1 max-w-sm truncate">{{ order.address?.full_address || 'No address provided' }}</p>
                  <p class="text-sm font-semibold text-primary-600 mt-2">${{ Number(order.total_price).toFixed(2) }}</p>
                </div>
                <button @click="acceptOrder(order)" :disabled="updating"
                  class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md transition-all active:scale-95 disabled:opacity-50">
                  Accept Order
                </button>
              </div>
            </div>
          </div>
          <div v-else class="bg-white rounded-2xl border border-slate-100 p-8 text-center text-slate-400 text-sm">
            No orders waiting for a driver right now.
          </div>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import Footer from '@/components/shop/Footer.vue'

const toast = inject('toast')

const availableOrders = ref([])
const activeOrders = ref([])
const loadingAvailable = ref(true)
const loadingActive = ref(true)
const updating = ref(false)

async function fetchAvailable() {
  loadingAvailable.value = true
  try {
    const res = await api.get('/driver/available-orders')
    availableOrders.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load available orders', 'error')
  } finally {
    loadingAvailable.value = false
  }
}

async function fetchActive() {
  loadingActive.value = true
  try {
    const res = await api.get('/driver/active-orders')
    activeOrders.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load active deliveries', 'error')
  } finally {
    loadingActive.value = false
  }
}

async function acceptOrder(order) {
  updating.value = true
  try {
    await api.post(`/driver/orders/${order.id}/accept`)
    toast('Order accepted! Please head to the restaurant.', 'success')
    await Promise.all([fetchAvailable(), fetchActive()])
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to accept order', 'error')
  } finally {
    updating.value = false
  }
}

async function updateStatus(order, newStatus) {
  updating.value = true
  try {
    await api.put(`/driver/orders/${order.id}/status`, { status: newStatus })
    toast(`Status updated to: ${newStatus.replace(/_/g, ' ')}`, 'success')
    await fetchActive()
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to update status', 'error')
  } finally {
    updating.value = false
  }
}

async function uploadDeliveryPhoto(event, order) {
  const file = event.target.files[0]
  if (!file) return // if user cancels camera

  updating.value = true
  try {
    const formData = new FormData()
    formData.append('status', 'delivered')
    formData.append('image', file)
    formData.append('_method', 'PUT')
    
    await api.post(`/driver/orders/${order.id}/status`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    toast('Order delivered successfully!', 'success')
    await fetchActive()
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to deliver order', 'error')
  } finally {
    updating.value = false
    event.target.value = ''
  }
}

onMounted(() => {
  fetchAvailable()
  fetchActive()
})
</script>
