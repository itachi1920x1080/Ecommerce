<template>
  <Teleport to="body">
    <Transition name="backdrop">
      <div v-if="show" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-md" @click="handleClose"></div>

        <Transition name="modal">
          <div v-if="show" class="relative z-10 w-full max-w-lg rounded-[2rem] bg-surface dark:bg-surface-dark shadow-2xl border border-zinc-200/50 dark:border-zinc-800/50 overflow-hidden">

            <!-- Success State: QR Code Display -->
            <div v-if="showQR" class="p-10 text-center flex flex-col items-center">
              <div class="w-16 h-16 mb-6 rounded-full bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center">
                <QrCodeIcon class="w-8 h-8 text-primary-600 dark:text-primary-400" />
              </div>
              <h3 class="text-2xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">Scan to Pay</h3>
              <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8">Scan this QR code with your banking app to complete payment.</p>

              <!-- QR Code Container -->
              <div class="p-6 bg-white rounded-3xl shadow-sm border border-zinc-200 mb-8 max-w-[240px] w-full aspect-square flex items-center justify-center">
                <img :src="qrCode" alt="Payment QR Code" class="w-full h-full object-contain" />
              </div>

              <div v-if="orderResult" class="flex items-center gap-2 mb-8 px-4 py-2 bg-zinc-50 dark:bg-zinc-800/50 rounded-full border border-zinc-200/50 dark:border-zinc-800/50 text-sm">
                <span class="text-zinc-500">Order #{{ orderResult.id || orderResult.order_number }}</span>
                <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                <span class="font-medium text-zinc-900 dark:text-zinc-50">${{ Number(orderResult.total_amount || orderResult.total_price || 0).toFixed(2) }}</span>
              </div>

              <button @click="$emit('close', true)" class="btn-primary w-full py-4 text-sm">
                Done
              </button>
            </div>

            <!-- Success State: COD -->
            <div v-else-if="success" class="p-10 text-center flex flex-col items-center">
              <div class="w-20 h-20 mb-8 rounded-full bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
                <CheckCircle2Icon class="w-10 h-10 text-green-500" />
              </div>
              <h3 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-4">Order Confirmed</h3>
              <p class="text-zinc-500 dark:text-zinc-400 mb-10 leading-relaxed max-w-sm mx-auto">
                Your order has been placed successfully. We'll send you an email confirmation shortly.
              </p>
              
              <button @click="$emit('close', true)" class="btn-primary w-full py-4 text-sm">
                View My Orders
              </button>
            </div>

            <!-- Checkout Form -->
            <div v-else class="flex flex-col h-full max-h-[85vh]">
              <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800/50 flex items-center justify-between sticky top-0 bg-surface dark:bg-surface-dark z-10">
                <div>
                  <h3 class="text-xl font-display font-medium text-zinc-900 dark:text-zinc-50">Checkout</h3>
                  <p class="text-sm text-zinc-500 dark:text-zinc-400">Complete your order securely.</p>
                </div>
                <button @click="$emit('close', false)" class="p-2 -mr-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50 transition-colors">
                  <XIcon class="w-5 h-5" />
                </button>
              </div>

              <div class="p-8 overflow-y-auto space-y-8 no-scrollbar">
                <!-- Address Section -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-zinc-900 dark:text-zinc-50">Delivery Address</label>
                    <button v-if="!showAddAddress" @click="showAddAddress = true" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">Add New</button>
                    <button v-else @click="showAddAddress = false" class="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-50">Cancel</button>
                  </div>

                  <Transition name="fade" mode="out-in">
                    <div v-if="showAddAddress" class="p-5 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200/50 dark:border-zinc-800/50 rounded-2xl space-y-4">
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input v-model="addressForm.receiver_name" type="text" placeholder="Receiver Name" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50" />
                        <input v-model="addressForm.phone_number" type="text" placeholder="Phone Number" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50" />
                      </div>
                      <textarea v-model="addressForm.full_address" placeholder="Full Address" rows="2" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all resize-none text-zinc-900 dark:text-zinc-50"></textarea>
                      <button @click="saveAddress" :disabled="savingAddress || !addressForm.receiver_name || !addressForm.phone_number || !addressForm.full_address" class="btn-primary w-full py-3 text-sm disabled:opacity-50">
                        {{ savingAddress ? 'Saving...' : 'Save Address' }}
                      </button>
                    </div>

                    <div v-else>
                      <div v-if="addressesLoading" class="h-[60px] skeleton rounded-xl"></div>
                      <select v-else-if="addresses.length" v-model="selectedAddressId"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm text-zinc-900 dark:text-zinc-50 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 cursor-pointer appearance-none transition-all">
                        <option v-for="addr in addresses" :key="addr.id" :value="addr.id">
                          {{ addr.receiver_name }} • {{ addr.phone_number }}
                        </option>
                      </select>
                      <div v-else class="p-4 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 text-center text-sm text-zinc-500">
                        No delivery addresses found.
                      </div>
                    </div>
                  </Transition>
                </div>

                <!-- Coupon Section -->
                <div class="space-y-4">
                  <label class="text-sm font-medium text-zinc-900 dark:text-zinc-50">Discount Code</label>
                  <div class="flex gap-3">
                    <input v-model="couponCode" type="text" placeholder="Enter code" :disabled="couponApplied"
                      class="flex-1 px-4 py-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm uppercase text-zinc-900 dark:text-zinc-50 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all disabled:opacity-50 disabled:bg-zinc-50" />
                    <button v-if="!couponApplied" @click="verifyCoupon" :disabled="!couponCode || verifyingCoupon"
                      class="px-6 py-3 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-sm font-medium rounded-xl hover:bg-zinc-800 dark:hover:bg-white transition-colors disabled:opacity-50">
                      {{ verifyingCoupon ? '...' : 'Apply' }}
                    </button>
                    <button v-else @click="removeCoupon"
                      class="px-6 py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-sm font-medium rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                      Remove
                    </button>
                  </div>
                  <p v-if="couponSuccess" class="text-xs font-medium text-green-600">{{ couponSuccess }}</p>
                  <p v-if="couponError" class="text-xs font-medium text-red-500">{{ couponError }}</p>
                </div>

                <!-- Payment Method -->
                <div class="space-y-4">
                  <label class="text-sm font-medium text-zinc-900 dark:text-zinc-50">Payment Method</label>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-zinc-800"
                      :class="method === 'KHQR' ? 'border-primary-500 shadow-sm' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600'">
                      <input type="radio" v-model="method" value="KHQR" class="hidden" />
                      <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="method === 'KHQR' ? 'border-primary-500' : 'border-zinc-300 dark:border-zinc-600'">
                        <div v-if="method === 'KHQR'" class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">KHQR Payment</p>
                      </div>
                      <QrCodeIcon class="w-6 h-6 text-zinc-400" />
                    </label>

                    <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-zinc-800"
                      :class="method === 'COD' ? 'border-primary-500 shadow-sm' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600'">
                      <input type="radio" v-model="method" value="COD" class="hidden" />
                      <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="method === 'COD' ? 'border-primary-500' : 'border-zinc-300 dark:border-zinc-600'">
                        <div v-if="method === 'COD'" class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">Cash on Delivery</p>
                      </div>
                      <BanknoteIcon class="w-6 h-6 text-zinc-400" />
                    </label>
                  </div>
                </div>

                <p v-if="error" class="text-sm text-red-500 flex items-center gap-2">
                  <AlertCircleIcon class="w-4 h-4 shrink-0" />
                  {{ error }}
                </p>
              </div>

              <!-- Footer Sticky -->
              <div class="p-8 border-t border-zinc-100 dark:border-zinc-800/50 bg-surface dark:bg-surface-dark mt-auto">
                <div class="flex items-center justify-between mb-6">
                  <span class="text-sm text-zinc-500">Total to pay</span>
                  <span class="text-2xl font-semibold text-zinc-900 dark:text-zinc-50">${{ finalTotal.toFixed(2) }}</span>
                </div>
                <button @click="handleCheckout" :disabled="loading || !selectedAddressId"
                  class="btn-primary w-full py-4 text-sm flex items-center justify-center gap-2 disabled:opacity-50">
                  <Loader2Icon v-if="loading" class="w-4 h-4 animate-spin" />
                  {{ loading ? 'Processing securely...' : `Pay $${finalTotal.toFixed(2)}` }}
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
import { ref, computed, inject, watch, onUnmounted } from 'vue'
import api from '@/api/axios.js'
import { useCartStore } from '@/stores/cart'
import { QrCode as QrCodeIcon, CheckCircle2 as CheckCircle2Icon, X as XIcon, Banknote as BanknoteIcon, AlertCircle as AlertCircleIcon, Loader2 as Loader2Icon } from '@lucide/vue'

const props = defineProps({ show: { type: Boolean, default: false } })
const emit  = defineEmits(['close'])

const toast   = inject('toast')
const cart    = useCartStore()

const method  = ref('KHQR')
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
const couponApplied = ref(null)
const verifyingCoupon = ref(false)
const couponError = ref('')
const couponSuccess = ref('')

const finalTotal = computed(() => {
  let subtotal = cart.cartTotal;
  if (!couponApplied.value) return subtotal;
  
  if (couponApplied.value.type === 'percent') {
    return Math.max(0, subtotal - (subtotal * couponApplied.value.value) / 100);
  }
  return Math.max(0, subtotal - couponApplied.value.value);
})

let pollInterval = null

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
        showQR.value = false
        success.value = true
        toast('Payment received successfully!', 'success')
      }
    } catch (e) {
      console.error('Polling error:', e)
    }
  }, 3000)
}

function handleClose() {
  stopPolling()
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
      payload.coupon_code = couponCode.value.toUpperCase()
    }

    const res = await api.post('/cart/checkout', payload)
    const data = res.data

    orderResult.value = data.order || data
    cart.items = []

    if (data.qr_code) {
      qrCode.value = data.qr_code
      showQR.value = true
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

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
