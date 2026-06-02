<template>
  <div class="p-6 space-y-5 max-w-2xl">
    <div>
      <h2 class="text-lg font-bold text-gray-900 dark:text-white">Settings</h2>
      <p class="text-xs text-gray-400 mt-0.5">Manage your store preferences</p>
    </div>

    <!-- Profile — shows real user from backend -->
    <section class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
      <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Profile</h3>
      <div class="flex items-center gap-4 mb-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600
                    flex items-center justify-center text-white text-xl font-bold shrink-0">
          {{ userInitials }}
        </div>
        <div>
          <p class="font-semibold text-gray-800 dark:text-gray-100">{{ currentUser?.name || 'Admin' }}</p>
          <p class="text-xs text-gray-400">{{ currentUser?.role || 'admin' }}</p>
          <p class="text-xs text-gray-400">{{ currentUser?.email || '' }}</p>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Full Name</label>
          <input v-model="profile.name" type="text"
            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Email</label>
          <input v-model="profile.email" type="email"
            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
        </div>
      </div>
    </section>

    <!-- Store Info -->
    <section class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
      <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Store Information</h3>
      <div class="space-y-3">
        <div>
          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Store Name</label>
          <input v-model="store.name" type="text"
            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                   border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"/>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Currency</label>
            <select v-model="store.currency"
              class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                     border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                     focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
              <option value="USD">USD ($)</option>
              <option value="KHR">KHR (៛)</option>
              <option value="THB">THB (฿)</option>
              <option value="SGD">SGD (S$)</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Timezone</label>
            <select v-model="store.timezone"
              class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-gray-50 dark:bg-gray-800
                     border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100
                     focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all cursor-pointer">
              <option value="Asia/Phnom_Penh">Asia/Phnom Penh</option>
              <option value="Asia/Bangkok">Asia/Bangkok</option>
              <option value="Asia/Singapore">Asia/Singapore</option>
              <option value="UTC">UTC</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <!-- Appearance -->
    <section class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
      <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">Appearance</h3>
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Dark Mode</p>
            <p class="text-xs text-gray-400">Switch between light and dark theme</p>
          </div>
          <button @click="$emit('toggleDark')"
            :class="isDark ? 'bg-emerald-600' : 'bg-gray-200 dark:bg-gray-700'"
            class="relative w-11 h-6 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            <span :class="isDark ? 'translate-x-5' : 'translate-x-1'"
              class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-300"/>
          </button>
        </div>

        <div v-for="item in toggles" :key="item.key" class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ item.label }}</p>
            <p class="text-xs text-gray-400">{{ item.desc }}</p>
          </div>
          <button @click="item.value = !item.value"
            :class="item.value ? 'bg-emerald-600' : 'bg-gray-200 dark:bg-gray-700'"
            class="relative w-11 h-6 rounded-full transition-colors duration-300 focus:outline-none">
            <span :class="item.value ? 'translate-x-5' : 'translate-x-1'"
              class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-300"/>
          </button>
        </div>
      </div>
    </section>

    <!-- Danger Zone -->
    <section class="rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-900/10 p-5">
      <h3 class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">Danger Zone</h3>
      <p class="text-xs text-red-500 dark:text-red-400/80 mb-3">These actions are irreversible. Please be careful.</p>
      <div class="flex gap-2 flex-wrap">
        <button class="px-4 py-2 rounded-xl text-xs font-medium border border-red-300 dark:border-red-700
                       text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all active:scale-95">
          Clear All Orders
        </button>
        <button class="px-4 py-2 rounded-xl text-xs font-medium bg-red-600 hover:bg-red-700 text-white transition-all active:scale-95">
          Reset Store Data
        </button>
      </div>
    </section>

    <!-- Save -->
    <div class="flex justify-end">
      <button @click="save"
        :class="saved ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-emerald-600 hover:bg-emerald-700'"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white
               active:scale-95 transition-all duration-200 shadow-sm min-w-[130px] justify-center">
        <svg v-if="saved" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ saved ? 'Saved!' : 'Save Changes' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isDark:      { type: Boolean, default: false },
  currentUser: { type: Object,  default: null },
})

defineEmits(['toggleDark'])

const saved = ref(false)

// Populate from real currentUser prop
const profile = ref({
  name:  props.currentUser?.name  || '',
  email: props.currentUser?.email || '',
})

watch(() => props.currentUser, (val) => {
  if (val) {
    profile.value.name  = val.name  || ''
    profile.value.email = val.email || ''
  }
}, { immediate: true })

const store = ref({ name: 'My Store', currency: 'USD', timezone: 'Asia/Phnom_Penh' })

const toggles = ref([
  { key: 'emailNotif', label: 'Email Notifications', desc: 'Receive order alerts via email',    value: true },
  { key: 'orderSound', label: 'Order Sound Alert',   desc: 'Play sound on new orders',          value: false },
  { key: 'autoBackup', label: 'Auto Backup',         desc: 'Back up data daily automatically', value: true },
])

const userInitials = computed(() =>
  (props.currentUser?.name || 'AD').split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
)

function save() {
  saved.value = true
  setTimeout(() => { saved.value = false }, 2000)
}
</script>