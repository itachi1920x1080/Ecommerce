<template>
  <div class="p-6 sm:p-8 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Products</h1>
        <p class="text-sm text-slate-400 mt-1">Manage your product catalog</p>
      </div>
      <button @click="openAddModal"
        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all active:scale-95"
        id="admin-add-product">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Product
      </button>
    </div>

    <!-- Product Table -->
    <ProductTable
      :products="products"
      :categories="categories"
      :loading="loading"
      @edit="openEditModal"
      @delete="handleDelete"
    />

    <!-- Add/Edit Modal -->
    <AddProductModal
      :show="showModal"
      :editing="editingProduct"
      :categories="categories"
      :saving="saving"
      @close="closeModal"
      @submit="handleSubmit"
    />

    <!-- Delete Confirm -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null"></div>
          <Transition name="modal">
            <div v-if="deleteTarget" class="relative z-10 w-full max-w-sm rounded-2xl bg-white border border-slate-200 p-6 shadow-2xl">
              <div class="flex flex-col items-center text-center gap-3">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                  <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-base font-semibold text-slate-800">Delete Product?</p>
                  <p class="text-sm text-slate-400 mt-1">"<span class="font-medium text-slate-600">{{ deleteTarget.name }}</span>" will be permanently removed.</p>
                </div>
                <div class="flex gap-3 w-full mt-2">
                  <button @click="deleteTarget = null" class="flex-1 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
                  <button @click="confirmDelete" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 active:scale-95 transition-all">Delete</button>
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
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import ProductTable from '@/components/ProductTable.vue'
import AddProductModal from '@/components/AddProductModal.vue'

const toast = inject('toast')

const products       = ref([])
const categories     = ref([])
const loading        = ref(true)
const saving         = ref(false)
const showModal      = ref(false)
const editingProduct = ref(null)
const deleteTarget   = ref(null)

// Fetch all data
async function loadData() {
  loading.value = true
  try {
    const [pRes, cRes] = await Promise.all([
      api.get('/products'),
      api.get('/categories')
    ])
    products.value   = pRes.data.data || pRes.data || []
    categories.value = cRes.data.data || cRes.data || []
  } catch (e) {
    toast('Failed to load products', 'error')
  } finally {
    loading.value = false
  }
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

// Submit (create or update) — uses FormData for image upload
async function handleSubmit(formData) {
  saving.value = true
  try {
    if (editingProduct.value) {
      // PUT doesn't support FormData well in Laravel, use POST with _method
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
    await loadData()
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
    products.value = products.value.filter(p => p.id !== deleteTarget.value.id)
    toast('Product deleted', 'success')
  } catch (e) {
    toast('Failed to delete product', 'error')
  } finally {
    deleteTarget.value = null
  }
}

onMounted(loadData)
</script>
