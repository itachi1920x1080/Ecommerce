<template>
  <div class="bg-surface dark:bg-surface-dark min-h-screen pt-20">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="mb-10">
        <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">Order History</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Track, return, or purchase items again.</p>
      </div>

      <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm overflow-hidden">
        
        <!-- Tabs -->
        <div class="px-8 border-b border-zinc-200/50 dark:border-zinc-800/50 bg-zinc-50 dark:bg-zinc-900/50">
          <div class="flex gap-8 overflow-x-auto no-scrollbar">
            <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
              class="py-5 text-sm font-medium transition-colors border-b-2 whitespace-nowrap"
              :class="activeTab === tab.value ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-50'">
              {{ tab.label }}
            </button>
          </div>
        </div>

        <div class="p-8">
          <div v-if="loading" class="space-y-6">
            <div v-for="i in 3" :key="i" class="h-32 skeleton rounded-2xl"></div>
          </div>
          
          <div v-else-if="filteredOrders.length > 0" class="space-y-6">
            <div v-for="order in filteredOrders" :key="order.id"
              class="border border-zinc-200/50 dark:border-zinc-800/50 rounded-2xl p-6 bg-white dark:bg-zinc-900">
              
              <!-- Order Header -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-zinc-100 dark:border-zinc-800/50">
                <div class="flex items-center gap-6">
                  <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Order Placed</p>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">{{ formatDate(order.created_at) }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Total</p>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">${{ Number(order.total_price || 0).toFixed(2) }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Order #</p>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">{{ order.id }}</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-4">
                  <span :class="statusClass(order.status)" class="px-3 py-1 text-xs font-semibold rounded-full border">
                    {{ order.status?.replace(/_/g, ' ').toUpperCase() }}
                  </span>
                  <router-link :to="`/invoice/${order.id}`" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">
                    View Details
                  </router-link>
                </div>
              </div>

              <!-- Order Items -->
              <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                <div v-for="item in order.items" :key="item.id" class="w-20 h-20 rounded-xl bg-zinc-50 dark:bg-zinc-800 overflow-hidden shrink-0 border border-zinc-100 dark:border-zinc-800">
                  <img :src="item.product?.image || 'https://placehold.co/100'" :alt="item.product?.name" class="w-full h-full object-cover" />
                </div>
              </div>

              <!-- Delivery Timeline (for active deliveries) -->
              <div v-if="isDeliveryOrder(order)" class="mt-8 pt-8 border-t border-zinc-100 dark:border-zinc-800/50">
                <div class="relative">
                  <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-zinc-100 dark:bg-zinc-800 sm:hidden"></div>
                  <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 sm:gap-0 relative">
                    <div v-for="(step, idx) in deliverySteps" :key="step.key" class="flex flex-row sm:flex-col items-center gap-4 sm:gap-0" :class="idx < deliverySteps.length - 1 ? 'sm:flex-1' : ''">
                      <div class="flex flex-col items-center z-10 bg-white dark:bg-zinc-900 sm:bg-transparent py-2 sm:py-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500"
                          :class="isStepDone(order.status, step.key)
                            ? 'bg-primary-500 text-white shadow-md shadow-primary-500/20'
                            : 'bg-zinc-50 dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 text-zinc-400'">
                          <CheckIcon v-if="isStepDone(order.status, step.key)" class="w-5 h-5" />
                          <span v-else class="text-sm font-medium">{{ idx + 1 }}</span>
                        </div>
                        <span class="text-xs font-medium mt-3 whitespace-nowrap hidden sm:block"
                          :class="isStepDone(order.status, step.key) ? 'text-zinc-900 dark:text-zinc-50' : 'text-zinc-400'">{{ step.label }}</span>
                      </div>
                      <div class="sm:hidden block">
                        <span class="text-sm font-medium" :class="isStepDone(order.status, step.key) ? 'text-zinc-900 dark:text-zinc-50' : 'text-zinc-400'">{{ step.label }}</span>
                      </div>
                      <div v-if="idx < deliverySteps.length - 1" class="hidden sm:block flex-1 h-0.5 mx-2 transition-all duration-500"
                        :class="isStepDone(order.status, deliverySteps[idx + 1].key) ? 'bg-primary-500' : 'bg-zinc-100 dark:bg-zinc-800'">
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Proof of Delivery -->
                <div v-if="order.status === 'delivered' && order.full_delivery_photo_url" class="mt-8 p-6 bg-zinc-50 dark:bg-zinc-800/30 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 flex flex-col sm:flex-row items-start gap-6">
                  <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-500/10 flex items-center justify-center shrink-0">
                    <CheckCircle2Icon class="w-6 h-6 text-green-500" />
                  </div>
                  <div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50 mb-3">Proof of Delivery</p>
                    <a :href="order.full_delivery_photo_url" target="_blank" class="block rounded-xl overflow-hidden border border-zinc-200/50 dark:border-zinc-700/50 hover:opacity-90 transition-opacity w-fit shadow-sm">
                      <img :src="order.full_delivery_photo_url" alt="Delivery Photo" class="w-40 h-40 object-cover" />
                    </a>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Empty -->
          <div v-else class="text-center py-24">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center">
              <ShoppingBagIcon class="w-8 h-8 text-zinc-400" />
            </div>
            <h3 class="text-xl font-medium text-zinc-900 dark:text-zinc-50 mb-2">No orders found</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8">You haven't placed any orders in this category yet.</p>
            <router-link to="/" class="btn-primary inline-flex px-8 py-3.5 text-sm">
              Start Shopping
            </router-link>
          </div>

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
import { Check as CheckIcon, CheckCircle2 as CheckCircle2Icon, ShoppingBag as ShoppingBagIcon } from '@lucide/vue'

const toast = inject('toast')
const orders = ref([])
const loading = ref(true)
const activeTab = ref('all')

const tabs = [
  { label: 'All Orders', value: 'all' },
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
    pending:        'bg-zinc-100 text-zinc-700 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700',
    preparing:      'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
    finding_driver: 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
    driver_assigned:'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20',
    at_restaurant:  'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
    delivering:     'bg-primary-50 text-primary-600 border-primary-200 dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/20',
    delivered:      'bg-green-50 text-green-600 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20',
    cancelled:      'bg-red-50 text-red-600 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
  }
  return map[status] || 'bg-zinc-50 text-zinc-500 border-zinc-200'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
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

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
