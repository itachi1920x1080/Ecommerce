<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm animate-slide-up">
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-500/5 border border-slate-200/60 p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
          <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-slate-800 mb-1">Welcome back</h1>
          <p class="text-sm text-slate-400">Sign in to your RUPP Shop account</p>
        </div>

        <!-- Error -->
        <div v-if="error" class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          {{ error }}
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Email</label>
            <input v-model="email" type="email" placeholder="you@example.com" required
              class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all bg-slate-50/50"
              id="login-email" />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Password</label>
            <input v-model="password" type="password" placeholder="••••••••" required
              class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all bg-slate-50/50"
              id="login-password" />
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 active:scale-[0.98]"
            id="login-submit">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>

        <p class="text-center text-sm text-slate-400 mt-6">
          Don't have an account?
          <router-link to="/register" class="text-blue-600 font-semibold hover:underline">Create one</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth    = useAuthStore()
const router  = useRouter()
const toast   = inject('toast')
const email   = ref('')
const password = ref('')
const error   = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value   = ''
  loading.value = true
  try {
    await auth.login(email.value, password.value)
    toast('Welcome back!', 'success')
    router.push(auth.isAdmin ? '/admin/dashboard' : '/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Invalid email or password'
  } finally {
    loading.value = false
  }
}
</script>
