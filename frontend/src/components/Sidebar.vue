<template>
  <aside class="w-64 shrink-0 bg-white dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800 flex flex-col fixed top-0 left-0 h-full z-30 transition-colors">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-6 border-b border-zinc-200 dark:border-zinc-800">
      <div class="w-9 h-9 rounded-xl bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center shadow-sm">
        <PackageIcon class="w-5 h-5 text-white dark:text-zinc-900" />
      </div>
      <span class="text-lg font-display font-bold text-zinc-900 dark:text-white tracking-tight">
        Tea Tik Kok <span class="font-normal text-zinc-500">Admin</span>
      </span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
      <p class="text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 px-3 mb-3">Overview</p>

      <router-link
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="sidebar-link"
        active-class="sidebar-link-active"
        :id="`sidebar-${item.label.toLowerCase()}`"
      >
        <component :is="item.icon" class="w-5 h-5 stroke-[1.5]" />
        <span class="flex-1">{{ item.label }}</span>
        <span v-if="item.badge" class="text-[10px] px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-medium tabular-nums border border-zinc-200 dark:border-zinc-700">
          {{ item.badge }}
        </span>
      </router-link>
    </nav>

    <!-- User / Back to Store -->
    <div class="px-4 py-6 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
      <router-link to="/" class="sidebar-link text-zinc-500 hover:text-zinc-900 dark:hover:text-white" id="sidebar-back-store">
        <StoreIcon class="w-5 h-5 stroke-[1.5]" />
        <span>Back to Store</span>
      </router-link>
      <button @click="handleLogout" class="sidebar-link text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 w-full" id="sidebar-logout">
        <LogOutIcon class="w-5 h-5 stroke-[1.5]" />
        <span>Sign out</span>
      </button>
    </div>
  </aside>

  <!-- Spacer for fixed sidebar -->
  <div class="w-64 shrink-0"></div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  LayoutDashboard,
  Package as PackageIcon,
  Tags,
  Ticket,
  ShoppingBag,
  Users,
  Store as StoreIcon,
  LogOut as LogOutIcon
} from '@lucide/vue'

const router = useRouter()
const auth   = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}

const navItems = [
  { to: '/admin/dashboard',  label: 'Dashboard',  icon: LayoutDashboard },
  { to: '/admin/products',   label: 'Products',   icon: PackageIcon },
  { to: '/admin/categories', label: 'Categories', icon: Tags },
  { to: '/admin/coupons',    label: 'Coupons',    icon: Ticket },
  { to: '/admin/orders',     label: 'Orders',     icon: ShoppingBag },
  { to: '/admin/users',      label: 'Users',      icon: Users },
]
</script>

<style scoped>
.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #71717a; /* zinc-500 */
  border-radius: 0.75rem;
  transition: all 0.2s ease;
}

/* Dark mode default */
@media (prefers-color-scheme: dark) {
  .sidebar-link {
    color: #a1a1aa; /* zinc-400 */
  }
}
.dark .sidebar-link {
  color: #a1a1aa;
}

.sidebar-link:hover {
  background: #f4f4f5; /* zinc-100 */
  color: #18181b; /* zinc-900 */
}

@media (prefers-color-scheme: dark) {
  .sidebar-link:hover {
    background: #27272a; /* zinc-800 */
    color: #ffffff;
  }
}
.dark .sidebar-link:hover {
  background: #27272a;
  color: #ffffff;
}

.sidebar-link-active {
  background: #18181b; /* zinc-900 */
  color: #ffffff !important;
}

@media (prefers-color-scheme: dark) {
  .sidebar-link-active {
    background: #ffffff;
    color: #18181b !important;
  }
}
.dark .sidebar-link-active {
  background: #ffffff;
  color: #18181b !important;
}
</style>
