<template>
  <div class="p-6 sm:p-8 space-y-8 bg-white dark:bg-zinc-950 min-h-[calc(100vh-64px)] transition-colors">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-white">Categories</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">Manage product categories</p>
      </div>
      <button @click="showModal = true"
        class="btn-primary flex items-center gap-2 px-6 py-3 text-xs">
        <PlusIcon class="w-4 h-4 stroke-[2]" />
        Add Category
      </button>
    </div>

    <!-- Category Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
      <div v-if="loading" class="p-8 space-y-4">
        <div v-for="i in 3" :key="i" class="h-16 skeleton rounded-xl"></div>
      </div>
      <table v-else class="w-full text-sm">
        <thead class="bg-zinc-50 dark:bg-zinc-950/50 text-left text-[10px] font-semibold uppercase text-zinc-500 dark:text-zinc-400 tracking-widest border-b border-zinc-200 dark:border-zinc-800">
          <tr>
            <th class="px-8 py-5">ID</th>
            <th class="px-6 py-5">Name</th>
            <th class="px-8 py-5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
          <tr v-for="cat in categories" :key="cat.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
            <td class="px-8 py-5 text-[10px] font-semibold tracking-widest uppercase text-zinc-400 dark:text-zinc-500">#{{ cat.id }}</td>
            <td class="px-6 py-5 font-medium text-zinc-900 dark:text-zinc-50">{{ cat.name }}</td>
            <td class="px-8 py-5 text-right">
              <button @click="handleDelete(cat)" class="text-[10px] font-semibold tracking-widest uppercase text-red-500 hover:text-red-600 transition-colors border-b border-transparent hover:border-red-500 pb-0.5">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add Modal -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showModal = false"></div>
          <Transition name="modal">
            <div class="relative z-10 w-full max-w-sm rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 shadow-2xl">
              <h3 class="text-2xl font-display font-medium text-zinc-900 dark:text-white mb-6">Add Category</h3>
              <form @submit.prevent="handleSubmit">
                <div class="mb-6">
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Category Name</label>
                  <input v-model="form.name" type="text" required placeholder="e.g. Electronics"
                    class="w-full px-5 py-3 rounded-xl text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all placeholder:text-zinc-400" />
                </div>
                <div class="flex gap-3">
                  <button type="button" @click="showModal = false" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                  <button type="submit" :disabled="saving" class="btn-primary flex-1 py-3.5 text-xs">
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
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteTarget = null"></div>
          <Transition name="modal">
            <div class="relative z-10 w-full max-w-sm rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 shadow-2xl text-center">
              <div class="w-16 h-16 mx-auto rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center mb-4">
                <TrashIcon class="w-8 h-8 text-red-500 stroke-[1.5]" />
              </div>
              <p class="text-xl font-display font-medium text-zinc-900 dark:text-white">Delete Category?</p>
              <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">"{{ deleteTarget.name }}" will be deleted.</p>
              <div class="flex gap-3 w-full mt-6">
                <button @click="deleteTarget = null" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                <button @click="confirmDelete" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/20 active:scale-95 transition-all">Delete</button>
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
import { Plus as PlusIcon, Trash2 as TrashIcon } from '@lucide/vue'

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

<style scoped>
.backdrop-enter-active, .backdrop-leave-active { transition: opacity 0.2s ease; }
.backdrop-enter-from, .backdrop-leave-to { opacity: 0; }
.modal-enter-active { transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-leave-active { transition: all 0.15s ease-in; }
.modal-enter-from { opacity: 0; transform: scale(0.92) translateY(16px); }
.modal-leave-to { opacity: 0; transform: scale(0.95) translateY(8px); }
</style>
