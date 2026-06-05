<template>
  <header 
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    :class="[
      isScrolled 
        ? 'glass border-b border-zinc-200/50 dark:border-zinc-800/50 py-3' 
        : 'bg-transparent border-transparent py-6',
      isTransparentTheme ? 'text-white' : 'text-zinc-900 dark:text-zinc-50'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-3 items-center h-14">

        <!-- LEFT: Logo -->
        <div class="flex items-center justify-start">
          <router-link to="/" class="group">
            <span
              class="text-2xl font-display font-bold tracking-tight leading-none group-hover:opacity-80 transition-opacity"
              :class="isTransparentTheme ? 'text-white' : 'text-zinc-900 dark:text-zinc-50'"
            >
              Tea Tik kok <span class="text-pink-400">Shop</span>
            </span>
          </router-link>
        </div>

        <!-- CENTER: Desktop Navigation -->
        <nav class="hidden md:flex items-center justify-center gap-8">
          <router-link 
            v-for="link in navLinks" 
            :key="link.to" 
            :to="link.to" 
            class="text-[16px] font-medium tracking-wide transition-colors whitespace-nowrap"
            :class="[
              link.highlight
                ? (isTransparentTheme ? 'text-white font-bold' : 'text-primary-600 dark:text-primary-400')
                : (isTransparentTheme ? 'text-white/80 hover:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50')
            ]"
          >
            {{ link.label }}
          </router-link>
        </nav>

        <!-- RIGHT: Icons & Auth -->
        <div class="flex items-center justify-end gap-5">

          <!-- Search -->
          <button
            @click="searchOpen = true"
            class="transition-colors"
            :class="isTransparentTheme ? 'text-white hover:text-white/80' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50'"
            id="nav-search-btn"
          >
            <SearchIcon class="w-5 h-5 stroke-[1.5]" />
          </button>

          <!-- Dark / Light Mode Toggle -->
          <button
            @click="toggleTheme"
            class="transition-all duration-300"
            :class="isTransparentTheme ? 'text-white hover:text-white/80' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50'"
            :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
          >
            <SunIcon v-if="isDark" class="w-5 h-5 stroke-[1.5]" />
            <MoonIcon v-else class="w-5 h-5 stroke-[1.5]" />
          </button>

          <!-- User Menu (logged in) -->
          <template v-if="auth.isLoggedIn">
            <div class="relative hidden md:block">
              <button
                @click="showDropdown = !showDropdown"
                class="flex items-center justify-center transition-colors"
                :class="isTransparentTheme ? 'text-white hover:text-white/80' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50'"
              >
                <img
                  v-if="auth.user?.avatar"
                  :src="auth.user.avatar.startsWith('http') ? auth.user.avatar : 'http://localhost:8000/storage/' + auth.user.avatar"
                  class="w-6 h-6 rounded-full object-cover border border-zinc-200 dark:border-zinc-700"
                  alt="Avatar"
                />
                <UserIcon v-else class="w-5 h-5 stroke-[1.5]" />
              </button>

              <Transition name="slide-up">
                <div
                  v-if="showDropdown"
                  class="absolute right-0 mt-4 w-56 bg-white dark:bg-zinc-900 rounded-2xl shadow-elegant border border-zinc-200/60 dark:border-zinc-800/60 overflow-hidden z-50 p-1"
                  id="user-dropdown"
                >
                  <div class="px-3 py-2 mb-1 border-b border-zinc-100 dark:border-zinc-800/50">
                    <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-50 truncate">{{ auth.user?.name }}</p>
                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">{{ auth.user?.email }}</p>
                  </div>
                  <div class="flex flex-col gap-0.5">
                    <router-link v-if="auth.isAdmin" to="/admin" @click="showDropdown = false" class="block px-3 py-2 text-[13px] font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors w-full text-left">Admin Panel</router-link>
                    <router-link v-if="auth.isDriver" to="/driver" @click="showDropdown = false" class="block px-3 py-2 text-[13px] font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors w-full text-left">Driver Dashboard</router-link>
                    <router-link to="/profile" @click="showDropdown = false" class="block px-3 py-2 text-[13px] font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors w-full text-left">My Profile</router-link>
                    <router-link to="/my-orders" @click="showDropdown = false" class="block px-3 py-2 text-[13px] font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors w-full text-left">Track Order</router-link>
                    <router-link to="/wishlist" @click="showDropdown = false" class="block px-3 py-2 text-[13px] font-medium text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors w-full text-left">Wishlist</router-link>
                    <div class="h-px bg-zinc-100 dark:bg-zinc-800/50 my-1"></div>
                    <button @click="handleLogout" class="block px-3 py-2 text-[13px] font-medium text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors w-full text-left">Sign out</button>
                  </div>
                </div>
              </Transition>
            </div>
          </template>

          <!-- Sign In (not logged in) -->
          <template v-else>
            <router-link
              to="/login"
              class="text-[13px] font-medium transition-colors hidden md:block"
              :class="isTransparentTheme ? 'text-white hover:text-white/80' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50'"
            >
              Sign In
            </router-link>
          </template>

          <!-- Wishlist (logged in) -->
          <router-link
            v-if="auth.isLoggedIn"
            to="/wishlist"
            class="relative hidden md:flex items-center transition-colors"
            :class="isTransparentTheme ? 'text-white hover:text-white/80' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50'"
          >
            <HeartIcon class="w-5 h-5 stroke-[1.5]" />
          </router-link>

          <!-- Cart -->
          <button
            @click="toggleCart"
            class="relative flex items-center transition-colors"
            :class="isTransparentTheme ? 'text-white hover:text-white/80' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50'"
            id="nav-cart"
          >
            <div class="relative">
              <ShoppingBagIcon class="w-5 h-5 stroke-[1.5]" />
              <Transition name="scale-in">
                <span
                  v-if="cart.totalItems > 0"
                  class="absolute -top-1.5 -right-2 w-4 h-4 rounded-full bg-primary-600 text-white text-[10px] font-bold flex items-center justify-center border border-white dark:border-zinc-950"
                >
                  {{ cart.totalItems }}
                </span>
              </Transition>
            </div>
          </button>

          <!-- Mobile Menu Toggle -->
          <button
            @click="mobileOpen = !mobileOpen"
            class="md:hidden flex items-center justify-center transition-colors ml-1"
            :class="isTransparentTheme ? 'text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-50'"
          >
            <MenuIcon v-if="!mobileOpen" class="w-5 h-5" />
            <XIcon v-else class="w-5 h-5" />
          </button>
        </div>

      </div>
    </div>

    <!-- Mobile Menu Panel -->
    <Transition name="slide-down">
      <div v-if="mobileOpen" class="md:hidden glass absolute top-full left-0 w-full max-h-[85vh] overflow-y-auto border-t border-zinc-200/50 dark:border-zinc-800/50">
        <div class="px-4 py-6 flex flex-col gap-2">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            @click="mobileOpen = false"
            class="text-[15px] font-medium text-zinc-900 dark:text-zinc-50 py-3 border-b border-zinc-100 dark:border-zinc-800/50"
            :class="link.highlight ? 'text-primary-600 dark:text-primary-400' : ''"
          >
            {{ link.label }}
          </router-link>

          <!-- Mobile Dark Mode Toggle -->
          <button
            @click="toggleTheme"
            class="flex items-center gap-3 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 border-b border-zinc-100 dark:border-zinc-800/50"
          >
            <SunIcon v-if="isDark" class="w-5 h-5" />
            <MoonIcon v-else class="w-5 h-5" />
            {{ isDark ? 'Light Mode' : 'Dark Mode' }}
          </button>

          <div class="mt-6">
            <template v-if="auth.isLoggedIn">
              <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-3 px-2">Account</p>
              <router-link v-if="auth.isAdmin" to="/admin" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Admin Panel</router-link>
              <router-link v-if="auth.isDriver" to="/driver" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Driver Dashboard</router-link>
              <router-link to="/profile" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">My Profile</router-link>
              <router-link to="/my-orders" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Track Order</router-link>
              <router-link to="/wishlist" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Wishlist</router-link>
              <button @click="handleLogout" class="block px-4 py-3 text-[15px] font-medium text-red-600 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Sign Out</button>
            </template>
            <template v-else>
              <router-link to="/login" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Sign In</router-link>
              <router-link to="/register" @click="mobileOpen = false" class="block px-4 py-3 text-[15px] font-medium text-primary-600 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors w-full text-left">Create Account</router-link>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </header>

  <SearchModal :show="searchOpen" @close="searchOpen = false" />
  <div v-if="!isTransparentTheme" class="h-24"></div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useTheme } from '@/composables/useTheme'
import {
  Menu as MenuIcon,
  X as XIcon,
  ShoppingBag as ShoppingBagIcon,
  Search as SearchIcon,
  User as UserIcon,
  Heart as HeartIcon,
  Sun as SunIcon,
  Moon as MoonIcon,
} from '@lucide/vue'
import SearchModal from '@/components/ui/SearchModal.vue'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()
const cart = useCartStore()

// Theme toggle
const { isDark, toggleTheme } = useTheme()

const mobileOpen = ref(false)
const showDropdown = ref(false)
const isScrolled = ref(false)
const searchOpen = ref(false)


const navLinks = computed(() => {
  const links = [
    { to: '/', label: 'Home' },
    { to: '/shop', label: 'Shop' },
    { to: '/contact', label: 'Contact' },
    { to: '/about', label: 'About Us' },
    { to: '/my-orders', label: 'Track Order' },
    { to: '/shop?sale=true', label: 'Sale', highlight: true },
  ]
  if (auth.isAdmin) {
    links.push({ to: '/admin', label: 'Admin', highlight: true })
  }
  return links
})

const toggleCartInject = inject('toggleCart')
function toggleCart() {
  if (toggleCartInject) toggleCartInject()
}

async function handleLogout() {
  showDropdown.value = false
  mobileOpen.value = false
  await auth.logout()
  router.push('/login')
}

function handleScroll() {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>