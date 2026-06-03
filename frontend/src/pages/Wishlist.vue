<template>
  <div>
    <section class="py-16 px-6 bg-white min-h-[70vh]">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h1 class="text-2xl font-bold text-slate-800">My Wishlist</h1>
            <p class="text-sm text-slate-400 mt-1">{{ items.length }} item{{ items.length !== 1 ? 's' : '' }} saved</p>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          <div v-for="i in 4" :key="i" class="bg-white rounded-2xl overflow-hidden border border-slate-100">
            <div class="aspect-square skeleton"></div>
            <div class="p-4 space-y-3">
              <div class="h-4 w-3/4 skeleton"></div>
              <div class="h-8 w-20 skeleton rounded-xl"></div>
            </div>
          </div>
        </div>

        <!-- Wishlist Grid -->
        <div v-else-if="items.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          <div v-for="item in items" :key="item.id" class="group relative bg-white rounded-2xl overflow-hidden border border-slate-100 hover:border-primary-200/50 transition-all duration-500 hover:shadow-xl hover:shadow-primary-500/10 hover:-translate-y-1 animate-slide-up">
            <div class="relative aspect-square overflow-hidden bg-slate-100">
              <img :src="item.product?.full_image_url || item.product?.image || 'https://placehold.co/400'" :alt="item.product?.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" />
              <button @click="removeFromWishlist(item)" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold text-slate-800 line-clamp-1">{{ item.product?.name }}</h3>
              <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                <span class="text-lg font-bold text-primary-600">${{ Number(item.product?.price || 0).toFixed(2) }}</span>
                <button @click="moveToCart(item)" class="px-4 py-2 bg-primary-50 hover:bg-primary-600 text-primary-600 hover:text-white text-xs font-semibold rounded-xl transition-all duration-300 active:scale-95">
                  Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty -->
        <div v-else class="text-center py-24">
          <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-700 mb-2">Your wishlist is empty</h3>
          <p class="text-sm text-slate-400 mb-6">Save your favorite products here for later</p>
          <router-link to="/" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
            Browse Products
          </router-link>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { useCartStore } from '@/stores/cart'
import Footer from '@/components/shop/Footer.vue'

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

async function moveToCart(item) {
  try {
    await cart.addToCart(item.product)
    toast(`${item.product?.name} added to cart!`, 'success')
  } catch (e) {
    toast('Failed to add to cart', 'error')
  }
}

onMounted(fetchWishlist)
</script>
