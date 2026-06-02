<template>
  <Teleport to="body">
    <Transition name="backdrop">
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>

        <Transition name="modal">
          <div v-if="show" class="relative z-10 w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
                  <svg class="w-4.5 h-4.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path v-if="editing" stroke-linecap="round" stroke-linejoin="round"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
                <h2 class="text-base font-semibold text-slate-800">{{ editing ? 'Edit Product' : 'Add New Product' }}</h2>
              </div>
              <button @click="$emit('close')" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

              <!-- Name -->
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Product Name <span class="text-red-400">*</span></label>
                <input v-model="form.name" type="text" placeholder="e.g. Wireless Headphones"
                  class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                  id="product-name-input" />
              </div>

              <!-- Price + Stock -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-slate-500 mb-1.5">Price ($) <span class="text-red-400">*</span></label>
                  <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="0.00"
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                    id="product-price-input" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-slate-500 mb-1.5">Stock</label>
                  <input v-model.number="form.stock" type="number" min="0" placeholder="0"
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                    id="product-stock-input" />
                </div>
              </div>

              <!-- Category -->
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Category</label>
                <select v-model="form.category_id"
                  class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all cursor-pointer"
                  id="product-category-select">
                  <option value="">Select category...</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Description</label>
                <textarea v-model="form.description" rows="3" placeholder="Product description..."
                  class="w-full px-3.5 py-2.5 rounded-xl text-sm resize-none bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                  id="product-description-input"></textarea>
              </div>

              <!-- Image Upload -->
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Product Image</label>
                <div class="flex items-center gap-3">
                  <label class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-400 bg-slate-50 cursor-pointer transition-colors">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm text-slate-500">{{ form.image ? 'Change image' : 'Upload image' }}</span>
                    <input type="file" accept="image/*" @change="handleImage" class="hidden" id="product-image-input" />
                  </label>
                  <!-- Preview -->
                  <div v-if="imagePreview" class="w-14 h-14 rounded-xl border border-slate-200 overflow-hidden shrink-0">
                    <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover" />
                  </div>
                </div>
              </div>

              <!-- Error -->
              <p v-if="error" class="text-xs text-red-500 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ error }}
              </p>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50">
              <button @click="$emit('close')"
                class="flex-1 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors">
                Cancel
              </button>
              <button @click="handleSubmit" :disabled="saving"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2">
                <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                {{ saving ? 'Saving...' : editing ? 'Save Changes' : 'Add Product' }}
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

const props = defineProps({
  show:       { type: Boolean, default: false },
  editing:    { type: Object,  default: null },
  categories: { type: Array,  default: () => [] },
  saving:     { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'submit'])

const error        = ref('')
const imagePreview = ref('')
const imageFile    = ref(null)

const defaultForm = () => ({
  name: '', price: '', stock: '', description: '', category_id: '', image: null
})

const form = ref(defaultForm())

// Populate form when editing
watch(() => props.editing, (val) => {
  error.value = ''
  imageFile.value = null
  if (val) {
    form.value = {
      name: val.name || '',
      price: val.price || '',
      stock: val.stock || '',
      description: val.description || '',
      category_id: val.category_id || '',
      image: null
    }
    imagePreview.value = val.full_image_url || val.image || ''
  } else {
    form.value = defaultForm()
    imagePreview.value = ''
  }
}, { immediate: true })

function handleImage(e) {
  const file = e.target.files[0]
  if (!file) return
  imageFile.value = file
  form.value.image = file
  // Create preview URL
  const reader = new FileReader()
  reader.onload = (ev) => { imagePreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function handleSubmit() {
  error.value = ''
  if (!form.value.name.trim()) {
    error.value = 'Product name is required.'
    return
  }
  if (!form.value.price || Number(form.value.price) <= 0) {
    error.value = 'Please enter a valid price.'
    return
  }

  // Build FormData for file upload
  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('price', form.value.price)
  formData.append('stock', form.value.stock || 0)
  formData.append('description', form.value.description || '')
  if (form.value.category_id) formData.append('category_id', form.value.category_id)
  if (imageFile.value) formData.append('image', imageFile.value)

  emit('submit', formData)
}
</script>
