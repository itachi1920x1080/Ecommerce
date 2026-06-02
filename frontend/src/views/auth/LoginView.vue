<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
      <h2 class="text-2xl font-semibold text-gray-800 mb-1">Welcome back</h2>
      <p class="text-sm text-gray-500 mb-6">Sign in to your account</p>

      <div v-if="error" class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg">
        {{ error }}
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
          <input
            v-model="email" type="email" placeholder="you@example.com"
            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">Password</label>
          <input
            v-model="password" type="password" placeholder="••••••••"
            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all"
          />
        </div>
      </div>

      <button
        @click="login"
        class="w-full mt-6 py-2.5 bg-green-700 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors"
      >
        Sign in
      </button>

      <p class="text-center text-sm text-gray-500 mt-5">
        No account?
        <router-link to="/register" class="text-green-700 font-medium hover:underline">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    router.push(auth.isAdmin ? '/admin' : '/')
  } catch (e) {
    error.value = 'Invalid email or password'
  }
}
</script>