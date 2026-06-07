import axios from 'axios'

// 1. កំណត់ baseURL ឱ្យត្រង់ទៅកាន់ Backend URL របស់អ្នកតែម្តង
let baseUrlRaw = import.meta.env.VITE_API_URL || 'https://ecommerce-production-3bc1.up.railway.app/api';
// ប្រាកដថា baseURL តែងតែបញ្ចប់ដោយ /api (ការពារករណីភ្លេចដាក់ក្នុង Railway Variables)
if (!baseUrlRaw.endsWith('/api')) {
  baseUrlRaw = baseUrlRaw.replace(/\/$/, '') + '/api';
}

const api = axios.create({
  baseURL: baseUrlRaw,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// 2. Request Interceptor: ប្រើ Bearer Token ជំនួសឱ្យ Cookie ព្រោះយើងឆ្លង Domain (up.railway.app)
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  // Let browser set correct Content-Type + boundary for FormData
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type']
  }
  return config
})

// 3. Response Interceptor (រក្សាទុកដដែល)
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // លុបអ្វីដែលមិនចាំបាច់ចេញ
      localStorage.removeItem('user')
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export function getStorageUrl(path) {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  let base = api.defaults.baseURL.replace(/\/api\/?$/, '');
  path = path.replace(/^\//, '');
  return `${base}/storage/${path}`;
}

export default api
