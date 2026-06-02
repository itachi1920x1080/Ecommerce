<template>
  <div class="p-6 sm:p-8 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Users</h1>
      <p class="text-sm text-slate-400 mt-1">Manage registered user accounts</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 6" :key="i" class="h-16 skeleton rounded-2xl"></div>
    </div>

    <!-- Users Table -->
    <div v-else class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
      <!-- Search -->
      <div class="px-6 py-4 border-b border-slate-100">
        <div class="relative max-w-xs">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="search" type="text" placeholder="Search users..."
            class="w-full pl-10 pr-4 py-2 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
            id="admin-users-search" />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50/50">
              <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">User</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Role</th>
              <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Joined</th>
              <th class="text-right px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filtered" :key="user.id"
              class="border-b border-slate-50 hover:bg-blue-50/30 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ (user.name || 'U').slice(0,2).toUpperCase() }}
                  </div>
                  <span class="font-medium text-slate-800">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-4 py-4 text-slate-500">{{ user.email }}</td>
              <td class="px-4 py-4">
                <!-- Editable role dropdown -->
                <select
                  :value="user.role"
                  @change="updateRole(user, $event.target.value)"
                  class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-50 border border-slate-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                  :class="user.role === 'admin' ? 'text-indigo-700' : 'text-slate-600'"
                  :id="`user-role-${user.id}`"
                >
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
              </td>
              <td class="px-4 py-4 text-slate-400">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="deleteTarget = user"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all"
                    :id="`delete-user-${user.id}`">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filtered.length === 0">
              <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-400">No users found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <Teleport to="body">
      <Transition name="backdrop">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null"></div>
          <Transition name="modal">
            <div v-if="deleteTarget" class="relative z-10 w-full max-w-sm rounded-2xl bg-white border border-slate-200 p-6 shadow-2xl">
              <div class="flex flex-col items-center text-center gap-3">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                  <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-base font-semibold text-slate-800">Delete User?</p>
                  <p class="text-sm text-slate-400 mt-1">"<span class="font-medium text-slate-600">{{ deleteTarget.name }}</span>" will be permanently removed.</p>
                </div>
                <div class="flex gap-3 w-full mt-2">
                  <button @click="deleteTarget = null" class="flex-1 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
                  <button @click="confirmDelete" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 active:scale-95 transition-all">Delete</button>
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
