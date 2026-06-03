<template>
  <nav
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    :class="scrolled
      ? 'bg-white/85 backdrop-blur-2xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] border-b border-slate-200/60'
      : 'bg-white/60 backdrop-blur-xl'"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">

        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2.5 group shrink-0" id="header-logo">
          <div class="relative w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/25 group-hover:shadow-primary-500/40 transition-all duration-300 group-hover:scale-105">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
          <span class="text-lg font-bold text-slate-800 tracking-tight">RUPP <span class="bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent">Shop</span></span>
        </router-link>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-0.5">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="nav-item"
            active-class="nav-item--active"
            :id="link.id"
          >
            <component :is="link.icon" v-if="link.icon" class="w-4 h-4" />
            <span>{{ link.label }}</span>
            <!-- Cart Badge -->
            <span
              v-if="link.badge && link.badge > 0"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] rounded-full bg-primary-500 text-white text-[10px] font-bold flex items-center justify-center animate-bounce-once ring-2 ring-white"
            >
              {{ link.badge > 9 ? '9+' : link.badge }}
            </span>
          </router-link>

          <!-- Admin Link (only for admin users) -->
          <router-link
            v-if="auth.isAdmin"
            to="/admin/dashboard"
            class="nav-item text-primary-600"
            active-class="nav-item--active"
            id="nav-admin"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Admin</span>
          </router-link>

          <!-- Driver Link (only for driver users) -->
          <router-link
            v-if="auth.isDriver"
            to="/driver"
            class="nav-item text-emerald-600"
            active-class="nav-item--active"
            id="nav-driver"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span>Driver Dashboard</span>
          </router-link>

          <!-- User Menu / Auth Buttons -->
          <template v-if="auth.isLoggedIn">
            <div class="relative ml-3">
              <button
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full bg-slate-100/80 hover:bg-slate-200/80 transition-all duration-200 group"
                id="nav-user-menu"
              >
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                  {{ userInitial }}
                </div>
                <span class="hidden lg:inline text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">{{ auth.user?.name || 'User' }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="showDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <!-- Dropdown -->
              <Transition name="slide">
                <div
                  v-if="showDropdown"
                  class="absolute right-0 mt-2 w-52 rounded-2xl bg-white border border-slate-200/80 shadow-xl shadow-slate-200/50 py-2 z-50 overflow-hidden"
                  id="user-dropdown"
                >
                  <div class="px-4 py-2.5 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-800">{{ auth.user?.name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth.user?.email }}</p>
                  </div>
                  <router-link
                    to="/profile"
                    @click="showDropdown = false"
                    class="block w-full text-left px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2.5 mt-1"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Profile
                  </router-link>
                  <button
                    @click="handleLogout"
                    class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2.5 mt-1"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign out
                  </button>
                </div>
              </Transition>
            </div>
          </template>

          <!-- Guest Buttons -->
          <template v-else>
            <router-link to="/login" class="nav-item" active-class="nav-item--active" id="nav-login">Sign in</router-link>
            <router-link
              to="/register"
              class="ml-2 px-5 py-2 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 hover:-translate-y-0.5 active:scale-95"
              id="nav-register"
            >
              Get Started
            </router-link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button
          @click="mobileOpen = !mobileOpen"
          class="md:hidden relative w-10 h-10 rounded-xl hover:bg-slate-100 transition-colors flex items-center justify-center"
          id="mobile-menu-toggle"
        >
          <svg v-if="!mobileOpen" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu Panel -->
    <Transition name="slide">
      <div v-if="mobileOpen" class="md:hidden border-t border-slate-200/60 bg-white/95 backdrop-blur-2xl" id="mobile-menu">
        <div class="px-4 py-4 space-y-1">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            @click="mobileOpen = false"
            class="mobile-link"
            active-class="mobile-link--active"
          >
            <component :is="link.icon" v-if="link.icon" class="w-4 h-4" />
            {{ link.label }}
            <span v-if="link.badge && link.badge > 0" class="ml-auto text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">{{ link.badge }}</span>
          </router-link>

          <router-link v-if="auth.isLoggedIn && auth.isAdmin" to="/admin/dashboard" @click="mobileOpen = false" class="mobile-link text-primary-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Admin Panel
          </router-link>

          <router-link v-if="auth.isLoggedIn && auth.isDriver" to="/driver" @click="mobileOpen = false" class="mobile-link text-emerald-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Driver Dashboard
          </router-link>

          <div class="pt-3 border-t border-slate-100">
            <template v-if="auth.isLoggedIn">
              <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-bold">
                  {{ userInitial }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-800">{{ auth.user?.name }}</p>
                  <p class="text-xs text-slate-400">{{ auth.user?.email }}</p>
                </div>
              </div>
              <router-link to="/profile" @click="mobileOpen = false" class="mobile-link w-full text-left text-slate-600 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                My Profile
              </router-link>
              <button @click="handleLogout" class="mobile-link w-full text-left text-red-600 hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign out
              </button>
            </template>
            <template v-else>
              <router-link to="/login" @click="mobileOpen = false" class="mobile-link">Sign in</router-link>
              <router-link to="/register" @click="mobileOpen = false" class="block mt-2 px-4 py-3 text-center bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-semibold rounded-xl">
                Get Started
              </router-link>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </nav>

  <!-- Spacer for fixed nav -->
  <div class="h-16"></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject, h } from 'vue'
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

const userInitial = computed(() =>
  (auth.user?.name || auth.user?.email || 'U').charAt(0).toUpperCase()
)

// Inline icon components as render functions
const HeartIcon = { render: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2', class: 'w-4 h-4' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })]) }
const CartIcon  = { render: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2', class: 'w-4 h-4' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z' })]) }

// Navigation links — dynamic based on auth state
const navLinks = computed(() => {
  const links = [
    { to: '/',        label: 'Home',      id: 'nav-home',   icon: null },
    { to: '/contact', label: 'Contact',   id: 'nav-contact', icon: null },
  ]

  if (auth.isLoggedIn) {
    links.push(
      { to: '/wishlist',  label: 'Wishlist',  id: 'nav-wishlist', icon: HeartIcon },
      { to: '/my-orders', label: 'My Orders', id: 'nav-orders',   icon: null },
      { to: '/cart',      label: 'Cart',      id: 'nav-cart',     icon: CartIcon, badge: cart.totalItems },
    )
  }

  return links
})

// Scroll handler
function handleScroll() {
  scrolled.value = window.scrollY > 10
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })

  // Close dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!e.target.closest('#nav-user-menu') && !e.target.closest('#user-dropdown')) {
      showDropdown.value = false
    }
  })

  // Fetch cart on mount if logged in
  if (auth.isLoggedIn) cart.fetchCart()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

async function handleLogout() {
  showDropdown.value = false
  mobileOpen.value = false
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
/* Desktop nav item */
.nav-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.875rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #64748b;
  border-radius: 0.75rem;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.nav-item:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.nav-item--active {
  color: #4f46e5;
  background: #eef2ff;
}

.nav-item--active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 50%;
  transform: translateX(-50%);
  width: 60%;
  height: 2px;
  background: linear-gradient(90deg, #6366f1, #818cf8);
  border-radius: 999px;
}

/* Mobile nav link */
.mobile-link {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  border-radius: 0.75rem;
  transition: all 0.15s ease;
}

.mobile-link:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.mobile-link--active {
  background: #eef2ff;
  color: #4f46e5;
}
</style>
