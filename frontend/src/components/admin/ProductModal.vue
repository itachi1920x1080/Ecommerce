<template>
  <Teleport to="body">
    <Transition name="backdrop">
      <div v-if="editing !== undefined || showAlways"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="$emit('close')">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')" />

        <Transition name="modal">
          <div class="relative z-10 w-full max-w-lg rounded-2xl shadow-2xl
                      bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                  <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="isEdit" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                  {{ isEdit ? 'Edit Product' : 'Add New Product' }}
                </h2>
              </div>
              <button @click="$emit('close')"
                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400
                       hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

              <!-- Name -->
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                  Product Name <span class="text-red-400">*</span>
                </label>
                <input v-model="form.name" type="text" placeholder="e.g. Wireless Headphones"
                  class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                         border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                         placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50
                         focus:border-emerald-400 transition-all"/>
              </div>

              <!-- Category + Status -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Category</label>
                  <select v-model="form.category_id"
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                           border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                           focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
                    <option value="">Select...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                  <select v-model="form.status"
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                           border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                           focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                  </select>
                </div>
              </div>

              <!-- Price + Discount + Stock -->
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                    Price ($) <span class="text-red-400">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                    <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="0.00"
                      class="w-full pl-7 pr-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                             border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                             placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Discount (%)</label>
                  <div class="relative">
                    <input v-model.number="form.discount_percent" type="number" min="0" max="100" placeholder="0"
                      class="w-full pl-3.5 pr-7 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                             border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                             placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Stock Qty</label>
                  <input v-model.number="form.stock" type="number" min="0" placeholder="0"
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                           border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                           placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Description</label>
                <textarea v-model="form.description" rows="3" placeholder="Short product description..."
                  class="w-full px-3.5 py-2.5 rounded-xl text-sm resize-none bg-gray-50 dark:bg-gray-800
                         border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                         placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
              </div>

              <!-- Image URL -->
              <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Image URL</label>
                <div class="flex gap-2">
                  <input v-model="form.image" type="url" placeholder="https://..."
                    class="flex-1 px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                           border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                           placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
                  <div class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 overflow-hidden flex items-center justify-center shrink-0">
                    <img v-if="form.image" :src="form.image" class="w-full h-full object-cover" @error="form.image = ''" alt="preview"/>
                    <svg v-else class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Error from parent (formError) -->
              <Transition name="fade">
                <p v-if="formError || localError"
                  class="text-xs text-red-500 dark:text-red-400 flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ formError || localError }}
                </p>
              </Transition>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <button @click="$emit('close')"
                class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300
                       bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600
                       hover:bg-gray-100 dark:hover:bg-gray-600 active:scale-95 transition-all">
                Cancel
              </button>
              <button @click="handleSubmit" :disabled="saving"
                class="px-5 py-2 rounded-xl text-sm font-medium bg-emerald-600 hover:bg-emerald-700
                       text-white shadow-sm active:scale-95 transition-all disabled:opacity-60
                       disabled:cursor-not-allowed flex items-center gap-2">
                <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <svg v-else-if="isEdit" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ saving ? 'Saving...' : isEdit ? 'Save Changes' : 'Add Product' }}
              </button>
            </div>

          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

// Props exactly matching what AdminView passes:
// :editing="editingProduct"  :categories="categories"  :saving="saving"  :formError="formError"
// @close  @submit
const props = defineProps({
  editing:   { type: Object,  default: null },   // null = add mode, object = edit mode
  categories:{ type: Array,   default: () => [] },
  saving:    { type: Boolean, default: false },
  formError: { type: String,  default: '' },
})

const emit = defineEmits(['close', 'submit'])

const localError  = ref('')
const showAlways  = ref(true) // modal is always mounted when v-if="showProductModal" in parent

const isEdit = computed(() => !!props.editing?.id)

const defaultForm = () => ({
  name: '', category_id: '', status: 'active',
  price: '', discount_percent: '', stock: '', description: '', image: '',
})

const form = ref(defaultForm())

// Populate form when editing prop changes
watch(() => props.editing, (val) => {
  localError.value = ''
  if (val) {
    form.value = {
      name:        val.name        ?? '',
      category_id: val.category_id ?? '',
      status:      val.status      ?? 'active',
      price:       val.price       ?? '',
      discount_percent: val.discount_percent ?? '',
      stock:       val.stock       ?? '',
      description: val.description ?? '',
      image:       val.image       ?? '',
    }
  } else {
    form.value = defaultForm()
  }
}, { immediate: true })

// Clear local error when form changes
watch(form, () => { localError.value = '' }, { deep: true })

function handleSubmit() {
  localError.value = ''
  if (!form.value.name.trim()) {
    localError.value = 'Product name is required.'
    return
  }
  if (!form.value.price || Number(form.value.price) <= 0) {
    localError.value = 'Please enter a valid price.'
    return
  }
  // Emit to AdminView which calls api.post / api.put
  emit('submit', { ...form.value })
}
</script>

<style scoped>
.backdrop-enter-active, .backdrop-leave-active { transition: opacity 0.2s ease; }
.backdrop-enter-from, .backdrop-leave-to { opacity: 0; }
.modal-enter-active { transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-leave-active { transition: all 0.15s ease-in; }
.modal-enter-from { opacity: 0; transform: scale(0.92) translateY(16px); }
.modal-leave-to { opacity: 0; transform: scale(0.95) translateY(8px); }
.fade-enter-active, .fade-leave-active { transition: all 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-4px); }
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>