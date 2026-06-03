<template>
  <div>
    <section class="py-16 px-6 bg-white min-h-[70vh]">
      <div class="max-w-6xl mx-auto">
        <div class="mb-10">
          <h1 class="text-2xl font-bold text-slate-800">Shopping Cart</h1>
          <p class="text-sm text-slate-400 mt-1">{{ cart.totalItems }} item{{ cart.totalItems !== 1 ? 's' : '' }} in your cart</p>
        </div>

        <div v-if="cart.items.length" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Cart Items -->
          <div class="lg:col-span-2 space-y-4">
            <div v-for="item in cart.items" :key="item.id"
              class="flex gap-4 p-4 bg-white rounded-2xl border border-slate-100 hover:border-primary-200/50 hover:shadow-md transition-all duration-300 animate-slide-up">
              <img :src="item.image || 'https://placehold.co/100'" :alt="item.name"
                class="w-24 h-24 rounded-xl object-cover bg-slate-100 shrink-0" />
              <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-slate-800 truncate">{{ item.name }}</h3>
                <p class="text-lg font-bold text-primary-600 mt-1">${{ Number(item.price || 0).toFixed(2) }}</p>

                <div class="flex items-center justify-between mt-3">
                  <!-- Quantity Controls -->
                  <div class="flex items-center gap-0.5 bg-slate-100 rounded-xl">
                    <button @click="updateQty(item, item.quantity - 1)"
                      class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-slate-700 rounded-l-xl hover:bg-slate-200 transition-colors">
                      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" d="M20 12H4"/>
                      </svg>
                    </button>
                    <span class="w-10 text-center text-sm font-semibold text-slate-700">{{ item.quantity }}</span>
                    <button @click="updateQty(item, item.quantity + 1)"
                      class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-slate-700 rounded-r-xl hover:bg-slate-200 transition-colors">
                      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" d="M12 4v16m8-8H4"/>
                      </svg>
                    </button>
                  </div>

                  <button @click="removeItem(item)" class="text-red-500 hover:text-red-600 hover:bg-red-50 p-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="lg:col-span-1">
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 sticky top-24">
              <h3 class="text-base font-semibold text-slate-800 mb-5">Order Summary</h3>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between text-slate-500">
                  <span>Subtotal</span>
                  <span class="font-medium text-slate-700">${{ cart.cartTotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                  <span>Shipping</span>
                  <span class="font-medium text-emerald-600">Free</span>
                </div>
                <div class="border-t border-slate-200 pt-3 flex justify-between">
                  <span class="font-semibold text-slate-800">Total</span>
                  <span class="text-xl font-bold text-primary-600">${{ cart.cartTotal.toFixed(2) }}</span>
                </div>
              </div>

              <button @click="checkout"
                class="w-full mt-6 py-3.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 hover:-translate-y-0.5 active:scale-[0.98]">
                Proceed to Checkout
              </button>
            </div>
          </div>
        </div>

        <!-- Empty Cart -->
        <div v-else class="text-center py-24">
          <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-700 mb-2">Your cart is empty</h3>
          <p class="text-sm text-slate-400 mb-6">Add some products and come back!</p>
          <router-link to="/" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
            Continue Shopping
          </router-link>
        </div>
      </div>
    </section>

    <!-- Checkout Modal -->
    <CheckoutModal :show="showCheckout" @close="showCheckout = false" @success="onCheckoutSuccess" />

    <Footer />
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'
import CheckoutModal from '@/components/CheckoutModal.vue'
import Footer from '@/components/shop/Footer.vue'

const cart  = useCartStore()
const toast = inject('toast')
const router = useRouter()
const showCheckout = ref(false)

async function updateQty(item, qty) {
  if (qty < 1) return removeItem(item)
  try {
    await cart.updateQuantity(item.id, qty)
  } catch (e) {
    toast('Failed to update quantity', 'error')
  }
}

async function removeItem(item) {
  try {
    await cart.removeFromCart(item.id)
    toast('Item removed from cart', 'info')
  } catch (e) {
    toast('Failed to remove item', 'error')
  }
}

function checkout() {
  showCheckout.value = true
}

function onCheckoutSuccess(order) {
  showCheckout.value = false
  toast('Order placed successfully!', 'success')
  router.push(`/invoice/${order.id}`)
}
</script>
