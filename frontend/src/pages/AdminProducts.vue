<template>
  <div class="p-6 sm:p-8 space-y-8 bg-white dark:bg-zinc-950 min-h-[calc(100vh-64px)] transition-colors">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-white">Products</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">Manage your product catalog</p>
      </div>
      <button @click="openAddModal"
        class="btn-primary flex items-center gap-2 px-6 py-3 text-xs"
        id="admin-add-product">
        <PlusIcon class="w-4 h-4 stroke-[2]" />
        Add Product
      </button>
    </div>

    <!-- Product Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
      <div v-if="loading" class="p-8 space-y-4">
        <div v-for="i in 5" :key="i" class="h-16 skeleton rounded-xl"></div>
      </div>
      <table v-else class="w-full text-sm">
        <thead class="bg-zinc-50 dark:bg-zinc-950/50 text-left text-[10px] font-semibold uppercase text-zinc-500 dark:text-zinc-400 tracking-widest border-b border-zinc-200 dark:border-zinc-800">
          <tr>
            <th class="px-8 py-5">Product</th>
            <th class="px-6 py-5">Price</th>
            <th class="px-6 py-5">Stock</th>
            <th class="px-8 py-5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
          <tr v-for="p in products" :key="p.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
            <td class="px-8 py-5 flex items-center gap-4">
              <img :src="p.full_image_url || p.image || 'https://placehold.co/40'" class="w-12 h-12 rounded-xl object-cover border border-zinc-200 dark:border-zinc-700" :alt="p.name" />
              <span class="font-medium text-zinc-900 dark:text-zinc-50">{{ p.name }}</span>
            </td>
            <td class="px-6 py-5">
              <div v-if="p.discount_percent > 0">
                <span class="line-through text-zinc-400 text-[10px] font-semibold tracking-widest uppercase">${{ Number(p.price).toFixed(2) }}</span>
                <div class="font-medium text-zinc-900 dark:text-zinc-50">${{ (p.price - (p.price * p.discount_percent / 100)).toFixed(2) }} <span class="text-[9px] bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-1.5 py-0.5 rounded border border-zinc-900 dark:border-zinc-100 uppercase tracking-widest ml-1">-{{ p.discount_percent }}%</span></div>
              </div>
              <div v-else class="font-medium text-zinc-900 dark:text-zinc-50">
                ${{ Number(p.price).toFixed(2) }}
              </div>
            </td>
            <td class="px-6 py-5 font-medium text-zinc-500 dark:text-zinc-400">{{ p.stock ?? '—' }}</td>
            <td class="px-8 py-5 text-right space-x-4">
              <button @click="openEditModal(p)" class="text-[10px] font-semibold tracking-widest uppercase text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors border-b border-transparent hover:border-zinc-900 dark:hover:border-white pb-0.5">Edit</button>
              <button @click="handleDelete(p)" class="text-[10px] font-semibold tracking-widest uppercase text-red-500 hover:text-red-600 transition-colors border-b border-transparent hover:border-red-500 pb-0.5">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage > 1" class="flex items-center justify-center gap-2">
      <button
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-4 py-2 rounded-full border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition"
      >← Prev</button>

      <template v-for="item in paginationPages" :key="item">
        <span v-if="item === '...'" class="text-zinc-400 px-1">...</span>
        <button
          v-else
          @click="goToPage(item)"
          :class="[
            'w-10 h-10 rounded-full text-sm font-medium transition',
            item === currentPage
              ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900'
              : 'border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'
          ]"
        >{{ item }}</button>
      </template>

      <button
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage === lastPage"
        class="px-4 py-2 rounded-full border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 disabled:opacity-30 disabled:cursor-not-allowed transition"
      >Next →</button>
    </div>

    <!-- Total -->
    <p class="text-center text-zinc-400 text-sm -mt-4">
      Page {{ currentPage }} / {{ lastPage }} — សរុប {{ total }} products
    </p>

    <!-- Add/Edit Modal -->
    <AddProductModal
      v-if="showModal"
      :editing="editingProduct"
      :categories="categories"
      :saving="saving"
      @close="closeModal"
      @submit="handleSubmit"
    />

    <!-- Delete Confirm -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="deleteTarget" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-md" @click="deleteTarget = null"></div>
          <Transition name="modal">
            <div v-if="deleteTarget" class="relative z-10 w-full max-w-sm rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 shadow-2xl">
              <div class="flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                  <TrashIcon class="w-8 h-8 text-red-500 stroke-[1.5]" />
                </div>
                <div>
                  <p class="text-xl font-display font-medium text-zinc-900 dark:text-white">Delete Product?</p>
                  <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">"<span class="font-medium text-zinc-900 dark:text-white">{{ deleteTarget.name }}</span>" will be permanently removed.</p>
                </div>
                <div class="flex gap-3 w-full mt-4">
                  <button @click="deleteTarget = null" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                  <button @click="confirmDelete" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/20 active:scale-95 transition-all">Delete</button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import AddProductModal from '@/components/admin/ProductModal.vue'
import { Plus as PlusIcon, Trash2 as TrashIcon } from '@lucide/vue'

const toast = inject('toast')

const products       = ref([])
const categories     = ref([])
const loading        = ref(true)
const saving         = ref(false)
const showModal      = ref(false)
const editingProduct = ref(null)
const deleteTarget   = ref(null)

// Pagination
const currentPage = ref(1)
const lastPage    = ref(1)
const total       = ref(0)
const perPage     = 20

// Smart pagination pages: 1 | 2 | 3 | ... | 20
const paginationPages = computed(() => {
  const pages = []
  const tp = lastPage.value
  const cp = currentPage.value

  if (tp <= 7) {
    for (let i = 1; i <= tp; i++) pages.push(i)
    return pages
  }

  pages.push(1)
  if (cp > 4) pages.push('...')

  const start = Math.max(2, cp - 1)
  const end   = Math.min(tp - 1, cp + 1)
  for (let i = start; i <= end; i++) pages.push(i)

  if (cp < tp - 3) pages.push('...')
  pages.push(tp)

  return pages
})

// Fetch products for a given page
async function loadData(page = 1) {
  loading.value = true
  try {
    const [pRes, cRes] = await Promise.all([
      api.get(`/products?per_page=${perPage}&page=${page}`),
      api.get('/categories')
    ])
    products.value   = pRes.data.data || []
    currentPage.value = pRes.data.current_page || 1
    lastPage.value    = pRes.data.last_page || 1
    total.value       = pRes.data.total || 0
    categories.value  = cRes.data.data || cRes.data || []
  } catch (e) {
    toast('Failed to load products', 'error')
  } finally {
    loading.value = false
  }
}

function goToPage(page) {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  loadData(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Modal handlers
function openAddModal() {
  editingProduct.value = null
  showModal.value = true
}
function openEditModal(product) {
  editingProduct.value = product
  showModal.value = true
}
function closeModal() {
  showModal.value = false
  editingProduct.value = null
}

// Submit (create or update)
async function handleSubmit(data) {
  saving.value = true
  try {
    const formData = data instanceof FormData ? data : (() => {
      const fd = new FormData()
      Object.entries(data).forEach(([k, v]) => { if (v !== '' && v !== null && v !== undefined) fd.append(k, v) })
      return fd
    })()

    if (editingProduct.value) {
      formData.append('_method', 'PUT')
      await api.post(`/products/${editingProduct.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      toast('Product updated!', 'success')
    } else {
      await api.post('/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      toast('Product created!', 'success')
    }
    closeModal()
    await loadData(currentPage.value) // ← stay on same page after edit
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to save product', 'error')
  } finally {
    saving.value = false
  }
}

// Delete handlers
function handleDelete(product) {
  deleteTarget.value = product
}
async function confirmDelete() {
  try {
    await api.delete(`/products/${deleteTarget.value.id}`)
    toast('Product deleted', 'success')
    await loadData(currentPage.value) // ← reload current page after delete
  } catch (e) {
    toast('Failed to delete product', 'error')
  } finally {
    deleteTarget.value = null
  }
}

onMounted(() => loadData(1))
</script>