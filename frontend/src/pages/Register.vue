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
        <h2 class="text-4xl font-display font-medium text-zinc-900 dark:text-white mb-4">Join RUPP Shop</h2>
        <p class="text-zinc-500 dark:text-zinc-400 font-light max-w-md mx-auto text-lg">
          Create an account to discover premium products, exclusive deals, and personalized recommendations.
        </p>
      </div>
    </div>

    <!-- Right: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative overflow-y-auto">
      <router-link to="/" class="absolute top-8 left-8 lg:hidden flex items-center gap-2 text-zinc-900 dark:text-zinc-50 font-display font-semibold">
        <ShoppingBagIcon class="w-5 h-5 text-primary-500" />
        RUPP Shop
      </router-link>

      <div class="w-full max-w-sm animate-fade-in my-auto py-12">
        <div class="mb-10 text-center lg:text-left">
          <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">Create account</h1>
          <p class="text-sm text-zinc-500 dark:text-zinc-400">Join us and start shopping today.</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5">
          <div class="space-y-1.5">
            <label for="reg-name" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Full Name</label>
            <input v-model="name" type="text" id="reg-name" required placeholder="John Doe"
              class="w-full px-4 py-3 rounded-xl bg-white/70 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
          </div>

          <div class="space-y-1.5">
            <label for="reg-email" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Email</label>
            <input v-model="email" type="email" id="reg-email" required placeholder="you@example.com"
              class="w-full px-4 py-3 rounded-xl bg-white/70 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
          </div>

          <div class="space-y-1.5">
            <label for="reg-password" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Password</label>
            <input v-model="password" type="password" id="reg-password" required placeholder="Min 8 characters"
              class="w-full px-4 py-3 rounded-xl bg-white/70 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
            
            <!-- Strength indicator -->
            <div class="flex gap-1.5 mt-2">
              <div v-for="i in 4" :key="i" class="h-1 flex-1 rounded-full transition-all duration-300"
                :class="i <= passwordStrength ? strengthColors[passwordStrength] : 'bg-zinc-200 dark:bg-zinc-800'"></div>
            </div>
            <p v-if="password" class="text-xs font-medium mt-1" :class="strengthLabels[passwordStrength]?.color">{{ strengthLabels[passwordStrength]?.text }}</p>
          </div>

          <div class="space-y-1.5">
            <label for="reg-confirm" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Confirm Password</label>
            <input v-model="confirmPassword" type="password" id="reg-confirm" required placeholder="Repeat password"
              class="w-full px-4 py-3 rounded-xl bg-white/70 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
          </div>

          <p v-if="error" class="text-sm font-medium text-red-500 flex items-center gap-2 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20">
            <AlertCircleIcon class="w-4 h-4 shrink-0" />
            {{ error }}
          </p>

          <button type="submit" :disabled="loading"
            class="btn-primary w-full py-3.5 mt-4 text-sm flex items-center justify-center gap-2 disabled:opacity-50">
            <Loader2Icon v-if="loading" class="w-4 h-4 animate-spin" />
            {{ loading ? 'Creating account...' : 'Create Account' }}
          </button>
        </form>

        <p class="text-sm text-center text-zinc-500 dark:text-zinc-400 mt-8">
          Already have an account?
          <router-link to="/login" class="font-medium text-zinc-900 dark:text-zinc-50 hover:underline underline-offset-4 ml-1">Sign in</router-link>
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

const auth = useAuthStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const error = ref('')
const loading = ref(false)

const passwordStrength = computed(() => {
  const p = password.value
  if (!p) return 0
  let score = 0
  if (p.length >= 8) score++
  if (/[A-Z]/.test(p)) score++
  if (/[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  return score
})

const strengthColors = { 1: 'bg-red-500', 2: 'bg-amber-500', 3: 'bg-blue-500', 4: 'bg-green-500' }
const strengthLabels = {
  0: { text: '', color: '' },
  1: { text: 'Weak', color: 'text-red-500' },
  2: { text: 'Fair', color: 'text-amber-500' },
  3: { text: 'Good', color: 'text-blue-500' },
  4: { text: 'Strong', color: 'text-green-500' },
}

async function handleRegister() {
  error.value = ''
  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match.'
    return
  }
  if (password.value.length < 8) {
    error.value = 'Password must be at least 8 characters.'
    return
  }
  loading.value = true
  try {
    await auth.register(name.value, email.value, password.value, confirmPassword.value)
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>