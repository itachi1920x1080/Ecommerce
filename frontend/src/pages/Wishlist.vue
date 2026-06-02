<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">My Wishlist</h1>
        <p class="text-sm text-slate-400 mt-1">Products you've saved for later</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden">
          <div class="aspect-square skeleton"></div>
          <div class="p-4 space-y-3">
            <div class="h-4 skeleton w-3/4"></div>
            <div class="h-3 skeleton w-full"></div>
            <div class="h-9 skeleton w-24 rounded-xl"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="items.length === 0" class="bg-white rounded-2xl border border-slate-200/60 p-16 text-center">
        <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-slate-100 flex items-center justify-center">
          <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-slate-600 mb-2">No wishlist items yet</h2>
        <p class="text-sm text-slate-400 mb-6">Browse products and tap the heart to save your favorites</p>
        <router-link to="/" class="inline-block px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
          Browse Products
        </router-link>
      </div>

      <!-- Wishlist Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <div v-for="item in items" :key="item.id"
          class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-1 transition-all duration-300 group">
          <div class="relative aspect-square bg-slate-100 overflow-hidden">
            <img :src="getImage(item)" :alt="getProduct(item).name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" @error="$event.target.src='https://placehold.co/400x400/e2e8f0/94a3b8?text=Product'" />
            <!-- Remove from Wishlist -->
            <button @click="toggleWishlist(getProduct(item).id || item.product_id)"
              class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center shadow-sm text-red-500 hover:scale-110 transition-all">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>
          <div class="p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-1 line-clamp-1">{{ getProduct(item).name || 'Product' }}</h3>
            <p class="text-lg font-bold text-blue-600 mb-3">${{ Number(getProduct(item).price || 0).toFixed(2) }}</p>
            <button @click="addToCart(getProduct(item))"
              class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-200 active:scale-95">
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { useCartStore } from '@/stores/cart'

const cart    = useCartStore()
const toast   = inject('toast')
const items   = ref([])
const loading = ref(true)

// Handle different response shapes
function getProduct(item) { return item.product || item }
function getImage(item) {
  const p = getProduct(item)
  return p.full_image_url || p.image || 'https://placehold.co/400x400/e2e8f0/94a3b8?text=Product'
}

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

async function toggleWishlist(productId) {
  try {
    await api.post(`/wishlists/${productId}/toggle`)
    items.value = items.value.filter(i => {
      const pid = i.product?.id || i.product_id || i.id
      return pid !== productId
    })
    toast('Removed from wishlist', 'info')
  } catch (e) {
    toast('Failed to update wishlist', 'error')
  }
}

async function addToCart(product) {
  try {
    await cart.addToCart(product.id)
    toast(`${product.name} added to cart!`, 'success')
  } catch (e) {
    toast('Failed to add to cart', 'error')
  }
}

onMounted(fetchWishlist)
</script>
