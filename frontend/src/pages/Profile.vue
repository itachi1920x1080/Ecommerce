<template>
  <div>
    <section class="py-16 px-6 bg-white min-h-[70vh]">
      <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">My Profile</h1>
        <p class="text-sm text-slate-400 mb-10">Manage your account settings and addresses</p>

        <!-- Profile Form -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-8">
          <h2 class="text-base font-semibold text-slate-800 mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Account Information
          </h2>
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Name</label>
                <input v-model="profileForm.name" type="text" required
                  class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Email</label>
                <input v-model="profileForm.email" type="email" required
                  class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">New Password <span class="text-slate-300">(optional)</span></label>
                <input v-model="profileForm.password" type="password" placeholder="Leave blank to keep current"
                  class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Confirm Password</label>
                <input v-model="profileForm.password_confirmation" type="password" placeholder="Repeat new password"
                  class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
              </div>
            </div>
            <button type="submit" :disabled="profileSaving"
              class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 flex items-center gap-2">
              <svg v-if="profileSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
              {{ profileSaving ? 'Saving...' : 'Save Changes' }}
            </button>
          </form>
        </div>

        <!-- Addresses -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-semibold text-slate-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              My Addresses
            </h2>
            <button @click="showAddAddress = !showAddAddress"
              class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
              Add Address
            </button>
          </div>

          <!-- Add Address Form -->
          <Transition name="slide">
            <form v-if="showAddAddress" @submit.prevent="addAddress" class="mb-5 p-4 bg-slate-50 rounded-xl space-y-3">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input v-model="addressForm.receiver_name" type="text" placeholder="Receiver Name" required
                  class="w-full px-3 py-2.5 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all" />
                <input v-model="addressForm.phone_number" type="text" placeholder="Phone number" required
                  class="w-full px-3 py-2.5 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all" />
              </div>
              <input v-model="addressForm.full_address" type="text" required placeholder="Full address"
                class="w-full px-3 py-2.5 rounded-xl text-sm bg-white border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-all" />
              <div class="flex gap-2">
                <button type="submit" :disabled="addressSaving"
                  class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors disabled:opacity-50">
                  {{ addressSaving ? 'Saving...' : 'Save Address' }}
                </button>
                <button type="button" @click="showAddAddress = false" class="px-5 py-2.5 bg-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-300 transition-colors">Cancel</button>
              </div>
            </form>
          </Transition>

          <!-- Address List -->
          <div v-if="addressesLoading" class="space-y-3">
            <div v-for="i in 2" :key="i" class="h-16 skeleton rounded-xl"></div>
          </div>
          <div v-else-if="addresses.length" class="space-y-3">
            <div v-for="addr in addresses" :key="addr.id"
              class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-primary-200/50 transition-all duration-300">
              <div>
                <p class="text-sm font-semibold text-slate-700">{{ addr.receiver_name }} <span v-if="addr.phone_number" class="text-xs text-slate-400 ml-2">{{ addr.phone_number }}</span></p>
                <p class="text-sm text-slate-400 mt-0.5">{{ addr.full_address }}</p>
              </div>
              <button @click="deleteAddress(addr.id)" class="text-red-500 hover:text-red-600 hover:bg-red-50 p-2 rounded-xl transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
          <p v-else class="text-sm text-slate-400 text-center py-6">No addresses saved yet</p>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { useAuthStore } from '@/stores/auth'
import Footer from '@/components/shop/Footer.vue'

const auth = useAuthStore()
const toast = inject('toast')

// Profile
const profileForm = ref({ name: auth.user?.name || '', email: auth.user?.email || '', password: '', password_confirmation: '' })
const profileSaving = ref(false)

async function updateProfile() {
  profileSaving.value = true
  try {
    const payload = { name: profileForm.value.name, email: profileForm.value.email }
    if (profileForm.value.password) {
      payload.password = profileForm.value.password
      payload.password_confirmation = profileForm.value.password_confirmation
    }
    await api.put('/user/profile', payload)
    auth.user = { ...auth.user, name: payload.name, email: payload.email }
    localStorage.setItem('user', JSON.stringify(auth.user))
    profileForm.value.password = ''
    profileForm.value.password_confirmation = ''
    toast('Profile updated!', 'success')
  } catch (e) { toast(e.response?.data?.message || 'Failed to update profile', 'error') }
  finally { profileSaving.value = false }
}

// Addresses
const addresses = ref([])
const addressesLoading = ref(true)
const showAddAddress = ref(false)
const addressSaving = ref(false)
const addressForm = ref({ receiver_name: '', phone_number: '', full_address: '' })

async function fetchAddresses() {
  addressesLoading.value = true
  try {
    const res = await api.get('/addresses')
    addresses.value = res.data.data || res.data || []
  } catch (e) { toast('Failed to load addresses', 'error') }
  finally { addressesLoading.value = false }
}

async function addAddress() {
  addressSaving.value = true
  try {
    const res = await api.post('/addresses', addressForm.value)
    addresses.value.push(res.data.data || res.data.address || res.data)
    addressForm.value = { receiver_name: '', phone_number: '', full_address: '' }
    showAddAddress.value = false
    toast('Address added!', 'success')
  } catch (e) { toast(e.response?.data?.message || 'Failed to add address', 'error') }
  finally { addressSaving.value = false }
}

async function deleteAddress(id) {
  try {
    await api.delete(`/addresses/${id}`)
    addresses.value = addresses.value.filter(a => a.id !== id)
    toast('Address deleted', 'info')
  } catch (e) { toast('Failed to delete address', 'error') }
}

onMounted(fetchAddresses)
</script>
