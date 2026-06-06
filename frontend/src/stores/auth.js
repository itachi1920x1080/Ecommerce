/**
 * Auth Store (Pinia)
 * Manages authentication state: login, register, logout.
 * Token persisted in localStorage with Sanctum Bearer auth.
 */
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios.js'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {

  const token = ref(localStorage.getItem('token') || '')
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  // Computed getters
  const isAdmin    = computed(() => user.value?.role === 'admin')
  const isDriver   = computed(() => user.value?.role === 'driver')
  const isLoggedIn = computed(() => !!token.value)
  const userName   = computed(() => user.value?.name || '')

  /**
   * Login with email/password via Sanctum token auth
   */
  async function login(email, password) {
    
    // ជំហានទី ១: សុំសោរ CSRF ពី Backend 
    await axios.get('https://ecommerce-production-3bc1.up.railway.app/sanctum/csrf-cookie', {
      withCredentials: true // សំខាន់បំផុត
    });

    // ជំហានទី ២: ធ្វើការ Login បញ្ជូន Email & Password
    // ចំណាំ៖ ដោយសារកូដ API Login របស់អ្នកស្ថិតក្នុង /api/login សូមប្រាកដថាប្រើ /api/login ឬ /login បើប្រើ web route
    const res = await axios.post('https://ecommerce-production-3bc1.up.railway.app/api/login', {
      email: email,
      password: password
    }, {
      withCredentials: true // សំខាន់បំផុត
    });

    token.value = res.data?.token || 'cookie-auth'
    user.value  = { email, role: res.data?.role, name: res.data?.name || email.split('@')[0] }

    localStorage.setItem('token', token.value)
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

  return { token, user, isAdmin, isDriver, isLoggedIn, userName, login, register, logout }
})