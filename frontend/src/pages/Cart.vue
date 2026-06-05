<template>
  <div class="bg-gradient-to-br from-white via-pink-50 to-pink-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 min-h-screen transition-colors">
    <section class="py-12 lg:py-20 px-4 sm:px-6">
      <div class="max-w-7xl mx-auto">
        <div class="mb-12 text-center">
          <h1 class="text-4xl lg:text-5xl font-display font-medium text-zinc-900 dark:text-white tracking-tight mb-3">Your Cart</h1>
          <p class="text-sm font-sans text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">{{ cart.totalItems }} item{{ cart.totalItems !== 1 ? 's' : '' }}</p>
        </div>

        <div v-if="cart.items.length" class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 relative items-start">
          
          <!-- Cart Items List -->
          <div class="lg:col-span-8 flex flex-col gap-6">
            <TransitionGroup name="fade">
              <div v-for="item in cart.items" :key="item.id"
                class="flex flex-col sm:flex-row gap-6 p-6 bg-white/70 dark:bg-zinc-900 backdrop-blur-sm border border-zinc-200 dark:border-zinc-800 hover:border-zinc-900 dark:hover:border-zinc-100 transition-all duration-500 group rounded-3xl">
                
                <!-- Product Image -->
                <div class="w-28 h-36 shrink-0 overflow-hidden bg-white/80 dark:bg-zinc-800 rounded-xl">
                  <img :src="item.image || 'https://placehold.co/200x300/fdfaf6/d4a96a?text=Product'" :alt="item.name"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                </div>
                
                <!-- Product Details -->
                <div class="flex-1 flex flex-col justify-between py-1">
                  <div class="flex justify-between items-start gap-4">
                    <div>
                      <span class="text-[9px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-1 block">{{ item.category?.name || 'Product' }}</span>
                      <h3 class="text-lg font-display font-medium text-zinc-900 dark:text-white mb-1 leading-tight">
                        <router-link :to="`/products/${item.product_id || item.id}`" class="hover:underline underline-offset-4">{{ item.name }}</router-link>
                      </h3>
                      <p v-if="item.variant" class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Variant: {{ item.variant.name || item.variant.type }}</p>
                    </div>
                    <button @click="removeItem(item)" class="text-zinc-400 dark:text-zinc-500 hover:text-red-500 dark:hover:text-red-400 transition-colors p-1" title="Remove Item">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>

                  <div class="flex items-center justify-between mt-6 pt-6 border-t border-zinc-100 dark:border-zinc-800/50">
                    <!-- Quantity Selector -->
                    <div class="flex items-center border border-zinc-200 dark:border-zinc-700 rounded-full overflow-hidden">
                      <button @click="updateQty(item, item.quantity - 1)"
                        class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <span class="text-lg leading-none mb-1">-</span>
                      </button>
                      <span class="w-8 text-center text-xs font-semibold text-zinc-900 dark:text-white">{{ item.quantity }}</span>
                      <button @click="updateQty(item, item.quantity + 1)"
                        class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <span class="text-lg leading-none mb-1">+</span>
                      </button>
                    </div>

                    <div class="text-lg font-medium text-zinc-900 dark:text-white">
                      ${{ (Number(item.price || 0) * item.quantity).toFixed(2) }}
                    </div>
                  </div>
                </div>
              </div>
            </TransitionGroup>
          </div>

          <!-- Order Summary -->
          <div class="lg:col-span-4 sticky top-28">
            <div class="bg-white/70 dark:bg-zinc-900 backdrop-blur-sm border border-zinc-200 dark:border-zinc-800 p-8 rounded-3xl">
              <h3 class="text-xl font-display font-medium text-zinc-900 dark:text-white mb-6 pb-4 border-b border-zinc-200 dark:border-zinc-800">Order Summary</h3>

              <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center text-sm">
                  <span class="text-zinc-500 dark:text-zinc-400">Subtotal</span>
                  <span class="font-medium text-zinc-900 dark:text-white">${{ cart.cartTotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                  <span class="text-zinc-500 dark:text-zinc-400">Shipping</span>
                  <span class="text-[10px] font-semibold uppercase tracking-widest text-zinc-900 dark:text-zinc-300">Calculated at checkout</span>
                </div>
              </div>
              
              <!-- Coupon Field -->
              <div class="mb-8 pb-8 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex gap-2">
                  <input type="text" placeholder="Promo Code" class="w-full px-4 py-3 text-xs bg-white/70 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-700 transition-all rounded-full" />
                  <button class="px-5 py-3 btn-primary text-[10px]">
                    Apply
                  </button>
                </div>
              </div>

              <div class="flex justify-between items-end mb-8">
                <span class="text-base font-semibold text-zinc-900 dark:text-white">Total</span>
                <span class="text-3xl font-display font-medium text-zinc-900 dark:text-white">${{ cart.cartTotal.toFixed(2) }}</span>
              </div>

              <button @click="proceedToCheckout"
                class="w-full py-4 btn-primary text-xs font-semibold tracking-widest uppercase">
                Checkout Securely
              </button>
              
              <p class="text-center text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mt-6 flex items-center justify-center gap-2">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Secure Encrypted Checkout
              </p>
            </div>
          </div>
        </div>

        <!-- Empty Cart -->
        <div v-else class="text-center py-24 md:py-32 bg-white/70 dark:bg-zinc-900 backdrop-blur-sm border border-zinc-200 dark:border-zinc-800 rounded-[2rem] transition-colors">
          <p class="text-sm font-sans text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">Your bag is empty</p>
          <h2 class="text-3xl font-display font-medium text-zinc-900 dark:text-white mb-8">Ready to discover something beautiful?</h2>
          <router-link to="/shop" class="inline-block px-10 py-4 btn-primary text-[11px] font-semibold tracking-widest uppercase">
            Continue Shopping
          </router-link>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import { inject } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'
import Footer from '@/components/shop/Footer.vue'

const cart  = useCartStore()
const toast = inject('toast')
const router = useRouter()

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

function proceedToCheckout() {
  if (!cart.items.length) {
    toast('Your cart is empty', 'error')
    return
  }
  router.push('/checkout')
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}
.fade-leave-active {
  position: absolute;
}
</style>