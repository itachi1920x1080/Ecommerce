<template>
  <div class="min-h-screen flex items-center justify-center bg-zinc-50 dark:bg-zinc-950">
    <div class="text-center">
      <Loader2Icon class="w-12 h-12 animate-spin text-primary-500 mx-auto mb-4" />
      <h2 class="text-xl font-medium text-zinc-900 dark:text-white">Authenticating...</h2>
      <p class="text-zinc-500 dark:text-zinc-400 mt-2">Please wait while we log you in.</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { Loader2 as Loader2Icon } from '@lucide/vue'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

onMounted(async () => {
  const token = route.query.token
  
  if (token) {
    try {
      await auth.loginWithToken(token)
      router.push('/')
    } catch (e) {
      console.error('Failed to login with token:', e)
      router.push('/login?error=auth_failed')
    }
  } else {
    router.push('/login')
  }
})
</script>
