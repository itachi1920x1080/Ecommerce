<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-pink-50 to-pink-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 pt-32 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-12 border-b border-zinc-200 dark:border-zinc-800 pb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <h1 class="text-4xl sm:text-5xl font-display font-medium text-zinc-900 dark:text-white tracking-tight mb-4">The Collection</h1>
          <p class="text-zinc-500 dark:text-zinc-400 text-lg max-w-xl">
            Explore our meticulously curated selection of premium products, designed to elevate your daily routine.
          </p>
        </div>
        
        <!-- Filter, Sort & Per Page -->
        <div class="flex items-center gap-3 flex-wrap">
          <!-- Category Filter -->
          <select v-model="selectedCategory" @change="fetchProducts(1)"
            class="bg-white/70 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-zinc-500 cursor-pointer backdrop-blur-sm">
            <option value="" class="bg-white dark:bg-zinc-950">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id" class="bg-white dark:bg-zinc-950">{{ cat.name }}</option>
          </select>

          <!-- Sort -->
          <select v-model="sortBy" @change="fetchProducts(1)"
            class="bg-white/70 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-zinc-500 cursor-pointer backdrop-blur-sm">
            <option value="newest" class="bg-white dark:bg-zinc-950">Newest</option>
            <option value="price_asc" class="bg-white dark:bg-zinc-950">Price: Low to High</option>
            <option value="price_desc" class="bg-white dark:bg-zinc-950">Price: High to Low</option>
          </select>


        </div>
      </div>

      <!-- Products Grid -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <SkeletonCard v-for="i in 8" :key="i" />
      </div>

      <div v-else-if="products.length === 0" class="text-center py-20">
        <p class="text-zinc-500 dark:text-zinc-400 text-lg">No products found matching your filters.</p>
        <button @click="clearFilters" class="mt-4 text-primary-600 hover:text-primary-700 font-medium">Clear Filters</button>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <ProductCard 
          v-for="product in products" 
          :key="product.id"
          :product="product"
          :isWishlisted="wishlistedIds.includes(product.id)"
          @addToCart="handleAddToCart"
          @toggleWishlist="handleToggleWishlist"
          @click="$router.push(`/products/${product.id}`)"
          class="cursor-pointer"
        />
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1" class="flex items-center justify-center gap-2 mt-16">
        <!-- Prev -->
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-4 py-2 rounded-full border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition"
        >← Prev</button>

        <!-- Page Numbers (smart: first, nearby, last with ... gaps) -->
        <template v-for="item in paginationPages" :key="item">
          <span v-if="item === '...'" class="text-zinc-400 px-1">...</span>
          <button
            v-else
            @click="goToPage(item)"
            :class="[
              'w-10 h-10 rounded-full text-sm font-medium transition',
              item === currentPage
                ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900'
                : 'border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'
            ]"
          >{{ item }}</button>
        </template>

        <!-- Next -->
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === lastPage"
          class="px-4 py-2 rounded-full border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition"
        >Next →</button>
      </div>

      <!-- Total -->
      <p class="text-center text-zinc-400 text-sm mt-4">
        Page {{ currentPage }} / {{ lastPage }} — សរុប {{ total }} products
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, inject } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api/axios.js'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import ProductCard from '@/components/shop/ProductCard.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'

const auth = useAuthStore()
const cart = useCartStore()
const toast = inject('toast')
const router = useRouter()
const route = useRoute()

const products = ref([])
const categories = ref([])
const wishlistedIds = ref([])
const loading = ref(true)

const selectedCategory = ref('')
const sortBy = ref('newest')
const perPage = 20 // fixed at 20 per page

// Pagination
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

// Smart pagination: 1 | 2 | 3 | ... | 19
const paginationPages = computed(() => {
  const pages = []
  const total = lastPage.value
  const current = currentPage.value

  if (total <= 7) {
    // Show all pages if total is small
    for (let i = 1; i <= total; i++) pages.push(i)
    return pages
  }

  pages.push(1)

  if (current > 4) pages.push('...')

  const start = Math.max(2, current - 1)
  const end = Math.min(total - 1, current + 1)

  for (let i = start; i <= end; i++) pages.push(i)

  if (current < total - 3) pages.push('...')

  pages.push(total)

  return pages
})

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams()
    params.append('page', page)
    params.append('per_page', perPage)

    if (selectedCategory.value) params.append('category_id', selectedCategory.value)
    if (sortBy.value === 'price_asc') params.append('sort', 'price_asc')
    if (sortBy.value === 'price_desc') params.append('sort', 'price_desc')
    if (route.query.search) params.append('search', route.query.search)
    if (route.query.sale === 'true') params.append('sale', 'true')

    const res = await api.get(`/products?${params.toString()}`)
    products.value = res.data.data || []
    currentPage.value = res.data.current_page || 1
    lastPage.value = res.data.last_page || 1
    total.value = res.data.total || 0
  } catch (e) {
    console.error('Failed to fetch products:', e)
  } finally {
    loading.value = false
  }
}

function goToPage(page) {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchProducts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function clearFilters() {
  selectedCategory.value = ''
  sortBy.value = 'newest'
  if (route.query.search || route.query.sale) {
    router.replace({ path: '/shop' })
  }
  fetchProducts(1)
}

async function fetchCategories() {
  try {
    const res = await api.get('/categories')
    categories.value = res.data.data || res.data || []
  } catch (e) {
    console.error('Failed to fetch categories:', e)
  }
}

async function fetchWishlist() {
  if (!auth.isLoggedIn) return
  try {
    const res = await api.get('/wishlists')
    const data = res.data.data || res.data || []
    wishlistedIds.value = data.map(w => w.product_id || w.id)
  } catch (e) {
    console.error('Failed to fetch wishlist:', e)
  }
}

async function handleAddToCart(product) {
  if (!auth.isLoggedIn) {
    toast('Please login to add items to bag', 'info')
    router.push('/login')
    return
  }
  try {
    await cart.addToCart(product)
    toast('Added to your bag', 'success')
  } catch (e) {
    toast('Failed to add to bag', 'error')
  }
}

async function handleToggleWishlist(productId) {
  if (!auth.isLoggedIn) {
    toast('Please login to use wishlist', 'info')
    router.push('/login')
    return
  }
  try {
    await api.post(`/wishlists/${productId}/toggle`)
    if (wishlistedIds.value.includes(productId)) {
      wishlistedIds.value = wishlistedIds.value.filter(id => id !== productId)
      toast('Removed from wishlist', 'info')
    } else {
      wishlistedIds.value.push(productId)
      toast('Added to wishlist', 'success')
    }
  } catch (e) {
    toast('Failed to update wishlist', 'error')
  }
}

// Watch route query changes
watch(() => route.query, () => {
  fetchProducts(1)
})

onMounted(() => {
  fetchProducts(1)
  fetchCategories()
  fetchWishlist()
})
</script>