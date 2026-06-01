<template>
  <div class="flex min-h-screen bg-gray-950 text-gray-100 font-sans">

    <!-- ── SIDEBAR ── -->
    <aside class="fixed inset-y-0 left-0 w-56 flex flex-col bg-gray-900 border-r border-white/10 z-50">
      <!-- Logo -->
      <div class="px-5 py-5 border-b border-white/10 text-base font-semibold">
        🛍️ Shop <span class="text-violet-400">Admin</span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 py-3 space-y-0.5">
        <p class="px-5 pt-2 pb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-600">Main</p>
        <button
          v-for="item in mainNav" :key="item.tab"
          @click="activeTab = item.tab"
          class="flex items-center gap-2.5 w-full px-5 py-2.5 text-[13.5px] transition-all border-l-[3px]"
          :class="activeTab === item.tab
            ? 'text-violet-400 bg-violet-500/10 border-violet-400'
            : 'text-gray-400 hover:text-gray-100 hover:bg-white/5 border-transparent'"
        >
          <span class="w-[17px] h-[17px] flex-shrink-0" v-html="item.icon"></span>
          {{ item.label }}
        </button>

        <p class="px-5 pt-4 pb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-600">Manage</p>
        <button
          v-for="item in manageNav" :key="item.tab"
          @click="activeTab = item.tab"
          class="flex items-center gap-2.5 w-full px-5 py-2.5 text-[13.5px] transition-all border-l-[3px]"
          :class="activeTab === item.tab
            ? 'text-violet-400 bg-violet-500/10 border-violet-400'
            : 'text-gray-400 hover:text-gray-100 hover:bg-white/5 border-transparent'"
        >
          <span class="w-[17px] h-[17px] flex-shrink-0" v-html="item.icon"></span>
          {{ item.label }}
        </button>
      </nav>

      <!-- User -->
      <div class="px-5 py-4 border-t border-white/10">
        <div class="flex items-center gap-2.5 mb-3">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
            {{ userName[0]?.toUpperCase() }}
          </div>
          <div>
            <div class="text-[13px] font-medium">{{ userName }}</div>
            <div class="text-[11px] text-gray-500">Administrator</div>
          </div>
        </div>
        <button
          @click="doLogout"
          class="w-full py-2 text-[12.5px] text-gray-400 border border-white/10 rounded-lg hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/30 transition-all"
        >Sign out</button>
      </div>
    </aside>

    <!-- ── MAIN ── -->
    <div class="ml-56 flex-1 flex flex-col">

      <!-- Topbar -->
      <header class="sticky top-0 z-40 h-14 bg-gray-900 border-b border-white/10 flex items-center justify-between px-7">
        <span class="text-[15px] font-medium">{{ tabTitles[activeTab] }}</span>
        <div class="flex items-center gap-2 text-[12px] text-emerald-400">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          Connected
        </div>
      </header>

      <!-- Content -->
      <div class="p-7">

        <!-- LOADING -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-20 text-gray-500 gap-3">
          <div class="w-6 h-6 border-2 border-white/10 border-t-violet-500 rounded-full animate-spin"></div>
          <span class="text-sm">Loading…</span>
        </div>

        <!-- ── DASHBOARD ── -->
        <template v-else-if="activeTab === 'dashboard'">
          <!-- Stat Cards -->
          <div class="grid grid-cols-4 gap-4 mb-7">
            <div
              v-for="s in statCards" :key="s.label"
              class="bg-gray-900 border border-white/10 rounded-xl p-5 hover:border-white/20 transition-all"
            >
              <div class="text-2xl mb-3">{{ s.icon }}</div>
              <div class="text-[11px] uppercase tracking-widest text-gray-500 mb-2">{{ s.label }}</div>
              <div class="text-2xl font-semibold" :class="s.colorClass">{{ s.value }}</div>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
              <span class="text-sm font-medium">Recent Orders</span>
            </div>
            <div v-if="!recentOrders.length" class="text-center py-10 text-gray-500 text-sm">No recent orders</div>
            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-white/10 text-[11px] uppercase tracking-wider text-gray-500">
                    <th class="text-left px-5 py-3">ID</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    <th class="text-left px-5 py-3">Amount</th>
                    <th class="text-left px-5 py-3">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="o in recentOrders" :key="o.id"
                    class="border-b border-white/5 hover:bg-white/[0.02] transition-colors"
                  >
                    <td class="px-5 py-3 text-[13.5px]">#{{ o.id }}</td>
                    <td class="px-5 py-3 text-[13.5px]">{{ o.user?.name || o.customer_name || '—' }}</td>
                    <td class="px-5 py-3 text-[13.5px]">{{ fmt(o.total_amount ?? o.total ?? 0) }}</td>
                    <td class="px-5 py-3"><StatusBadge :status="o.status" /></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>

        <!-- ── PRODUCTS ── -->
        <template v-else-if="activeTab === 'products'">
          <!-- Add form -->
          <div class="bg-gray-900 border border-white/10 rounded-xl p-6 mb-6">
            <h3 class="text-sm font-medium mb-4">➕ Add New Product</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs text-gray-400 mb-1.5">Product Name</label>
                <input v-model="newProduct.name" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors" placeholder="Product name">
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1.5">Price ($)</label>
                <input v-model.number="newProduct.price" type="number" step="0.01" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors" placeholder="0.00">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs text-gray-400 mb-1.5">Stock</label>
                <input v-model.number="newProduct.stock" type="number" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors" placeholder="0">
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1.5">Category</label>
                <select v-model="newProduct.category_id" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors">
                  <option value="">— Select —</option>
                  <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-xs text-gray-400 mb-1.5">Description</label>
              <textarea v-model="newProduct.description" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors resize-y min-h-[80px]" placeholder="Description…"></textarea>
            </div>
            <div class="mb-5">
              <label class="block text-xs text-gray-400 mb-1.5">Image URL (optional)</label>
              <input v-model="newProduct.image" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors" placeholder="https://…">
            </div>
            <button @click="addProduct" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors">
              Add Product
            </button>
          </div>

          <!-- Table -->
          <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
              <span class="text-sm font-medium">All Products ({{ products.length }})</span>
              <input v-model="productSearch" class="px-3 py-1.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 w-52 transition-colors" placeholder="Search…">
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-white/10 text-[11px] uppercase tracking-wider text-gray-500">
                    <th class="text-left px-5 py-3">Product</th>
                    <th class="text-left px-5 py-3">Category</th>
                    <th class="text-left px-5 py-3">Price</th>
                    <th class="text-left px-5 py-3">Stock</th>
                    <th class="text-left px-5 py-3">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="p in filteredProducts" :key="p.id"
                    class="border-b border-white/5 hover:bg-white/[0.02] transition-colors"
                  >
                    <td class="px-5 py-3">
                      <div class="flex items-center gap-2.5">
                        <img v-if="p.image" :src="p.image" class="w-9 h-9 rounded-lg object-cover border border-white/10">
                        <div v-else class="w-9 h-9 rounded-lg bg-gray-800 border border-white/10 flex items-center justify-center text-lg">📦</div>
                        <span class="text-[13.5px]">{{ p.name }}</span>
                      </div>
                    </td>
                    <td class="px-5 py-3 text-[13.5px] text-gray-400">{{ p.category?.name || '—' }}</td>
                    <td class="px-5 py-3 text-[13.5px]">{{ fmt(p.price) }}</td>
                    <td class="px-5 py-3">
                      <span :class="p.stock > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'" class="px-2.5 py-1 rounded-full text-xs font-medium">
                        {{ p.stock ?? 0 }}
                      </span>
                    </td>
                    <td class="px-5 py-3">
                      <div class="flex items-center gap-2">
                        <button @click="openEditProduct(p)" class="px-3 py-1 text-xs border border-white/15 text-gray-300 rounded-lg hover:border-violet-500 hover:text-violet-400 transition-all">Edit</button>
                        <button @click="deleteProduct(p.id)" class="px-3 py-1 text-xs bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg hover:bg-red-500/20 transition-all">Delete</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>

        <!-- ── ORDERS ── -->
        <template v-else-if="activeTab === 'orders'">
          <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
              <span class="text-sm font-medium">All Orders ({{ orders.length }})</span>
              <input v-model="orderSearch" class="px-3 py-1.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 w-52 transition-colors" placeholder="Search ID / customer…">
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-white/10 text-[11px] uppercase tracking-wider text-gray-500">
                    <th class="text-left px-5 py-3">ID</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    <th class="text-left px-5 py-3">Items</th>
                    <th class="text-left px-5 py-3">Total</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Date</th>
                    <th class="text-left px-5 py-3">Update</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="o in filteredOrders" :key="o.id"
                    class="border-b border-white/5 hover:bg-white/[0.02] transition-colors"
                  >
                    <td class="px-5 py-3 text-[13.5px]">#{{ o.id }}</td>
                    <td class="px-5 py-3 text-[13.5px]">{{ o.user?.name || o.customer_name || '—' }}</td>
                    <td class="px-5 py-3 text-[13.5px] text-gray-400">{{ o.items?.length ?? o.order_items?.length ?? '—' }}</td>
                    <td class="px-5 py-3 text-[13.5px]">{{ fmt(o.total_amount ?? o.total ?? 0) }}</td>
                    <td class="px-5 py-3"><StatusBadge :status="o.status" /></td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ o.created_at?.slice(0,10) || '—' }}</td>
                    <td class="px-5 py-3">
                      <select
                        :value="o.status"
                        @change="updateOrderStatus(o.id, $event.target.value)"
                        class="px-2 py-1.5 bg-gray-800 border border-white/10 rounded-lg text-xs text-gray-100 outline-none focus:border-violet-500 transition-colors"
                      >
                        <option v-for="s in orderStatuses" :key="s" :value="s">{{ s }}</option>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>

        <!-- ── CATEGORIES ── -->
        <template v-else-if="activeTab === 'categories'">
          <div class="bg-gray-900 border border-white/10 rounded-xl p-6 mb-6 max-w-md">
            <h3 class="text-sm font-medium mb-4">➕ Add Category</h3>
            <label class="block text-xs text-gray-400 mb-1.5">Category Name</label>
            <input v-model="newCategoryName" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors mb-4" placeholder="e.g. Skincare">
            <button @click="addCategory" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors">
              Add Category
            </button>
          </div>

          <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10">
              <span class="text-sm font-medium">All Categories ({{ categories.length }})</span>
            </div>
            <table class="w-full">
              <thead>
                <tr class="border-b border-white/10 text-[11px] uppercase tracking-wider text-gray-500">
                  <th class="text-left px-5 py-3">ID</th>
                  <th class="text-left px-5 py-3">Name</th>
                  <th class="text-left px-5 py-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="c in categories" :key="c.id"
                  class="border-b border-white/5 hover:bg-white/[0.02] transition-colors"
                >
                  <td class="px-5 py-3 text-[13.5px] text-gray-400">{{ c.id }}</td>
                  <td class="px-5 py-3 text-[13.5px]">{{ c.name }}</td>
                  <td class="px-5 py-3">
                    <button @click="deleteCategory(c.id)" class="px-3 py-1 text-xs bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg hover:bg-red-500/20 transition-all">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </template>

      </div>
    </div>

    <!-- ── EDIT MODAL ── -->
    <div v-if="editingProduct" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-5" @click.self="editingProduct = null">
      <div class="bg-gray-900 border border-white/15 rounded-2xl p-7 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h3 class="text-base font-semibold mb-5">✏️ Edit Product</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs text-gray-400 mb-1.5">Name</label>
            <input v-model="editingProduct.name" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1.5">Price</label>
            <input v-model.number="editingProduct.price" type="number" step="0.01" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs text-gray-400 mb-1.5">Stock</label>
            <input v-model.number="editingProduct.stock" type="number" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1.5">Category</label>
            <select v-model="editingProduct.category_id" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors">
              <option value="">— Select —</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-xs text-gray-400 mb-1.5">Description</label>
          <textarea v-model="editingProduct.description" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors resize-y min-h-[80px]"></textarea>
        </div>
        <div class="mb-6">
          <label class="block text-xs text-gray-400 mb-1.5">Image URL</label>
          <input v-model="editingProduct.image" class="w-full px-3 py-2.5 bg-gray-800 border border-white/10 rounded-lg text-sm text-gray-100 outline-none focus:border-violet-500 transition-colors">
        </div>
        <div class="flex justify-end gap-3">
          <button @click="editingProduct = null" class="px-4 py-2 text-sm border border-white/15 text-gray-300 rounded-lg hover:border-white/30 transition-all">Cancel</button>
          <button @click="saveEditProduct" class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 text-white rounded-lg transition-colors">Save Changes</button>
        </div>
      </div>
    </div>

    <!-- ── TOAST ── -->
    <div
      class="fixed bottom-7 right-7 px-4 py-3 rounded-lg text-sm z-50 border transition-all duration-200 pointer-events-none"
      :class="[
        toast.show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2',
        toast.type === 'ok' ? 'bg-gray-800 border-emerald-500/30 text-emerald-400' : 'bg-gray-800 border-red-500/30 text-red-400'
      ]"
    >
      {{ toast.msg }}
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from '../axios'

// ── Sub-component ──
const StatusBadge = {
  props: ['status'],
  template: `
    <span class="px-2.5 py-1 rounded-full text-xs font-medium"
      :class="{
        'bg-amber-500/10 text-amber-400':  status === 'pending',
        'bg-blue-500/10 text-blue-400':    status === 'processing' || status === 'shipped',
        'bg-emerald-500/10 text-emerald-400': status === 'delivered',
        'bg-red-500/10 text-red-400':      status === 'cancelled',
      }">{{ status || 'pending' }}</span>
  `
}

// ── User ──
const userName = ref(JSON.parse(localStorage.getItem('user') || '{}').name || 'Admin')

// ── Tabs ──
const activeTab  = ref('dashboard')
const tabTitles  = { dashboard: 'Dashboard', products: 'Products', orders: 'Orders', categories: 'Categories' }

const mainNav = [
  { tab: 'dashboard', label: 'Dashboard', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>' },
  { tab: 'products',  label: 'Products',  icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>' },
  { tab: 'orders',    label: 'Orders',    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/></svg>' },
]
const manageNav = [
  { tab: 'categories', label: 'Categories', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h8m-8 6h4"/></svg>' },
]

// ── State ──
const loading         = ref(false)
const products        = ref([])
const orders          = ref([])
const categories      = ref([])
const recentOrders    = ref([])
const statCards       = ref([])
const productSearch   = ref('')
const orderSearch     = ref('')
const newCategoryName = ref('')
const editingProduct  = ref(null)
const orderStatuses   = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']
const newProduct      = ref({ name: '', price: '', stock: 0, category_id: '', description: '', image: '' })
const toast           = ref({ show: false, msg: '', type: 'ok' })

// ── Toast ──
function showToast(msg, type = 'ok') {
  toast.value = { show: true, msg, type }
  setTimeout(() => toast.value.show = false, 3000)
}

// ── Helpers ──
function fmt(v) {
  if (!v && v !== 0) return '—'
  return '$' + parseFloat(v).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

// ── Computed ──
const filteredProducts = computed(() => {
  if (!productSearch.value) return products.value
  return products.value.filter(p => p.name.toLowerCase().includes(productSearch.value.toLowerCase()))
})
const filteredOrders = computed(() => {
  if (!orderSearch.value) return orders.value
  const q = orderSearch.value.toLowerCase()
  return orders.value.filter(o => String(o.id).includes(q) || (o.user?.name || '').toLowerCase().includes(q))
})

// ── Fetch ──
async function fetchDashboard() {
  loading.value = true
  try {
    const { data } = await axios.get('/dashboard/analytics')
    const s = data.analytics || data.stats || data
    statCards.value = [
      { icon: '📦', label: 'Total Products',  value: s.total_products  ?? '—', colorClass: 'text-violet-400' },
      { icon: '🛒', label: 'Total Orders',    value: s.total_orders    ?? '—', colorClass: 'text-emerald-400' },
      { icon: '💰', label: 'Revenue',         value: fmt(s.total_revenue ?? 0), colorClass: 'text-amber-400' },
      { icon: '👤', label: 'Total Customers', value: s.total_customers ?? s.total_users ?? '—', colorClass: 'text-blue-400' },
    ]
    recentOrders.value = data.recent_orders || s.recent_orders || []
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to load dashboard', 'err')
  } finally { loading.value = false }
}

async function fetchProducts() {
  loading.value = true
  try {
    const [pRes, cRes] = await Promise.all([axios.get('/products'), axios.get('/categories')])
    products.value   = pRes.data.data || pRes.data
    categories.value = cRes.data.data || cRes.data
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to load products', 'err')
  } finally { loading.value = false }
}

async function fetchOrders() {
  loading.value = true
  try {
    const { data } = await axios.get('/orders')
    orders.value = data.data || data
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to load orders', 'err')
  } finally { loading.value = false }
}

async function fetchCategories() {
  loading.value = true
  try {
    const { data } = await axios.get('/categories')
    categories.value = data.data || data
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to load categories', 'err')
  } finally { loading.value = false }
}

// ── Product actions ──
async function addProduct() {
  if (!newProduct.value.name || !newProduct.value.price) { showToast('Name and price required', 'err'); return }
  try {
    await axios.post('/products', newProduct.value)
    showToast('Product added!')
    newProduct.value = { name: '', price: '', stock: 0, category_id: '', description: '', image: '' }
    fetchProducts()
  } catch (e) { showToast(e.response?.data?.message || 'Error', 'err') }
}

async function deleteProduct(id) {
  if (!confirm('Delete this product?')) return
  try { await axios.delete('/products/' + id); showToast('Deleted!'); fetchProducts() }
  catch (e) { showToast(e.response?.data?.message || 'Failed', 'err') }
}

function openEditProduct(p) {
  editingProduct.value = { ...p, category_id: p.category_id || p.category?.id || '' }
}

async function saveEditProduct() {
  try {
    await axios.put('/products/' + editingProduct.value.id, editingProduct.value)
    showToast('Updated!')
    editingProduct.value = null
    fetchProducts()
  } catch (e) { showToast(e.response?.data?.message || 'Failed', 'err') }
}

// ── Order actions ──
async function updateOrderStatus(id, status) {
  try { await axios.put('/orders/' + id + '/status', { status }); showToast('Status updated to "' + status + '"') }
  catch (e) { showToast(e.response?.data?.message || 'Failed', 'err') }
}

// ── Category actions ──
async function addCategory() {
  if (!newCategoryName.value.trim()) { showToast('Name required', 'err'); return }
  try {
    await axios.post('/categories', { name: newCategoryName.value.trim() })
    showToast('Category added!')
    newCategoryName.value = ''
    fetchCategories()
  } catch (e) { showToast(e.response?.data?.message || 'Failed', 'err') }
}

async function deleteCategory(id) {
  if (!confirm('Delete this category?')) return
  try { await axios.delete('/categories/' + id); showToast('Deleted!'); fetchCategories() }
  catch (e) { showToast(e.response?.data?.message || 'Failed', 'err') }
}

// ── Logout ──
function doLogout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  window.location.href = '/login'
}

// ── Watch tabs ──
watch(activeTab, (tab) => {
  if (tab === 'dashboard')  fetchDashboard()
  if (tab === 'products')   fetchProducts()
  if (tab === 'orders')     fetchOrders()
  if (tab === 'categories') fetchCategories()
})

onMounted(() => fetchDashboard())
</script>