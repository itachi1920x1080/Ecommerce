<template>
  <div class="min-h-screen bg-white dark:bg-zinc-950 font-sans text-zinc-900 dark:text-zinc-50 selection:bg-zinc-900 selection:text-white dark:selection:bg-white dark:selection:text-zinc-900 overflow-x-hidden">
    
    <HeroSection />

    <!-- Marquee -->
    <div class="border-y border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 py-4 overflow-hidden flex whitespace-nowrap">
      <div class="animate-marquee inline-block text-[10px] font-semibold tracking-[0.2em] uppercase text-zinc-500 dark:text-zinc-400">
        <span class="mx-8">• FREE SHIPPING OVER $40</span>
        <span class="mx-8">• COMPLIMENTARY SAMPLES</span>
        <span class="mx-8">• 30-DAY RETURNS</span>
        <span class="mx-8">• SECURE CHECKOUT</span>
        <span class="mx-8">• CRUELTY FREE</span>
        <span class="mx-8">• SUSTAINABLE PACKAGING</span>
        <span class="mx-8">• FREE SHIPPING OVER $40</span>
        <span class="mx-8">• COMPLIMENTARY SAMPLES</span>
        <span class="mx-8">• 30-DAY RETURNS</span>
        <span class="mx-8">• SECURE CHECKOUT</span>
        <span class="mx-8">• CRUELTY FREE</span>
        <span class="mx-8">• SUSTAINABLE PACKAGING</span>
      </div>
    </div>

    <!-- The Essentials (Bento Grid) -->
    <section class="py-24 sm:py-32 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <div class="mb-16">
        <h2 class="text-4xl sm:text-5xl font-display font-medium tracking-tight mb-4">The Essentials</h2>
        <p class="text-zinc-500 dark:text-zinc-400 max-w-xl">Curated collections to form the foundation of your daily ritual.</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 auto-rows-[300px] sm:auto-rows-[400px]">
        <!-- Bento Item 1 -->
        <router-link to="/shop?category=skincare" class="group relative md:col-span-2 overflow-hidden bg-zinc-100 dark:bg-zinc-900 block">
          <img src="https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=1000" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-1000 ease-out" alt="Skincare" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <div class="absolute bottom-8 left-8">
            <p class="text-white/80 text-xs font-semibold tracking-[0.2em] uppercase mb-2">Category</p>
            <h3 class="text-3xl font-display font-medium text-white">Skincare</h3>
          </div>
        </router-link>
        
        <!-- Bento Item 2 -->
        <router-link to="/shop?category=body" class="group relative overflow-hidden bg-zinc-100 dark:bg-zinc-900 block">
          <img src="https://images.unsplash.com/photo-1608248593842-83b6cb593c6e?auto=format&fit=crop&q=80&w=600" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-1000 ease-out" alt="Body" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <div class="absolute bottom-8 left-8">
            <p class="text-white/80 text-xs font-semibold tracking-[0.2em] uppercase mb-2">Category</p>
            <h3 class="text-3xl font-display font-medium text-white">Body</h3>
          </div>
        </router-link>
        
        <!-- Bento Item 3 -->
        <router-link to="/shop?category=makeup" class="group relative overflow-hidden bg-zinc-100 dark:bg-zinc-900 block">
          <img src="https://images.unsplash.com/photo-1596462502278-27bf85033e5a?auto=format&fit=crop&q=80&w=600" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-1000 ease-out" alt="Makeup" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <div class="absolute bottom-8 left-8">
            <p class="text-white/80 text-xs font-semibold tracking-[0.2em] uppercase mb-2">Category</p>
            <h3 class="text-3xl font-display font-medium text-white">Makeup</h3>
          </div>
        </router-link>
        
        <!-- Bento Item 4 -->
        <router-link to="/shop?sale=true" class="group relative md:col-span-2 overflow-hidden bg-zinc-900 dark:bg-zinc-100 block">
          <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
            <p class="text-zinc-400 dark:text-zinc-500 text-xs font-semibold tracking-[0.2em] uppercase mb-4">Limited Time</p>
            <h3 class="text-4xl md:text-5xl font-display font-medium text-white dark:text-zinc-900 mb-6 group-hover:scale-105 transition-transform duration-700">The Summer Archive</h3>
            <span class="border-b border-white dark:border-zinc-900 text-white dark:text-zinc-900 pb-1 text-sm font-medium">Explore Archive</span>
          </div>
        </router-link>
      </div>
    </section>

    <!-- New Arrivals (Horizontal Scroll) -->
    <section class="py-24 sm:py-32 bg-zinc-50 dark:bg-zinc-900/30 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 flex flex-col sm:flex-row justify-between items-end gap-6">
        <div>
          <h2 class="text-3xl sm:text-4xl font-display font-medium tracking-tight mb-3">Latest Additions</h2>
          <p class="text-zinc-500 dark:text-zinc-400">Discover what's new in our catalog.</p>
        </div>
        <router-link to="/shop" class="group flex items-center gap-2 text-sm font-medium border-b border-zinc-900 dark:border-white pb-1">
          Shop Collection <ArrowRightIcon class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </router-link>
      </div>

      <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative">
        <div class="flex overflow-x-auto gap-6 sm:gap-8 pb-12 snap-x snap-mandatory no-scrollbar" style="scroll-padding-left: 24px;">
          <div v-if="loading" v-for="i in 4" :key="'skel'+i" class="w-[280px] sm:w-[320px] shrink-0 snap-start">
            <SkeletonCard />
          </div>
          
          <div v-else v-for="product in featuredProducts" :key="product.id" class="w-[280px] sm:w-[320px] shrink-0 snap-start">
            <ProductCard 
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
    </section>

    <!-- Immersive Editorial Section -->
    <section class="bg-black text-white py-32 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-8 items-center">
        <div class="order-2 lg:order-1 relative aspect-[3/4] w-full max-w-md mx-auto lg:mx-0 overflow-hidden">
          <img src="https://images.unsplash.com/photo-1599305090598-fe179d501227?auto=format&fit=crop&q=80&w=800" alt="Editorial" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity duration-700" />
        </div>
        
        <div class="order-1 lg:order-2 lg:pl-16">
          <p class="text-white/50 text-xs font-semibold tracking-[0.2em] uppercase mb-6">Our Philosophy</p>
          <h2 class="text-4xl sm:text-5xl lg:text-6xl font-display font-medium leading-[1.1] mb-8">
            Formulated for <br/> <span class="italic font-light text-white/70">Longevity.</span>
          </h2>
          <p class="text-white/70 text-lg font-light leading-relaxed mb-10 max-w-lg">
            We believe that true beauty comes from taking care of the skin you are in. Our products are formulated with scientifically proven ingredients to support your skin's natural balance.
          </p>
          <router-link to="/brand" class="inline-flex px-8 py-4 bg-white text-black text-sm font-medium rounded-full hover:bg-zinc-200 transition-colors">
            Read Our Story
          </router-link>
        </div>
      </div>
    </section>

    <!-- Deep Integration Newsletter -->
    <section class="py-32 px-4 sm:px-6 lg:px-8 bg-zinc-100 dark:bg-zinc-900">
      <div class="max-w-3xl mx-auto text-center">
        <p class="text-zinc-500 text-xs font-semibold tracking-[0.2em] uppercase mb-4">The Journal</p>
        <h2 class="text-4xl md:text-5xl font-display font-medium mb-12 tracking-tight">Stay ahead of the curve.</h2>
        
        <form @submit.prevent="toast('Subscribed successfully!', 'success')" class="relative max-w-lg mx-auto">
          <input 
            type="email" 
            placeholder="Email address" 
            required
            class="w-full bg-transparent border-b-2 border-zinc-300 dark:border-zinc-700 text-center text-lg py-4 px-12 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors placeholder-zinc-400"
          />
          <button type="submit" class="absolute right-0 top-1/2 -translate-y-1/2 p-2 text-zinc-900 dark:text-white hover:text-zinc-500 transition-colors">
            <ArrowRightIcon class="w-6 h-6" />
          </button>
        </form>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios.js'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import Footer from '@/components/shop/Footer.vue'
import HeroSection from '@/components/shop/HeroSection.vue'
import ProductCard from '@/components/shop/ProductCard.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import { ArrowRight as ArrowRightIcon } from '@lucide/vue'

const auth = useAuthStore()
const cart = useCartStore()
const toast = inject('toast')
const router = useRouter()

const products = ref([])
const categories = ref([])
const wishlistedIds = ref([])
const loading = ref(true)

const featuredProducts = computed(() => products.value.slice(0, 8))

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

<style scoped>
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
.animate-marquee {
  animation: marquee 30s linear infinite;
  display: inline-flex;
  min-width: 200%;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
