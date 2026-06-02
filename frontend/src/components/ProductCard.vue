<template>
  <!-- Skeleton loading state -->
  <div v-if="loading" class="group">
    <div class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden">
      <div class="aspect-square skeleton"></div>
      <div class="p-4 space-y-3">
        <div class="h-4 skeleton w-3/4"></div>
        <div class="h-3 skeleton w-full"></div>
        <div class="h-3 skeleton w-1/2"></div>
        <div class="flex justify-between items-center pt-2">
          <div class="h-6 skeleton w-20"></div>
          <div class="h-9 skeleton w-24 rounded-xl"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Card -->
  <div v-else class="group bg-white rounded-2xl border border-slate-200/60 overflow-hidden hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-1 transition-all duration-300">
    <!-- Image Container -->
    <div class="relative aspect-square bg-slate-100 overflow-hidden">
      <img
        :src="imageUrl"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
        @error="imgError = true"
      />
      <!-- Stock Badge -->
      <span v-if="product.stock <= 5 && product.stock > 0"
        class="absolute top-3 left-3 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-100 text-amber-700">
        Only {{ product.stock }} left
      </span>
      <span v-else-if="product.stock === 0"
        class="absolute top-3 left-3 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-red-100 text-red-600">
        Out of Stock
      </span>

      <!-- Wishlist Button -->
      <button
        v-if="showWishlist"
        @click.stop="$emit('toggleWishlist', product.id)"
        class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center shadow-sm hover:bg-white hover:scale-110 transition-all duration-200"
        :class="wishlisted ? 'text-red-500' : 'text-slate-400'"
        :id="`wishlist-btn-${product.id}`"
      >
        <svg class="w-5 h-5" :fill="wishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
      </button>
    </div>

    <!-- Info -->
    <div class="p-4">
      <h3 class="text-sm font-semibold text-slate-800 mb-1 line-clamp-1">{{ product.name }}</h3>
      <p class="text-xs text-slate-400 mb-3 line-clamp-2 leading-relaxed">{{ product.description || 'No description available' }}</p>
      <div class="flex items-center justify-between">
        <p class="text-lg font-bold text-blue-600">${{ Number(product.price).toFixed(2) }}</p>
        <button
          v-if="product.stock > 0"
          @click.stop="$emit('addToCart', product)"
          class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-200 active:scale-95"
          :id="`add-cart-${product.id}`"
        >
          Add to Cart
        </button>
        <span v-else class="px-4 py-2 bg-slate-100 text-slate-400 text-xs font-semibold rounded-xl cursor-not-allowed">
          Unavailable
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  product:      { type: Object, required: true },
  loading:      { type: Boolean, default: false },
  wishlisted:   { type: Boolean, default: false },
  showWishlist: { type: Boolean, default: true }
})

defineEmits(['addToCart', 'toggleWishlist'])

const imgError = ref(false)

// Fallback image handling
const imageUrl = computed(() => {
  if (imgError.value) return 'https://placehold.co/400x400/e2e8f0/94a3b8?text=No+Image'
  return props.product.full_image_url || props.product.image || props.product.image_url || 'https://placehold.co/400x400/e2e8f0/94a3b8?text=Product'
})
</script>
