<template>
  <div>
    <!-- Loading Skeleton Grid -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <ProductCard v-for="i in skeletonCount" :key="i" :product="{}" :loading="true" :show-wishlist="false" />
    </div>

    <!-- Empty State -->
    <div v-else-if="products.length === 0" class="py-20 text-center">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-slate-600 mb-1">No products found</h3>
      <p class="text-sm text-slate-400">Try adjusting your search or filters</p>
    </div>

    <!-- Products Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        :wishlisted="wishlistedIds.includes(product.id)"
        :show-wishlist="showWishlist"
        @addToCart="$emit('addToCart', $event)"
        @toggleWishlist="$emit('toggleWishlist', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import ProductCard from '@/components/ProductCard.vue'

defineProps({
  products:      { type: Array,   default: () => [] },
  loading:       { type: Boolean, default: false },
  wishlistedIds: { type: Array,   default: () => [] },
  showWishlist:  { type: Boolean, default: true },
  skeletonCount: { type: Number,  default: 8 }
})

defineEmits(['addToCart', 'toggleWishlist'])
</script>
