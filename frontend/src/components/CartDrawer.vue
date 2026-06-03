<template>
  <!-- Backdrop -->
  <Transition name="backdrop">
    <div v-if="open" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm" @click="$emit('close')"></div>
  </Transition>

  <!-- Drawer Panel -->
  <Transition name="drawer">
    <aside v-if="open" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-cream-50 shadow-2xl flex flex-col border-l border-cream-200">

      <!-- Header -->
      <div class="flex items-center justify-between px-8 py-6 border-b border-cream-200 bg-white">
        <div class="flex items-center gap-3">
          <svg class="w-6 h-6 text-blush-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
          <h2 class="text-xl font-display font-bold text-charcoal-900">Shopping Cart</h2>
          <span v-if="cart.totalItems > 0" class="px-2.5 py-1 bg-blush-50 text-blush-600 text-[10px] font-bold rounded-full">
            {{ cart.totalItems }}
          </span>
        </div>
        <button @click="$emit('close')" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-cream-100 transition-colors" id="close-cart-drawer">
          <svg class="w-5 h-5 text-charcoal-800/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
      <div v-else-if="cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white">
        <div class="w-24 h-24 rounded-full bg-cream-50 border border-cream-200 flex items-center justify-center mb-6 shadow-sm">
          <svg class="w-12 h-12 text-blush-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
        </div>
        <h3 class="text-xl font-display font-bold text-charcoal-900 mb-2">Your cart is empty</h3>
        <p class="text-sm font-sans text-charcoal-800/60 mb-8">Add items to get started</p>
        <button @click="$emit('close')" class="px-8 py-3.5 bg-blush-500 text-white text-[11px] font-bold tracking-widest uppercase rounded-full hover:bg-blush-600 hover:shadow-lg hover:shadow-blush-500/20 transition-all">
          Browse Products
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else class="flex-1 overflow-y-auto p-6 space-y-4 bg-cream-50/50">
        <!-- Iterate over cart.items exactly as requested -->
        <div v-for="item in cart.items" :key="item.id"
          class="flex gap-4 p-4 rounded-3xl bg-white border border-cream-200 hover:border-blush-200 shadow-sm transition-colors group">
          
          <!-- Product Image -->
          <div class="w-20 h-20 rounded-2xl bg-cream-50 overflow-hidden shrink-0 border border-cream-100">
            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" @error="$event.target.src='https://placehold.co/64x64/fdfaf6/d4a96a?text=Product'" />
          </div>
          
          <!-- Details -->
          <div class="flex-1 min-w-0 flex flex-col justify-center">
            <h4 class="text-sm font-bold text-charcoal-900 truncate">{{ item.name }}</h4>
            <div class="flex items-center gap-2 mt-1">
              <div v-if="item.discount_percent > 0" class="inline-flex px-2 py-0.5 bg-blush-50 text-blush-600 text-[10px] font-bold tracking-wider uppercase rounded-full">
                -{{ item.discount_percent }}%
              </div>
              <p class="text-[11px] font-sans text-charcoal-800/40">Qty: {{ item.quantity }}</p>
            </div>
            <p class="text-base font-bold bg-gradient-to-r from-charcoal-900 to-charcoal-800 bg-clip-text text-transparent mt-1.5">${{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
          
          <!-- Remove -->
          <button @click="removeItem(item.id)"
            class="w-8 h-8 rounded-full flex items-center justify-center text-charcoal-800/20 hover:text-blush-500 hover:bg-blush-50 transition-all shrink-0 self-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="cart.items.length > 0" class="border-t border-cream-200 bg-white p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between">
          <span class="text-sm font-sans font-bold text-charcoal-800/40 uppercase tracking-widest">Subtotal</span>
          <span class="text-2xl font-display font-bold text-charcoal-900">${{ cart.cartTotal.toFixed(2) }}</span>
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
