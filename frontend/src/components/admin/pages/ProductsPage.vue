<template>
  <div class="p-6 space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Products</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ filtered.length }} items found</p>
      </div>
      <button @click="$emit('add')"
        class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
               bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white
               shadow-sm transition-all duration-150">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Product
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" type="text" placeholder="Search products..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm bg-white dark:bg-gray-900
                 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
      </div>
      <!-- Category filter — uses real categories from backend -->
      <select v-model="filterCat"
        class="px-3.5 py-2.5 rounded-xl text-sm bg-white dark:bg-gray-900
               border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200
               focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
        <option value="">All Categories</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <select v-model="filterStatus"
        class="px-3.5 py-2.5 rounded-xl text-sm bg-white dark:bg-gray-900
               border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200
               focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="draft">Draft</option>
      </select>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

      <!-- Loading -->
      <div v-if="products.length === 0 && isLoading" class="p-8 space-y-3">
        <div v-for="i in 5" :key="i" class="h-14 rounded-xl bg-gray-100 dark:bg-gray-800 animate-pulse"/>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Product</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Category</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Price</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Stock</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
              <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <TransitionGroup name="list">
              <tr v-for="product in paginated" :key="product.id"
                class="border-b border-gray-50 dark:border-gray-800/60
                       hover:bg-emerald-50/40 dark:hover:bg-emerald-900/10 transition-colors duration-150">

                <!-- Product -->
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden shrink-0 flex items-center justify-center">
                      <img v-if="product.image" :src="product.image" class="w-full h-full object-cover"
                           :alt="product.name" @error="product.image = ''"/>
                      <svg v-else class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">{{ product.name }}</p>
                      <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ product.description || 'No description' }}</p>
                    </div>
                  </div>
                </td>

                <!-- Category — lookup by category_id -->
                <td class="px-4 py-3.5">
                  <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                    {{ categoryName(product.category_id) }}
                  </span>
                </td>

                <!-- Price -->
                <td class="px-4 py-3.5 font-semibold text-gray-800 dark:text-gray-100">
                  ${{ Number(product.price).toFixed(2) }}
                </td>

                <!-- Stock -->
                <td class="px-4 py-3.5">
                  <span :class="(product.stock ?? 0) <= 10
                    ? 'text-red-600 dark:text-red-400 font-semibold'
                    : 'text-gray-700 dark:text-gray-300'">
                    {{ product.stock ?? '—' }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-4 py-3.5">
                  <span :class="statusClass(product.status || 'active')"
                    class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize">
                    {{ product.status || 'active' }}
                  </span>
                </td>

                <!-- Actions — emit to AdminView which calls api -->
                <td class="px-5 py-3.5">
                  <div class="flex items-center justify-end gap-1.5">
                    <button @click="$emit('edit', product)"
                      class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400
                             hover:text-emerald-600 hover:bg-emerald-50 dark:hover:text-emerald-400
                             dark:hover:bg-emerald-900/30 transition-all duration-150">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button @click="confirmDelete(product)"
                      class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400
                             hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400
                             dark:hover:bg-red-900/30 transition-all duration-150">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </TransitionGroup>

            <!-- Empty -->
            <tr v-if="paginated.length === 0">
              <td colspan="6" class="px-5 py-16 text-center">
                <div class="flex flex-col items-center gap-2">
                  <svg class="w-10 h-10 text-gray-200 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                  </svg>
                  <p class="text-sm text-gray-400">No products found</p>
                  <button @click="$emit('add')" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
                    Add one now
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1"
        class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs text-gray-400">Page {{ page }} of {{ totalPages }}</p>
        <div class="flex gap-1.5">
          <button v-for="p in totalPages" :key="p" @click="page = p"
            :class="p === page
              ? 'bg-emerald-600 text-white'
              : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'"
            class="w-7 h-7 rounded-lg text-xs font-medium transition-all">
            {{ p }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Dialog -->
    <Transition name="backdrop">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null"/>
        <Transition name="modal">
          <div v-if="deleteTarget"
            class="relative z-10 w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900
                   border border-gray-200 dark:border-gray-700 p-6 shadow-2xl">
            <div class="flex flex-col items-center text-center gap-3">
              <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <div>
                <p class="font-semibold text-gray-800 dark:text-gray-100">Delete Product?</p>
                <p class="text-sm text-gray-400 mt-1">
                  "<span class="font-medium text-gray-600 dark:text-gray-300">{{ deleteTarget.name }}</span>"
                  will be permanently removed.
                </p>
              </div>
              <div class="flex gap-2 w-full mt-1">
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
import { ref, computed } from 'vue'

// ALL data comes from AdminView — no local dummy data
const props = defineProps({
  products:   { type: Array,   default: () => [] },
  categories: { type: Array,   default: () => [] },
  isDark:     { type: Boolean, default: false },
  isLoading:  { type: Boolean, default: false },
})

// Emit to AdminView which handles the API calls
const emit = defineEmits(['delete', 'edit', 'add'])

const search       = ref('')
const filterCat    = ref('')
const filterStatus = ref('')
const page         = ref(1)
const perPage      = 10

// Lookup category name from categories array
function categoryName(category_id) {
  if (!category_id) return '—'
  const cat = props.categories.find(c => c.id === category_id)
  return cat?.name ?? '—'
}

const filtered = computed(() => {
  page.value = 1
  return props.products.filter(p => {
    const matchSearch = p.name?.toLowerCase().includes(search.value.toLowerCase())
    const matchCat    = !filterCat.value    || p.category_id == filterCat.value
    const matchStatus = !filterStatus.value || p.status       === filterStatus.value
    return matchSearch && matchCat && matchStatus
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((page.value - 1) * perPage, page.value * perPage))

// Delete: confirm locally, then emit to parent which calls api.delete
const deleteTarget = ref(null)
function confirmDelete(p) { deleteTarget.value = p }
function doDelete() {
  emit('delete', deleteTarget.value)
  deleteTarget.value = null
}

function statusClass(status) {
  return {
    active:   'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
    inactive: 'bg-gray-100   dark:bg-gray-800        text-gray-500   dark:text-gray-400',
    draft:    'bg-amber-100  dark:bg-amber-900/40    text-amber-700  dark:text-amber-300',
  }[status] ?? 'bg-gray-100 text-gray-600'
}
</script>

<style scoped>
.list-enter-active, .list-leave-active { transition: all 0.25s ease; }
.list-enter-from { opacity: 0; transform: translateX(-10px); }
.list-leave-to   { opacity: 0; transform: translateX(10px); }
.backdrop-enter-active, .backdrop-leave-active { transition: opacity 0.2s ease; }
.backdrop-enter-from, .backdrop-leave-to { opacity: 0; }
.modal-enter-active { transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-leave-active { transition: all 0.15s ease-in; }
.modal-enter-from   { opacity: 0; transform: scale(0.92) translateY(16px); }
.modal-leave-to     { opacity: 0; transform: scale(0.95); }
</style>