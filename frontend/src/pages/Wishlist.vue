<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-pink-50 to-pink-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 pt-32 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-12 border-b border-zinc-200 dark:border-zinc-800 pb-8 flex items-end justify-between">
        <div>
          <h1 class="text-4xl sm:text-5xl font-display font-medium text-zinc-900 dark:text-white tracking-tight mb-4">My Wishlist</h1>
          <p class="text-zinc-500 dark:text-zinc-400 text-lg">
            {{ items.length }} item{{ items.length !== 1 ? 's' : '' }} saved
          </p>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <SkeletonCard v-for="i in 4" :key="i" />
      </div>

      <!-- Wishlist Grid -->
      <div v-else-if="items.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <ProductCard 
          v-for="item in items" 
          :key="item.id"
          :product="item.product"
          :isWishlisted="true"
          @addToCart="moveToCart"
          @toggleWishlist="removeFromWishlist(item)"
          @click="$router.push(`/products/${item.product?.id}`)"
          class="cursor-pointer"
        />
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-32">
        <div class="w-24 h-24 mx-auto mb-8 bg-white/60 dark:bg-zinc-900 backdrop-blur-sm rounded-full flex items-center justify-center">
          <HeartIcon class="w-10 h-10 text-zinc-300 dark:text-zinc-700" />
        </div>
        <h3 class="text-2xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-3">Your wishlist is empty</h3>
        <p class="text-zinc-500 dark:text-zinc-400 mb-10 max-w-sm mx-auto font-light">Save your favorite products here to easily find them later when you're ready to purchase.</p>
        <router-link to="/shop" class="btn-primary px-10 py-4 text-base inline-flex items-center justify-center">
          Browse Collection
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { useCartStore } from '@/stores/cart'
import ProductCard from '@/components/shop/ProductCard.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import { Heart as HeartIcon } from '@lucide/vue'

const cart = useCartStore()
const toast = inject('toast')
const items = ref([])
const loading = ref(true)

async function fetchWishlist() {
  loading.value = true
  try {
    const res = await api.get('/wishlists')
    items.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load wishlist', 'error')
  } finally {
    loading.value = false
  }
}

async function removeFromWishlist(item) {
  try {
    await api.post(`/wishlists/${item.product_id || item.product?.id}/toggle`)
    items.value = items.value.filter(i => i.id !== item.id)
    toast('Removed from wishlist', 'info')
  } catch (e) {
    toast('Failed to remove item', 'error')
  }
}

async function moveToCart(product) {
  try {
    await cart.addToCart(product)
    toast(`${product?.name} added to cart!`, 'success')
  } catch (e) {
    toast('Failed to add to cart', 'error')
  }
}

onMounted(fetchWishlist)
</script>