<template>
  <div class="p-6 sm:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Coupons</h1>
        <p class="text-sm text-slate-400 mt-1">Manage discount coupons</p>
      </div>
      <button @click="showModal = true"
        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all active:scale-95">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Coupon
      </button>
    </div>

    <!-- Coupon Table -->
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
      <div v-if="loading" class="p-6 space-y-3">
        <div v-for="i in 3" :key="i" class="h-14 skeleton rounded-xl"></div>
      </div>
      <table v-else class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500 tracking-wider">
          <tr>
            <th class="px-5 py-3">Code</th>
            <th class="px-5 py-3">Discount</th>
            <th class="px-5 py-3">Expiry Date</th>
            <th class="px-5 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="c in coupons" :key="c.id" class="hover:bg-slate-50 transition-colors">
            <td class="px-5 py-3 font-bold text-slate-700">{{ c.code }}</td>
            <td class="px-5 py-3 text-slate-600">{{ c.discount_amount ? '$' + Number(c.discount_amount).toFixed(2) : Number(c.discount_percentage) + '%' }}</td>
            <td class="px-5 py-3 text-slate-600">
              <span :class="isExpired(c.expires_at) ? 'text-red-500 font-semibold' : 'text-slate-500'">
                {{ formatDate(c.expires_at) }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <button @click="handleDelete(c)" class="text-red-600 hover:underline text-xs font-medium">Delete</button>
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
              <h3 class="text-lg font-bold text-slate-800 mb-4">Add Coupon</h3>
              <form @submit.prevent="handleSubmit">
                <div class="space-y-4 mb-4">
                  <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Coupon Code</label>
                    <input v-model="form.code" type="text" required placeholder="e.g. SUMMER20"
                      class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 uppercase transition-all" />
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-slate-500 mb-1.5">Amount ($)</label>
                      <input v-model="form.discount_amount" type="number" step="0.01" placeholder="0.00"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all" />
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-500 mb-1.5">Percent (%)</label>
                      <input v-model="form.discount_percentage" type="number" step="1" max="100" placeholder="0"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Expiry Date</label>
                    <input v-model="form.expires_at" type="date" required
                      class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all" />
                  </div>
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
              <p class="text-base font-semibold text-slate-800">Delete Coupon?</p>
              <p class="text-sm text-slate-400 mt-1">"{{ deleteTarget.code }}" will be permanently deleted.</p>
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

const coupons = ref([])
const loading = ref(true)
const showModal = ref(false)
const saving = ref(false)
const form = ref({ code: '', discount_amount: '', discount_percentage: '', expires_at: '' })
const deleteTarget = ref(null)

async function fetchCoupons() {
  loading.value = true
  try {
    const res = await api.get('/coupons')
    coupons.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load coupons', 'error')
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  saving.value = true
  try {
    const payload = { ...form.value, code: form.value.code.toUpperCase() }
    if (!payload.discount_amount) delete payload.discount_amount
    if (!payload.discount_percentage) delete payload.discount_percentage
    
    const res = await api.post('/coupons', payload)
    coupons.value.push(res.data.data || res.data)
    showModal.value = false
    form.value = { code: '', discount_amount: '', discount_percentage: '', expires_at: '' }
    toast('Coupon created', 'success')
  } catch (e) {
    toast(e.response?.data?.message || 'Failed to create coupon', 'error')
  } finally {
    saving.value = false
  }
}

function handleDelete(c) {
  deleteTarget.value = c
}

async function confirmDelete() {
  try {
    await api.delete(`/coupons/${deleteTarget.value.id}`)
    coupons.value = coupons.value.filter(c => c.id !== deleteTarget.value.id)
    toast('Coupon deleted', 'success')
  } catch (e) {
    toast('Failed to delete coupon', 'error')
  } finally {
    deleteTarget.value = null
  }
}

function isExpired(dateStr) {
  if (!dateStr) return false
  return new Date(dateStr) < new Date()
}

function formatDate(dateStr) {
  if (!dateStr) return 'No expiry'
  return new Date(dateStr).toLocaleDateString()
}

onMounted(fetchCoupons)
</script>
