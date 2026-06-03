<template>
  <div class="min-h-screen bg-white dark:bg-zinc-950 pt-32 pb-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
      
      <!-- Header -->
      <div class="border-b border-zinc-200 dark:border-zinc-800 pb-8">
        <h1 class="text-4xl sm:text-5xl font-display font-medium text-zinc-900 dark:text-white tracking-tight mb-4">Driver Dashboard</h1>
        <p class="text-zinc-500 dark:text-zinc-400 text-lg font-light">
          Manage and deliver customer orders
        </p>
      </div>

      <!-- Active Deliveries -->
      <div>
        <h2 class="text-2xl font-display font-medium text-zinc-900 dark:text-white mb-6 flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center shrink-0 border border-zinc-200/50 dark:border-zinc-800/50 text-zinc-900 dark:text-zinc-50">
            <PackageIcon class="w-5 h-5 stroke-[1.5]" />
          </div>
          My Active Deliveries
        </h2>

        <div v-if="loadingActive" class="space-y-4">
          <div v-for="i in 2" :key="i" class="h-32 skeleton rounded-3xl"></div>
        </div>
        
        <div v-else-if="activeOrders.length" class="space-y-6">
          <div v-for="order in activeOrders" :key="order.id" class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50 p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
              <div>
                <h3 class="font-medium text-lg text-zinc-900 dark:text-zinc-50">Order #{{ order.id }}</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ order.address?.full_address || 'No address' }}</p>
              </div>
              <span class="px-4 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-50 text-xs font-medium rounded-full uppercase tracking-wider">
                {{ order.status.replace(/_/g, ' ') }}
              </span>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
              <button v-if="order.status === 'driver_assigned'" @click="updateStatus(order, 'at_restaurant')" :disabled="updating"
                class="btn-primary flex-1 py-3 text-sm disabled:opacity-50">
                Arrived at Restaurant
              </button>
              
              <button v-if="order.status === 'at_restaurant'" @click="updateStatus(order, 'delivering')" :disabled="updating"
                class="btn-primary flex-1 py-3 text-sm disabled:opacity-50">
                Start Delivering
              </button>
              
              <label v-if="order.status === 'delivering'"
                class="btn-primary flex-1 py-3 text-sm cursor-pointer flex items-center justify-center gap-2"
                :class="{ 'opacity-50 pointer-events-none': updating }">
                <input type="file" accept="image/*" capture="environment" class="hidden" @change="uploadDeliveryPhoto($event, order)" />
                <CameraIcon class="w-4 h-4" />
                Snap & Deliver
              </label>
            </div>
          </div>
        </div>
        
        <div v-else class="bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50 p-12 text-center text-zinc-500 dark:text-zinc-400 text-sm font-medium tracking-wide">
          You have no active deliveries.
        </div>
      </div>

      <!-- Available Orders -->
      <div>
        <h2 class="text-2xl font-display font-medium text-zinc-900 dark:text-white mb-6 flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center shrink-0 border border-zinc-200/50 dark:border-zinc-800/50 text-zinc-900 dark:text-zinc-50">
            <ClipboardListIcon class="w-5 h-5 stroke-[1.5]" />
          </div>
          Available Orders
        </h2>

        <div v-if="loadingAvailable" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-24 skeleton rounded-3xl"></div>
        </div>
        
        <div v-else-if="availableOrders.length" class="space-y-6">
          <div v-for="order in availableOrders" :key="order.id" class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50 p-8 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
              <div>
                <h3 class="font-medium text-lg text-zinc-900 dark:text-zinc-50">Order #{{ order.id }}</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm truncate">{{ order.address?.full_address || 'No address provided' }}</p>
                <p class="text-base font-medium text-zinc-900 dark:text-zinc-50 mt-2">${{ Number(order.total_price).toFixed(2) }}</p>
              </div>
              <button @click="acceptOrder(order)" :disabled="updating"
                class="btn-primary px-8 py-3 text-sm disabled:opacity-50 shrink-0 w-full sm:w-auto">
                Accept Order
              </button>
            </div>
          </div>
        </div>
        
        <div v-else class="bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50 p-12 text-center text-zinc-500 dark:text-zinc-400 text-sm font-medium tracking-wide">
          No orders waiting for a driver right now.
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { Package as PackageIcon, ClipboardList as ClipboardListIcon, Camera as CameraIcon } from '@lucide/vue'

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
