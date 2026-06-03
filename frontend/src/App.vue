<template>
  <div class="min-h-screen bg-white dark:bg-zinc-950">
    <!-- Shop Layout: Navbar -->
    <Navbar v-if="!isAdminRoute" />

    <!-- Toast Notifications -->
    <ToastProvider />

    <!-- Cart Drawer -->
    <CartDrawer v-if="!isAdminRoute" :open="cartOpen" @close="cartOpen = false" />

    <!-- Admin Layout -->
    <div v-if="isAdminRoute" class="admin-layout">
      <Sidebar />
      <main class="flex-1 min-w-0">
        <router-view v-slot="{ Component }">
          <Transition name="page" mode="out-in">
            <component :is="Component" />
          </Transition>
        </router-view>
      </main>
    </div>

    <!-- Shop Layout -->
    <div v-else class="flex flex-col min-h-[calc(100vh-4rem)]">
      <main class="flex-1">
        <router-view v-slot="{ Component }">
          <Transition name="page" mode="out-in">
            <component :is="Component" />
          </Transition>
        </router-view>
      </main>
    </div>
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

const auth  = useAuthStore()
const route = useRoute()

// Detect admin routes to swap layout
const isAdminRoute = computed(() => route.path.startsWith('/admin'))

// Cart drawer state
const cartOpen = ref(false)
provide('toggleCart', () => { cartOpen.value = !cartOpen.value })

// ── Global Toast Setup ──
const { addToast } = useToast()
provide('toast', (message, type = 'info', duration = 3500) => {
  addToast(message, type, duration)
})
</script>