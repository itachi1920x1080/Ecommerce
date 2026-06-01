<template>
  <div class="product-detail" v-if="product">
    <img :src="product.image || 'https://placehold.co/400x400?text=Skincare'" :alt="product.name" />
    <div class="product-info">
      <h2>{{ product.name }}</h2>
      <p>{{ product.description }}</p>
      <p class="price">${{ product.price }}</p>
      <p>Stock: {{ product.stock }}</p>
      <button @click="addToCart" class="btn-primary">Add to Cart</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../axios'

const route = useRoute()
const product = ref(null)

onMounted(async () => {
  const res = await api.get('/products/' + route.params.id)
  product.value = res.data
})

function addToCart() {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]')
  const existing = cart.find(i => i.id === product.value.id)
  if (existing) {
    existing.quantity++
  } else {
    cart.push({ ...product.value, quantity: 1 })
  }
  localStorage.setItem('cart', JSON.stringify(cart))
  alert('Added to cart!')
}
</script>
