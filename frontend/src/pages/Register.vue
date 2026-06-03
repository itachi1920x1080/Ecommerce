<template>
  <div class="min-h-screen flex">
    <!-- Left: Decorative Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 relative overflow-hidden items-center justify-center p-12">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-white/5 animate-float"></div>
        <div class="absolute -left-12 -bottom-24 w-72 h-72 rounded-full bg-white/5 animate-float delay-300"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>
      </div>
      <div class="relative z-10 text-center">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center mb-8">
          <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-white mb-4">Join RUPP Shop</h2>
        <p class="text-primary-200/80 max-w-sm mx-auto leading-relaxed">Create your free account and discover amazing products with exclusive deals.</p>
      </div>
    </div>

    <!-- Right: Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-white">
      <div class="w-full max-w-md animate-slide-up">
        <div class="lg:hidden flex items-center gap-2.5 mb-10">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
          <span class="text-lg font-bold text-slate-800">RUPP <span class="text-primary-600">Shop</span></span>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Create your account</h1>
        <p class="text-sm text-slate-400 mb-8">Join us and start shopping today</p>

        <form @submit.prevent="handleRegister" class="space-y-5">
          <div>
            <label for="reg-name" class="block text-xs font-medium text-slate-500 mb-1.5">Full Name</label>
            <input v-model="name" type="text" id="reg-name" required placeholder="Your full name"
              class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
          </div>

          <div>
            <label for="reg-email" class="block text-xs font-medium text-slate-500 mb-1.5">Email Address</label>
            <input v-model="email" type="email" id="reg-email" required placeholder="you@example.com"
              class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
          </div>

          <div>
            <label for="reg-password" class="block text-xs font-medium text-slate-500 mb-1.5">Password</label>
            <input v-model="password" type="password" id="reg-password" required placeholder="Min 8 characters"
              class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
            <!-- Strength indicator -->
            <div class="flex gap-1 mt-2">
              <div v-for="i in 4" :key="i" class="h-1 flex-1 rounded-full transition-all duration-300"
                :class="i <= passwordStrength ? strengthColors[passwordStrength] : 'bg-slate-200'"></div>
            </div>
            <p v-if="password" class="text-xs mt-1" :class="strengthLabels[passwordStrength]?.color">{{ strengthLabels[passwordStrength]?.text }}</p>
          </div>

          <div>
            <label for="reg-confirm" class="block text-xs font-medium text-slate-500 mb-1.5">Confirm Password</label>
            <input v-model="confirmPassword" type="password" id="reg-confirm" required placeholder="Repeat password"
              class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
          </div>

          <p v-if="error" class="text-sm text-red-500 bg-red-50 px-4 py-2.5 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ error }}
          </p>

          <button type="submit" :disabled="loading"
            class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 hover:-translate-y-0.5 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loading ? 'Creating account...' : 'Create Account' }}
          </button>
        </form>

        <p class="text-sm text-center text-slate-400 mt-8">
          Already have an account?
          <router-link to="/login" class="text-primary-600 font-semibold hover:text-primary-700 transition-colors">Sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

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

const strengthColors = { 1: 'bg-red-400', 2: 'bg-amber-400', 3: 'bg-blue-400', 4: 'bg-emerald-400' }
const strengthLabels = {
  0: { text: '', color: '' },
  1: { text: 'Weak', color: 'text-red-500' },
  2: { text: 'Fair', color: 'text-amber-500' },
  3: { text: 'Good', color: 'text-blue-500' },
  4: { text: 'Strong', color: 'text-emerald-500' },
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
