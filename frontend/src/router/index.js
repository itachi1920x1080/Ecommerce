import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
    { path: '/', component: () => import('../views/shop/HomeView.vue') },
    { path: '/login', component: () => import('../views/auth/LoginView.vue') },
    { path: '/register', component: () => import('../views/auth/RegisterView.vue') },
    { path: '/products', component: () => import('../views/shop/ProductsView.vue') },
    { path: '/products/:id', component: () => import('../views/shop/ProductDetailView.vue') },
    { path: '/cart', component: () => import('../views/shop/CartView.vue'), meta: { auth: true } },
    { path: '/checkout', component: () => import('../views/shop/CheckoutView.vue'), meta: { auth: true } },
    { path: '/admin', component: () => import('../views/admin/AdminView.vue'), meta: { auth: true, admin: true } },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()
    if (to.meta.auth && !auth.isLoggedIn) {
        next('/login')
    } else if (to.meta.admin && !auth.isAdmin) {
        next('/')
    } else {
        next()
    }
})

export default router
