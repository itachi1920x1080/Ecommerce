<template>
  <aside class="w-64 shrink-0 bg-white border-r border-slate-200/60 flex flex-col fixed top-0 left-0 h-full z-30">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-sm">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      <span class="text-base font-bold text-slate-800 tracking-tight">RUPP <span class="text-blue-600">Admin</span></span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 px-3 mb-2">Overview</p>

      <router-link
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="sidebar-link"
        active-class="sidebar-link-active"
        :id="`sidebar-${item.label.toLowerCase()}`"
      >
        <span class="w-5 h-5" v-html="item.icon"></span>
        <span class="flex-1">{{ item.label }}</span>
        <span v-if="item.badge" class="text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold tabular-nums">
          {{ item.badge }}
        </span>
      </router-link>
    </nav>

    <!-- User / Back to Store -->
    <div class="px-4 py-4 border-t border-slate-100 space-y-2">
      <router-link to="/" class="sidebar-link text-slate-500 hover:text-blue-600" id="sidebar-back-store">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
        </svg>
        <span>Back to Store</span>
      </router-link>
      <button @click="handleLogout" class="sidebar-link text-red-500 hover:bg-red-50 w-full" id="sidebar-logout">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
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

const router = useRouter()
const auth   = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}

// SVG icons for sidebar items
const iconDashboard = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`
const iconProducts  = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>`
const iconCategories= `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h7"/></svg>`
const iconCoupons   = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14V6a2 2 0 00-2-2H3a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2zM15 10l-4 4-4-4"/></svg>`
const iconOrders    = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>`
const iconUsers     = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>`

const navItems = [
  { to: '/admin/dashboard',  label: 'Dashboard',  icon: iconDashboard },
  { to: '/admin/products',   label: 'Products',   icon: iconProducts },
  { to: '/admin/categories', label: 'Categories', icon: iconCategories },
  { to: '/admin/coupons',    label: 'Coupons',    icon: iconCoupons },
  { to: '/admin/orders',     label: 'Orders',     icon: iconOrders },
  { to: '/admin/users',      label: 'Users',      icon: iconUsers },
]
</script>

<style scoped>
.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.625rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #64748b;
  border-radius: 0.75rem;
  transition: all 0.15s ease;
}
.sidebar-link:hover {
  background: #f1f5f9;
  color: #1e293b;
}
.sidebar-link-active {
  background: #eff6ff;
  color: #2563eb;
}
</style>
