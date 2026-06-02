<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header for non-admin routes -->
    <Header v-if="!isAdminRoute" />

    <!-- Toast Notifications -->
    <div class="toast-container">
      <TransitionGroup name="fade">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="['toast', `toast-${toast.type}`, toast.exiting ? 'toast-exit' : '']"
        >
          <!-- Icon -->
          <svg v-if="toast.type === 'success'" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <svg v-else-if="toast.type === 'error'" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <svg v-else class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span class="flex-1">{{ toast.message }}</span>
        </div>
      </TransitionGroup>
    </div>

    <!-- Cart Drawer -->
    <CartDrawer v-if="!isAdminRoute" :open="cartOpen" @close="cartOpen = false" />

    <!-- Main Content with admin sidebar layout or regular layout -->
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

    <div v-else :class="auth.isLoggedIn ? '' : ''">
      <router-view v-slot="{ Component }">
        <Transition name="page" mode="out-in">
          <component :is="Component" />
        </Transition>
      </router-view>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, provide } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Header from '@/components/Header.vue'
import Sidebar from '@/components/Sidebar.vue'
import CartDrawer from '@/components/CartDrawer.vue'

const auth  = useAuthStore()
const route = useRoute()

// Detect admin routes to swap layout
const isAdminRoute = computed(() => route.path.startsWith('/admin'))

// Cart drawer state
const cartOpen = ref(false)
provide('toggleCart', () => { cartOpen.value = !cartOpen.value })

// ── Toast System ──
const toasts = ref([])
let toastId = 0

function showToast(message, type = 'info', duration = 3500) {
  const id = ++toastId
  toasts.value.push({ id, message, type, exiting: false })
  setTimeout(() => {
    const t = toasts.value.find(t => t.id === id)
    if (t) t.exiting = true
    setTimeout(() => {
      toasts.value = toasts.value.filter(t => t.id !== id)
    }, 300)
  }, duration)
}

// Provide toast function to all children
provide('toast', showToast)
</script>