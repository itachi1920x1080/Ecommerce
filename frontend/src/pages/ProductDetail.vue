<template>
  <div class="bg-gradient-to-br from-white via-pink-50 to-pink-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 min-h-screen pt-20">
    <!-- Loading State -->
    <div v-if="loading" class="py-12 px-6 max-w-7xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        <div class="aspect-[4/5] skeleton rounded-3xl"></div>
        <div class="space-y-6 pt-10">
          <div class="h-4 w-32 skeleton rounded-full"></div>
          <div class="h-12 w-3/4 skeleton rounded-full"></div>
          <div class="h-8 w-24 skeleton rounded-full"></div>
          <div class="h-24 w-full skeleton rounded-2xl mt-8"></div>
        </div>
      </div>
    </div>

    <template v-else-if="product">
      <!-- Breadcrumbs -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
          <router-link to="/" class="hover:text-zinc-900 dark:hover:text-zinc-50 transition-colors">Home</router-link>
          <span>/</span>
          <router-link to="/shop" class="hover:text-zinc-900 dark:hover:text-zinc-50 transition-colors">Shop</router-link>
          <span>/</span>
          <span class="text-zinc-900 dark:text-zinc-50 truncate">{{ product.name }}</span>
        </div>
      </div>

      <!-- Split Layout Hero -->
      <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 items-start">
          
          <!-- Left: Sticky Gallery -->
          <div class="w-full lg:w-1/2 lg:sticky lg:top-32 flex flex-col gap-4">
            <div class="relative aspect-[4/5] bg-white/50 dark:bg-zinc-900 backdrop-blur-sm rounded-[2.5rem] overflow-hidden border border-zinc-200/50 dark:border-zinc-800/50 group">
              <img :src="activeImage" :alt="product.name" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
              
              <div class="absolute top-6 left-6 flex flex-col gap-2 z-10">
                <span v-if="product.discount_percent > 0" class="px-4 py-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs uppercase tracking-widest font-bold rounded-full shadow-lg">
                  -{{ product.discount_percent }}% OFF
                </span>
              </div>
            </div>

            <!-- Thumbnails -->
            <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
              <button 
                v-for="(img, idx) in productImages" 
                :key="idx"
                @click="activeImage = img"
                class="w-20 aspect-square rounded-2xl overflow-hidden bg-white/50 dark:bg-zinc-900 border-2 transition-all duration-300 shrink-0"
                :class="activeImage === img ? 'border-primary-500 opacity-100' : 'border-transparent opacity-60 hover:opacity-100'"
              >
                <img :src="img" :alt="`${product.name} view ${idx + 1}`" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Right: Scrollable Details -->
          <div class="w-full lg:w-1/2 flex flex-col pt-4 lg:pt-10">
            <span class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-widest mb-4">
              {{ product.category?.name || 'Exclusive Collection' }}
            </span>
            
            <h1 class="text-4xl sm:text-5xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-6 leading-tight">
              {{ product.name }}
            </h1>
            
            <div class="flex items-center gap-4 mb-8">
              <div class="flex items-center gap-1 text-primary-500">
                <StarIcon class="w-4 h-4 fill-current" v-for="s in 5" :key="s" />
              </div>
              <a href="#reviews" class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-50 underline underline-offset-4 transition-colors">
                {{ reviews.length }} Reviews
              </a>
            </div>

            <!-- Price -->
            <div class="flex items-end gap-3 mb-10 pb-10 border-b border-zinc-200/50 dark:border-zinc-800/50">
              <span class="text-3xl sm:text-4xl font-semibold text-zinc-900 dark:text-zinc-50">
                ${{ product.discount_percent > 0 ? (product.price - (product.price * product.discount_percent / 100)).toFixed(2) : Number(product.price).toFixed(2) }}
              </span>
              <span v-if="product.discount_percent > 0" class="text-lg text-zinc-400 line-through mb-1">
                ${{ Number(product.price).toFixed(2) }}
              </span>
            </div>

            <!-- Description -->
            <p class="text-base text-zinc-600 dark:text-zinc-400 leading-relaxed mb-10 font-light">
              {{ product.description || 'Experience the ultimate luxury. Crafted with premium ingredients to elevate your daily routine.' }}
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 mb-12">
              <button 
                v-if="product.stock > 0"
                @click="addToCart"
                class="btn-primary flex-1 py-4 text-sm"
              >
                <ShoppingBagIcon class="w-5 h-5" />
                Add to Bag
              </button>
              <button 
                v-else-if="product.out_of_stock_status === 'preorder'"
                @click="addToCart"
                class="btn-primary flex-1 py-4 text-sm bg-zinc-800"
              >
                <ShoppingBagIcon class="w-5 h-5" />
                Pre-order
              </button>
              <button 
                v-else
                disabled
                class="btn-primary flex-1 py-4 text-sm opacity-50 cursor-not-allowed"
              >
                <ShoppingBagIcon class="w-5 h-5" />
                Out of Stock
              </button>
              
              <button 
                @click="toggleWishlist"
                class="px-6 py-4 rounded-full border flex items-center justify-center transition-all duration-300"
                :class="isWishlisted 
                  ? 'border-primary-500 text-primary-600 bg-primary-50 dark:bg-primary-500/10' 
                  : 'border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-50 hover:bg-white/70 dark:hover:bg-zinc-800'"
              >
                <HeartIcon class="w-5 h-5 transition-transform" :class="isWishlisted ? 'fill-current scale-110' : ''" />
              </button>
            </div>

            <!-- Accordions -->
            <div class="flex flex-col border-t border-zinc-200/50 dark:border-zinc-800/50">
              <details class="group" open>
                <summary class="flex justify-between items-center font-medium cursor-pointer list-none py-6 text-zinc-900 dark:text-zinc-50">
                  <span>Product Details</span>
                  <span class="transition group-open:rotate-180">
                    <ChevronDownIcon class="w-5 h-5 text-zinc-400" />
                  </span>
                </summary>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 font-light pb-6 leading-relaxed">
                  Carefully formulated to provide an exquisite experience. Every detail of this product was considered to ensure it meets our highest standards of luxury and efficacy. Discover the perfect addition to your daily ritual.
                  <ul class="mt-4 space-y-2 list-disc list-inside">
                    <li>Premium quality materials</li>
                    <li>Ethically sourced</li>
                    <li>Designed for longevity</li>
                  </ul>
                </div>
              </details>
              
              <div class="h-px bg-zinc-200/50 dark:bg-zinc-800/50 w-full"></div>

              <details class="group">
                <summary class="flex justify-between items-center font-medium cursor-pointer list-none py-6 text-zinc-900 dark:text-zinc-50">
                  <span>Shipping & Returns</span>
                  <span class="transition group-open:rotate-180">
                    <ChevronDownIcon class="w-5 h-5 text-zinc-400" />
                  </span>
                </summary>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 font-light pb-6 leading-relaxed">
                  Enjoy complimentary express shipping on all orders over $40. If you are not completely satisfied with your purchase, you may return it within 30 days for a full refund.
                </div>
              </details>
            </div>
          </div>
        </div>
      </section>

      <!-- Reviews Section -->
      <section id="reviews" class="bg-pink-50/60 dark:bg-zinc-900/30 border-t border-zinc-200/50 dark:border-zinc-800/50 py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50">Customer Reviews</h2>
            <span class="px-4 py-2 bg-white/70 dark:bg-zinc-800 backdrop-blur-sm rounded-full text-xs font-semibold text-zinc-900 dark:text-zinc-50">{{ reviews.length }} Reviews</span>
          </div>

          <!-- Write Review -->
          <div v-if="auth.isLoggedIn" class="bg-white/70 dark:bg-zinc-900 backdrop-blur-sm rounded-3xl p-8 shadow-sm border border-zinc-200/50 dark:border-zinc-800/50 mb-16">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-50 mb-6">Write a Review</h3>
            <div class="flex gap-2 mb-6">
              <button v-for="star in 5" :key="star" @click="reviewForm.rating = star" class="text-2xl transition-transform hover:scale-110" :class="star <= reviewForm.rating ? 'text-primary-500' : 'text-zinc-200 dark:text-zinc-700'">
                ★
              </button>
            </div>
            <textarea v-model="reviewForm.comment" rows="4" placeholder="Share your thoughts on this product..."
              class="w-full px-4 py-3 bg-white/70 dark:bg-zinc-800 border-none rounded-xl text-sm text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 focus:ring-2 focus:ring-primary-500 focus:outline-none transition-all resize-none mb-6"></textarea>
            <div class="flex justify-end">
              <button @click="submitReview" :disabled="reviewSubmitting"
                class="btn-primary px-8 py-3 text-sm disabled:opacity-50">
                {{ reviewSubmitting ? 'Submitting...' : 'Post Review' }}
              </button>
            </div>
          </div>

          <!-- Reviews List -->
          <div class="space-y-6">
            <div v-for="review in reviews" :key="review.id" class="bg-white/70 dark:bg-zinc-900 backdrop-blur-sm p-8 rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm">
              <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-white/80 dark:bg-zinc-800 flex items-center justify-center text-zinc-900 dark:text-zinc-50 font-medium">
                  {{ (review.user?.name || 'U').charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="font-medium text-zinc-900 dark:text-zinc-50">{{ review.user?.name || 'Anonymous' }}</p>
                  <div class="flex items-center gap-3 mt-1">
                    <div class="flex text-primary-500 text-xs">
                      <span v-for="s in 5" :key="s" :class="s <= review.rating ? 'opacity-100' : 'opacity-20'">★</span>
                    </div>
                    <span class="text-xs text-zinc-500">{{ formatDate(review.created_at) }}</span>
                  </div>
                </div>
              </div>
              <p class="text-zinc-600 dark:text-zinc-400 font-light leading-relaxed">{{ review.comment }}</p>
            </div>
            
            <div v-if="!reviews.length" class="text-center py-16 text-zinc-500 font-light">
              No reviews yet. Be the first to share your experience.
            </div>
          </div>
        </div>
      </section>
    </template>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, inject, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios.js'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import Footer from '@/components/shop/Footer.vue'
import { Star as StarIcon, ShoppingBag as ShoppingBagIcon, Heart as HeartIcon, ChevronDown as ChevronDownIcon } from '@lucide/vue'

const route = useRoute()
const router = useRouter()
const auth  = useAuthStore()
const cart  = useCartStore()
const toast = inject('toast')

const product         = ref(null)
const variants        = ref([])
const reviews         = ref([])
const isWishlisted    = ref(false)
const loading         = ref(true)
const reviewSubmitting = ref(false)
const reviewForm      = ref({ rating: 5, comment: '' })

const activeImage = ref('')

const productImages = computed(() => {
  if (!product.value) return []
  const mainImage = product.value.full_image_url || product.value.image || 'https://placehold.co/800x1000/f4f4f5/52525b?text=Product'
  return [mainImage]
})

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
    
    if (productImages.value.length > 0) {
      activeImage.value = productImages.value[0]
    }

    if (auth.isLoggedIn) {
      try {
        const wRes = await api.get('/wishlists')
        const wData = wRes.data.data || wRes.data || []
        isWishlisted.value = wData.some(w => (w.product_id || w.id) == route.params.id)
      } catch (_) {}
    }
  } catch (e) {
    toast('Failed to load product', 'error')
    router.push('/shop')
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
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(fetchProduct)
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

details > summary {
  list-style: none;
}
details > summary::-webkit-details-marker {
  display: none;
}
</style>