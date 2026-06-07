<template>
  <Teleport to="body">
    <Transition name="backdrop">
      <div v-if="editing !== undefined || showAlways"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="$emit('close')">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$emit('close')" />

        <Transition name="modal">
          <div class="relative z-10 w-full max-w-lg rounded-3xl shadow-2xl
                      bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                  <EditIcon v-if="isEdit" class="w-4 h-4 text-zinc-900 dark:text-zinc-100 stroke-[2]" />
                  <PlusIcon v-else class="w-4 h-4 text-zinc-900 dark:text-zinc-100 stroke-[2]" />
                </div>
                <h2 class="text-lg font-display font-medium text-zinc-900 dark:text-white">
                  {{ isEdit ? 'Edit Product' : 'Add New Product' }}
                </h2>
              </div>
              <button @click="$emit('close')"
                class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-400
                       hover:text-zinc-600 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <XIcon class="w-5 h-5 stroke-[2]" />
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-6 space-y-5 max-h-[70vh] overflow-y-auto">

              <!-- Name -->
              <div>
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">
                  Product Name <span class="text-red-500">*</span>
                </label>
                <input v-model="form.name" type="text" placeholder="e.g. Wireless Headphones"
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                         border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                         placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all"/>
              </div>

              <!-- Category + Status -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Category</label>
                  <select v-model="form.category_id"
                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                           border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                           focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all cursor-pointer">
                    <option value="">Select...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Status</label>
                  <select v-model="form.status"
                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                           border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                           focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all cursor-pointer">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">If Out of Stock</label>
                  <select v-model="form.out_of_stock_status"
                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                           border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                           focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all cursor-pointer">
                    <option value="show">Show as Out of Stock</option>
                    <option value="hide">Hide Product</option>
                    <option value="preorder">Allow Pre-order</option>
                  </select>
                </div>
              </div>

              <!-- Price + Discount + Stock -->
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">
                    Price <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400">$</span>
                    <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="0.00"
                      class="w-full pl-8 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                             border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                             placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all"/>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Discount</label>
                  <div class="relative">
                    <input v-model.number="form.discount_percent" type="number" min="0" max="100" placeholder="0"
                      class="w-full pl-4 pr-8 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                             border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                             placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all"/>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400">%</span>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Stock Qty</label>
                  <input v-model.number="form.stock" type="number" min="0" placeholder="0"
                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950
                           border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                           placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all"/>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Description</label>
                <textarea v-model="form.description" rows="3" placeholder="Short product description..."
                  class="w-full px-4 py-3 rounded-xl resize-none bg-zinc-50 dark:bg-zinc-950
                         border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white
                         placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 transition-all"/>
              </div>

              <!-- Image Upload -->
              <div>
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-300 mb-2">Product Image</label>
                <div class="flex gap-3 items-center">
                  <input type="file" accept="image/*" @change="handleImageUpload"
                    class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-full file:border-0
                      file:text-xs file:font-semibold
                      file:bg-zinc-100 file:text-zinc-900 dark:file:bg-zinc-800 dark:file:text-white
                      hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700
                      cursor-pointer focus:outline-none" />
                  <div class="w-12 h-12 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center justify-center shrink-0">
                    <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" @error="imagePreview = ''" alt="preview"/>
                    <ImageIcon v-else class="w-5 h-5 text-zinc-300 dark:text-zinc-600" />
                  </div>
                </div>
              </div>

              <!-- Error from parent (formError) -->
              <Transition name="fade">
                <p v-if="formError || localError"
                  class="text-sm text-red-500 dark:text-red-400 flex items-center gap-2">
                  <AlertCircleIcon class="w-4 h-4 shrink-0" />
                  {{ formError || localError }}
                </p>
              </Transition>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50">
              <button @click="$emit('close')"
                class="px-6 py-3 rounded-full text-xs font-semibold uppercase tracking-widest text-zinc-600 dark:text-zinc-300
                       bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                       hover:bg-zinc-100 dark:hover:bg-zinc-700 active:scale-95 transition-all">
                Cancel
              </button>
              <button @click="handleSubmit" :disabled="saving"
                class="btn-primary px-8 py-3 text-xs flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <Loader2Icon v-if="saving" class="w-4 h-4 animate-spin" />
                <CheckIcon v-else-if="isEdit" class="w-4 h-4" />
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
import { Edit as EditIcon, Plus as PlusIcon, X as XIcon, Image as ImageIcon, AlertCircle as AlertCircleIcon, Loader2 as Loader2Icon, Check as CheckIcon } from '@lucide/vue'
import { getStorageUrl } from '@/api/axios.js'

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
  name: '', category_id: '', status: 'active', out_of_stock_status: 'show',
  price: '', discount_percent: '', stock: '', description: '', image: '',
})

const form = ref(defaultForm())
const imagePreview = ref('')

function handleImageUpload(e) {
  const file = e.target.files[0]
  if (file) {
    form.value.image = file
    imagePreview.value = URL.createObjectURL(file)
  } else {
    form.value.image = ''
    imagePreview.value = ''
  }
}

// Populate form when editing prop changes
watch(() => props.editing, (val) => {
  localError.value = ''
  if (val) {
    form.value = {
      name:        val.name        ?? '',
      category_id: val.category_id ?? '',
      status:      val.status      ?? 'active',
      out_of_stock_status: val.out_of_stock_status ?? 'show',
      price:       val.price       ?? '',
      discount_percent: val.discount_percent ?? '',
      stock:       val.stock       ?? '',
      description: val.description ?? '',
      image:       val.image       ?? '',
    }
    imagePreview.value = val.image_url ? getStorageUrl(val.image_url) : (val.full_image_url || val.image || '')
  } else {
    form.value = defaultForm()
    imagePreview.value = ''
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