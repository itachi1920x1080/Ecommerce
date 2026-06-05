<template>
  <!-- Backdrop -->
  <Transition name="backdrop">
    <div v-if="open" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm" @click="$emit('close')"></div>
  </Transition>

  <!-- Drawer Panel -->
  <Transition name="drawer">
    <aside v-if="open" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-zinc-50 dark:bg-zinc-900 shadow-2xl flex flex-col border-l border-zinc-200 dark:border-zinc-700">

      <!-- Header -->
      <div class="flex items-center justify-between px-8 py-6 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <div class="flex items-center gap-3">
          <svg class="w-6 h-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
          <h2 class="text-xl font-display font-bold text-zinc-900 dark:text-zinc-50">Shopping Cart</h2>
          <span v-if="cart.totalItems > 0" class="px-2.5 py-1 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-[10px] font-bold rounded-full">
            {{ cart.totalItems }}
          </span>
        </div>
        <button @click="$emit('close')" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" id="close-cart-drawer">
          <svg class="w-5 h-5 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
      <div v-else-if="cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white dark:bg-zinc-900">
        <div class="w-24 h-24 rounded-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center mb-6 shadow-sm">
          <svg class="w-12 h-12 text-pink-300 dark:text-pink-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
        </div>
        <h3 class="text-xl font-display font-bold text-zinc-900 dark:text-zinc-50 mb-2">Your cart is empty</h3>
        <p class="text-sm font-sans text-zinc-500 dark:text-zinc-400 mb-8">Add items to get started</p>
        <button @click="$emit('close')" class="px-8 py-3.5 bg-pink-500 text-white text-[11px] font-bold tracking-widest uppercase rounded-full hover:bg-pink-600 hover:shadow-lg hover:shadow-pink-500/20 transition-all">
          Browse Products
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else class="flex-1 overflow-y-auto p-6 space-y-4 bg-zinc-50/50 dark:bg-zinc-900/50">
        <div v-for="item in cart.items" :key="item.id"
          class="flex gap-4 p-4 rounded-3xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-pink-200 dark:hover:border-pink-800 shadow-sm transition-colors group">
          
          <!-- Product Image -->
          <div class="w-20 h-20 rounded-2xl bg-zinc-50 dark:bg-zinc-700 overflow-hidden shrink-0 border border-zinc-100 dark:border-zinc-600">
            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" @error="$event.target.src='https://placehold.co/64x64/fdfaf6/d4a96a?text=Product'" />
          </div>
          
          <!-- Details -->
          <div class="flex-1 min-w-0 flex flex-col justify-center">
            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 truncate">{{ item.name }}</h4>
            <div class="flex items-center gap-2 mt-1">
              <div v-if="item.discount_percent > 0" class="inline-flex px-2 py-0.5 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-[10px] font-bold tracking-wider uppercase rounded-full">
                -{{ item.discount_percent }}%
              </div>
              <p class="text-[11px] font-sans text-zinc-400 dark:text-zinc-500">Qty: {{ item.quantity }}</p>
            </div>
            <p class="text-base font-bold text-zinc-900 dark:text-zinc-50 mt-1.5">${{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
          
          <!-- Remove -->
          <button @click="removeItem(item.id)"
            class="w-8 h-8 rounded-full flex items-center justify-center text-zinc-300 dark:text-zinc-600 hover:text-pink-500 hover:bg-pink-50 dark:hover:bg-pink-900/20 transition-all shrink-0 self-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="cart.items.length > 0" class="border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between">
          <span class="text-sm font-sans font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Subtotal</span>
          <span class="text-2xl font-display font-bold text-zinc-900 dark:text-zinc-50">${{ cart.cartTotal.toFixed(2) }}</span>
        </div>
        <button @click="showCheckoutModal = true"
          class="block w-full py-4 bg-gradient-to-r from-pink-400 to-pink-500 text-white text-[11px] font-bold uppercase tracking-widest rounded-full text-center hover:shadow-xl hover:shadow-pink-500/25 transition-all duration-300 active:scale-[0.98]"
          id="checkout-btn">
          Proceed to Checkout
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