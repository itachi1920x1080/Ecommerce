<template>
  <div>
    <!-- Loading Skeleton Grid -->
    <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
      <div v-for="i in 8" :key="i" class="bg-white rounded-2xl overflow-hidden border border-slate-100">
        <div class="aspect-square skeleton"></div>
        <div class="p-4 space-y-3">
          <div class="h-4 w-3/4 skeleton"></div>
          <div class="h-3 w-full skeleton"></div>
          <div class="flex justify-between items-center pt-3 border-t border-slate-100">
            <div class="h-5 w-16 skeleton"></div>
            <div class="h-8 w-20 skeleton rounded-xl"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Cards -->
    <div v-else-if="products.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
      <ProductCard
        v-for="(product, idx) in products"
        :key="product.id"
        :product="product"
        :is-wishlisted="wishlistedIds.includes(product.id)"
        @addToCart="$emit('addToCart', $event)"
        @toggleWishlist="$emit('toggleWishlist', $event)"
        class="animate-slide-up"
        :style="{ animationDelay: (idx % 8) * 80 + 'ms' }"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-20">
      <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-slate-700 mb-2">No products found</h3>
      <p class="text-sm text-slate-400">Try adjusting your search or filter criteria</p>
    </div>
  </div>
</template>

<script setup>
import ProductCard from '@/components/shop/ProductCard.vue'

defineProps({
  products:      { type: Array, required: true },
  loading:       { type: Boolean, default: false },
  wishlistedIds: { type: Array, default: () => [] },
})

defineEmits(['addToCart', 'toggleWishlist'])
</script>
