<template>
  <footer class="bg-charcoal-900 pt-20 pb-10 px-6">
    <div class="max-w-7xl mx-auto">
      <!-- Top Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
        <!-- Brand -->
        <div class="lg:col-span-1">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-cream-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-blush-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
            <span class="text-xl font-display font-bold text-cream-50 tracking-wider">RUPP <span class="text-blush-400">Shop</span></span>
          </div>
          <p class="text-sm font-sans text-charcoal-800/40 text-cream-100/60 leading-relaxed max-w-xs">
            Your premium destination for quality products. Fast delivery, secure payments, and exceptional customer service.
          </p>
          <!-- Social Icons -->
          <div class="flex gap-4 mt-8">
            <a v-for="social in socials" :key="social.label" :href="social.url" target="_blank"
              class="w-10 h-10 rounded-full bg-charcoal-800 hover:bg-blush-500 flex items-center justify-center text-cream-100/60 hover:text-white transition-all duration-300 hover:-translate-y-1 shadow-sm"
              :aria-label="social.label"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" v-html="social.path"></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-[11px] font-bold text-cream-50 mb-6 uppercase tracking-[0.2em]">Quick Links</h4>
          <ul class="space-y-4">
            <li v-for="link in quickLinks" :key="link.to">
              <router-link :to="link.to" class="text-sm font-sans text-cream-100/60 hover:text-blush-400 transition-colors duration-300 flex items-center gap-3 group">
                <span class="w-1 h-1 rounded-full bg-blush-400 opacity-0 group-hover:opacity-100 transition-all duration-300 -translate-x-2 group-hover:translate-x-0"></span>
                <span class="transition-transform duration-300 group-hover:translate-x-1">{{ link.label }}</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div>
          <h4 class="text-[11px] font-bold text-cream-50 mb-6 uppercase tracking-[0.2em]">Support</h4>
          <ul class="space-y-4">
            <li v-for="link in supportLinks" :key="link.label">
              <a href="#" class="text-sm font-sans text-cream-100/60 hover:text-blush-400 transition-colors duration-300 flex items-center gap-3 group">
                <span class="w-1 h-1 rounded-full bg-blush-400 opacity-0 group-hover:opacity-100 transition-all duration-300 -translate-x-2 group-hover:translate-x-0"></span>
                <span class="transition-transform duration-300 group-hover:translate-x-1">{{ link.label }}</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div>
          <h4 class="text-[11px] font-bold text-cream-50 mb-6 uppercase tracking-[0.2em]">Contact</h4>
          <ul class="space-y-4">
            <li class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blush-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span class="text-sm font-sans text-cream-100/60 leading-relaxed">Royal University of Phnom Penh (RUPP)</span>
            </li>
            <li class="flex items-center gap-3">
              <svg class="w-5 h-5 text-blush-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <span class="text-sm font-sans text-cream-100/60">012 345 678</span>
            </li>
            <li class="flex items-center gap-3">
              <svg class="w-5 h-5 text-blush-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="text-sm font-sans text-cream-100/60">support@ruppshop.com</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-charcoal-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <p class="text-[11px] font-sans font-bold uppercase tracking-widest text-charcoal-800/40 text-cream-100/40">© {{ new Date().getFullYear() }} RUPP Shop. All rights reserved.</p>
        <div class="flex items-center gap-8">
          <a href="#" class="text-[11px] font-sans font-bold uppercase tracking-widest text-cream-100/40 hover:text-cream-50 transition-colors">Privacy Policy</a>
          <a href="#" class="text-[11px] font-sans font-bold uppercase tracking-widest text-cream-100/40 hover:text-cream-50 transition-colors">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const quickLinks = [
  { to: '/',          label: 'Home' },
  { to: '/contact',   label: 'Contact' },
  { to: '/cart',      label: 'Shopping Cart' },
  { to: '/my-orders', label: 'Track Orders' },
]

const supportLinks = [
  { label: 'Help Center' },
  { label: 'Shipping Info' },
  { label: 'Returns & Exchange' },
  { label: 'FAQs' },
]

const socials = [
  { label: 'Facebook', url: '#', path: '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>' },
  { label: 'Telegram', url: '#', path: '<path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>' },
  { label: 'Instagram', url: '#', path: '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>' },
]
</script>
