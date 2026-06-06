import axios from 'axios'

// 1. កំណត់ baseURL ឱ្យត្រង់ទៅកាន់ Backend URL របស់អ្នកតែម្តង
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'https://ecommerce-production-3bc1.up.railway.app/api', // ត្រូវប្រាកដថា URL នេះគឺ https://ecommerce-production-3bc1.up.railway.app
  withCredentials: true, // នេះសំខាន់បំផុត៖ ដើម្បីឱ្យ Browser បញ្ជូន Cookie ទៅឱ្យ Backend
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// 2. លុប Request Interceptor ចាស់ដែលប្រើ Bearer Token ចោល
// (ប្រសិនបើអ្នកប្រើ Sanctum Cookie, អ្នកមិនចាំបាច់ផ្ញើ Token តាម Header ទៀតទេ)

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

export default api
