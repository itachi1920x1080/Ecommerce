<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6">
    <div class="max-w-4xl mx-auto">

      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Shopping Cart</h1>
        <p class="text-sm text-slate-400 mt-1">{{ cart.totalItems }} items in your cart</p>
      </div>

      <!-- Loading -->
      <div v-if="cart.loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="flex gap-4 p-4 bg-white rounded-2xl border border-slate-200/60">
          <div class="w-20 h-20 skeleton rounded-xl shrink-0"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 skeleton w-1/2"></div>
            <div class="h-3 skeleton w-1/3"></div>
            <div class="h-5 skeleton w-20"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="cart.items.length === 0" class="bg-white rounded-2xl border border-slate-200/60 p-16 text-center">
        <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-slate-100 flex items-center justify-center">
          <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-slate-600 mb-2">Your cart is empty</h2>
        <p class="text-sm text-slate-400 mb-6">Start shopping to add items to your cart</p>
        <router-link to="/"
          class="inline-block px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
          Browse Products
        </router-link>
      </div>

      <!-- Cart Items -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Items Column -->
        <div class="lg:col-span-2 space-y-3">
          <div v-for="item in cart.items" :key="item.id"
            class="flex gap-4 p-4 bg-white rounded-2xl border border-slate-200/60 hover:shadow-md transition-shadow">
            <!-- Product Image -->
            <div class="w-20 h-20 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-100">
              <img :src="getImage(item)" :alt="getName(item)" class="w-full h-full object-cover" @error="$event.target.src='https://placehold.co/80x80/e2e8f0/94a3b8?text=P'" />
            </div>
            <!-- Details -->
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-slate-800 truncate">{{ getName(item) }}</h3>
              <p class="text-xs text-slate-400 mt-0.5">Qty: {{ item.quantity }}</p>
              <p class="text-xs text-slate-400">${{ Number(getPrice(item)).toFixed(2) }} each</p>
            </div>
            <!-- Price + Remove -->
            <div class="flex flex-col items-end justify-between shrink-0">
              <p class="text-base font-bold text-blue-600">${{ (getPrice(item) * item.quantity).toFixed(2) }}</p>
              <button @click="removeItem(item.id)"
                class="text-xs text-red-500 hover:text-red-600 font-medium hover:underline transition-colors">
                Remove
              </button>
            </div>
          </div>
        </div>

        <!-- Summary Column -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl border border-slate-200/60 p-6 sticky top-24">
            <h3 class="text-base font-semibold text-slate-800 mb-5">Order Summary</h3>
            <div class="space-y-3 mb-5">
              <div class="flex justify-between text-sm">
                <span class="text-slate-400">Subtotal ({{ cart.totalItems }} items)</span>
                <span class="text-slate-700 font-medium">${{ cart.grandTotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-slate-400">Shipping</span>
                <span class="text-emerald-600 font-medium">Free</span>
              </div>
              <hr class="border-slate-100" />
              <div class="flex justify-between">
                <span class="text-base font-semibold text-slate-800">Total</span>
                <span class="text-xl font-bold text-blue-600">${{ cart.grandTotal.toFixed(2) }}</span>
              </div>
            </div>
            <button @click="showCheckout = true"
              class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-200 active:scale-[0.98]"
              id="checkout-btn">
              Proceed to Checkout
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Modal -->
    <CheckoutModal :show="showCheckout" @close="handleCheckoutClose" @success="handleCheckoutSuccess" />
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'
import CheckoutModal from '@/components/CheckoutModal.vue'

const cart   = useCartStore()
const router = useRouter()
const toast  = inject('toast')
const showCheckout = ref(false)

// Helpers for nested product object
function getName(item) { return item.product?.name || item.name || 'Product' }
function getPrice(item) { return Number(item.product?.price || item.price || 0) }
function getImage(item) {
  return item.product?.full_image_url || item.product?.image || item.image || 'https://placehold.co/80x80/e2e8f0/94a3b8?text=P'
}

async function removeItem(id) {
  try {
    await cart.removeItem(id)
    toast('Item removed', 'success')
  } catch (e) {
    toast('Failed to remove item', 'error')
  }
}

function handleCheckoutClose() {
  showCheckout.value = false
}

function handleCheckoutSuccess() {
  showCheckout.value = false
}

onMounted(() => {
  cart.fetchCart()
})
</script>
