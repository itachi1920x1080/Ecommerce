<template>
  <div class="rounded-2xl bg-white border border-slate-200/60 shadow-sm overflow-hidden">

    <!-- Header with Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-b border-slate-100">
      <div>
        <h3 class="text-base font-semibold text-slate-800">{{ title }}</h3>
        <p class="text-xs text-slate-400 mt-0.5">{{ filtered.length }} items</p>
      </div>
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
          v-model="search"
          type="text"
          placeholder="Search products..."
          class="pl-10 pr-4 py-2 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all w-64"
          id="product-table-search"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-6 space-y-3">
      <div v-for="i in 5" :key="i" class="h-14 skeleton rounded-xl"></div>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/50">
            <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Product</th>
            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Price</th>
            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Stock</th>
            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Category</th>
            <th class="text-right px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in paginated" :key="product.id"
            class="border-b border-slate-50 hover:bg-blue-50/30 transition-colors">
            <td class="px-6 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                  <img :src="getImage(product)" :alt="product.name" class="w-full h-full object-cover" @error="$event.target.src='https://placehold.co/40x40/e2e8f0/94a3b8?text=P'" />
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium text-slate-800 truncate max-w-[200px]">{{ product.name }}</p>
                  <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ product.description || 'No description' }}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3.5 font-semibold text-slate-700">${{ Number(product.price).toFixed(2) }}</td>
            <td class="px-4 py-3.5">
              <span :class="(product.stock ?? 0) <= 5 ? 'text-red-600 font-semibold' : 'text-slate-600'">
                {{ product.stock ?? 0 }}
              </span>
            </td>
            <td class="px-4 py-3.5">
              <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-600">
                {{ getCategoryName(product.category_id) }}
              </span>
            </td>
            <td class="px-6 py-3.5">
              <div class="flex items-center justify-end gap-1.5">
                <button @click="$emit('edit', product)"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button @click="$emit('delete', product)"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="paginated.length === 0">
            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-400">No products found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex items-center justify-between px-6 py-3.5 border-t border-slate-100">
      <p class="text-xs text-slate-400">Page {{ page }} of {{ totalPages }}</p>
      <div class="flex gap-1">
        <button v-for="p in totalPages" :key="p" @click="page = p"
          :class="p === page ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
          class="w-8 h-8 rounded-lg text-xs font-medium transition-colors">
          {{ p }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  products:   { type: Array,   default: () => [] },
  categories: { type: Array,   default: () => [] },
  loading:    { type: Boolean, default: false },
  title:      { type: String,  default: 'Products' }
})

defineEmits(['edit', 'delete'])

const search  = ref('')
const page    = ref(1)
const perPage = 10

function getImage(p) {
  return p.full_image_url || p.image || 'https://placehold.co/40x40/e2e8f0/94a3b8?text=P'
}

function getCategoryName(catId) {
  if (!catId) return '—'
  return props.categories.find(c => c.id === catId)?.name || '—'
}

const filtered = computed(() => {
  page.value = 1
  return props.products.filter(p =>
    p.name?.toLowerCase().includes(search.value.toLowerCase())
  )
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((page.value - 1) * perPage, page.value * perPage))
</script>
