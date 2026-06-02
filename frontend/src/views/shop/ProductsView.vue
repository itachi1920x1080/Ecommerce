<template>
  <div class="products-page">
    <h2>Our Products</h2>
    <div v-if="loading">Loading...</div>
    <div class="products-grid">
      <div v-for="product in products" :key="product.id" class="product-card">
        <img :src="product.image_url || 'https://placehold.co/300x300?text=Skincare'" :alt="product.name" />
        <h3>{{ product.name }}</h3>
        <p class="price">${{ product.price }}</p>
        <router-link :to="'/products/' + product.id" class="btn-primary">View</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../axios'

const products = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get('/products')
    products.value = res.data.data || res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>
