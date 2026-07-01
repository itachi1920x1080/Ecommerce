<template>
  <div class="min-h-screen flex bg-gradient-to-br from-white via-pink-50 to-pink-100 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 transition-colors duration-300">
    
    <!-- Left: Branding / Image -->
    <div class="hidden lg:flex w-1/2 bg-white/40 dark:bg-zinc-900/60 backdrop-blur-sm relative overflow-hidden items-center justify-center transition-colors duration-300">
      <!-- Abstract Glows -->
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500/20 rounded-full blur-[100px] animate-float"></div>
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-zinc-500/20 rounded-full blur-[100px] animate-float" style="animation-delay: -3s;"></div>
      
      <div class="relative z-10 p-12 text-center">
        <router-link to="/" class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-zinc-900/10 dark:bg-white/10 backdrop-blur-md border border-zinc-900/10 dark:border-white/10 text-zinc-900 dark:text-white mb-8 hover:scale-105 transition-transform">
          <ShoppingBagIcon class="w-8 h-8" />
        </router-link>
        <h2 class="text-4xl font-display font-medium text-zinc-900 dark:text-white mb-4">Welcome back</h2>
        <p class="text-zinc-500 dark:text-zinc-400 font-light max-w-md mx-auto text-lg">
          Sign in to access your curated collection of premium products and exclusive offers.
        </p>
      </div>
    </div>

    <!-- Right: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative">
      <router-link to="/" class="absolute top-8 left-8 lg:hidden flex items-center gap-2 text-zinc-900 dark:text-zinc-50 font-display font-semibold">
        <ShoppingBagIcon class="w-5 h-5 text-primary-500" />
        RUPP Shop
      </router-link>

      <div class="w-full max-w-sm animate-fade-in">
        <div class="mb-10 text-center lg:text-left">
          <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">Sign in</h1>
          <p class="text-sm text-zinc-500 dark:text-zinc-400">Enter your details to proceed.</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <div class="space-y-1.5">
            <label for="email" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Email</label>
            <input v-model="email" type="email" id="email" required placeholder="you@example.com"
              class="w-full px-4 py-3 rounded-xl bg-white/70 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
          </div>

          <div class="space-y-1.5">
            <div class="flex items-center justify-between">
              <label for="password" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Password</label>
              <a href="#" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">Forgot password?</a>
            </div>
            <input v-model="password" type="password" id="password" required placeholder="••••••••"
              class="w-full px-4 py-3 rounded-xl bg-white/70 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
          </div>

          <p v-if="error" class="text-sm font-medium text-red-500 flex items-center gap-2 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
            <AlertCircleIcon class="w-4 h-4 shrink-0" />
            {{ error }}
          </p>

          <button type="submit" :disabled="loading"
            class="btn-primary w-full py-3.5 mt-2 text-sm flex items-center justify-center gap-2 disabled:opacity-50">
            <Loader2Icon v-if="loading" class="w-4 h-4 animate-spin" />
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>

          <div class="relative mt-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-zinc-200 dark:border-zinc-800"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white/70 dark:bg-zinc-900 text-zinc-500">Or continue with</span>
            </div>
          </div>

          <a :href="googleLoginUrl"
            class="w-full py-3.5 flex items-center justify-center gap-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm font-medium transition-colors shadow-sm mt-4">
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Sign in with Google
          </a>
        </form>

        <p class="text-sm text-center text-zinc-500 dark:text-zinc-400 mt-8">
          Don't have an account?
          <router-link to="/register" class="font-medium text-zinc-900 dark:text-zinc-50 hover:underline underline-offset-4 ml-1">Sign up</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ShoppingBag as ShoppingBagIcon, AlertCircle as AlertCircleIcon, Loader2 as Loader2Icon } from '@lucide/vue'
import api from '@/api/axios.js'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const googleLoginUrl = computed(() => {
  // Use the axios configured baseUrl to point to the backend auth endpoint
  const base = api.defaults.baseURL.replace(/\/api\/?$/, '');
  return `${base}/api/auth/login`;
})

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(email.value, password.value) 
    router.push('/')
  } catch (e) {
    // បង្ហាញ Error ឱ្យច្បាស់ដើម្បីងាយស្រួលដោះស្រាយ
    error.value = e.response?.data?.message || 'Login failed. Please check your credentials.'
    console.error("Login Error:", e)
  } finally {
    loading.value = false
  }
}
</script>