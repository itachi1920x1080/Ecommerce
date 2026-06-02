<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto space-y-8 animate-fade-in">
      
      <!-- Header -->
      <div class="text-center">
        <h1 class="text-3xl font-bold text-slate-800">Contact Us</h1>
        <p class="text-slate-500 mt-2">Have a question or need help? Reach out to us on our social platforms!</p>
      </div>

      <!-- Contact Methods -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Telegram -->
        <a href="https://t.me/rupp_shop_bot" target="_blank"
          class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group flex flex-col items-center text-center animate-slide-up" style="animation-delay: 0.1s">
          <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.21-1.12-.33-1.08-.7.02-.19.27-.39.75-.6 2.95-1.28 4.92-2.13 5.89-2.53 2.79-1.16 3.37-1.36 3.75-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .24z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-800">Telegram Bot</h3>
          <p class="text-blue-600 font-medium mt-1">@rupp_shop_bot</p>
          <p class="text-sm text-slate-400 mt-2">Chat with us instantly for fast support and inquiries.</p>
        </a>

        <!-- Facebook -->
        <a href="https://facebook.com" target="_blank"
          class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group flex flex-col items-center text-center animate-slide-up" style="animation-delay: 0.2s">
          <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
              <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-800">Facebook Page</h3>
          <p class="text-blue-600 font-medium mt-1">RUPP Shop</p>
          <p class="text-sm text-slate-400 mt-2">Follow us for the latest updates, sales, and products.</p>
        </a>

      </div>

    </div>
  </div>
</template>

<script setup>
</script>
