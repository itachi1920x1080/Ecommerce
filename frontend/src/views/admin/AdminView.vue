<template>
  <div class="flex min-h-screen font-sans" :class="isDark ? 'dark' : ''">
  <div class="flex min-h-screen w-full bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 transition-colors duration-300">

    <!-- Sidebar -->
    <AdminSidebar
      :isDark="isDark"
      :activePage="activePage"
      :products="products"
      :orders="orders"
      :customers="customers"
      @navigate="activePage = $event"
      @logout="handleLogout"
    />

    <!-- Main -->
    <main class="flex-1 flex flex-col min-w-0 ml-60">

      <!-- Topbar -->
      <AdminTopbar
        :isDark="isDark"
        :loading="loading"
        :apiOk="apiOk"
        :pageTitle="pageTitle"
        @toggleDark="isDark = !isDark"
        @refresh="loadAll"
        @export="exportCSV"
        @addProduct="showProductModal = true"
      />

      <!-- Error banner -->
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        leave-active-class="transition-all duration-200 ease-in"
        leave-to-class="opacity-0 -translate-y-2">
        <div v-if="errorMsg"
          class="mx-6 mt-4 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm px-4 py-3 rounded-xl">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span class="flex-1">{{ errorMsg }}</span>
          <button @click="errorMsg = ''" class="opacity-60 hover:opacity-100 transition-opacity">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </Transition>

      <!-- Pages -->
      <div class="flex-1 overflow-auto">
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 translate-y-3"
          leave-active-class="transition-all duration-200 ease-in"
          leave-to-class="opacity-0 translate-y-3"
          mode="out-in">

          <DashboardPage v-if="activePage === 'dashboard'" :key="'dashboard'"
            :loading="loading" :analytics="analytics" :orders="orders"
            :products="products" :isDark="isDark"
            @navigate="activePage = $event"
            @markPaid="markPaid" />

          <ProductsPage v-else-if="activePage === 'products'" :key="'products'"
            :products="products" :categories="categories" :isDark="isDark"
            @delete="deleteProduct" @edit="openEditProduct" @add="openAddProduct" />

          <OrdersPage v-else-if="activePage === 'orders'" :key="'orders'"
            :orders="orders"
            @updateStatus="updateOrderStatus" />

          <CustomersPage v-else-if="activePage === 'customers'" :key="'customers'"
            :customers="customers" />

          <CategoriesPage v-else-if="activePage === 'categories'" :key="'categories'"
            :categories="categories"
            @add="addCategory" @delete="deleteCategory" />

          <AnalyticsPage v-else-if="activePage === 'analytics'" :key="'analytics'"
            :analytics="analytics" :orders="orders" :products="products" :isDark="isDark" />

          <SettingsPage v-else-if="activePage === 'settings'" :key="'settings'"
            :isDark="isDark"
            :currentUser="currentUser"
            @toggleDark="isDark = !isDark" />

        </Transition>
      </div>
    </main>

    <!-- Add/Edit Product Modal -->
    <ProductModal
      v-if="showProductModal"
      :editing="editingProduct"
      :categories="categories"
      :saving="saving"
      :formError="formError"
      @close="showProductModal = false; editingProduct = null; formError = ''"
      @submit="submitProduct"
    />

  </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios.js'

// Components
import AdminSidebar   from '@/components/admin/AdminSidebar.vue'
import AdminTopbar    from '@/components/admin/AdminTopbar.vue'
import DashboardPage  from '@/components/admin/pages/DashboardPage.vue'
import ProductsPage   from '@/components/admin/pages/ProductsPage.vue'
import OrdersPage     from '@/components/admin/pages/OrdersPage.vue'
import CustomersPage  from '@/components/admin/pages/CustomersPage.vue'
import CategoriesPage from '@/components/admin/pages/CategoriesPage.vue'
import AnalyticsPage  from '@/components/admin/pages/AnalyticsPage.vue'
import SettingsPage   from '@/components/admin/pages/SettingsPage.vue'
import ProductModal   from '@/components/admin/ProductModal.vue'

const router      = useRouter()
const isDark      = ref(localStorage.getItem('adminDark') === 'true')
const activePage  = ref('dashboard')
const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'))

// Data
const orders      = ref([])
const products    = ref([])
const categories  = ref([])
const customers   = ref([])
const analytics   = ref(null)
const loading     = ref(false)
const saving      = ref(false)
const errorMsg    = ref('')
const apiOk       = ref(false)

// Modal
const showProductModal = ref(false)
const editingProduct   = ref(null)
const formError        = ref('')

let pollTimer = null

// Watch dark mode and save to localStorage
import { watch } from 'vue'
watch(isDark, val => localStorage.setItem('adminDark', val))

// Page title
const pageTitle = computed(() => ({
  dashboard: 'Dashboard overview', products: 'Products',
  orders: 'Orders', customers: 'Customers',
  categories: 'Categories', analytics: 'Analytics', settings: 'Settings',
}[activePage.value] || 'Dashboard'))

// Auth
const handleLogout = async () => {
  try { await api.post('/logout') } catch (_) {}
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}

// Load all data
async function loadAll() {
  loading.value = true
  errorMsg.value = ''
  try {
    const [oRes, pRes, aRes, cRes] = await Promise.all([
      api.get('/orders'),
      api.get('/products'),
      api.get('/dashboard/analytics'),
      api.get('/categories'),
    ])
    orders.value     = oRes.data?.data    ?? oRes.data    ?? []
    products.value   = pRes.data?.data    ?? pRes.data    ?? []
    analytics.value  = aRes.data
    categories.value = cRes.data?.data    ?? cRes.data    ?? []

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
  formError.value = ''
  showProductModal.value = true
}
function openEditProduct(p) {
  editingProduct.value = p
  formError.value = ''
  showProductModal.value = true
}
async function submitProduct(form) {
  saving.value = true
  formError.value = ''
  try {
    if (editingProduct.value) {
      await api.put(`/products/${editingProduct.value.id}`, form)
    } else {
      await api.post('/products', form)
    }
    showProductModal.value = false
    editingProduct.value = null
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
    await api.delete(`/products/${p.id}`)
    products.value = products.value.filter(x => x.id !== p.id)
  } catch (e) { errorMsg.value = e.message }
}

// Orders
async function markPaid(order) {
  try {
    await api.put(`/orders/${order.id}/status`, { status: 'paid' })
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
async function addCategory(name) {
  try {
    await api.post('/categories', { name })
    await loadAll()
  } catch (e) { errorMsg.value = e.message }
}
async function deleteCategory(cat) {
  if (!confirm(`Delete "${cat.name}"?`)) return
  try {
    await api.delete(`/categories/${cat.id}`)
    categories.value = categories.value.filter(c => c.id !== cat.id)
  } catch (e) { errorMsg.value = e.message }
}

// Export CSV
function exportCSV() {
  const rows = [['ID','Customer','Total','Status','Date']]
  orders.value.forEach(o => rows.push([
    o.id, o.user?.name || '', Number(o.total_price || 0).toFixed(2), o.status, o.created_at || ''
  ]))
  const a = document.createElement('a')
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(rows.map(r => r.join(',')).join('\n'))
  a.download = `orders-${new Date().toISOString().split('T')[0]}.csv`
  a.click()
}

onMounted(() => {
  loadAll()
  pollTimer = setInterval(() => { if (apiOk.value) loadAll() }, 15000)
})
onUnmounted(() => { if (pollTimer) clearInterval(pollTimer) })
</script>