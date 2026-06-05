<template>
  <Teleport to="body">
    <Transition name="backdrop">
      <div v-if="show" class="fixed inset-0 z-[100] bg-black/40 backdrop-blur-md" @click="$emit('close')"></div>
    </Transition>

    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[101] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
        <div class="w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-[2rem] shadow-2xl border border-zinc-200/50 dark:border-zinc-800/50 pointer-events-auto overflow-hidden flex flex-col max-h-[90vh]">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 dark:border-zinc-800/50">
            <h2 class="text-xl font-display font-medium text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
              <SearchIcon class="w-5 h-5 text-primary-500" />
              Search & Scan
            </h2>
            <button @click="$emit('close')" class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
              <XIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Tabs -->
          <div class="flex items-center gap-4 px-6 pt-4 border-b border-zinc-100 dark:border-zinc-800/50 overflow-x-auto hide-scrollbar">
            <button @click="switchMode('text')"
              class="pb-3 text-sm font-medium transition-colors relative whitespace-nowrap"
              :class="mode === 'text' ? 'text-primary-600 dark:text-primary-400' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-300'">
              Text Search
              <div v-if="mode === 'text'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600 dark:bg-primary-400 rounded-t-full"></div>
            </button>
            <button @click="switchMode('image')"
              class="pb-3 text-sm font-medium transition-colors relative whitespace-nowrap"
              :class="mode === 'image' ? 'text-primary-600 dark:text-primary-400' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-300'">
              Upload Image (OCR)
              <div v-if="mode === 'image'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600 dark:bg-primary-400 rounded-t-full"></div>
            </button>
            <button @click="switchMode('barcode')"
              class="pb-3 text-sm font-medium transition-colors relative whitespace-nowrap"
              :class="mode === 'barcode' ? 'text-primary-600 dark:text-primary-400' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-300'">
              Scan Barcode
              <div v-if="mode === 'barcode'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600 dark:bg-primary-400 rounded-t-full"></div>
            </button>
          </div>

          <!-- Content Area -->
          <div class="p-6 overflow-y-auto">
            
            <!-- Text Search -->
            <div v-if="mode === 'text'" class="space-y-6">
              <div class="relative">
                <SearchIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" />
                <input v-model="searchQuery" @keyup.enter="executeSearch" type="text" placeholder="What are you looking for?"
                  class="w-full pl-12 pr-4 py-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-zinc-900 dark:text-zinc-50 transition-all text-lg placeholder:text-zinc-400"
                  autofocus />
              </div>
              <button @click="executeSearch" class="btn-primary w-full py-4 text-base">Search Products</button>
            </div>

            <!-- Image OCR Search -->
            <div v-else-if="mode === 'image'" class="space-y-6 text-center">
              <div v-if="ocrLoading" class="py-12 space-y-4">
                <div class="w-12 h-12 mx-auto border-4 border-zinc-200 dark:border-zinc-800 border-t-primary-500 rounded-full animate-spin"></div>
                <div>
                  <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-50">Analyzing image...</p>
                  <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Extracting text using AI</p>
                </div>
              </div>
              <div v-else class="py-8">
                <div class="w-20 h-20 mx-auto bg-primary-50 dark:bg-primary-500/10 rounded-full flex items-center justify-center mb-6">
                  <ImageIcon class="w-10 h-10 text-primary-500" />
                </div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-50 mb-2">Upload a Product Photo</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-8 max-w-sm mx-auto">Take a photo or upload an image of a product, and we'll automatically extract the text to search for it.</p>
                
                <label class="btn-primary px-8 py-4 text-sm cursor-pointer inline-flex items-center gap-2">
                  <UploadIcon class="w-4 h-4" />
                  <span>Choose Image</span>
                  <input type="file" accept="image/*" class="hidden" @change="handleImageUpload">
                </label>
              </div>
            </div>

            <!-- Barcode Scanner -->
            <div v-else-if="mode === 'barcode'" class="space-y-4">
              <div id="reader" class="w-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-black min-h-[300px]"></div>
              <p class="text-sm text-center text-zinc-500 dark:text-zinc-400">Point your camera at a barcode or QR code</p>
            </div>

          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, inject, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { Search as SearchIcon, X as XIcon, Image as ImageIcon, Upload as UploadIcon } from '@lucide/vue'
import { Html5QrcodeScanner } from 'html5-qrcode'
import Tesseract from 'tesseract.js'

const props = defineProps({ show: { type: Boolean, default: false } })
const emit = defineEmits(['close'])

const router = useRouter()
const toast = inject('toast')

const mode = ref('text')
const searchQuery = ref('')
const ocrLoading = ref(false)
let html5QrcodeScanner = null

// Watcher to clean up scanner when modal closes
watch(() => props.show, (newVal) => {
  if (newVal) {
    mode.value = 'text'
    searchQuery.value = ''
  } else {
    stopBarcodeScanner()
  }
})

function switchMode(newMode) {
  if (ocrLoading.value) return
  
  if (mode.value === 'barcode') {
    stopBarcodeScanner()
  }

  mode.value = newMode

  if (newMode === 'barcode') {
    nextTick(() => {
      startBarcodeScanner()
    })
  }
}

function startBarcodeScanner() {
  if (!html5QrcodeScanner) {
    html5QrcodeScanner = new Html5QrcodeScanner(
      "reader",
      { fps: 10, qrbox: { width: 250, height: 250 } },
      /* verbose= */ false
    )
  }
  html5QrcodeScanner.render(onBarcodeScanSuccess, onBarcodeScanFailure)
}

function stopBarcodeScanner() {
  if (html5QrcodeScanner) {
    try {
      html5QrcodeScanner.clear().catch(err => console.error("Scanner clear error", err))
    } catch (e) {
      console.error(e)
    }
  }
}

function onBarcodeScanSuccess(decodedText) {
  stopBarcodeScanner()
  emit('close')
  toast(`Scanned: ${decodedText}`, 'success')
  router.push({ path: '/shop', query: { search: decodedText } })
}

function onBarcodeScanFailure(error) {
  // Silent ignore for continuous scanning
}

async function handleImageUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  ocrLoading.value = true
  try {
    const result = await Tesseract.recognize(file, 'eng', {
      logger: m => console.log(m)
    })
    
    // Process text: remove symbols and extra spaces
    let text = result.data.text.replace(/[^a-zA-Z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim()
    
    if (text) {
      // Limit to 40 chars
      const finalSearch = text.substring(0, 40).trim()
      toast(`Found text: "${finalSearch}"`, 'success')
      emit('close')
      router.push({ path: '/shop', query: { search: finalSearch } })
    } else {
      toast('No readable text found on the image.', 'error')
    }
  } catch (e) {
    console.error('OCR Error:', e)
    toast('Failed to analyze the image.', 'error')
  } finally {
    ocrLoading.value = false
    event.target.value = '' // reset input
  }
}

function executeSearch() {
  if (!searchQuery.value.trim()) return
  emit('close')
  router.push({ path: '/shop', query: { search: searchQuery.value.trim() } })
}
</script>

<style>
/* Adjust Html5QrcodeScanner generated UI to match our theme */
#reader {
  border: none !important;
}
#reader button {
  background: var(--color-primary-600) !important;
  color: white !important;
  border-radius: 99px !important;
  padding: 0.5rem 1rem !important;
  font-weight: 500 !important;
  font-size: 0.875rem !important;
  border: none !important;
  margin: 0.5rem !important;
  cursor: pointer !important;
}
#reader a {
  display: none !important;
}
</style>
