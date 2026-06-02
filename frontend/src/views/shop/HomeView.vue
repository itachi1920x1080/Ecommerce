<template>
  <div>
    <!-- Hero -->
    <section class="relative bg-gradient-to-br from-green-50 to-emerald-100 py-24 px-6 text-center">
      <div class="max-w-2xl mx-auto">
        <span class="inline-block px-4 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-6 tracking-wide uppercase">100% Natural</span>
        <h1 class="text-5xl font-bold text-gray-800 mb-5 leading-tight">
          🌿 Natural<br><span class="text-green-700">Skincare</span>
        </h1>
        <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">Discover our premium skincare collection crafted from nature's finest ingredients.</p>
        <div class="flex items-center justify-center gap-4">
          <router-link to="/products" class="px-7 py-3 bg-green-700 hover:bg-green-600 text-white font-medium rounded-full transition-colors shadow-sm">
            Shop Now
          </router-link>
          <router-link to="/products" class="px-7 py-3 border border-green-700 text-green-700 hover:bg-green-50 font-medium rounded-full transition-colors">
            Browse All
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="py-16 px-6 bg-white">
      <div class="max-w-4xl mx-auto grid grid-cols-3 gap-8 text-center">
        <div class="p-6">
          <div class="text-3xl mb-3">🌱</div>
          <h3 class="font-semibold text-gray-800 mb-2">All Natural</h3>
          <p class="text-sm text-gray-500">Made with organic, plant-based ingredients</p>
        </div>
        <div class="p-6">
          <div class="text-3xl mb-3">🐰</div>
          <h3 class="font-semibold text-gray-800 mb-2">Cruelty Free</h3>
          <p class="text-sm text-gray-500">Never tested on animals, ever</p>
        </div>
        <div class="p-6">
          <div class="text-3xl mb-3">♻️</div>
          <h3 class="font-semibold text-gray-800 mb-2">Eco Packaging</h3>
          <p class="text-sm text-gray-500">Sustainable packaging that's kind to the planet</p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-16 px-6 bg-green-700 text-center">
      <h2 class="text-2xl font-bold text-white mb-3">Ready to glow naturally?</h2>
      <p class="text-green-200 mb-7 text-sm">Join thousands of happy customers who trust SkincareShop.</p>
      <router-link to="/products" class="px-8 py-3 bg-white text-green-700 font-semibold rounded-full hover:bg-green-50 transition-colors">
        Explore Products
      </router-link>
    </section>
  </div>
</template>