<template>
  <div class="admin-page">
    <h2>Admin Dashboard</h2>
    <h3>Products</h3>
    <button @click="showForm = true" class="btn-primary">Add Product</button>
    <div v-if="showForm" class="admin-form">
      <input v-model="form.name" placeholder="Product name" />
      <input v-model="form.description" placeholder="Description" />
      <input v-model="form.price" type="number" placeholder="Price" />
      <input v-model="form.stock" type="number" placeholder="Stock" />
      <input v-model="form.category" placeholder="Category" />
      <input v-model="form.image" placeholder="Image URL" />
      <button @click="addProduct" class="btn-primary">Save</button>
      <button @click="showForm = false">Cancel</button>
    </div>
    <div class="products-grid">
      <div v-for="product in products" :key="product.id" class="product-card">
        <h3>{{ product.name }}</h3>
        <p>${{ product.price }} | Stock: {{ product.stock }}</p>
        <button @click="deleteProduct(product.id)" class="btn-danger">Delete</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../axios'

const products = ref([])
const showForm = ref(false)
const form = ref({ name: '', description: '', price: '', stock: '', category: '', image: '' })

onMounted(async () => {
  const res = await api.get('/products')
  products.value = res.data
})

async function addProduct() {
  await api.post('/products', form.value)
  const res = await api.get('/products')
  products.value = res.data
  showForm.value = false
  form.value = { name: '', description: '', price: '', stock: '', category: '', image: '' }
}

async function deleteProduct(id) {
  await api.delete('/products/' + id)
  products.value = products.value.filter(p => p.id !== id)
}
</script>
