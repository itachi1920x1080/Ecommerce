<template>
  <div class="group relative bg-white rounded-2xl overflow-hidden border border-slate-100 hover:border-primary-200/50 transition-all duration-500 hover:shadow-xl hover:shadow-primary-500/10 hover:-translate-y-1">
    <!-- Image Container -->
    <div class="relative aspect-square overflow-hidden bg-slate-100">
      <img
        :src="product.full_image_url || product.image || 'https://placehold.co/400x400/e2e8f0/94a3b8?text=Product'"
        :alt="product.name"
        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
        loading="lazy"
      />

      <!-- Overlay on hover -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

      <!-- Wishlist Button -->
      <button
        @click.stop="$emit('toggleWishlist', product.id)"
        class="absolute top-3 right-3 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 shadow-lg"
        :class="isWishlisted
          ? 'bg-red-500 text-white scale-100'
          : 'bg-white/90 backdrop-blur text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0'"
      >
        <svg class="w-4 h-4" :fill="isWishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
      </button>

      <!-- Category Badge -->
      <span v-if="product.category?.name" class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur text-xs font-semibold text-slate-600 rounded-lg shadow-sm">
        {{ product.category.name }}
      </span>
    </div>

    <!-- Content -->
    <div class="p-4">
      <h3 class="text-sm font-semibold text-slate-800 line-clamp-1 group-hover:text-primary-600 transition-colors duration-300">
        {{ product.name }}
      </h3>

      <p v-if="product.description" class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
        {{ product.description }}
      </p>

      <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
        <span class="text-lg font-bold bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent">
          ${{ Number(product.price).toFixed(2) }}
        </span>

        <button
          @click.stop="$emit('addToCart', product)"
          class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-50 hover:bg-primary-600 text-primary-600 hover:text-white text-xs font-semibold rounded-xl transition-all duration-300 active:scale-95"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
          </svg>
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: { type: Object, required: true },
  isWishlisted: { type: Boolean, default: false },
})

defineEmits(['addToCart', 'toggleWishlist'])
</script>
