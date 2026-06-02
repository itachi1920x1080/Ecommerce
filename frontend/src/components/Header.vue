<template>
  <nav class="fixed top-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 transition-all duration-300"
       :class="scrolled ? 'shadow-sm' : ''">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">

        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2.5 group" id="header-logo">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
          <span class="text-lg font-bold text-slate-800 tracking-tight">RUPP <span class="text-blue-600">Shop</span></span>
        </router-link>

        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-1">
          <router-link to="/" class="nav-link" active-class="nav-link-active" id="nav-home">Home</router-link>
          <router-link to="/contact" class="nav-link" active-class="nav-link-active" id="nav-contact">Contact</router-link>

          <template v-if="auth.isLoggedIn">
            <router-link to="/wishlist" class="nav-link" active-class="nav-link-active" id="nav-wishlist">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
              Wishlist
            </router-link>
            <router-link to="/my-orders" class="nav-link" active-class="nav-link-active" id="nav-orders">My Orders</router-link>

            <!-- Cart Button -->
            <button @click="toggleCart()" class="nav-link relative" id="nav-cart">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
              Cart
              <span v-if="cart.totalItems > 0"
                class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center animate-bounce-once">
                {{ cart.totalItems > 9 ? '9+' : cart.totalItems }}
              </span>
            </button>

            <router-link v-if="auth.isAdmin" to="/admin/dashboard"
              class="nav-link text-indigo-600" active-class="nav-link-active" id="nav-admin">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              Admin
            </router-link>

            <!-- User dropdown -->
            <div class="relative ml-2">
              <button @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-medium text-slate-700 transition-all" id="nav-user-menu">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                  {{ (auth.user?.name || auth.user?.email || 'U').charAt(0).toUpperCase() }}
                </div>
                <span class="hidden lg:inline">{{ auth.user?.name || 'User' }}</span>
                <svg class="w-3.5 h-3.5 transition-transform" :class="showDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <Transition name="fade">
                <div v-if="showDropdown"
                  class="absolute right-0 mt-2 w-48 rounded-xl bg-white border border-slate-200 shadow-xl py-1 z-50" id="user-dropdown">
                  <button @click="handleLogout"
                    class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign out
                  </button>
                </div>
              </Transition>
            </div>
          </template>

          <!-- Guest buttons -->
          <template v-else>
            <router-link to="/login" class="nav-link" active-class="nav-link-active" id="nav-login">Sign in</router-link>
            <router-link to="/register"
              class="ml-2 px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-200 hover:-translate-y-0.5 active:scale-95"
              id="nav-register">
              Get Started
            </router-link>
          </template>
        </div>

        <!-- Mobile Hamburger -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors" id="mobile-menu-toggle">
          <svg v-if="!mobileOpen" class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition name="fade">
      <div v-if="mobileOpen" class="md:hidden border-t border-slate-200 bg-white/95 backdrop-blur-xl" id="mobile-menu">
        <div class="px-4 py-3 space-y-1">
          <router-link to="/" @click="mobileOpen = false" class="mobile-link">Home</router-link>
          <router-link to="/contact" @click="mobileOpen = false" class="mobile-link">Contact</router-link>
          <template v-if="auth.isLoggedIn">
            <button @click="toggleCart(); mobileOpen = false" class="mobile-link w-full text-left">
              Cart <span v-if="cart.totalItems > 0" class="ml-1 text-blue-600 font-semibold">({{ cart.totalItems }})</span>
            </button>
            <router-link to="/wishlist" @click="mobileOpen = false" class="mobile-link">Wishlist</router-link>
            <router-link to="/my-orders" @click="mobileOpen = false" class="mobile-link">My Orders</router-link>
            <router-link v-if="auth.isAdmin" to="/admin/dashboard" @click="mobileOpen = false" class="mobile-link text-indigo-600">Admin Panel</router-link>
            <button @click="handleLogout" class="mobile-link w-full text-left text-red-600">Sign out</button>
          </template>
          <template v-else>
            <router-link to="/login" @click="mobileOpen = false" class="mobile-link">Sign in</router-link>
            <router-link to="/register" @click="mobileOpen = false" class="block px-3 py-2.5 text-center bg-blue-600 text-white text-sm font-semibold rounded-xl">Get Started</router-link>
          </template>
        </div>
      </div>
    </Transition>
  </nav>

  <!-- Spacer for fixed nav -->
  <div class="h-16"></div>
</template>

<script setup>
import { ref, inject, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth   = useAuthStore()
const cart   = useCartStore()
const router = useRouter()

const toggleCart   = inject('toggleCart')
const showDropdown = ref(false)
const mobileOpen   = ref(false)
const scrolled     = ref(false)

// Track scroll for shadow effect
function handleScroll() {
  scrolled.value = window.scrollY > 10
}
onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))

// Close dropdown on outside click
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('#nav-user-menu') && !e.target.closest('#user-dropdown')) {
      showDropdown.value = false
    }
  })
})

// Fetch cart on mount if logged in
onMounted(() => {
  if (auth.isLoggedIn) cart.fetchCart()
})

async function handleLogout() {
  showDropdown.value = false
  mobileOpen.value = false
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.nav-link {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.875rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  border-radius: 0.75rem;
  transition: all 0.15s ease;
}
.nav-link:hover {
  background: #f1f5f9;
  color: #1e293b;
}
.nav-link-active {
  background: #eff6ff;
  color: #2563eb;
}
.mobile-link {
  display: block;
  padding: 0.625rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  border-radius: 0.75rem;
  transition: background 0.15s ease;
}
.mobile-link:hover {
  background: #f1f5f9;
}
@keyframes bounce-once {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.3); }
}
.animate-bounce-once {
  animation: bounce-once 0.4s ease;
}
</style>
