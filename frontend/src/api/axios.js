/**
 * Axios API Service Layer
 * Centralized HTTP client with auth token injection and 401 auto-logout.
 */
import axios from 'axios'

const defaultBaseUrl = window.location.hostname === 'localhost' 
  ? 'http://localhost:8000/api'
  : `http://${window.location.hostname}:8000/api`;

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || defaultBaseUrl,
  headers: { 'Content-Type': 'application/json' }
})

// ── Request interceptor: attach Bearer token ──
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// ── Response interceptor: auto-logout on 401 ──
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      // Redirect to login if not already there
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
