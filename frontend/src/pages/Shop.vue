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
        
        <!-- Filter & Sort -->
        <div class="flex items-center gap-4">
          <select v-model="selectedCategory" class="bg-white/70 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-zinc-500 cursor-pointer backdrop-blur-sm">
            <option value="" class="bg-white dark:bg-zinc-950">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id" class="bg-white dark:bg-zinc-950">{{ cat.name }}</option>
          </select>
          <select v-model="sortBy" class="bg-white/70 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-sm rounded-full px-4 py-2 focus:outline-none focus:border-zinc-500 cursor-pointer backdrop-blur-sm">
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

      <div v-else-if="filteredProducts.length === 0" class="text-center py-20">
        <p class="text-zinc-500 dark:text-zinc-400 text-lg">No products found matching your filters.</p>
        <button @click="clearFilters" class="mt-4 text-primary-600 hover:text-primary-700 font-medium">Clear Filters</button>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <ProductCard 
          v-for="product in filteredProducts" 
          :key="product.id"
          :product="product"
          :isWishlisted="wishlistedIds.includes(product.id)"
          @addToCart="handleAddToCart"
          @toggleWishlist="handleToggleWishlist"
          @click="$router.push(`/products/${product.id}`)"
          class="cursor-pointer"
        />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
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

const filteredProducts = computed(() => {
  let result = products.value

  if (route.query.search) {
    const q = route.query.search.toLowerCase()
    result = result.filter(p => 
      (p.name && p.name.toLowerCase().includes(q)) || 
      (p.description && p.description.toLowerCase().includes(q))
    )
  }

  if (selectedCategory.value) {
    result = result.filter(p => String(p.category_id) === String(selectedCategory.value))
  }

  if (route.query.sale === 'true') {
    result = result.filter(p => p.discount_percent > 0)
  }

  if (sortBy.value === 'price_asc') {
    result = [...result].sort((a, b) => Number(a.price) - Number(b.price))
  } else if (sortBy.value === 'price_desc') {
    result = [...result].sort((a, b) => Number(b.price) - Number(a.price))
  } else {
    result = [...result].sort((a, b) => Number(b.id) - Number(a.id))
  }

  return result
})

function clearFilters() {
  selectedCategory.value = ''
  sortBy.value = 'newest'
  if (route.query.search || route.query.sale) {
    router.replace({ path: '/shop' })
  }
}

async function fetchProducts() {
  loading.value = true
  try {
    const res = await api.get('/products')
    products.value = res.data.data || res.data || []
  } catch (e) {
    console.error('Failed to fetch products:', e)
  } finally {
    loading.value = false
  }
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

onMounted(() => {
  fetchProducts()
  fetchCategories()
  fetchWishlist()
})
</script>