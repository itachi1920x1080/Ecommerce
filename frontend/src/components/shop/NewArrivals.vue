<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Section Header -->
      <div class="flex items-center justify-center gap-4 mb-8">
        <div class="flex-1 h-px bg-slate-200"></div>
        <h2 class="text-2xl font-bold tracking-[0.15em] uppercase text-charcoal-800 whitespace-nowrap">
          New Arrivals
        </h2>
        <div class="flex-1 h-px bg-slate-200"></div>
      </div>

      <!-- Category Tabs -->
      <div class="flex items-center justify-center gap-2 mb-10 flex-wrap">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          class="px-5 py-2 rounded-full text-[13px] font-semibold tracking-wide border transition-all duration-200"
          :class="activeTab === tab.value
            ? 'bg-charcoal-800 text-white border-charcoal-800'
            : 'bg-white text-slate-400 border-slate-200 hover:border-blush-400 hover:text-blush-500'"
          @click="setTab(tab.value)"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Products Grid -->
      <div v-if="!loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <ProductCard
          v-for="product in displayedProducts"
          :key="product.id"
          :product="product"
          :isWishlisted="wishlistStore.items.some(i => i.id === product.id)"
          @addToCart="cartStore.addItem({ ...product, quantity: 1 })"
          @toggleWishlist="wishlistStore.toggle(product)"
        />
      </div>

      <!-- Skeleton Loader -->
      <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-for="i in 10" :key="i" class="animate-pulse">
          <div class="bg-cream-50 aspect-square rounded-2xl mb-3"></div>
          <div class="h-3 bg-slate-100 rounded mb-2 w-1/2"></div>
          <div class="h-3 bg-slate-100 rounded mb-2"></div>
          <div class="h-3 bg-slate-100 rounded w-1/3"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && displayedProducts.length === 0" class="text-center py-20">
        <p class="text-slate-400 text-sm">No products found in this category.</p>
      </div>

      <!-- View All Button -->
      <div class="flex justify-center mt-12">
        <router-link
          to="/shop"
          class="group flex items-center gap-3 border-2 border-charcoal-800 text-charcoal-800 px-10 py-3 rounded-full text-[13px] font-bold tracking-widest hover:bg-charcoal-800 hover:text-white transition-all duration-300"
        >
          VIEW ALL PRODUCTS
          <svg
            class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </router-link>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import ProductCard from '@/components/shop/ProductCard.vue'
import { useProductStore } from '@/stores/product'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'

const productStore = useProductStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const loading = ref(false)
const activeTab = ref('all')

const tabs = [
  { label: 'All', value: 'all' },
  { label: 'Skincare', value: 'skincare' },
  { label: 'Makeup', value: 'makeup' },
  { label: 'Hair Care', value: 'hair' },
  { label: 'Body Care', value: 'body' },
  { label: 'Supplements', value: 'supplements' },
]

const displayedProducts = computed(() => {
  const products = productStore.newArrivals || []
  if (activeTab.value === 'all') return products.slice(0, 10)
  return products.filter(p => p.category?.slug === activeTab.value).slice(0, 10)
})

const setTab = async (val) => {
  activeTab.value = val
}

const fetchProducts = async () => {
  loading.value = true
  try {
    await productStore.fetchNewArrivals(
      activeTab.value === 'all' ? null : activeTab.value
    )
  } finally {
    loading.value = false
  }
}

watch(activeTab, fetchProducts)
onMounted(fetchProducts)
</script>