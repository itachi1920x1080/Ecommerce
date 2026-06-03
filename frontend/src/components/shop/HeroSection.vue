<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 py-24 sm:py-32 lg:py-36 px-6">
    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-white/5 animate-float"></div>
      <div class="absolute -left-12 -bottom-24 w-72 h-72 rounded-full bg-white/5 animate-float delay-200"></div>
      <div class="absolute right-1/4 top-16 w-24 h-24 rounded-full bg-white/10 animate-pulse-soft"></div>
      <div class="absolute left-1/3 bottom-20 w-16 h-16 rounded-full bg-primary-400/20 animate-float delay-400"></div>
      <!-- Grid pattern overlay -->
      <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto text-center">
      <!-- Badge -->
      <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm text-primary-100 text-xs font-semibold rounded-full mb-8 tracking-wide uppercase border border-white/10 animate-fade-in">
        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse-soft"></span>
        ✨ NEW COLLECTION {{ new Date().getFullYear() }}
      </span>

      <!-- Main Heading -->
      <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white mb-6 leading-[1.1] tracking-tight animate-slide-up">
        Discover<br/>
        <span class="bg-gradient-to-r from-primary-200 via-accent-200 to-primary-200 bg-clip-text text-transparent">Amazing Products</span>
      </h1>

      <!-- Subtitle -->
      <p class="text-base sm:text-lg text-primary-200/80 mb-10 max-w-xl mx-auto leading-relaxed animate-slide-up delay-100">
        Shop the latest trends with the best prices. Fast delivery, secure payments, and premium quality guaranteed.
      </p>

      <!-- CTA Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up delay-200">
        <a
          href="#products"
          class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary-700 font-semibold rounded-2xl hover:shadow-2xl hover:shadow-primary-900/30 transition-all duration-300 hover:-translate-y-1 active:scale-95"
        >
          Shop Now
          <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
        <a
          href="#features"
          class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 border-2 border-white/20 text-white font-semibold rounded-2xl hover:bg-white/10 hover:border-white/30 transition-all duration-300"
        >
          Learn More
        </a>
      </div>

      <!-- Stats Bar -->
      <div class="flex items-center justify-center gap-8 sm:gap-12 mt-16 animate-slide-up delay-300">
        <div v-for="stat in stats" :key="stat.label" class="text-center">
          <p class="text-2xl sm:text-3xl font-extrabold text-white">{{ stat.value }}</p>
          <p class="text-xs sm:text-sm text-primary-300/70 mt-1">{{ stat.label }}</p>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="mt-12 animate-slide-up delay-500">
        <a href="#products" class="inline-flex flex-col items-center gap-1 text-primary-300/50 hover:text-primary-200 transition-colors text-xs uppercase tracking-widest">
          SCROLL
          <svg class="w-4 h-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
          </svg>
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
const stats = [
  { value: '50K+', label: 'Customers' },
  { value: '200+', label: 'Products' },
  { value: '100%', label: 'Satisfaction' },
]
</script>
