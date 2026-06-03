<template>
  <div class="p-6 sm:p-8 space-y-8 bg-white dark:bg-zinc-950 min-h-[calc(100vh-64px)] transition-colors">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-display font-medium text-zinc-900 dark:text-white">Users</h1>
      <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">Manage registered user accounts</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 6" :key="i" class="h-16 skeleton rounded-xl"></div>
    </div>

    <!-- Users Table -->
    <div v-else class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
      <!-- Search -->
      <div class="px-8 py-6 border-b border-zinc-200 dark:border-zinc-800">
        <div class="relative max-w-xs">
          <SearchIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
          <input v-model="search" type="text" placeholder="Search users..."
            class="w-full pl-11 pr-5 py-3 rounded-xl text-sm bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 text-zinc-900 dark:text-white placeholder:text-zinc-400 transition-all"
            id="admin-users-search" />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50">
              <th class="text-left px-8 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">User</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Email</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Role</th>
              <th class="text-left px-4 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Joined</th>
              <th class="text-right px-8 py-5 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
            <tr v-for="user in filtered" :key="user.id"
              class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-900 dark:text-white text-xs font-display font-medium shrink-0">
                    {{ (user.name || 'U').slice(0,2).toUpperCase() }}
                  </div>
                  <span class="font-medium text-zinc-900 dark:text-zinc-50">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-4 py-5 font-medium text-zinc-500 dark:text-zinc-400">{{ user.email }}</td>
              <td class="px-4 py-5">
                <!-- Editable role dropdown -->
                <select
                  :value="user.role"
                  @change="updateRole(user, $event.target.value)"
                  class="px-4 py-2.5 rounded-full text-[9px] font-semibold uppercase tracking-widest bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:focus:ring-zinc-800 cursor-pointer transition-all appearance-none"
                  :class="user.role === 'admin' ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'"
                  :id="`user-role-${user.id}`"
                >
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                  <option value="driver">Driver</option>
                </select>
              </td>
              <td class="px-4 py-5 text-[10px] font-semibold tracking-widest uppercase text-zinc-500 dark:text-zinc-400">{{ formatDate(user.created_at) }}</td>
              <td class="px-8 py-5">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="deleteTarget = user"
                    class="w-10 h-10 rounded-full flex items-center justify-center text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                    :id="`delete-user-${user.id}`">
                    <TrashIcon class="w-4 h-4 stroke-[1.5]" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filtered.length === 0">
              <td colspan="5" class="px-8 py-20 text-center text-xs font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">No users found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteTarget = null"></div>
          <Transition name="modal">
            <div v-if="deleteTarget" class="relative z-10 w-full max-w-sm rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 shadow-2xl">
              <div class="flex flex-col items-center text-center gap-4">
                <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                  <TrashIcon class="w-8 h-8 text-red-500 stroke-[1.5]" />
                </div>
                <div>
                  <p class="text-xl font-display font-medium text-zinc-900 dark:text-white">Delete User?</p>
                  <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">"<span class="font-medium text-zinc-900 dark:text-white">{{ deleteTarget.name }}</span>" will be permanently removed.</p>
                </div>
                <div class="flex gap-3 w-full mt-4">
                  <button @click="deleteTarget = null" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                  <button @click="confirmDelete" class="flex-1 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/20 active:scale-95 transition-all">Delete</button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import api from '@/api/axios.js'
import { Search as SearchIcon, Trash2 as TrashIcon } from '@lucide/vue'

const toast        = inject('toast')
const users        = ref([])
const loading      = ref(true)
const search       = ref('')
const deleteTarget = ref(null)

const filtered = computed(() =>
  users.value.filter(u => {
    const q = search.value.toLowerCase()
    return (u.name || '').toLowerCase().includes(q)
      || (u.email || '').toLowerCase().includes(q)
      || (u.role || '').toLowerCase().includes(q)
  })
)

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function updateRole(user, newRole) {
  try {
    await api.put(`/admin/users/${user.id}`, { role: newRole })
    user.role = newRole
    toast(`${user.name}'s role updated to ${newRole}`, 'success')
  } catch (e) {
    toast('Failed to update user role', 'error')
  }
}

async function confirmDelete() {
  try {
    await api.delete(`/admin/users/${deleteTarget.value.id}`)
    users.value = users.value.filter(u => u.id !== deleteTarget.value.id)
    toast('User deleted', 'success')
  } catch (e) {
    toast('Failed to delete user', 'error')
  } finally {
    deleteTarget.value = null
  }
}

async function loadUsers() {
  loading.value = true
  try {
    const res = await api.get('/admin/users')
    users.value = res.data.data || res.data || []
  } catch (e) {
    toast('Failed to load users', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(loadUsers)
</script>
