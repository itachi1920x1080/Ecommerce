<template>
  <section class="relative h-screen w-full flex items-center justify-center overflow-hidden bg-gradient-to-br from-white via-pink-50 to-pink-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 transition-colors duration-500">
    
    <!-- Immersive Background -->
    <div class="absolute inset-0 w-full h-full">
      <img
        src="https://images.unsplash.com/photo-1615397323286-905c317ff6ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80"
        alt="Hero Background"
        class="w-full h-full object-cover opacity-20 dark:opacity-60 scale-105 animate-slow-pan transition-opacity duration-500"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-pink-50/50 dark:from-black/20 dark:via-transparent dark:to-black/60 transition-all duration-500"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 flex flex-col items-center text-center mt-20">
  
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-pink-100/80 dark:bg-white/10 backdrop-blur-md border border-pink-200 dark:border-white/20 text-zinc-900 dark:text-white text-xs font-semibold uppercase tracking-[0.2em] mb-8 animate-fade-in transition-colors duration-500">
        The 2026 Editorial
      </div>
      
      <h1 class="text-6xl sm:text-7xl md:text-8xl lg:text-[8rem] font-display font-medium text-zinc-900 dark:text-white tracking-tighter leading-[0.9] mb-8 animate-slide-up transition-colors duration-500">
        REDEFINE <br /> 
        <span class="text-pink-300 dark:text-white/70 italic font-light transition-colors duration-500">YOUR</span> REGIMEN.
      </h1>
      
      <p class="text-lg sm:text-xl text-zinc-600 dark:text-white/80 font-light max-w-2xl mx-auto mb-12 animate-slide-up delay-100 transition-colors duration-500">
        Experience a new paradigm in luxury skincare and essentials. Curated for the modern aesthete.
      </p>
      
      <router-link
        to="/shop"
        class="group relative inline-flex items-center justify-center px-10 py-5 bg-zinc-900 dark:bg-white text-white dark:text-black overflow-hidden rounded-full font-medium tracking-wide transition-all duration-300 hover:scale-105 active:scale-95 animate-slide-up delay-200 hover:shadow-xl hover:shadow-pink-200/50"
      >
        <span class="relative z-10 flex items-center gap-2">
          Explore Collection
          <ArrowRightIcon class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </span>
      </router-link>

    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 animate-fade-in delay-500 z-10">
      <span class="text-zinc-400 dark:text-white/50 text-xs font-medium tracking-widest uppercase transition-colors duration-500">Scroll</span>
      <div class="w-px h-16 bg-pink-200 dark:bg-white/20 relative overflow-hidden transition-colors duration-500">
        <div class="w-full h-1/2 bg-zinc-900 dark:bg-white absolute top-0 left-0 animate-scroll-line"></div>
      </div>
    </div>
    
  </section>
</template>

<script setup>
import { ArrowRight as ArrowRightIcon } from '@lucide/vue'
</script>

<style scoped>
@keyframes slow-pan {
  0% { transform: scale(1.05) translate(0, 0); }
  100% { transform: scale(1.1) translate(-2%, -2%); }
}
.animate-slow-pan {
  animation: slow-pan 20s ease-out forwards;
}

@keyframes scroll-line {
  0% { transform: translateY(-100%); }
  100% { transform: translateY(200%); }
}
.animate-scroll-line {
  animation: scroll-line 2s cubic-bezier(0.77, 0, 0.175, 1) infinite;
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up {
  animation: slide-up 1s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-500 { animation-delay: 500ms; }
</style>