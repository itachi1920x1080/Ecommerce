// stores/auth.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/axios.js'

export const useAuthStore = defineStore('auth', () => {

  const token = ref(localStorage.getItem('token') || '')
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  const isAdmin     = computed(() => user.value?.role === 'admin')
  const isLoggedIn  = computed(() => !!token.value)

  async function login(email, password) {
    const res = await api.post('/login', { email, password })

    // Save token + user
    token.value = res.data.token
    user.value  = { email, role: res.data.role }

    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(user.value))
  }

  async function logout() {
    try { await api.post('/logout') } catch (_) {}
    token.value = ''
    user.value  = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { token, user, isAdmin, isLoggedIn, login, logout }
})