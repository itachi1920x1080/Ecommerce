<template>
  <div class="p-6 space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Categories</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ categories.length }} categories</p>
      </div>
      <button @click="openAdd"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
               bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Category
      </button>
    </div>

    <!-- Loading skeleton -->
    <div v-if="categories.length === 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="h-40 rounded-2xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
      <TransitionGroup name="list">
        <div v-for="cat in categories" :key="cat.id"
          class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800
                 shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">

          <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                 :style="{ background: (catColor(cat.id)) + '20' }">
              {{ cat.icon || '📦' }}
            </div>
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="confirmDeleteCat(cat)"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400
                       hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400
                       dark:hover:bg-red-900/30 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>

          <p class="font-semibold text-gray-800 dark:text-gray-100">{{ cat.name }}</p>
          <p class="text-xs text-gray-400 mt-0.5 mb-3">{{ cat.description || 'No description' }}</p>

          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-500 dark:text-gray-400">
              {{ cat.products_count ?? '—' }} products
            </span>
            <div class="h-1.5 w-24 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-700"
                   :style="{ width: '60%', background: catColor(cat.id) }"/>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>

    <!-- Add Category Modal -->
    <Transition name="backdrop">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"/>
        <Transition name="modal">
          <div v-if="showModal"
            class="relative z-10 w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900
                   border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden">

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-100">New Category</h3>
              <button @click="showModal = false"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400
                       hover:text-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <div class="p-5 space-y-3.5">
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                  Name <span class="text-red-400">*</span>
                </label>
                <input v-model="form.name" type="text" placeholder="Category name"
                  class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                         border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                         focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
              </div>
              <p v-if="formError" class="text-xs text-red-500">{{ formError }}</p>
            </div>

            <div class="flex gap-2 px-5 pb-5">
              <button @click="showModal = false"
                class="flex-1 py-2 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-700
                       text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                Cancel
              </button>
              <button @click="saveCategory"
                class="flex-1 py-2 rounded-xl text-sm font-medium bg-emerald-600 hover:bg-emerald-700
                       text-white active:scale-95 transition-all">
                Create
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>

    <!-- Delete Confirm -->
    <Transition name="backdrop">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null"/>
        <Transition name="modal">
          <div v-if="deleteTarget"
            class="relative z-10 w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900
                   border border-gray-200 dark:border-gray-700 p-6 shadow-2xl">
            <div class="flex flex-col items-center text-center gap-3">
              <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <div>
                <p class="font-semibold text-gray-800 dark:text-gray-100">Delete Category?</p>
                <p class="text-sm text-gray-400 mt-1">
                  "<span class="font-medium text-gray-600 dark:text-gray-300">{{ deleteTarget.name }}</span>"
                  will be permanently removed.
                </p>
              </div>
              <div class="flex gap-2 w-full">
                <button @click="deleteTarget = null"
                  class="flex-1 py-2 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-700
                         text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                  Cancel
                </button>
                <button @click="doDelete"
                  class="flex-1 py-2 rounded-xl text-sm font-medium bg-red-600 hover:bg-red-700
                         text-white active:scale-95 transition-all">
                  Delete
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// categories from AdminView (real backend data)
const props = defineProps({
  categories: { type: Array, default: () => [] },
})

// @add(name) → AdminView calls api.post('/categories', { name })
// @delete(cat) → AdminView calls api.delete('/categories/:id')
const emit = defineEmits(['add', 'delete'])

const showModal  = ref(false)
const deleteTarget = ref(null)
const formError  = ref('')
const form       = ref({ name: '' })

function openAdd() {
  form.value = { name: '' }
  formError.value = ''
  showModal.value = true
}

function saveCategory() {
  if (!form.value.name.trim()) {
    formError.value = 'Name is required.'
    return
  }
  emit('add', form.value.name.trim())  // AdminView handles the API call
  showModal.value = false
}

function confirmDeleteCat(cat) {
  deleteTarget.value = cat
}

function doDelete() {
  emit('delete', deleteTarget.value)   // AdminView handles the API call
  deleteTarget.value = null
}

const palette = ['#6366f1','#8b5cf6','#0ea5e9','#10b981','#f59e0b','#ef4444','#14b8a6','#f97316']
function catColor(id) {
  return palette[id % palette.length]
}
</script>

<style scoped>
.list-enter-active, .list-leave-active { transition: all 0.25s ease; }
.list-enter-from { opacity: 0; transform: scale(0.95); }
.list-leave-to   { opacity: 0; transform: scale(0.95); }
.backdrop-enter-active, .backdrop-leave-active { transition: opacity 0.2s ease; }
.backdrop-enter-from, .backdrop-leave-to { opacity: 0; }
.modal-enter-active { transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-leave-active { transition: all 0.15s ease-in; }
.modal-enter-from   { opacity: 0; transform: scale(0.92) translateY(16px); }
.modal-leave-to     { opacity: 0; transform: scale(0.95); }
</style>