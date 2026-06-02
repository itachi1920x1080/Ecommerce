<template>
  <div>
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-24 sm:py-32 px-6">
      <!-- Decorative circles -->
      <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-white/5"></div>
      <div class="absolute -left-8 -bottom-20 w-56 h-56 rounded-full bg-white/5"></div>
      <div class="absolute right-1/4 top-10 w-20 h-20 rounded-full bg-white/10"></div>

      <div class="relative z-10 max-w-4xl mx-auto text-center">
        <span class="inline-block px-4 py-1.5 bg-white/15 backdrop-blur-sm text-blue-100 text-xs font-semibold rounded-full mb-6 tracking-wide uppercase animate-fade-in">
          🛍️ Welcome to RUPP Shop
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight animate-slide-up">
          Discover<br/><span class="text-blue-200">Amazing Products</span>
        </h1>
        <p class="text-lg text-blue-200 mb-10 max-w-lg mx-auto animate-slide-up" style="animation-delay: 0.1s">
          Shop the latest trends with the best prices. Fast delivery, secure payments, and premium quality.
        </p>
        <div class="flex items-center justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s">
          <a href="#products"
            class="px-8 py-3.5 bg-white text-blue-700 font-semibold rounded-xl hover:shadow-xl hover:shadow-blue-900/20 transition-all duration-200 hover:-translate-y-0.5 active:scale-95">
            Shop Now
          </a>
          <a href="#features"
            class="px-8 py-3.5 border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-200">
            Learn More
          </a>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-16 px-6 bg-white">
      <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
          <div v-for="(feature, i) in features" :key="feature.title"
            class="text-center p-6 rounded-2xl hover:bg-slate-50 transition-colors animate-slide-up"
            :style="{ animationDelay: i * 100 + 'ms' }">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl flex items-center justify-center" :class="feature.bg">
              <span class="text-2xl">{{ feature.emoji }}</span>
            </div>
            <h3 class="text-base font-semibold text-slate-800 mb-2">{{ feature.title }}</h3>
            <p class="text-sm text-slate-400 leading-relaxed">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-16 px-6 bg-slate-50">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
          <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-3">Featured Products</h2>
          <p class="text-sm text-slate-400">Browse our curated collection of premium products</p>
        </div>

        <!-- Search & Filters -->
        <div class="flex flex-col sm:flex-row gap-3 mb-8 max-w-2xl mx-auto">
          <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input v-model="search" type="text" placeholder="Search products..."
              class="w-full pl-10 pr-12 py-3 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all shadow-sm"
              id="home-search" />
            <button @click="openScanner" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Image Search">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
              </svg>
            </button>
          </div>
          <select v-model="category"
            class="px-4 py-3 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all shadow-sm cursor-pointer"
            id="home-category-filter">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <div class="flex gap-2">
            <input v-model.number="minPrice" type="number" placeholder="Min $" min="0"
              class="w-24 px-3 py-3 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all shadow-sm"
              id="home-min-price" />
            <input v-model.number="maxPrice" type="number" placeholder="Max $" min="0"
              class="w-24 px-3 py-3 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all shadow-sm"
              id="home-max-price" />
          </div>
        </div>

        <!-- Product Grid -->
        <ProductGrid
          :products="filteredProducts"
          :loading="loading"
          :wishlisted-ids="wishlistedIds"
          @addToCart="handleAddToCart"
          @toggleWishlist="handleToggleWishlist"
        />

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-10">
          <button v-for="p in totalPages" :key="p" @click="currentPage = p; fetchProducts()"
            :class="p === currentPage ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
            class="w-10 h-10 rounded-xl text-sm font-medium transition-all">
            {{ p }}
          </button>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-center">
      <div class="max-w-2xl mx-auto">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to Start Shopping?</h2>
        <p class="text-blue-200 mb-8">Create an account today and enjoy exclusive deals and fast delivery.</p>
        <router-link v-if="!auth.isLoggedIn" to="/register"
          class="inline-block px-8 py-3.5 bg-white text-blue-700 font-semibold rounded-xl hover:shadow-xl hover:shadow-blue-900/30 transition-all duration-200 hover:-translate-y-0.5">
          Create Account
        </router-link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-6 bg-slate-900 text-center">
      <p class="text-sm text-slate-500">© {{ new Date().getFullYear() }} RUPP Shop. All rights reserved.</p>
    </footer>

    <!-- Unified Scanner Modal -->
    <div v-if="showScanner" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-fade-in" @click.self="closeScanner">
      <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl animate-scale-up flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Product Scanner
          </h3>
          <button @click="closeScanner" class="text-slate-400 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-red-50" :disabled="ocrLoading">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-slate-100">
          <button @click="switchScannerMode('image')" :class="scannerMode === 'image' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-3 text-sm transition-all" :disabled="ocrLoading">
            Image Search
          </button>
          <button @click="switchScannerMode('barcode')" :class="scannerMode === 'barcode' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-3 text-sm transition-all" :disabled="ocrLoading">
            Scan Barcode
          </button>
        </div>

        <div class="p-6 text-center">
          <!-- Image Search Tab -->
          <div v-show="scannerMode === 'image'">
            <div v-if="ocrLoading" class="py-10">
              <div class="w-12 h-12 mx-auto border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
              <p class="text-sm font-semibold text-slate-700">Analyzing image...</p>
              <p class="text-xs text-slate-400 mt-1">Extracting text from product</p>
            </div>
            <div v-else>
              <div class="w-20 h-20 mx-auto bg-blue-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
              </div>
              <p class="text-sm text-slate-600 mb-6">Upload or take a photo of a product to search for it automatically.</p>
              
              <label class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl cursor-pointer transition-colors shadow-sm">
                <span>Take Photo / Upload Image</span>
                <input type="file" accept="image/*" class="hidden" @change="handleImageUpload">
              </label>
            </div>
          </div>

          <!-- Barcode Scanner Tab -->
          <div v-show="scannerMode === 'barcode'">
            <div id="reader" class="w-full rounded-xl overflow-hidden border-2 border-slate-200"></div>
            <p class="text-xs text-center text-slate-500 mt-4">Point your camera at a barcode or QR code to search</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, inject } from 'vue'
import api from '@/api/axios.js'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import ProductGrid from '@/components/ProductGrid.vue'
import { Html5QrcodeScanner } from 'html5-qrcode'
import Tesseract from 'tesseract.js'

const auth  = useAuthStore()
const cart  = useCartStore()
const toast = inject('toast')

// Scanner State
const showScanner = ref(false)
const scannerMode = ref('image') // 'image' or 'barcode'
const ocrLoading = ref(false)
let html5QrcodeScanner = null

function openScanner() {
  showScanner.value = true
  scannerMode.value = 'image' // Default to image search
}

function switchScannerMode(mode) {
  if (ocrLoading.value) return
  scannerMode.value = mode

  if (mode === 'barcode') {
    setTimeout(() => {
      if (!html5QrcodeScanner) {
        html5QrcodeScanner = new Html5QrcodeScanner(
          "reader",
          { fps: 10, qrbox: { width: 250, height: 250 } },
          /* verbose= */ false
        )
      }
      html5QrcodeScanner.render(onBarcodeScanSuccess, onBarcodeScanFailure)
    }, 100)
  } else {
    stopBarcodeScanner()
  }
}

function stopBarcodeScanner() {
  if (html5QrcodeScanner) {
    html5QrcodeScanner.clear().catch(error => {
      console.error("Failed to clear html5QrcodeScanner. ", error)
    })
  }
}

function closeScanner() {
  if (ocrLoading.value) return // Prevent closing while processing
  showScanner.value = false
  stopBarcodeScanner()
}

function onBarcodeScanSuccess(decodedText) {
  search.value = decodedText
  closeScanner()
  toast(`Scanned Barcode: ${decodedText}`, 'success')
}

function onBarcodeScanFailure() {
  // Ignored
}

async function handleImageUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  ocrLoading.value = true
  try {
    const result = await Tesseract.recognize(file, 'eng', {
      logger: m => console.log(m)
    })
    
    // Process text: remove symbols and extra spaces
    let text = result.data.text.replace(/[^a-zA-Z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim()
    
    if (text) {
      // Limit to 30 chars so we don't dump a whole essay into the search bar
      search.value = text.substring(0, 30).trim()
      toast(`Extracted: "${search.value}"`, 'success')
      closeScanner()
      // watch(search) triggers fetchProducts automatically
    } else {
      toast('No readable text found on the image.', 'error')
    }
  } catch (e) {
    console.error('OCR Error:', e)
    toast('Failed to analyze the image.', 'error')
  } finally {
    ocrLoading.value = false
    event.target.value = '' // reset input
  }
}

// Data
const products     = ref([])
const categories   = ref([])
const wishlistedIds = ref([])
const loading      = ref(true)
const currentPage  = ref(1)
const totalPages   = ref(1)

// Filters
const search   = ref('')
const category = ref('')
const minPrice = ref(null)
const maxPrice = ref(null)

// Features data
const features = [
  { emoji: '🚀', title: 'Fast Delivery', desc: 'Get your orders delivered quickly with our express shipping options.', bg: 'bg-blue-100' },
  { emoji: '🔒', title: 'Secure Payment', desc: 'Your transactions are safe with our trusted payment methods.', bg: 'bg-indigo-100' },
  { emoji: '⭐', title: 'Premium Quality', desc: 'Only the best products curated for our valued customers.', bg: 'bg-violet-100' },
]

// Fetch products from API
async function fetchProducts() {
  loading.value = true
  try {
    const params = { page: currentPage.value }
    if (search.value) params.search = search.value
    if (minPrice.value) params.min_price = minPrice.value
    if (maxPrice.value) params.max_price = maxPrice.value

    const res = await api.get('/products', { params })
    products.value = res.data.data || res.data || []
    totalPages.value = res.data.last_page || 1
  } catch (e) {
    console.error('Failed to fetch products:', e)
  } finally {
    loading.value = false
  }
}

// Client-side category filter (since API may not support it)
const filteredProducts = computed(() => {
  if (!category.value) return products.value
  return products.value.filter(p => p.category_id == category.value)
})

// Fetch categories
async function fetchCategories() {
  try {
    const res = await api.get('/categories')
    categories.value = res.data.data || res.data || []
  } catch (e) {
    console.error('Failed to fetch categories:', e)
  }
}

// Fetch wishlist IDs
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

// Add to cart handler
async function handleAddToCart(product) {
  if (!auth.isLoggedIn) {
    toast('Please login to add items to cart', 'info')
    return
  }
  try {
    await cart.addToCart(product)
    toast(`${product.name} added to cart!`, 'success')
  } catch (e) {
    toast('Failed to add to cart', 'error')
  }
}

// Toggle wishlist handler
async function handleToggleWishlist(productId) {
  if (!auth.isLoggedIn) {
    toast('Please login to use wishlist', 'info')
    return
  }
  try {
    await api.post(`/wishlists/${productId}/toggle`)
    if (wishlistedIds.value.includes(productId)) {
      wishlistedIds.value = wishlistedIds.value.filter(id => id !== productId)
      toast('Removed from wishlist', 'info')
    } else {
      wishlistedIds.value.push(productId)
      toast('Added to wishlist!', 'success')
    }
  } catch (e) {
    toast('Failed to update wishlist', 'error')
  }
}

// Debounced search
let searchTimeout
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchProducts()
  }, 400)
})

watch([minPrice, maxPrice], () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchProducts()
  }, 600)
})

onMounted(() => {
  fetchProducts()
  fetchCategories()
  fetchWishlist()
})
</script>
