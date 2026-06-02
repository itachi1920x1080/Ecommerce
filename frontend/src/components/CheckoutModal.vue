<template>
  <Teleport to="body">
    <Transition name="backdrop">
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="handleClose"></div>

        <Transition name="modal">
          <div v-if="show" class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">

            <!-- Success State: QR Code Display -->
            <div v-if="showQR" class="p-8 text-center">
              <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-800 mb-2">Scan to Pay</h3>
              <p class="text-sm text-slate-500 mb-6">Scan this QR code with your KHQR app to complete payment</p>

              <!-- Beautifully displayed QR Code -->
              <div class="inline-block p-4 bg-white border-2 border-slate-200 rounded-2xl shadow-sm mb-6">
                <img :src="qrCode" alt="KHQR Payment QR Code" class="w-52 h-52 object-contain" />
              </div>

              <p v-if="orderResult" class="text-sm text-slate-500 mb-4">
                Order #{{ orderResult.id || orderResult.order_number }} • ${{ Number(orderResult.total_amount || orderResult.total_price || 0).toFixed(2) }}
              </p>

              <!-- Close button that will trigger redirect -->
              <button @click="$emit('close', true)"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Done
              </button>
            </div>

            <!-- Success State: COD -->
            <div v-else-if="success" class="p-8 text-center">
              <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-800 mb-2">Order Placed!</h3>
              <p class="text-sm text-slate-500 mb-6">Your order has been placed successfully. We'll deliver it soon!</p>
              
              <!-- Close button that will trigger redirect -->
              <button @click="$emit('close', true)"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Done
              </button>
            </div>

            <!-- Checkout Form -->
            <div v-else>
              <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-lg font-semibold text-slate-800">Complete Your Order</h3>
                <p class="text-sm text-slate-400 mt-0.5">Choose your payment method</p>
              </div>

              <div class="p-6 space-y-4">
                <!-- Payment Options -->
                <div class="space-y-3">
                  <label
                    class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                    :class="method === 'KHQR' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'"
                  >
                    <input type="radio" v-model="method" value="KHQR" class="accent-blue-600 w-4 h-4" />
                    <div class="flex-1">
                      <p class="text-sm font-semibold text-slate-700">KHQR Payment</p>
                      <p class="text-xs text-slate-400">Scan QR code with your banking app</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                  </label>

                  <label
                    class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                    :class="method === 'COD' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'"
                  >
                    <input type="radio" v-model="method" value="COD" class="accent-blue-600 w-4 h-4" />
                    <div class="flex-1">
                      <p class="text-sm font-semibold text-slate-700">Cash on Delivery</p>
                      <p class="text-xs text-slate-400">Pay when you receive your order</p>
                    </div>
                    <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </label>
                </div>

                <!-- Error -->
                <p v-if="error" class="text-sm text-red-500 flex items-center gap-1.5">
                  <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ error }}
                </p>
              </div>

              <!-- Footer -->
              <div class="flex gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50">
                <button @click="$emit('close', false)"
                  class="flex-1 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors">
                  Cancel
                </button>
                <button @click="handleCheckout" :disabled="loading"
                  class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2">
                  <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                  </svg>
                  {{ loading ? 'Processing...' : 'Confirm Payment' }}
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, inject, watch, onUnmounted } from 'vue'
import api from '@/api/axios.js'
import { useCartStore } from '@/stores/cart'

const props = defineProps({ show: { type: Boolean, default: false } })
const emit  = defineEmits(['close'])

const toast   = inject('toast')
const cart    = useCartStore()

// Local State
const method  = ref('KHQR') // Default to KHQR
const loading = ref(false)
const error   = ref('')
const success = ref(false)
const showQR  = ref(false)
const qrCode  = ref('')
const orderResult = ref(null)

let pollInterval = null

// Reset state when modal opens/closes
watch(() => props.show, (newVal) => {
  if (newVal) {
    method.value = 'KHQR'
    loading.value = false
    error.value = ''
    success.value = false
    showQR.value = false
    qrCode.value = ''
    orderResult.value = null
  } else {
    stopPolling()
  }
})

// Cleanup on unmount
onUnmounted(() => {
  stopPolling()
})

function stopPolling() {
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
}

function startPolling(orderId) {
  stopPolling()
  pollInterval = setInterval(async () => {
    try {
      const res = await api.get(`/orders/${orderId}/status`)
      if (res.data.status === 'paid' || res.data.status === 'completed') {
        stopPolling()
        showQR.value = false // Hide QR
        success.value = true // Show success screen
        toast('Payment received successfully!', 'success')
      }
    } catch (e) {
      console.error('Polling error:', e)
    }
  }, 3000) // Check every 3 seconds
}

// If user clicks backdrop
function handleClose() {
  stopPolling()
  // If they click outside, consider it cancelled unless they already succeeded
  emit('close', success.value || showQR.value)
}

async function handleCheckout() {
  error.value   = ''
  loading.value = true
  
  try {
    // 1. Call Backend API POST /api/cart/checkout with payload { payment_method: "KHQR" }
    // The axios interceptor handles the Bearer token automatically
    const res = await api.post('/cart/checkout', { payment_method: method.value })
    const data = res.data

    orderResult.value = data.order || data
    
    // 2. Clear local cart since checkout is complete
    cart.items = []

    // 3. Handle KHQR SVG string response beautifully
    if (data.qr_code) {
      qrCode.value = data.qr_code
      showQR.value = true
      // Start polling for payment success!
      startPolling(orderResult.value.id || orderResult.value.order_number)
    } else {
      success.value = true
    }
    
    toast('Order placed successfully!', 'success')
  } catch (e) {
    error.value = e.response?.data?.message || 'Checkout failed. Please try again.'
    toast(error.value, 'error')
  } finally {
    loading.value = false
  }
}
</script>
