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

              <div class="p-6 space-y-6">
                <!-- Address Selection -->
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-700">Delivery Address</label>
                    <button v-if="!showAddAddress" @click="showAddAddress = true" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Add New</button>
                    <button v-else @click="showAddAddress = false" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Cancel</button>
                  </div>

                  <div v-if="showAddAddress" class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <input v-model="addressForm.receiver_name" type="text" placeholder="Receiver Name" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
                      <input v-model="addressForm.phone_number" type="text" placeholder="Phone Number" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
                    </div>
                    <textarea v-model="addressForm.full_address" placeholder="Full Address" rows="2" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
                    <button @click="saveAddress" :disabled="savingAddress || !addressForm.receiver_name || !addressForm.phone_number || !addressForm.full_address" class="w-full py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900 transition-colors disabled:opacity-50">
                      {{ savingAddress ? 'Saving...' : 'Save Address' }}
                    </button>
                  </div>

                  <template v-else>
                    <div v-if="addressesLoading" class="h-12 skeleton rounded-xl"></div>
                    <select v-else-if="addresses.length" v-model="selectedAddressId"
                      class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 cursor-pointer">
                      <option v-for="addr in addresses" :key="addr.id" :value="addr.id">
                        {{ addr.receiver_name }} ({{ addr.phone_number }}) - {{ addr.full_address }}
                      </option>
                    </select>
                    <div v-else class="p-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 text-sm">
                      No delivery addresses found. Please add a new address above.
                    </div>
                  </template>
                </div>

                <!-- Coupon -->
                <div class="space-y-3">
                  <label class="text-sm font-semibold text-slate-700">Discount Coupon</label>
                  <div class="flex gap-2">
                    <input v-model="couponCode" type="text" placeholder="Enter code" :disabled="couponApplied"
                      class="flex-1 px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 uppercase" />
                    <button v-if="!couponApplied" @click="verifyCoupon" :disabled="!couponCode || verifyingCoupon"
                      class="px-5 py-3 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                      {{ verifyingCoupon ? '...' : 'Apply' }}
                    </button>
                    <button v-else @click="removeCoupon"
                      class="px-5 py-3 bg-red-100 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-200 transition-colors">
                      Remove
                    </button>
                  </div>
                  <p v-if="couponSuccess" class="text-xs font-semibold text-emerald-600">{{ couponSuccess }}</p>
                  <p v-if="couponError" class="text-xs text-red-500">{{ couponError }}</p>
                </div>

                <!-- Payment Options -->
                <div class="space-y-3">
                  <label class="text-sm font-semibold text-slate-700">Payment Method</label>
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

const addresses = ref([])
const addressesLoading = ref(false)
const selectedAddressId = ref(null)
const showAddAddress = ref(false)
const savingAddress = ref(false)
const addressForm = ref({ receiver_name: '', phone_number: '', full_address: '' })

const couponCode = ref('')
const couponApplied = ref(null) // the coupon object if valid
const verifyingCoupon = ref(false)
const couponError = ref('')
const couponSuccess = ref('')

let pollInterval = null

// Fetch addresses
async function fetchAddresses() {
  addressesLoading.value = true
  try {
    const res = await api.get('/addresses')
    addresses.value = res.data.data || res.data || []
    if (addresses.value.length > 0) {
      selectedAddressId.value = addresses.value[0].id
    }
  } catch (e) {
    console.error('Failed to load addresses', e)
  } finally {
    addressesLoading.value = false
  }
}

// Add new address
async function saveAddress() {
  savingAddress.value = true
  try {
    const res = await api.post('/addresses', addressForm.value)
    const newAddr = res.data.data || res.data.address || res.data
    addresses.value.push(newAddr)
    selectedAddressId.value = newAddr.id
    showAddAddress.value = false
    addressForm.value = { receiver_name: '', phone_number: '', full_address: '' }
    toast('Address added!', 'success')
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to add address', 'error')
  } finally {
    savingAddress.value = false
  }
}

// Verify coupon
async function verifyCoupon() {
  if (!couponCode.value) return
  verifyingCoupon.value = true
  couponError.value = ''
  couponSuccess.value = ''
  try {
    const res = await api.post('/coupons/verify', { code: couponCode.value.toUpperCase() })
    couponApplied.value = res.data.data || res.data.coupon || res.data
    couponSuccess.value = 'Coupon applied successfully!'
  } catch (e) {
    couponError.value = e.response?.data?.message || 'Invalid or expired coupon'
    couponApplied.value = null
  } finally {
    verifyingCoupon.value = false
  }
}

function removeCoupon() {
  couponCode.value = ''
  couponApplied.value = null
  couponSuccess.value = ''
  couponError.value = ''
}

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
    removeCoupon()
    fetchAddresses()
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
  if (!selectedAddressId.value) {
    error.value = 'Please select a delivery address.'
    return
  }

  error.value   = ''
  loading.value = true
  
  try {
    const payload = {
      payment_method: method.value,
      address_id: selectedAddressId.value,
    }
    if (couponApplied.value) {
      payload.coupon_id = couponApplied.value.id || couponApplied.value.code
    }

    const res = await api.post('/cart/checkout', payload)
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
