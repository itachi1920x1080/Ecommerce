<template>
  <aside class="flex flex-col w-64 bg-surface dark:bg-surface-dark border-r border-zinc-200/50 dark:border-zinc-800/50 h-screen sticky top-0 transition-colors">
    <!-- Brand -->
    <div class="px-6 py-8 border-b border-zinc-100 dark:border-zinc-800/50">
      <router-link to="/admin" class="flex items-center gap-3 group">
        <div class="w-10 h-10 rounded-xl bg-primary-500 text-white flex items-center justify-center shadow-md shadow-primary-500/20 group-hover:scale-105 transition-transform">
          <ShoppingBagIcon class="w-5 h-5" />
        </div>
        <div>
          <span class="text-lg font-display font-bold text-zinc-900 dark:text-zinc-50 tracking-wide">RUPP</span>
          <span class="text-lg font-display font-medium text-primary-500 tracking-wide ml-1">Admin</span>
        </div>
      </router-link>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto no-scrollbar">
      <router-link v-for="item in navItems" :key="item.to" :to="item.to"
        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all group"
        :class="[
          $route.path === item.to || ($route.path.startsWith(item.to) && item.to !== '/admin')
            ? 'bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400'
            : 'text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-50'
        ]">
        <component :is="item.icon" class="w-5 h-5 transition-transform group-hover:scale-110" 
          :class="[
            $route.path === item.to || ($route.path.startsWith(item.to) && item.to !== '/admin')
              ? 'text-primary-600 dark:text-primary-400'
              : 'text-zinc-400 group-hover:text-zinc-500'
          ]" />
        {{ item.label }}
      </router-link>
    </nav>

    <!-- User & Logout -->
    <div class="p-4 border-t border-zinc-100 dark:border-zinc-800/50">
      <button @click="handleLogout"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 transition-colors group">
        <LogOutIcon class="w-5 h-5 text-red-400 group-hover:text-red-500 transition-transform group-hover:-translate-x-1" />
        Sign Out
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { 
  LayoutDashboardIcon, 
  PackageIcon, 
  TagsIcon, 
  TicketIcon, 
  ShoppingCartIcon, 
  UsersIcon, 
  ShoppingBagIcon, 
  LogOutIcon 
} from '@lucide/vue'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const navItems = [
  { to: '/admin',           label: 'Dashboard',  icon: LayoutDashboardIcon },
  { to: '/admin/products',  label: 'Products',   icon: PackageIcon },
  { to: '/admin/categories',label: 'Categories', icon: TagsIcon },
  { to: '/admin/coupons',   label: 'Coupons',    icon: TicketIcon },
  { to: '/admin/orders',    label: 'Orders',     icon: ShoppingCartIcon },
  { to: '/admin/users',     label: 'Users',      icon: UsersIcon },
]

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
