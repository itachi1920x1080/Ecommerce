<template>
  <div class="flex min-h-screen font-sans" :class="isDark ? 'dark' : ''">
  <div class="flex min-h-screen w-full bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="w-60 shrink-0 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 fixed top-0 left-0 h-full z-20">

      <!-- Logo -->
      <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <div class="w-8 h-8 rounded-xl bg-emerald-500 flex items-center justify-center shadow-sm">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <span class="text-base font-semibold text-gray-900 dark:text-white tracking-tight">Glow Admin</span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-600 px-3 mb-2">Main</p>

        <button v-for="item in navMain" :key="item.key" @click="activePage = item.key"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
            activePage === item.key
              ? 'bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-950/60 dark:to-green-950/60 text-emerald-700 dark:text-emerald-300'
              : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-white'
          ]">
          <span class="shrink-0 w-4 h-4" v-html="item.svg"></span>
          <span class="flex-1 text-left">{{ item.label }}</span>
          <span v-if="item.count !== undefined"
            class="text-[11px] bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 px-1.5 py-0.5 rounded-full font-semibold tabular-nums">
            {{ item.count }}
          </span>
        </button>

        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-600 px-3 mt-5 mb-2">Manage</p>

        <button v-for="item in navManage" :key="item.key" @click="activePage = item.key"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
            activePage === item.key
              ? 'bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-950/60 dark:to-green-950/60 text-emerald-700 dark:text-emerald-300'
              : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-white'
          ]">
          <span class="shrink-0 w-4 h-4" v-html="item.svg"></span>
          <span>{{ item.label }}</span>
        </button>
      </nav>

      <!-- User / Logout -->
      <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
          {{ userInitials }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ currentUser?.name || 'Admin' }}</p>
          <p class="text-xs text-gray-400 truncate">Admin</p>
        </div>
        <button @click="handleLogout" title="Logout"
          class="p-1.5 rounded-lg text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </button>
      </div>
    </aside>

    <!-- ═══ MAIN ═══ -->
    <main class="flex-1 flex flex-col min-w-0 ml-60">

      <!-- Topbar -->
      <header class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div>
          <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ pageTitle }}</h1>
          <p class="text-xs text-gray-400 mt-0.5">{{ todayStr }}</p>
        </div>
        <div class="flex items-center gap-2">
          <!-- API status -->
          <span :class="[
            'flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-xl border',
            apiOk
              ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400'
              : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-600 dark:text-red-400'
          ]">
            <span :class="['w-1.5 h-1.5 rounded-full', apiOk ? 'bg-emerald-500' : 'bg-red-500']"></span>
            {{ apiOk ? 'Connected' : 'API Error' }}
          </span>

          <!-- Dark mode -->
          <button @click="isDark = !isDark"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 transition-all hover:scale-105 active:scale-95 select-none">
            <svg v-if="!isDark" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            {{ isDark ? 'Light' : 'Dark' }}
          </button>

          <!-- Refresh -->
          <button @click="loadAll" :disabled="loading"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 transition-all hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg :class="loading ? 'animate-spin' : ''" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Refresh
          </button>

          <!-- Export -->
          <button @click="exportCSV"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 transition-all hover:scale-105 active:scale-95">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export
          </button>

          <!-- Add Product -->
          <button @click="openAddProduct"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium shadow-sm transition-all hover:scale-105 active:scale-95 hover:shadow-md">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add product
          </button>
        </div>
      </header>

      <!-- Error banner -->
      <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0 -translate-y-2" leave-active-class="transition-all duration-200" leave-to-class="opacity-0 -translate-y-2">
        <div v-if="errorMsg" class="mx-6 mt-4 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm px-4 py-3 rounded-xl">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span class="flex-1">{{ errorMsg }}</span>
          <button @click="errorMsg = ''" class="opacity-60 hover:opacity-100 transition-opacity">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </Transition>

      <!-- ════ PAGE: DASHBOARD ════ -->
      <div v-if="activePage === 'dashboard'" class="p-6 space-y-5">

        <!-- Skeleton -->
        <template v-if="loading && !analytics">
          <div class="grid grid-cols-4 gap-4">
            <div v-for="n in 4" :key="n" class="h-28 bg-gray-100 dark:bg-gray-800 rounded-2xl animate-pulse"></div>
          </div>
          <div class="grid grid-cols-5 gap-4">
            <div class="col-span-3 h-56 bg-gray-100 dark:bg-gray-800 rounded-2xl animate-pulse"></div>
            <div class="col-span-2 h-56 bg-gray-100 dark:bg-gray-800 rounded-2xl animate-pulse"></div>
          </div>
        </template>

        <template v-else>
          <!-- Stat cards -->
          <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <div v-for="(stat, i) in stats" :key="stat.label"
              class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800 hover:-translate-y-0.5 transition-all duration-200 cursor-default group">
              <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ stat.label }}</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-2 mb-1.5 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors tabular-nums">
                {{ stat.prefix }}{{ stat.value }}{{ stat.suffix }}
              </p>
              <p :class="['text-xs font-medium flex items-center gap-1', stat.up ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400']">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" :stroke="stat.up ? '#10b981' : '#ef4444'" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline v-if="stat.up" points="18 15 12 9 6 15"/>
                  <polyline v-else points="6 9 12 15 18 9"/>
                </svg>
                {{ stat.change }}
              </p>
            </div>
          </div>

          <!-- Chart + Orders -->
          <div class="grid grid-cols-5 gap-4">
            <!-- Bar chart -->
            <div class="col-span-3 bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 hover:shadow-md transition-all duration-200">
              <div class="flex items-center justify-between mb-5">
                <div>
                  <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Sales this week</h2>
                  <p class="text-xs text-gray-400 mt-0.5">Last 7 days · /api/dashboard/analytics</p>
                </div>
                <span class="text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full font-medium">
                  ${{ weekTotal.toFixed(2) }} total
                </span>
              </div>
              <div class="flex items-end gap-2 h-36">
                <div v-for="day in chartDays" :key="day.label" class="flex-1 flex flex-col items-center gap-1.5 group cursor-default">
                  <span class="text-[10px] font-semibold text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity">${{ Number(day.val).toFixed(0) }}</span>
                  <div class="w-full rounded-t-lg transition-all duration-500 group-hover:brightness-110"
                    :style="{ height: maxVal > 0 ? (day.val / maxVal * 120) + 'px' : '3px' }"
                    :class="day.active ? 'bg-emerald-500' : isDark ? 'bg-emerald-900' : 'bg-emerald-100'">
                  </div>
                  <span class="text-[10px] text-gray-400">{{ day.label }}</span>
                </div>
              </div>
            </div>

            <!-- Recent orders -->
            <div class="col-span-2 bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 hover:shadow-md transition-all duration-200">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Recent orders</h2>
                <span class="text-xs text-gray-400 tabular-nums">{{ orders.length }} total</span>
              </div>
              <div v-if="orders.length === 0" class="flex flex-col items-center py-8 text-gray-300 dark:text-gray-700 gap-2">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <p class="text-xs">No orders yet</p>
              </div>
              <div v-else class="space-y-1 max-h-64 overflow-y-auto pr-1">
                <div v-for="order in orders.slice(0, 8)" :key="order.id"
                  class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all cursor-pointer">
                  <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">#{{ order.id }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ order.user?.name || '—' }}</p>
                  </div>
                  <div class="flex flex-col items-end gap-1">
                    <div class="flex items-center gap-1">
                      <span :class="statusBadge(order.status)">{{ order.status }}</span>
                      <button v-if="order.status === 'pending'" @click="markPaid(order)"
                        title="Mark as paid"
                        class="p-0.5 text-gray-300 hover:text-emerald-500 transition-colors rounded">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                      </button>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white tabular-nums">${{ Number(order.total_price || 0).toFixed(2) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Top products -->
          <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Top products</h2>
              <button @click="activePage = 'products'" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">View all</button>
            </div>
            <div v-if="products.length === 0" class="flex flex-col items-center py-10 text-gray-300 dark:text-gray-700 gap-2">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
              <p class="text-xs">No products yet</p>
            </div>
            <div v-else class="divide-y divide-gray-50 dark:divide-gray-800/60">
              <div v-for="p in products.slice(0, 5)" :key="p.id"
                class="flex items-center px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all group cursor-pointer">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mr-4 shrink-0 group-hover:scale-110 transition-transform overflow-hidden">
                  <img v-if="p.image" :src="p.image" :alt="p.name" class="w-full h-full object-cover rounded-xl" />
                  <svg v-else width="17" height="17" viewBox="0 0 24 24" fill="none" :stroke="isDark ? '#6ee7b7' : '#10b981'" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ p.name }}</p>
                  <p class="text-xs text-gray-400 truncate">{{ p.category?.name || '—' }}</p>
                </div>
                <span :class="[
                  'text-[11px] font-semibold px-2.5 py-1 rounded-full mr-6 shrink-0',
                  Number(p.stock || 0) <= 5
                    ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                    : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                ]">
                  {{ Number(p.stock || 0) <= 5 ? 'Low stock' : 'In stock' }} · {{ p.stock || 0 }}
                </span>
                <p class="text-sm font-semibold text-gray-900 dark:text-white w-16 text-right tabular-nums shrink-0">${{ Number(p.price || 0).toFixed(2) }}</p>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- ════ PAGE: PRODUCTS ════ -->
      <div v-else-if="activePage === 'products'" class="p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All products ({{ products.length }})</h2>
          <div class="flex items-center gap-2">
            <input v-model="productSearch" placeholder="Search…"
              class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-300 focus:outline-none focus:border-emerald-400 dark:focus:border-emerald-500 transition-colors w-44" />
            <button @click="openAddProduct"
              class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium shadow-sm transition-all hover:scale-105 active:scale-95">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add product
            </button>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Product</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Category</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Price</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Stock</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Status</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5 w-20">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
              <tr v-if="filteredProducts.length === 0">
                <td colspan="6" class="text-center py-12 text-gray-400 text-sm">No products found</td>
              </tr>
              <tr v-for="p in filteredProducts" :key="p.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0 overflow-hidden">
                      <img v-if="p.image" :src="p.image" class="w-full h-full object-cover rounded-lg" />
                      <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white">{{ p.name }}</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 text-sm">{{ p.category?.name || '—' }}</td>
                <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white tabular-nums">${{ Number(p.price || 0).toFixed(2) }}</td>
                <td class="px-5 py-3.5 tabular-nums text-gray-700 dark:text-gray-300">{{ p.stock || 0 }}</td>
                <td class="px-5 py-3.5">
                  <span :class="[
                    'text-[11px] font-semibold px-2.5 py-1 rounded-full',
                    Number(p.stock || 0) <= 5
                      ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                      : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                  ]">
                    {{ Number(p.stock || 0) <= 5 ? 'Low stock' : 'In stock' }}
                  </span>
                </td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-2">
                    <button @click="openEditProduct(p)"
                      class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button @click="deleteProduct(p)"
                      class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ════ PAGE: ORDERS ════ -->
      <div v-else-if="activePage === 'orders'" class="p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All orders ({{ orders.length }})</h2>
          <select v-model="orderFilter"
            class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:border-emerald-400 transition-colors">
            <option value="">All status</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Order</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Customer</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Items</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Total</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Status</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Date</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5 w-32">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
              <tr v-if="filteredOrders.length === 0">
                <td colspan="7" class="text-center py-12 text-gray-400 text-sm">No orders found</td>
              </tr>
              <tr v-for="order in filteredOrders" :key="order.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-white">#{{ order.id }}</td>
                <td class="px-5 py-3.5 text-gray-600 dark:text-gray-300">{{ order.user?.name || '—' }}</td>
                <td class="px-5 py-3.5 text-gray-500 tabular-nums">{{ order.order_items?.length || 0 }}</td>
                <td class="px-5 py-3.5 font-semibold text-gray-900 dark:text-white tabular-nums">${{ Number(order.total_price || 0).toFixed(2) }}</td>
                <td class="px-5 py-3.5"><span :class="statusBadge(order.status)">{{ order.status }}</span></td>
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ formatDate(order.created_at) }}</td>
                <td class="px-5 py-3.5">
                  <select v-if="order.status === 'pending'"
                    @change="updateOrderStatus(order, $event.target.value)"
                    class="px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-200 focus:outline-none focus:border-emerald-400 transition-colors">
                    <option value="pending">Pending</option>
                    <option value="paid">Mark paid</option>
                    <option value="cancelled">Cancel</option>
                  </select>
                  <span v-else class="text-xs text-gray-400">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ════ PAGE: CUSTOMERS ════ -->
      <div v-else-if="activePage === 'customers'" class="p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Customers ({{ customers.length }})</h2>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Name</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Email</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Orders</th>
                <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 px-5 py-3.5">Joined</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
              <tr v-if="customers.length === 0">
                <td colspan="4" class="text-center py-12 text-gray-400 text-sm">No customers yet</td>
              </tr>
              <tr v-for="c in customers" :key="c.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                      {{ (c.name || 'U').slice(0, 2).toUpperCase() }}
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white">{{ c.name }}</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">{{ c.email }}</td>
                <td class="px-5 py-3.5">
                  <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    {{ c.orders_count || 0 }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ formatDate(c.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ════ PAGE: CATEGORIES ════ -->
      <div v-else-if="activePage === 'categories'" class="p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Categories ({{ categories.length }})</h2>
          <div class="flex items-center gap-2">
            <input v-model="newCategoryName" placeholder="New category…"
              class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-300 focus:outline-none focus:border-emerald-400 transition-colors w-44"
              @keyup.enter="addCategory" />
            <button @click="addCategory"
              class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium shadow-sm transition-all hover:scale-105 active:scale-95">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add
            </button>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div v-if="categories.length === 0" class="flex flex-col items-center py-12 text-gray-300 dark:text-gray-700 gap-2">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <p class="text-xs">No categories yet</p>
          </div>
          <div v-else class="divide-y divide-gray-50 dark:divide-gray-800/60">
            <div v-for="cat in categories" :key="cat.id"
              class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white">{{ cat.name }}</span>
              </div>
              <button @click="deleteCategory(cat)"
                class="p-1.5 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ════ PAGE: ANALYTICS ════ -->
      <div v-else-if="activePage === 'analytics'" class="p-6 space-y-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Analytics</h2>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
          <div v-for="stat in stats" :key="stat.label"
            class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 hover:shadow-md hover:-translate-y-0.5 transition-all cursor-default group">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ stat.label }}</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-2 mb-1.5 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors tabular-nums">
              {{ stat.prefix }}{{ stat.value }}{{ stat.suffix }}
            </p>
            <p :class="['text-xs font-medium', stat.up ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500']">{{ stat.change }}</p>
          </div>
        </div>
        <!-- Big chart -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 hover:shadow-md transition-all">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5">Weekly revenue</h3>
          <div class="flex items-end gap-3 h-48">
            <div v-for="day in chartDays" :key="day.label" class="flex-1 flex flex-col items-center gap-2 group cursor-default">
              <span class="text-[10px] font-semibold text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity">${{ Number(day.val).toFixed(0) }}</span>
              <div class="w-full rounded-t-xl transition-all duration-500 group-hover:brightness-110"
                :style="{ height: maxVal > 0 ? (day.val / maxVal * 160) + 'px' : '3px' }"
                :class="day.active ? 'bg-emerald-500' : isDark ? 'bg-emerald-900' : 'bg-emerald-100'">
              </div>
              <span class="text-xs text-gray-400">{{ day.label }}</span>
            </div>
          </div>
        </div>
        <!-- Low stock alert -->
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-5 border border-amber-200 dark:border-amber-800">
          <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-3 flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Low stock alert
          </h3>
          <p v-if="lowStockProducts.length === 0" class="text-sm text-gray-400">All products are well stocked.</p>
          <div v-else class="space-y-2">
            <div v-for="p in lowStockProducts" :key="p.id" class="flex items-center justify-between text-sm">
              <span class="text-gray-800 dark:text-white">{{ p.name }}</span>
              <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">{{ p.stock }} left</span>
            </div>
          </div>
        </div>
      </div>

    </main>

    <!-- ═══ ADD/EDIT PRODUCT MODAL ═══ -->
    <Transition
      enter-active-class="transition-all duration-200"
      enter-from-class="opacity-0"
      leave-active-class="transition-all duration-200"
      leave-to-class="opacity-0">
      <div v-if="showProductModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showProductModal = false">
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 scale-95"
          leave-active-class="transition-all duration-200"
          leave-to-class="opacity-0 scale-95">
          <div v-if="showProductModal"
            class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
              <h2 class="font-semibold text-gray-900 dark:text-white">
                {{ editingProduct ? 'Edit product' : 'Add new product' }}
              </h2>
              <button @click="showProductModal = false"
                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
            <div class="px-6 py-5 space-y-4">
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Product name *</label>
                <input v-model="productForm.name" type="text" placeholder="e.g. Vitamin C Serum"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white placeholder-gray-300 focus:outline-none focus:border-emerald-400 dark:focus:border-emerald-500 transition-colors" />
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Price ($) *</label>
                  <input v-model="productForm.price" type="number" step="0.01" placeholder="0.00"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white placeholder-gray-300 focus:outline-none focus:border-emerald-400 dark:focus:border-emerald-500 transition-colors" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Stock *</label>
                  <input v-model="productForm.stock" type="number" placeholder="0"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white placeholder-gray-300 focus:outline-none focus:border-emerald-400 dark:focus:border-emerald-500 transition-colors" />
                </div>
              </div>
              <!-- Category from GET /api/categories -->
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Category</label>
                <select v-model="productForm.category_id"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white focus:outline-none focus:border-emerald-400 dark:focus:border-emerald-500 transition-colors">
                  <option value="">— Select category —</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Description</label>
                <textarea v-model="productForm.description" rows="2" placeholder="Short description…"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white placeholder-gray-300 focus:outline-none focus:border-emerald-400 dark:focus:border-emerald-500 transition-colors resize-none"></textarea>
              </div>
              <div v-if="formError" class="text-xs text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg">
                {{ formError }}
              </div>
            </div>
            <div class="px-6 pb-5 flex gap-2.5 justify-end">
              <button @click="showProductModal = false"
                class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all hover:scale-105 active:scale-95">
                Cancel
              </button>
              <button @click="submitProduct" :disabled="saving"
                class="flex items-center gap-1.5 px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 disabled:opacity-60 text-white text-sm font-medium shadow-sm transition-all hover:scale-105 active:scale-95">
                <svg v-if="saving" class="animate-spin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                {{ saving ? 'Saving…' : (editingProduct ? 'Update product' : 'Save product') }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>

  </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios.js'   // ← your existing axios instance with Bearer token

const router     = useRouter()
const isDark     = ref(false)
const activePage = ref('dashboard')

// ── Auth ──────────────────────────────────────────────────────────
const currentUser = ref(JSON.parse(localStorage.getItem('admin_user') || 'null'))

const handleLogout = async () => {
  try { await api.post('/logout') } catch (_) {}
  localStorage.removeItem('token')
  localStorage.removeItem('admin_user')
  router.push('/login')
}

// ── Data ──────────────────────────────────────────────────────────
const orders         = ref([])
const products       = ref([])
const categories     = ref([])
const customers      = ref([])
const analytics      = ref(null)
const loading        = ref(false)
const saving         = ref(false)
const errorMsg       = ref('')
const apiOk          = ref(false)
const showProductModal = ref(false)
const editingProduct   = ref(null)
const formError        = ref('')
const productSearch    = ref('')
const orderFilter      = ref('')
const newCategoryName  = ref('')
const productForm      = ref({ name: '', price: '', stock: '', description: '', category_id: '' })
let   pollTimer        = null

// ── SVG nav icons ─────────────────────────────────────────────────
const svgDashboard = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`
const svgProducts  = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>`
const svgOrders    = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>`
const svgCustomers = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`
const svgCats      = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`
const svgAnalytics = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`
const svgSettings  = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>`

const navMain = computed(() => [
  { key: 'dashboard', label: 'Dashboard', svg: svgDashboard },
  { key: 'products',  label: 'Products',  svg: svgProducts,  count: products.value.length },
  { key: 'orders',    label: 'Orders',    svg: svgOrders,    count: orders.value.filter(o => o.status === 'pending').length },
  { key: 'customers', label: 'Customers', svg: svgCustomers, count: customers.value.length },
])
const navManage = [
  { key: 'categories', label: 'Categories', svg: svgCats },
  { key: 'analytics',  label: 'Analytics',  svg: svgAnalytics },
  { key: 'settings',   label: 'Settings',   svg: svgSettings },
]

// ── Computed ──────────────────────────────────────────────────────
const userInitials = computed(() => (currentUser.value?.name || 'AD').slice(0, 2).toUpperCase())

const pageTitle = computed(() => ({
  dashboard: 'Dashboard overview', products: 'Products', orders: 'Orders',
  customers: 'Customers', categories: 'Categories', analytics: 'Analytics', settings: 'Settings',
}[activePage.value] || 'Dashboard'))

const filteredProducts = computed(() =>
  products.value.filter(p => !productSearch.value || p.name.toLowerCase().includes(productSearch.value.toLowerCase()))
)
const filteredOrders = computed(() =>
  orders.value.filter(o => !orderFilter.value || o.status === orderFilter.value)
)
const lowStockProducts = computed(() => products.value.filter(p => Number(p.stock || 0) <= 5))

const stats = computed(() => {
  const a = analytics.value || {}
  return [
    { label: 'Total revenue',  prefix: '$', value: Number(a.total_revenue  ?? 0).toFixed(2), up: true, change: `${a.total_orders ?? orders.value.length} total orders` },
    { label: 'Total orders',   prefix: '',  value: a.total_orders   ?? orders.value.length,  up: true, change: `${orders.value.filter(o => o.status === 'pending').length} pending` },
    { label: 'Total products', prefix: '',  value: a.total_products ?? products.value.length, up: true, change: `${lowStockProducts.value.length} low stock` },
    { label: 'Customers',      prefix: '',  value: a.total_customers ?? customers.value.length, up: true, change: 'registered users' },
  ]
})

const dayLabels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']
const chartDays = computed(() => {
  const todayDow = new Date().getDay() === 0 ? 6 : new Date().getDay() - 1
  if (analytics.value?.weekly_sales) {
    return dayLabels.map((l, i) => ({ label: l, val: Number(analytics.value.weekly_sales[l] || 0), active: i === todayDow }))
  }
  const map = {}
  orders.value.forEach(o => {
    const dow = new Date(o.created_at || Date.now()).getDay()
    const l = dayLabels[dow === 0 ? 6 : dow - 1]
    map[l] = (map[l] || 0) + Number(o.total_price || 0)
  })
  return dayLabels.map((l, i) => ({ label: l, val: map[l] || 0, active: i === todayDow }))
})
const maxVal    = computed(() => Math.max(...chartDays.value.map(d => d.val), 1))
const weekTotal = computed(() => chartDays.value.reduce((s, d) => s + d.val, 0))
const todayStr  = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })

// ── API calls  — your exact routes from api.php ───────────────────
async function loadAll() {
  loading.value = true
  errorMsg.value = ''
  try {
    const [oRes, pRes, aRes, cRes] = await Promise.all([
      api.get('/orders'),                // GET /api/orders          admin:sanctum
      api.get('/products'),              // GET /api/products         public
      api.get('/dashboard/analytics'),   // GET /api/dashboard/analytics admin:sanctum
      api.get('/categories'),            // GET /api/categories       public
    ])
    orders.value     = oRes.data?.data ?? oRes.data ?? []
    products.value   = pRes.data?.data ?? pRes.data ?? []
    analytics.value  = aRes.data
    categories.value = cRes.data?.data ?? cRes.data ?? []

    // Build customers list from orders (unique users)
    const seen = new Set()
    customers.value = orders.value
      .filter(o => o.user && !seen.has(o.user.id) && seen.add(o.user.id))
      .map(o => ({ ...o.user, orders_count: orders.value.filter(x => x.user?.id === o.user.id).length }))

    apiOk.value = true
  } catch (e) {
    apiOk.value = false
    errorMsg.value = e.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

// Product CRUD
function openAddProduct() {
  editingProduct.value = null
  productForm.value = { name: '', price: '', stock: '', description: '', category_id: '' }
  formError.value = ''
  showProductModal.value = true
}
function openEditProduct(p) {
  editingProduct.value = p
  productForm.value = { name: p.name, price: p.price, stock: p.stock, description: p.description || '', category_id: p.category_id || '' }
  formError.value = ''
  showProductModal.value = true
}
async function submitProduct() {
  formError.value = ''
  if (!productForm.value.name.trim()) { formError.value = 'Name is required.'; return }
  if (!productForm.value.price)       { formError.value = 'Price is required.'; return }
  if (!productForm.value.stock)       { formError.value = 'Stock is required.'; return }
  saving.value = true
  try {
    const payload = {
      name: productForm.value.name.trim(), price: parseFloat(productForm.value.price),
      stock: parseInt(productForm.value.stock), description: productForm.value.description?.trim(),
      category_id: productForm.value.category_id || null,
    }
    if (editingProduct.value) {
      await api.put(`/products/${editingProduct.value.id}`, payload) // PUT /api/products/{id}
    } else {
      await api.post('/products', payload)                            // POST /api/products
    }
    showProductModal.value = false
    await loadAll()
  } catch (e) {
    formError.value = e.response?.data?.message || e.message
  } finally {
    saving.value = false
  }
}
async function deleteProduct(p) {
  if (!confirm(`Delete "${p.name}"?`)) return
  try {
    await api.delete(`/products/${p.id}`)                            // DELETE /api/products/{id}
    products.value = products.value.filter(x => x.id !== p.id)
  } catch (e) { errorMsg.value = e.response?.data?.message || e.message }
}

// Orders
async function markPaid(order) {
  try {
    await api.put(`/orders/${order.id}/status`, { status: 'paid' })  // PUT /api/orders/{id}/status
    await loadAll()
  } catch (e) { errorMsg.value = e.message }
}
async function updateOrderStatus(order, status) {
  try {
    await api.put(`/orders/${order.id}/status`, { status })
    await loadAll()
  } catch (e) { errorMsg.value = e.message }
}

// Categories
async function addCategory() {
  if (!newCategoryName.value.trim()) return
  try {
    await api.post('/categories', { name: newCategoryName.value.trim() })  // POST /api/categories
    newCategoryName.value = ''
    await loadAll()
  } catch (e) { errorMsg.value = e.message }
}
async function deleteCategory(cat) {
  if (!confirm(`Delete "${cat.name}"?`)) return
  try {
    await api.delete(`/categories/${cat.id}`)                              // DELETE /api/categories/{id}
    categories.value = categories.value.filter(c => c.id !== cat.id)
  } catch (e) { errorMsg.value = e.message }
}

// Export CSV
function exportCSV() {
  const rows = [['ID','Customer','Total','Status','Date']]
  orders.value.forEach(o => rows.push([o.id, o.user?.name || '', Number(o.total_price || 0).toFixed(2), o.status, o.created_at || '']))
  const a = document.createElement('a')
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(rows.map(r => r.join(',')).join('\n'))
  a.download = `orders-${new Date().toISOString().split('T')[0]}.csv`
  a.click()
}

// Helpers
function statusBadge(status) {
  const base = 'text-[10px] font-semibold px-2 py-0.5 rounded-full '
  return { paid: base + 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
           pending: base + 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
           cancelled: base + 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400',
         }[status] || base + 'bg-gray-100 text-gray-500'
}
function formatDate(str) {
  if (!str) return '—'
  return new Date(str).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(() => {
  loadAll()
  pollTimer = setInterval(() => { if (apiOk.value) loadAll() }, 15000)
})
onUnmounted(() => { if (pollTimer) clearInterval(pollTimer) })
</script>