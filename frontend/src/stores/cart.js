import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios.js'

export const useCartStore = defineStore('cart', () => {

  const items = ref([])
  const loading = ref(false)

  // Getters
  const totalItems = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))
  const cartTotal = computed(() => items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0))

  async function fetchCart() {
    loading.value = true
    try {
      const res = await api.get('/cart')
      const serverCarts = res.data.cart || res.data.data || res.data || []
      
      // Map server carts to a flat structure so the UI works seamlessly
      items.value = serverCarts.map(c => ({
        cart_id: c.id,          // Needed to delete from backend
        id: c.product_id,       // The actual product ID
        name: c.product?.name || 'Unknown Product',
        price: Number(c.product?.price || 0),
        image: c.product?.full_image_url || c.product?.image || 'https://placehold.co/64x64/e2e8f0/94a3b8?text=Product',
        quantity: c.quantity
      }))
    } catch (e) {
      console.error('Failed to fetch cart:', e)
    } finally {
      loading.value = false
    }
  }

  async function addToCart(product) {
    // 1. Optimistic Local Update (Instant UI feedback)
    const existing = items.value.find(i => i.id === product.id)
    if (existing) {
      existing.quantity++
    } else {
      items.value.push({
        cart_id: null, // Will be populated after sync
        id: product.id,
        name: product.name,
        price: Number(product.price || 0),
        image: product.full_image_url || product.image || 'https://placehold.co/64x64/e2e8f0/94a3b8?text=Product',
        quantity: 1
      })
    }

    // 2. Sync with Backend
    try {
      await api.post('/cart', { product_id: product.id, quantity: 1 })
      await fetchCart() // Refresh to get the generated cart_id for future deletes
    } catch (e) {
      console.error('Failed to sync add to cart:', e)
    }
  }

  async function removeFromCart(id) {
    // Find the item locally
    const item = items.value.find(i => i.id === id)
    if (!item) return

    // Optimistically remove locally
    items.value = items.value.filter(i => i.id !== id)

    // Sync with Backend
    if (item.cart_id) {
      try {
        await api.delete(`/cart/${item.cart_id}`)
      } catch (e) {
        console.error('Failed to remove item from server:', e)
        // If it fails, maybe refetch cart to restore state
        await fetchCart()
      }
    }
  }

  return { 
    items, 
    loading, 
    totalItems, 
    cartTotal, 
    fetchCart, 
    addToCart, 
    removeFromCart 
  }
})
