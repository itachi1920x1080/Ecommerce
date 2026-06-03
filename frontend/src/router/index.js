/**
 * Vue Router Configuration
 * Route guards for guest/auth/admin access control.
 */
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  // ── Public Routes ──
  {
    path: '/',
    name: 'home',
    component: () => import('@/pages/Home.vue')
  },
  {
    path: '/products/:id',
    name: 'product-detail',
    component: () => import('@/pages/ProductDetail.vue')
  },
  {
    path: '/contact',
    name: 'contact',
    component: () => import('@/pages/Contact.vue')
  },

  // ── Guest Routes (redirect if logged in) ──
  {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/pages/Register.vue'),
    meta: { guest: true }
  },

  // ── Auth Routes (require login) ──
  {
    path: '/cart',
    name: 'cart',
    component: () => import('@/pages/Cart.vue'),
    meta: { auth: true }
  },
  {
    path: '/wishlist',
    name: 'wishlist',
    component: () => import('@/pages/Wishlist.vue'),
    meta: { auth: true }
  },
  {
    path: '/my-orders',
    name: 'my-orders',
    component: () => import('@/pages/MyOrders.vue'),
    meta: { auth: true }
  },
  {
    path: '/invoice/:id',
    name: 'invoice',
    component: () => import('@/pages/Invoice.vue'),
    meta: { auth: true }
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/pages/Profile.vue'),
    meta: { auth: true }
  },
  {
    path: '/driver',
    name: 'driver-dashboard',
    component: () => import('@/pages/DriverDashboard.vue'),
    meta: { auth: true, driver: true }
  },

  // ── Admin Routes (require admin role) ──
  {
    path: '/admin',
    redirect: '/admin/dashboard'
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('@/pages/AdminDashboard.vue'),
    meta: { auth: true, admin: true }
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: () => import('@/pages/AdminProducts.vue'),
    meta: { auth: true, admin: true }
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: () => import('@/pages/AdminOrders.vue'),
    meta: { auth: true, admin: true }
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('@/pages/AdminUsers.vue'),
    meta: { auth: true, admin: true }
  },
  {
    path: '/admin/categories',
    name: 'admin-categories',
    component: () => import('@/pages/AdminCategories.vue'),
    meta: { auth: true, admin: true }
  },
  {
    path: '/admin/coupons',
    name: 'admin-coupons',
    component: () => import('@/pages/AdminCoupons.vue'),
    meta: { auth: true, admin: true }
  },

  // ── 404 Catch-all ──
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// ── Navigation Guards ──
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  // Guest-only routes: redirect to home if already logged in
  if (to.meta.guest && auth.isLoggedIn) {
    return next('/')
  }

  // Auth routes: redirect to login if not logged in
  if (to.meta.auth && !auth.isLoggedIn) {
    return next('/login')
  }

  // Admin routes: redirect to home if not admin
  if (to.meta.admin && !auth.isAdmin) {
    return next('/')
  }

  // Driver routes: redirect to home if not driver
  if (to.meta.driver && !auth.isDriver) {
    return next('/')
  }

  next()
})

export default router
