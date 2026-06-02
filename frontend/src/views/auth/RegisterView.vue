<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
      <h2 class="text-2xl font-semibold text-gray-800 mb-1">Create account</h2>
      <p class="text-sm text-gray-500 mb-6">Join SkincareShop today</p>

      <div v-if="error" class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg">
        {{ error }}
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">Name</label>
          <input
            v-model="name" type="text" placeholder="Your name"
            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all"
          />
        </div>
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
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm Password</label>
          <input
            v-model="password_confirmation" type="password" placeholder="••••••••"
            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all"
          />
        </div>
      </div>

      <button
        @click="register"
        class="w-full mt-6 py-2.5 bg-green-700 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors"
      >
        Create account
      </button>

      <p class="text-center text-sm text-gray-500 mt-5">
        Have account?
        <router-link to="/login" class="text-green-700 font-medium hover:underline">Login</router-link>
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
const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')

async function register() {
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value, password_confirmation.value)
    router.push('/')
  } catch (e) {
    error.value = 'Registration failed. Please try again.'
  }
}
</script>