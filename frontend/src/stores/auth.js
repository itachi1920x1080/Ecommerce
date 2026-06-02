/**
 * Auth Store (Pinia)
 * Manages authentication state: login, register, logout.
 * Token persisted in localStorage with Sanctum Bearer auth.
 */
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios.js'

export const useAuthStore = defineStore('auth', () => {

  const token = ref(localStorage.getItem('token') || '')
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  // Computed getters
  const isAdmin    = computed(() => user.value?.role === 'admin')
  const isLoggedIn = computed(() => !!token.value)
  const userName   = computed(() => user.value?.name || '')

  /**
   * Login with email/password via Sanctum token auth
   */
  async function login(email, password) {
    const res = await api.post('/login', { email, password })

    token.value = res.data.token
    user.value  = { email, role: res.data.role, name: res.data.name || email.split('@')[0] }

    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(user.value))
  }

  /**
   * Register a new user account
   */
  async function register(name, email, password, password_confirmation) {
    const res = await api.post('/register', { name, email, password, password_confirmation })

    token.value = res.data.token
    user.value  = { name, email, role: 'user' }

    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(user.value))
  }

  /**
   * Logout and clear all auth state
   */
  async function logout() {
    try { await api.post('/logout') } catch (_) {}
    token.value = ''
    user.value  = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { token, user, isAdmin, isLoggedIn, userName, login, register, logout }
})