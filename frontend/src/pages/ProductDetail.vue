<template>
  <div>
    <!-- Loading -->
    <div v-if="loading" class="py-20 px-6">
      <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="aspect-square skeleton rounded-2xl"></div>
        <div class="space-y-4">
          <div class="h-6 w-1/3 skeleton"></div>
          <div class="h-10 w-3/4 skeleton"></div>
          <div class="h-4 w-full skeleton"></div>
          <div class="h-4 w-2/3 skeleton"></div>
          <div class="h-12 w-40 skeleton rounded-xl mt-6"></div>
        </div>
      </div>
    </div>

    <template v-else-if="product">
      <!-- Product Hero -->
      <section class="py-12 px-6 bg-white">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10">
          <!-- Image -->
          <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-100 group">
            <img :src="product.full_image_url || product.image || 'https://placehold.co/600'" :alt="product.name"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
            <span v-if="product.category?.name" class="absolute top-4 left-4 px-3 py-1.5 bg-white/90 backdrop-blur text-xs font-semibold text-slate-600 rounded-lg shadow-sm">
              {{ product.category.name }}
            </span>
          </div>

          <!-- Info -->
          <div class="flex flex-col justify-center">
            <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">{{ product.category?.name || 'Product' }}</span>
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-4">{{ product.name }}</h1>
            <p class="text-slate-400 leading-relaxed mb-6">{{ product.description || 'No description available.' }}</p>

            <div class="text-3xl font-extrabold bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent mb-6">
              ${{ Number(product.price).toFixed(2) }}
            </div>

            <!-- Variants -->
            <div v-if="variants.length" class="mb-6">
              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Variants</p>
              <div class="flex flex-wrap gap-2">
                <button v-for="v in variants" :key="v.id"
                  @click="selectedVariant = v"
                  :class="selectedVariant?.id === v.id ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'"
                  class="px-4 py-2 rounded-xl text-sm font-medium border-2 transition-all duration-200">
                  {{ v.name || v.type }} <span v-if="v.price" class="text-xs text-slate-400">+${{ Number(v.price).toFixed(2) }}</span>
                </button>
              </div>
            </div>

            <!-- Stock -->
            <p v-if="product.stock !== undefined && product.stock !== null" class="text-sm mb-6"
              :class="product.stock > 0 ? 'text-emerald-600' : 'text-red-500'">
              {{ product.stock > 0 ? `✓ ${product.stock} in stock` : '✕ Out of stock' }}
            </p>

            <!-- Actions -->
            <div class="flex gap-3">
              <button @click="addToCart"
                :disabled="product.stock === 0"
                class="flex-1 sm:flex-none px-8 py-3.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                Add to Cart
              </button>
              <button @click="toggleWishlist"
                :class="isWishlisted ? 'bg-red-50 text-red-500 border-red-200' : 'bg-slate-50 text-slate-400 border-slate-200 hover:text-red-500 hover:border-red-200'"
                class="w-12 h-12 rounded-xl border-2 flex items-center justify-center transition-all duration-300">
                <svg class="w-5 h-5" :fill="isWishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Reviews Section -->
      <section class="py-16 px-6 bg-surface">
        <div class="max-w-4xl mx-auto">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="text-xl font-bold text-slate-800">Customer Reviews</h2>
              <p class="text-sm text-slate-400 mt-1">{{ reviews.length }} review{{ reviews.length !== 1 ? 's' : '' }}</p>
            </div>
          </div>

          <!-- Write Review -->
          <div v-if="auth.isLoggedIn" class="bg-white rounded-2xl border border-slate-100 p-6 mb-8">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Write a Review</h3>
            <div class="flex gap-1 mb-3">
              <button v-for="star in 5" :key="star" @click="reviewForm.rating = star"
                class="text-2xl transition-transform hover:scale-110"
                :class="star <= reviewForm.rating ? 'text-amber-400' : 'text-slate-200'">
                ★
              </button>
            </div>
            <textarea v-model="reviewForm.comment" rows="3" placeholder="Share your experience..."
              class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all resize-none mb-3"></textarea>
            <button @click="submitReview" :disabled="reviewSubmitting"
              class="px-6 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors disabled:opacity-50">
              {{ reviewSubmitting ? 'Submitting...' : 'Submit Review' }}
            </button>
          </div>

          <!-- Review List -->
          <div v-if="reviews.length" class="space-y-4">
            <div v-for="review in reviews" :key="review.id" class="bg-white rounded-2xl border border-slate-100 p-5 animate-slide-up">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold">
                  {{ (review.user?.name || 'U').charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-700">{{ review.user?.name || 'Anonymous' }}</p>
                  <div class="flex items-center gap-1">
                    <span v-for="s in 5" :key="s" class="text-sm" :class="s <= review.rating ? 'text-amber-400' : 'text-slate-200'">★</span>
                    <span class="text-xs text-slate-400 ml-2">{{ formatDate(review.created_at) }}</span>
                  </div>
                </div>
              </div>
              <p class="text-sm text-slate-500 leading-relaxed">{{ review.comment }}</p>
            </div>
          </div>
          <div v-else class="text-center py-12 text-sm text-slate-400">No reviews yet. Be the first to review!</div>
        </div>
      </section>
    </template>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios.js'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import Footer from '@/components/shop/Footer.vue'

const route = useRoute()
const auth  = useAuthStore()
const cart  = useCartStore()
const toast = inject('toast')

const product         = ref(null)
const variants        = ref([])
const reviews         = ref([])
const selectedVariant = ref(null)
const isWishlisted    = ref(false)
const loading         = ref(true)
const reviewSubmitting = ref(false)
const reviewForm      = ref({ rating: 5, comment: '' })

async function fetchProduct() {
  loading.value = true
  try {
    const [pRes, vRes, rRes] = await Promise.all([
      api.get(`/products/${route.params.id}`),
      api.get(`/products/${route.params.id}/variants`).catch(() => ({ data: [] })),
      api.get(`/products/${route.params.id}/reviews`).catch(() => ({ data: [] })),
    ])
    product.value  = pRes.data.data || pRes.data
    variants.value = vRes.data.data || vRes.data || []
    reviews.value  = rRes.data.data || rRes.data || []

    if (auth.isLoggedIn) {
      try {
        const wRes = await api.get('/wishlists')
        const wData = wRes.data.data || wRes.data || []
        isWishlisted.value = wData.some(w => (w.product_id || w.id) == route.params.id)
      } catch (_) {}
    }
  } catch (e) {
    toast('Failed to load product', 'error')
  } finally {
    loading.value = false
  }
}

async function addToCart() {
  if (!auth.isLoggedIn) { toast('Please login to add to cart', 'info'); return }
  try {
    await cart.addToCart(product.value)
    toast(`${product.value.name} added to cart!`, 'success')
  } catch (e) { toast('Failed to add to cart', 'error') }
}

async function toggleWishlist() {
  if (!auth.isLoggedIn) { toast('Please login to use wishlist', 'info'); return }
  try {
    await api.post(`/wishlists/${product.value.id}/toggle`)
    isWishlisted.value = !isWishlisted.value
    toast(isWishlisted.value ? 'Added to wishlist!' : 'Removed from wishlist', 'info')
  } catch (e) { toast('Failed to update wishlist', 'error') }
}

async function submitReview() {
  if (!reviewForm.value.comment.trim()) { toast('Please write a comment', 'info'); return }
  reviewSubmitting.value = true
  try {
    const res = await api.post(`/products/${route.params.id}/reviews`, reviewForm.value)
    reviews.value.unshift(res.data.data || res.data)
    reviewForm.value = { rating: 5, comment: '' }
    toast('Review submitted!', 'success')
  } catch (e) { toast(e.response?.data?.message || 'Failed to submit review', 'error') }
  finally { reviewSubmitting.value = false }
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(fetchProduct)
</script>
