<template>
  <div class="group relative flex flex-col transition-all duration-500">
    <!-- Image Container -->
    <div class="relative w-full aspect-[3/4] overflow-hidden bg-zinc-100 dark:bg-zinc-900 mb-4">
      <img
        :src="product.full_image_url || (product.image_url ? getStorageUrl(product.image_url) : product.image) || 'https://placehold.co/400x500/f4f4f5/52525b?text=Product'"
        :alt="product.name"
        class="w-full h-full object-cover transition-transform duration-[1.5s] ease-out group-hover:scale-105"
        loading="lazy"
        @error="$event.target.src='https://placehold.co/400x500/f4f4f5/52525b?text=Product'"
      />

      <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

      <!-- Floating Action: Add to Bag -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 translate-y-4 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 ease-out z-10 w-3/4 max-w-[200px]">
        <button
          v-if="product.stock > 0"
          @click.stop="$emit('addToCart', product)"
          class="w-full py-3 bg-white/95 dark:bg-black/90 backdrop-blur-md text-zinc-900 dark:text-white text-xs font-semibold uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-white dark:hover:bg-black transition-colors shadow-lg"
        >
          Add to Bag
        </button>
        <button
          v-else-if="product.out_of_stock_status === 'preorder'"
          @click.stop="$emit('addToCart', product)"
          class="w-full py-3 bg-white/95 dark:bg-black/90 backdrop-blur-md text-zinc-900 dark:text-white text-xs font-semibold uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-white dark:hover:bg-black transition-colors shadow-lg"
        >
          Pre-order
        </button>
        <button
          v-else
          disabled
          class="w-full py-3 bg-zinc-200/90 dark:bg-zinc-800/90 backdrop-blur-md text-zinc-500 dark:text-zinc-400 text-xs font-semibold uppercase tracking-widest flex items-center justify-center gap-2 cursor-not-allowed"
        >
          Out of Stock
        </button>
      </div>

      <!-- Wishlist Button -->
      <button
        @click.stop="$emit('toggleWishlist', product.id)"
        class="absolute top-4 right-4 p-2 transition-all duration-300 z-10"
        :class="isWishlisted
          ? 'text-red-500 scale-100'
          : 'text-zinc-400 hover:text-zinc-900 dark:hover:text-white opacity-0 group-hover:opacity-100 -translate-y-2 group-hover:translate-y-0'"
      >
        <HeartIcon class="w-5 h-5 transition-transform" :class="isWishlisted ? 'animate-bounce-once' : ''" :fill="isWishlisted ? 'currentColor' : 'none'" />
      </button>

      <!-- Category & Sale Badges -->
      <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
        <span v-if="product.discount_percent > 0" class="px-3 py-1 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[10px] uppercase tracking-wider font-semibold w-fit">
          -{{ product.discount_percent }}%
        </span>
      </div>
    </div>

    <!-- Content -->
    <div class="flex flex-col text-center px-2 py-3 rounded-2xl bg-white/60 dark:bg-zinc-900/60 backdrop-blur-sm border border-zinc-100/80 dark:border-zinc-800/50 transition-all duration-300 group-hover:bg-white/80 dark:group-hover:bg-zinc-900/80 group-hover:shadow-sm">
      <span v-if="product.category?.name" class="text-[10px] uppercase tracking-widest font-semibold text-zinc-400 dark:text-zinc-500 mb-1">
        {{ product.category.name }}
      </span>
      
      <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-50 mb-1 group-hover:opacity-70 transition-opacity">
        {{ product.name }}
      </h3>

      <div class="flex items-center justify-center gap-2">
        <span v-if="product.discount_percent > 0" class="text-xs text-zinc-400 line-through">
          ${{ Number(product.price).toFixed(2) }}
        </span>
        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-50">
          ${{ product.discount_percent > 0 ? (product.price - (product.price * product.discount_percent / 100)).toFixed(2) : Number(product.price).toFixed(2) }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Heart as HeartIcon } from '@lucide/vue'
import { getStorageUrl } from '@/api/axios'

defineProps({
  product: { type: Object, required: true },
  isWishlisted: { type: Boolean, default: false },
})

defineEmits(['addToCart', 'toggleWishlist'])
</script>