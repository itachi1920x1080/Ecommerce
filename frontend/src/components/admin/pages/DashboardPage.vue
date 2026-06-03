<template>
  <div class="p-6 lg:p-8 space-y-8">

    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-[2rem] bg-zinc-900 dark:bg-black p-8 text-white shadow-xl">
      <div class="relative z-10">
        <p class="text-zinc-400 font-medium tracking-wide text-sm uppercase">Good {{ timeGreeting }},</p>
        <h1 class="text-3xl font-display font-medium mt-2">{{ adminName }} 👋</h1>
        <p class="text-zinc-400 mt-2 max-w-md">Here's an overview of your store's performance today.</p>
      </div>
      
      <!-- Abstract Glows -->
      <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-primary-500/20 blur-[80px]" />
      <div class="absolute right-10 -bottom-20 w-48 h-48 rounded-full bg-blue-500/20 blur-[60px]" />
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="grid grid-cols-2 xl:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="i" class="h-32 skeleton rounded-3xl" />
    </div>

    <!-- Stat Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
      <div v-for="(stat, i) in stats" :key="stat.label"
        class="rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-sm animate-fade-in group hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors"
        :style="{ animationDelay: i * 80 + 'ms' }">
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ stat.label }}</p>
          <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110" :class="stat.iconBg">
            <component :is="stat.icon" class="w-5 h-5" :class="stat.iconColor" />
          </div>
        </div>
        <p class="text-3xl font-semibold text-zinc-900 dark:text-zinc-50 tracking-tight">{{ stat.value }}</p>
        <p class="text-xs text-zinc-400 mt-2 font-medium">{{ stat.sub }}</p>
      </div>
    </div>

    <!-- Recent Orders + Top Products -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

      <!-- Recent Orders -->
      <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-base font-medium text-zinc-900 dark:text-zinc-50">Recent Orders</h3>
          <button @click="$emit('navigate', 'orders')"
            class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">
            View all
          </button>
        </div>

        <div v-if="loading" class="space-y-4">
          <div v-for="i in 4" :key="i" class="h-[68px] skeleton rounded-2xl" />
        </div>

        <div v-else-if="recentOrders.length === 0"
          class="py-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
          <p class="text-sm text-zinc-500 dark:text-zinc-400">No orders yet.</p>
        </div>

        <div v-else class="space-y-3">
          <div v-for="order in recentOrders" :key="order.id"
            class="flex items-center gap-4 p-3 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors group">
            <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-900 dark:text-zinc-50 text-xs font-semibold shrink-0">
              {{ (order.user?.name || 'U').slice(0,2).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50 truncate">
                {{ order.user?.name || 'Unknown' }}
              </p>
              <p class="text-xs text-zinc-500 truncate mt-0.5">
                {{ order.items?.[0]?.product?.name || 'Multiple items' }}
              </p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-50">
                ${{ Number(order.total_price || 0).toFixed(2) }}
              </p>
              <div class="mt-1">
                <span :class="statusClass(order.status)" class="text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider border">
                  {{ order.status }}
                </span>
              </div>
            </div>
            <button @click="$emit('markPaid', order)"
              v-if="order.status === 'pending'"
              class="opacity-0 group-hover:opacity-100 text-xs px-3 py-1.5 rounded-lg bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 hover:bg-primary-100 font-medium transition-all shrink-0">
              Mark Paid
            </button>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-base font-medium text-zinc-900 dark:text-zinc-50">Products Overview</h3>
          <button @click="$emit('navigate', 'products')"
            class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700">
            Manage
          </button>
        </div>

        <div v-if="loading" class="space-y-4">
          <div v-for="i in 5" :key="i" class="h-12 skeleton rounded-2xl" />
        </div>

        <div v-else-if="topProducts.length === 0"
          class="py-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
          <p class="text-sm text-zinc-500 dark:text-zinc-400">No products yet.</p>
        </div>

        <div v-else class="space-y-5">
          <div v-for="(product, i) in topProducts" :key="product.id" class="space-y-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-zinc-400 w-4">{{ i + 1 }}</span>
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 overflow-hidden shrink-0">
                  <img v-if="product.image" :src="product.image" class="w-full h-full object-cover" @error="product.image=''" :alt="product.name"/>
                </div>
                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-50 truncate max-w-[160px]">
                  {{ product.name }}
                </span>
              </div>
              <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-50">
                ${{ Number(product.price).toFixed(2) }}
              </span>
            </div>
            <div class="flex items-center gap-3">
              <div class="h-1.5 flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-zinc-900 dark:bg-zinc-100 transition-all duration-1000"
                     :style="{ width: stockPct(product) + '%' }"/>
              </div>
              <span class="text-xs font-medium text-zinc-500 w-16 text-right">{{ product.stock ?? 0 }} left</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { DollarSign as DollarSignIcon, ShoppingCart as ShoppingCartIcon, Package as PackageIcon, TrendingUp as TrendingUpIcon } from '@lucide/vue'

const props = defineProps({
  loading:   { type: Boolean, default: false },
  analytics: { type: Object,  default: null },
  orders:    { type: Array,   default: () => [] },
  products:  { type: Array,   default: () => [] },
  isDark:    { type: Boolean, default: false },
})

defineEmits(['navigate', 'markPaid'])

const hour = new Date().getHours()
const timeGreeting = hour < 12 ? 'morning' : hour < 17 ? 'afternoon' : 'evening'
const adminName = 'Admin'

const totalRevenue = computed(() =>
  props.orders.reduce((sum, o) => sum + Number(o.total_price || 0), 0)
)
const pendingCount = computed(() =>
  props.orders.filter(o => o.status === 'pending').length
)
const lowStockCount = computed(() =>
  props.products.filter(p => (p.stock ?? 0) <= 5).length
)

const stats = computed(() => [
  {
    label: 'Total Revenue',
    value: '$' + totalRevenue.value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
    sub: `${props.orders.length} orders total`,
    icon: DollarSignIcon,
    iconBg: 'bg-green-50 dark:bg-green-500/10',
    iconColor: 'text-green-600 dark:text-green-400',
  },
  {
    label: 'Total Orders',
    value: props.orders.length.toLocaleString(),
    sub: `${pendingCount.value} pending`,
    icon: ShoppingCartIcon,
    iconBg: 'bg-blue-50 dark:bg-blue-500/10',
    iconColor: 'text-blue-600 dark:text-blue-400',
  },
  {
    label: 'Total Products',
    value: props.products.length.toLocaleString(),
    sub: `${lowStockCount.value} low stock`,
    icon: PackageIcon,
    iconBg: 'bg-purple-50 dark:bg-purple-500/10',
    iconColor: 'text-purple-600 dark:text-purple-400',
  },
  {
    label: 'Avg Order Value',
    value: props.orders.length
      ? '$' + (totalRevenue.value / props.orders.length).toFixed(2)
      : '$0.00',
    sub: 'per order',
    icon: TrendingUpIcon,
    iconBg: 'bg-amber-50 dark:bg-amber-500/10',
    iconColor: 'text-amber-600 dark:text-amber-400',
  },
])

const recentOrders = computed(() =>
  [...props.orders].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5)
)

const topProducts = computed(() =>
  [...props.products].slice(0, 5)
)

const maxStock = computed(() =>
  Math.max(...props.products.map(p => p.stock ?? 0), 1)
)

function stockPct(product) {
  return Math.round(((product.stock ?? 0) / maxStock.value) * 100)
}

function statusClass(status) {
  return {
    completed:  'bg-green-50 border-green-200 text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400',
    paid:       'bg-green-50 border-green-200 text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400',
    processing: 'bg-blue-50 border-blue-200 text-blue-700 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400',
    pending:    'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400',
    cancelled:  'bg-red-50 border-red-200 text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400',
  }[status] ?? 'bg-zinc-50 border-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400'
}
</script>