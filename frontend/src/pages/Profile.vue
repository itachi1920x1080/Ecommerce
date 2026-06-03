<template>
  <div class="bg-surface dark:bg-surface-dark min-h-screen pt-20">
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="mb-10">
        <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-zinc-50 mb-2">My Profile</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Manage your account settings and addresses.</p>
      </div>

      <div class="space-y-8">
        <!-- Account Info -->
        <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm">
          <h2 class="text-xl font-medium text-zinc-900 dark:text-zinc-50 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center">
              <UserIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
            </div>
            Account Information
          </h2>
          
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="space-y-4 mb-6">
              <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Profile Photo</label>
              <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 overflow-hidden flex items-center justify-center">
                  <img v-if="avatarPreview || auth.user?.avatar" :src="avatarPreview || (auth.user.avatar.startsWith('http') ? auth.user.avatar : 'http://localhost:8000/storage/' + auth.user.avatar)" alt="Avatar" class="w-full h-full object-cover" />
                  <UserIcon v-else class="w-8 h-8 text-zinc-400" />
                </div>
                <div>
                  <input type="file" ref="avatarInput" @change="handleAvatarChange" accept="image/*" class="hidden" />
                  <button type="button" @click="$refs.avatarInput.click()" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-sm font-medium text-zinc-900 dark:text-zinc-50 rounded-xl transition-colors">
                    Change Photo
                  </button>
                </div>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Name</label>
                <input v-model="profileForm.name" type="text" required
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/50 dark:border-zinc-700/50 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50" />
              </div>
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Email</label>
                <input v-model="profileForm.email" type="email" required
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/50 dark:border-zinc-700/50 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50" />
              </div>
            </div>

            <div class="h-px bg-zinc-100 dark:bg-zinc-800/50 my-6"></div>

            <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-50 mb-4">Change Password</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">New Password</label>
                <input v-model="profileForm.password" type="password" placeholder="Leave blank to keep current"
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/50 dark:border-zinc-700/50 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
              </div>
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Confirm Password</label>
                <input v-model="profileForm.password_confirmation" type="password" placeholder="Repeat new password"
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/50 dark:border-zinc-700/50 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all text-zinc-900 dark:text-zinc-50 placeholder-zinc-400" />
              </div>
            </div>

            <div class="pt-2 flex justify-end">
              <button type="submit" :disabled="profileSaving"
                class="btn-primary px-8 py-3.5 text-sm flex items-center gap-2 disabled:opacity-50">
                <Loader2Icon v-if="profileSaving" class="w-4 h-4 animate-spin" />
                {{ profileSaving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Addresses -->
        <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm">
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-medium text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-500/10 flex items-center justify-center">
                <MapPinIcon class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
              My Addresses
            </h2>
            <button @click="showAddAddress = !showAddAddress"
              class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-colors flex items-center gap-1.5">
              <PlusIcon class="w-4 h-4" />
              Add Address
            </button>
          </div>

          <!-- Add Address Form -->
          <Transition name="fade">
            <form v-if="showAddAddress" @submit.prevent="addAddress" class="mb-8 p-6 bg-zinc-50 dark:bg-zinc-800/30 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input v-model="addressForm.receiver_name" type="text" placeholder="Receiver Name" required
                  class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm focus:outline-none focus:border-primary-500 transition-all text-zinc-900 dark:text-zinc-50" />
                <input v-model="addressForm.phone_number" type="text" placeholder="Phone Number" required
                  class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm focus:outline-none focus:border-primary-500 transition-all text-zinc-900 dark:text-zinc-50" />
              </div>
              <textarea v-model="addressForm.full_address" required placeholder="Full Address" rows="2"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm focus:outline-none focus:border-primary-500 transition-all text-zinc-900 dark:text-zinc-50 resize-none"></textarea>
              <div class="flex gap-3 justify-end pt-2">
                <button type="button" @click="showAddAddress = false" class="px-6 py-3 rounded-xl text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                <button type="submit" :disabled="addressSaving" class="btn-primary px-6 py-3 text-sm disabled:opacity-50">
                  {{ addressSaving ? 'Saving...' : 'Save Address' }}
                </button>
              </div>
            </form>
          </Transition>

          <!-- Address List -->
          <div v-if="addressesLoading" class="space-y-4">
            <div v-for="i in 2" :key="i" class="h-[88px] skeleton rounded-2xl"></div>
          </div>
          <div v-else-if="addresses.length" class="space-y-4">
            <div v-for="addr in addresses" :key="addr.id"
              class="flex items-center justify-between p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50 hover:border-primary-500/50 transition-colors group">
              <div>
                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                  {{ addr.receiver_name }}
                  <span v-if="addr.phone_number" class="text-xs text-zinc-500 dark:text-zinc-400 font-normal">{{ addr.phone_number }}</span>
                </p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ addr.full_address }}</p>
              </div>
              <button @click="deleteAddress(addr.id)" class="text-zinc-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors opacity-0 group-hover:opacity-100">
                <Trash2Icon class="w-5 h-5" />
              </button>
            </div>
          </div>
          <div v-else class="text-center py-10 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No addresses saved yet.</p>
          </div>
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
import { User as UserIcon, MapPin as MapPinIcon, Plus as PlusIcon, Trash2 as Trash2Icon, Loader2 as Loader2Icon } from '@lucide/vue'

const auth = useAuthStore()
const toast = inject('toast')

// Profile
const profileForm = ref({ name: auth.user?.name || '', email: auth.user?.email || '', password: '', password_confirmation: '' })
const profileSaving = ref(false)
const avatarFile = ref(null)
const avatarPreview = ref(null)

function handleAvatarChange(event) {
  const file = event.target.files[0]
  if (file) {
    avatarFile.value = file
    avatarPreview.value = URL.createObjectURL(file)
  }
}

async function updateProfile() {
  profileSaving.value = true
  try {
    const formData = new FormData()
    formData.append('name', profileForm.value.name)
    formData.append('email', profileForm.value.email)
    
    if (profileForm.value.password) {
      formData.append('password', profileForm.value.password)
      formData.append('password_confirmation', profileForm.value.password_confirmation)
    }

    if (avatarFile.value) {
      formData.append('avatar', avatarFile.value)
    }

    // Use POST with _method=PUT for file uploads in Laravel
    formData.append('_method', 'PUT')
    
    const res = await api.post('/user/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    auth.user = res.data.user
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
