<template>
  <div class="bg-surface dark:bg-surface-dark min-h-screen pt-20">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
      <!-- Checkout Header -->
      <div class="mb-12">
        <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 text-center mb-8">Secure Checkout</h1>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Main Checkout Area (8 cols) -->
        <div class="lg:col-span-8">
          
          <div class="bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/50 rounded-3xl p-8 shadow-sm">
            
            <div v-if="success" class="text-center py-12">
              <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
                <CheckCircle2Icon class="w-10 h-10 text-green-500" />
              </div>
              <h3 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-4">Order Placed!</h3>
              <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8 max-w-sm mx-auto">
                Your order has been placed successfully. We'll send you an email confirmation shortly.
              </p>
              <router-link to="/my-orders" class="btn-primary inline-flex px-8 py-4 text-sm">
                View My Orders
              </router-link>
            </div>

            <div v-else-if="showQR" class="text-center py-12 flex flex-col items-center">
              <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center">
                <QrCodeIcon class="w-8 h-8 text-primary-600 dark:text-primary-400" />
              </div>
              <h3 class="text-2xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">Scan to Pay</h3>
              <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8">Scan this QR code with your KHQR app to complete payment</p>
              
              <div class="p-6 bg-white border border-zinc-200 rounded-3xl shadow-sm mb-8 inline-block max-w-[240px]">
                <img :src="qrCode" alt="KHQR Payment" class="w-full h-full object-contain" />
              </div>

              <button @click="router.push('/my-orders')" class="btn-primary w-full max-w-xs py-4 text-sm">
                I've made the payment
              </button>
            </div>

            <div v-else class="space-y-12">
              
              <!-- Step 1: Address -->
              <div>
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-50 mb-6 flex items-center gap-3">
                  <span class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-sm font-semibold">1</span>
                  Delivery Address
                </h2>
                
                <div class="pl-11 space-y-4">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input v-model="checkoutForm.receiver_name" type="text" placeholder="Receiver Name" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:border-primary-500 transition-all" />
                    <input v-model="checkoutForm.phone_number" type="text" placeholder="Phone Number" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:border-primary-500 transition-all" />
                  </div>
                  <textarea v-model="checkoutForm.full_address" placeholder="Full Address" rows="3" class="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm focus:outline-none focus:border-primary-500 transition-all resize-none"></textarea>
                </div>
              </div>

              <!-- Step 2: Payment Method -->
              <div>
                <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-50 mb-6 flex items-center gap-3">
                  <span class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-sm font-semibold">2</span>
                  Payment Method
                </h2>
                
                <div class="pl-11 grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <label class="flex items-center gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all bg-white dark:bg-zinc-800"
                    :class="checkoutForm.payment_method === 'KHQR' ? 'border-primary-500 shadow-sm' : 'border-zinc-200 dark:border-zinc-700'">
                    <input type="radio" v-model="checkoutForm.payment_method" value="KHQR" class="hidden" />
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="checkoutForm.payment_method === 'KHQR' ? 'border-primary-500' : 'border-zinc-300 dark:border-zinc-600'">
                      <div v-if="checkoutForm.payment_method === 'KHQR'" class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">KHQR Payment</p>
                    </div>
                    <QrCodeIcon class="w-6 h-6 text-zinc-400" />
                  </label>

                  <label class="flex items-center gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all bg-white dark:bg-zinc-800"
                    :class="checkoutForm.payment_method === 'COD' ? 'border-primary-500 shadow-sm' : 'border-zinc-200 dark:border-zinc-700'">
                    <input type="radio" v-model="checkoutForm.payment_method" value="COD" class="hidden" />
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="checkoutForm.payment_method === 'COD' ? 'border-primary-500' : 'border-zinc-300 dark:border-zinc-600'">
                      <div v-if="checkoutForm.payment_method === 'COD'" class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">Cash on Delivery</p>
                    </div>
                    <BanknoteIcon class="w-6 h-6 text-zinc-400" />
                  </label>
                </div>
              </div>

              <!-- Action -->
              <div class="pl-11 pt-4">
                <button @click="handleCheckout" :disabled="isSubmitting || !isFormValid"
                  class="btn-primary w-full py-4 text-sm disabled:opacity-50 flex justify-center items-center gap-2">
                  <Loader2Icon v-if="isSubmitting" class="w-4 h-4 animate-spin" />
                  {{ isSubmitting ? 'Processing securely...' : `Pay $${cart.cartTotal.toFixed(2)}` }}
                </button>
              </div>

            </div>

          </div>
        </div>

        <!-- Order Summary Sidebar (4 cols) -->
        <div class="lg:col-span-4">
          <div class="bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/50 rounded-3xl p-8 sticky top-28 shadow-sm">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-50 mb-6">Order Summary</h3>
            
            <div class="space-y-4 mb-6 pb-6 border-b border-zinc-100 dark:border-zinc-800/50 max-h-[40vh] overflow-y-auto no-scrollbar">
              <div v-for="item in cart.items" :key="item.id" class="flex gap-4">
                <div class="w-16 h-16 rounded-xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden shrink-0 border border-zinc-200/50 dark:border-zinc-700/50">
                  <img :src="item.image || 'https://placehold.co/100'" :alt="item.name" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 flex flex-col justify-center">
                  <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50 line-clamp-1">{{ item.name }}</p>
                  <p class="text-xs text-zinc-500 mt-1">Qty: {{ item.quantity }}</p>
                  <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-50 mt-1">${{ (Number(item.price || 0) * item.quantity).toFixed(2) }}</p>
                </div>
              </div>
            </div>

            <div class="space-y-3 mb-6 text-sm">
              <div class="flex justify-between text-zinc-500">
                <span>Subtotal</span>
                <span class="font-medium text-zinc-900 dark:text-zinc-50">${{ cart.cartTotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-zinc-500">
                <span>Shipping</span>
                <span class="font-medium text-zinc-900 dark:text-zinc-50">Free</span>
              </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-zinc-100 dark:border-zinc-800/50">
              <span class="text-sm font-medium text-zinc-900 dark:text-zinc-50">Total</span>
              <span class="text-2xl font-semibold text-zinc-900 dark:text-zinc-50">${{ cart.cartTotal.toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios.js'
import Footer from '@/components/shop/Footer.vue'
import { CheckCircle2 as CheckCircle2Icon, QrCode as QrCodeIcon, Banknote as BanknoteIcon, Loader2 as Loader2Icon } from '@lucide/vue'

const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()
const toast = inject('toast')

const isSubmitting = ref(false)
const success = ref(false)
const showQR = ref(false)
const qrCode = ref('')
const orderResult = ref(null)

const checkoutForm = ref({
  receiver_name: '',
  phone_number: '',
  full_address: '',
  payment_method: 'KHQR'
})

onMounted(() => {
  if (cart.items.length === 0) {
    toast('Your cart is empty', 'error')
    router.push('/shop')
  }
})

const isFormValid = computed(() => {
  return checkoutForm.value.receiver_name.length > 2 &&
         checkoutForm.value.phone_number.length > 5 &&
         checkoutForm.value.full_address.length > 10
})

async function handleCheckout() {
  if (!isFormValid.value) return

  isSubmitting.value = true
  try {
    // Save address first
    const addrRes = await api.post('/addresses', {
      receiver_name: checkoutForm.value.receiver_name,
      phone_number: checkoutForm.value.phone_number,
      full_address: checkoutForm.value.full_address
    })
    const newAddr = addrRes.data.data || addrRes.data.address || addrRes.data

    const payload = {
      payment_method: checkoutForm.value.payment_method,
      address_id: newAddr.id,
    }

    const res = await api.post('/cart/checkout', payload)
    const data = res.data

    orderResult.value = data.order || data
    cart.items = []

    if (data.qr_code) {
      qrCode.value = data.qr_code
      showQR.value = true
    } else {
      success.value = true
    }
    
    toast('Order placed successfully!', 'success')
  } catch (error) {
    toast(error.response?.data?.message || 'Checkout failed. Please try again.', 'error')
  } finally {
    isSubmitting.value = false
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
