<template>
  <div class="p-6 space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Customers</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ filtered.length }} registered customers</p>
      </div>
    </div>

    <!-- Search -->
    <div class="relative max-w-sm">
      <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input v-model="search" type="text" placeholder="Search customers..."
        class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm bg-white dark:bg-gray-900
               border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
               placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
    </div>

    <!-- Empty / Loading -->
    <div v-if="customers.length === 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="h-36 rounded-2xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
    </div>

    <!-- Cards Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
      <TransitionGroup name="list">
        <div v-for="customer in paginated" :key="customer.id"
          class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800
                 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

          <div class="flex items-start gap-3">
            <!-- Avatar with initials -->
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white text-sm font-bold shrink-0"
                 :style="{ background: avatarColor(customer.id) }">
              {{ initials(customer.name) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm truncate">{{ customer.name }}</p>
              <p class="text-xs text-gray-400 truncate">{{ customer.email }}</p>
            </div>
            <!-- Role badge -->
            <span :class="customer.role === 'admin'
              ? 'bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300'
              : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300'"
              class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold capitalize shrink-0">
              {{ customer.role || 'user' }}
            </span>
          </div>

          <div class="mt-3.5 grid grid-cols-3 gap-2 text-center">
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 py-2">
              <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ customer.orders_count ?? 0 }}</p>
              <p class="text-[10px] text-gray-400">Orders</p>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 py-2">
              <p class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate px-1">
                {{ customer.email_verified_at ? '✓' : '—' }}
              </p>
              <p class="text-[10px] text-gray-400">Verified</p>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 py-2">
              <p class="text-sm font-bold text-gray-800 dark:text-gray-100">
                {{ customer.created_at ? new Date(customer.created_at).getFullYear() : '—' }}
              </p>
              <p class="text-[10px] text-gray-400">Since</p>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>

    <!-- No results -->
    <div v-if="customers.length > 0 && filtered.length === 0"
      class="py-12 text-center text-sm text-gray-400">
      No customers match your search.
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex justify-center gap-1.5">
      <button v-for="p in totalPages" :key="p" @click="page = p"
        :class="p === page
          ? 'bg-emerald-600 text-white'
          : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'"
        class="w-8 h-8 rounded-lg text-xs font-medium transition-all">
        {{ p }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// customers is built in AdminView from orders data
// shape: { id, name, email, role, email_verified_at, created_at, orders_count }
const props = defineProps({
  customers: { type: Array, default: () => [] },
})

const search  = ref('')
const page    = ref(1)
const perPage = 9

const filtered = computed(() => {
  page.value = 1
  const q = search.value.toLowerCase()
  if (!q) return props.customers
  return props.customers.filter(c =>
    (c.name  || '').toLowerCase().includes(q) ||
    (c.email || '').toLowerCase().includes(q)
  )
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((page.value - 1) * perPage, page.value * perPage))

function initials(name) {
  return (name || 'U').split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
}

const colors = [
  '#6366f1','#8b5cf6','#0ea5e9','#10b981',
  '#f59e0b','#ef4444','#14b8a6','#f97316','#a855f7','#22c55e',
]
function avatarColor(id) {
  return colors[id % colors.length]
}
</script>

<style scoped>
.list-enter-active, .list-leave-active { transition: all 0.25s ease; }
.list-enter-from { opacity: 0; transform: translateY(10px); }
.list-leave-to   { opacity: 0; transform: translateY(-10px); }
</style>