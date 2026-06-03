<template>
  <!-- Backdrop -->
  <Transition name="backdrop">
    <div v-if="open" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm" @click="$emit('close')"></div>
  </Transition>

  <!-- Drawer Panel -->
  <Transition name="drawer">
    <aside v-if="open" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-white shadow-2xl flex flex-col">

      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
          <h2 class="text-lg font-semibold text-slate-800">Shopping Cart</h2>
          <span v-if="cart.totalItems > 0" class="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
            {{ cart.totalItems }}
          </span>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-slate-100 transition-colors" id="close-cart-drawer">
          <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Loading -->
      <div v-if="cart.loading" class="flex-1 p-6 space-y-4">
        <div v-for="i in 3" :key="i" class="flex gap-3">
          <div class="w-16 h-16 skeleton rounded-xl shrink-0"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 skeleton w-3/4"></div>
            <div class="h-3 skeleton w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center p-6 text-center">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
          <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-600 mb-1">Your cart is empty</h3>
        <p class="text-sm text-slate-400 mb-5">Add items to get started</p>
        <button @click="$emit('close')" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors">
          Browse Products
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else class="flex-1 overflow-y-auto p-6 space-y-3">
        <!-- Iterate over cart.items exactly as requested -->
        <div v-for="item in cart.items" :key="item.id"
          class="flex gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition-colors">
          
          <!-- Product Image -->
          <div class="w-16 h-16 rounded-lg bg-white overflow-hidden shrink-0 border border-slate-100">
            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" @error="$event.target.src='https://placehold.co/64x64/e2e8f0/94a3b8?text=Product'" />
          </div>
          
          <!-- Details -->
          <div class="flex-1 min-w-0">
            <h4 class="text-sm font-medium text-slate-800 truncate">{{ item.name }}</h4>
            <div v-if="item.discount_percent > 0" class="inline-flex mt-0.5 px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded">
              -{{ item.discount_percent }}% OFF
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Qty: {{ item.quantity }}</p>
            <p class="text-sm font-semibold text-primary-600 mt-1">${{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
          
          <!-- Remove -->
          <button @click="removeItem(item.id)"
            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all shrink-0 self-start">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="cart.items.length > 0" class="border-t border-slate-100 p-6 space-y-4">
        <div class="flex items-center justify-between">
          <span class="text-sm text-slate-500">Subtotal</span>
          <span class="text-xl font-bold text-slate-800">${{ cart.cartTotal.toFixed(2) }}</span>
        </div>
        <button @click="showCheckoutModal = true"
          class="block w-full py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-semibold rounded-xl text-center hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-200 active:scale-[0.98]"
          id="checkout-btn">
          Checkout
        </button>
      </div>
    </aside>
  </Transition>

  <!-- Checkout Modal Instance -->
  <CheckoutModal 
    :show="showCheckoutModal" 
    @close="handleCheckoutClose"
  />

</template>

<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import CheckoutModal from '@/components/CheckoutModal.vue'

const props = defineProps({ open: { type: Boolean, default: false } })
const emit = defineEmits(['close'])

const cart = useCartStore()
const toast = inject('toast')
const router = useRouter()

const showCheckoutModal = ref(false)

async function removeItem(id) {
  try {
    await cart.removeFromCart(id)
    toast('Item removed from cart', 'success')
  } catch (e) {
    toast('Failed to remove item', 'error')
  }
}

function handleCheckoutClose(success) {
  showCheckoutModal.value = false
  if (success) {
    emit('close')
    router.push('/my-orders')
  }
}
</script>
