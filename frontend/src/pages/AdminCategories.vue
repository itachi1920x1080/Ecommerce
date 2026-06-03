<template>
  <div class="p-6 sm:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Categories</h1>
        <p class="text-sm text-slate-400 mt-1">Manage product categories</p>
      </div>
      <button @click="showModal = true"
        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all active:scale-95">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Category
      </button>
    </div>

    <!-- Category Table -->
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
      <div v-if="loading" class="p-6 space-y-3">
        <div v-for="i in 3" :key="i" class="h-14 skeleton rounded-xl"></div>
      </div>
      <table v-else class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 tracking-wider">
          <tr>
            <th class="px-5 py-3">ID</th>
            <th class="px-5 py-3">Name</th>
            <th class="px-5 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="cat in categories" :key="cat.id" class="hover:bg-slate-50 transition-colors">
            <td class="px-5 py-3 text-slate-500">#{{ cat.id }}</td>
            <td class="px-5 py-3 font-medium text-slate-700">{{ cat.name }}</td>
            <td class="px-5 py-3 text-right">
              <button @click="handleDelete(cat)" class="text-red-600 hover:underline text-xs font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add Modal -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
          <Transition name="modal">
            <div class="relative z-10 w-full max-w-sm rounded-2xl bg-white border border-slate-200 p-6 shadow-2xl">
              <h3 class="text-lg font-bold text-slate-800 mb-4">Add Category</h3>
              <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                  <label class="block text-xs font-medium text-slate-500 mb-1.5">Category Name</label>
                  <input v-model="form.name" type="text" required placeholder="e.g. Electronics"
                    class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all" />
                </div>
                <div class="flex gap-3">
                  <button type="button" @click="showModal = false" class="flex-1 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
                  <button type="submit" :disabled="saving" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 transition-all">
                    {{ saving ? 'Saving...' : 'Save' }}
                  </button>
                </div>
              </form>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirm -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null"></div>
          <Transition name="modal">
            <div class="relative z-10 w-full max-w-sm rounded-2xl bg-white border border-slate-200 p-6 shadow-2xl text-center">
              <div class="w-14 h-14 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
              </div>
              <p class="text-base font-semibold text-slate-800">Delete Category?</p>
              <p class="text-sm text-slate-400 mt-1">"{{ deleteTarget.name }}" will be deleted.</p>
              <div class="flex gap-3 w-full mt-4">
                <button @click="deleteTarget = null" class="flex-1 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
                <button @click="confirmDelete" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 active:scale-95 transition-all">Delete</button>
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

const toast = inject('toast')

const categories = ref([])
const loading = ref(true)
const showModal = ref(false)
const saving = ref(false)
const form = ref({ name: '' })
const deleteTarget = ref(null)

async function fetchCategories() {
  loading.value = true
  try {
    const res = await api.get('/categories')
    categories.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load categories', 'error')
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  saving.value = true
  try {
    const res = await api.post('/categories', form.value)
    categories.value.push(res.data.data || res.data)
    showModal.value = false
    form.value.name = ''
    toast('Category created', 'success')
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to create category', 'error')
  } finally {
    saving.value = false
  }
}

function handleDelete(cat) {
  deleteTarget.value = cat
}

async function confirmDelete() {
  try {
    await api.delete(`/categories/${deleteTarget.value.id}`)
    categories.value = categories.value.filter(c => c.id !== deleteTarget.value.id)
    toast('Category deleted', 'success')
  } catch (e) {
    toast('Failed to delete category', 'error')
  } finally {
    deleteTarget.value = null
  }
}

onMounted(fetchCategories)
</script>
