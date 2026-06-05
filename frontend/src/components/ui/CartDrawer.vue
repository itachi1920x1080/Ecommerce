<template>
  <Transition name="backdrop">
    <div v-if="open" class="fixed inset-0 z-[100] bg-black/40 backdrop-blur-md" @click="$emit('close')"></div>
  </Transition>

  <!-- Mobile: bottom sheet | Desktop: side drawer -->
  <Transition name="drawer">
    <aside v-if="open"
      class="fixed z-[101] flex flex-col bg-white dark:bg-zinc-950 shadow-2xl border-zinc-200/50 dark:border-zinc-800/50
             inset-x-0 bottom-0 max-h-[90vh] rounded-t-[2.5rem] border-t
             sm:inset-x-auto sm:bottom-auto sm:top-0 sm:right-0 sm:h-full sm:max-h-none sm:w-full sm:max-w-[440px] sm:rounded-none sm:border-t-0 sm:border-l">

      <div class="sm:hidden flex justify-center pt-4 pb-2">
        <div class="w-12 h-1.5 rounded-full bg-zinc-200 dark:bg-zinc-800"></div>
      </div>

      <div class="flex items-center justify-between px-6 sm:px-8 py-6 border-b border-zinc-100 dark:border-zinc-800/50">
        <div class="flex items-center gap-3">
          <ShoppingBagIcon class="w-6 h-6 text-zinc-900 dark:text-zinc-50" />
          <h2 class="text-xl font-display font-medium text-zinc-900 dark:text-zinc-50">Your Bag</h2>
          <span v-if="cart.totalItems > 0" class="flex items-center justify-center w-6 h-6 bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 text-xs font-semibold rounded-full">
            {{ cart.totalItems }}
          </span>
        </div>
        <button @click="$emit('close')" class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" id="close-cart-drawer">
          <XIcon class="w-5 h-5" />
        </button>
      </div>

      <div v-if="cart.loading" class="flex-1 p-6 space-y-6 overflow-y-auto">
        <div v-for="i in 3" :key="i" class="flex gap-4">
          <div class="w-24 h-24 skeleton rounded-2xl shrink-0"></div>
          <div class="flex-1 space-y-3 py-2">
            <div class="h-4 skeleton w-3/4 rounded-full"></div>
            <div class="h-4 skeleton w-1/4 rounded-full"></div>
          </div>
        </div>
      </div>

      <div v-else-if="cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
        <div class="w-24 h-24 rounded-full bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-6">
          <ShoppingBagIcon class="w-10 h-10 text-zinc-300 dark:text-zinc-600" />
        </div>
        <h3 class="text-xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">Your bag is empty</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8">Looks like you haven't added anything yet.</p>
        <button @click="$emit('close')" class="btn-primary px-8 py-3.5 text-sm">
          Continue Shopping
        </button>
      </div>

      <div v-else class="flex-1 overflow-y-auto p-6 space-y-4">
        <div v-for="item in cart.items" :key="item.id"
          class="flex gap-4 p-4 rounded-3xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800/50 hover:border-zinc-200 dark:hover:border-zinc-700 transition-colors group">
          <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white dark:bg-zinc-900 overflow-hidden shrink-0 border border-zinc-100 dark:border-zinc-800/50 relative">
            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" @error="$event.target.src='https://placehold.co/100x100/f4f4f5/52525b?text=Product'" />
          </div>
          
          <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
            <div>
              <div class="flex justify-between items-start gap-2">
                <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-50 truncate">{{ item.name }}</h4>
                <button @click="removeItem(item.id)" class="text-zinc-400 hover:text-red-500 transition-colors p-1 -mt-1 -mr-1">
                  <Trash2Icon class="w-4 h-4" />
                </button>
              </div>
              <div class="flex items-center gap-2 mt-1">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Qty: {{ item.quantity }}</p>
                <div v-if="item.discount_percent > 0" class="inline-flex px-2 py-0.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[10px] font-semibold tracking-wider uppercase rounded-full">
                  -{{ item.discount_percent }}%
                </div>
              </div>
            </div>
            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-50 mt-2">${{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
        </div>
      </div>

      <div v-if="cart.items.length > 0" class="border-t border-zinc-100 dark:border-zinc-800/50 bg-white dark:bg-zinc-950 p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between">
          <span class="text-sm text-zinc-500 dark:text-zinc-400">Subtotal</span>
          <span class="text-2xl font-semibold text-zinc-900 dark:text-zinc-50">${{ cart.cartTotal.toFixed(2) }}</span>
        </div>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center">Shipping & taxes calculated at checkout.</p>
        <button @click="showCheckoutModal = true"
          class="btn-primary w-full py-4 text-sm"
          id="checkout-btn">
          Checkout
        </button>
      </div>
    </aside>
  </Transition>

  <CheckoutModal :show="showCheckoutModal" @close="handleCheckoutClose" />
</template>

<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import CheckoutModal from '@/components/ui/CheckoutModal.vue'
import { ShoppingBag as ShoppingBagIcon, X as XIcon, Trash2 as Trash2Icon } from '@lucide/vue'

defineProps({ open: { type: Boolean, default: false } })
const emit = defineEmits(['close'])

const cart = useCartStore()
const toast = inject('toast')
const router = useRouter()
const showCheckoutModal = ref(false)

async function removeItem(id) {
  try {
    await cart.removeFromCart(id)
    toast('Item removed from cart', 'success')
  } catch {
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
