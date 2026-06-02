<template>
  <div>
    <!-- Hide navbar on admin route -->
    <nav v-if="!isAdminRoute" class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
        <router-link to="/" class="text-lg font-semibold text-green-700">🌿 SkincareShop</router-link>
        <div class="flex items-center gap-6 text-sm font-medium text-gray-600">
          <router-link to="/" class="hover:text-green-700 transition-colors">Home</router-link>
          <router-link to="/products" class="hover:text-green-700 transition-colors">Products</router-link>
          <router-link to="/cart" class="hover:text-green-700 transition-colors">Cart</router-link>
          <template v-if="auth.isLoggedIn">
            <router-link v-if="auth.isAdmin" to="/admin" class="hover:text-violet-600 transition-colors">Admin</router-link>
            <a href="#" @click.prevent="logout" class="hover:text-red-500 transition-colors">Logout</a>
          </template>
          <template v-else>
            <router-link to="/login" class="hover:text-green-700 transition-colors">Login</router-link>
            <router-link to="/register" class="px-4 py-1.5 bg-green-700 text-white rounded-full hover:bg-green-600 transition-colors">Register</router-link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Add top padding only for non-admin pages -->
    <div :class="!isAdminRoute ? 'pt-14' : ''">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from './stores/auth'
import { useRouter, useRoute } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

function logout() {
  auth.logout()
  router.push('/login')
}
</script>