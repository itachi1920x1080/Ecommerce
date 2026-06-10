<template>
  <div class="min-h-screen bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 transition-colors duration-300">

    <!-- Navbar -->
    <Navbar v-if="!isAdminRoute" />

    <!-- Toast -->
    <ToastProvider />

    <!-- Cart Drawer -->
    <CartDrawer v-if="!isAdminRoute" :open="cartOpen" @close="cartOpen = false" />

    <!-- Admin Layout -->
    <div v-if="isAdminRoute" class="admin-layout">
      <Sidebar />
      <main class="flex-1 min-w-0 bg-white dark:bg-zinc-950 transition-colors duration-300">
        <router-view v-slot="{ Component }">
          <Transition name="page" mode="out-in">
            <component :is="Component" />
          </Transition>
        </router-view>
      </main>
    </div>

    <!-- Shop Layout -->
    <div v-else class="flex flex-col min-h-[calc(100vh-4rem)] bg-white dark:bg-zinc-950 transition-colors duration-300">
      <main class="flex-1">
        <router-view v-slot="{ Component }">
          <Transition name="page" mode="out-in">
            <component :is="Component" />
          </Transition>
        </router-view>
      </main>
    </div>

    <!-- Chatbot Widget -->
    <Chat />
  </div>
</template>

<script setup>
import { computed, ref, provide } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import Navbar from '@/components/shop/Navbar.vue'
import Sidebar from '@/components/Sidebar.vue'
import CartDrawer from '@/components/ui/CartDrawer.vue'
import ToastProvider from '@/components/ui/ToastProvider.vue'
import Chat from '@/components/Chat.vue'

const auth  = useAuthStore()
const route = useRoute()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

const cartOpen = ref(false)
provide('toggleCart', () => { cartOpen.value = !cartOpen.value })

const { addToast } = useToast()
provide('toast', (message, type = 'info', duration = 3500) => {
  addToast(message, type, duration)
})
</script>