<template>
  <div class="cart-page">
    <h2>Shopping Cart</h2>
    <div v-if="cart.length === 0">Your cart is empty</div>
    <div v-for="item in cart" :key="item.id" class="cart-item">
      <span>{{ item.name }}</span>
      <span>x{{ item.quantity }}</span>
      <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
      <button @click="remove(item.id)">Remove</button>
    </div>
    <div class="cart-total" v-if="cart.length > 0">
      <strong>Total: ${{ total }}</strong>
      <router-link to="/checkout" class="btn-primary">Checkout</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const cart = ref(JSON.parse(localStorage.getItem('cart') || '[]'))

const total = computed(() =>
  cart.value.reduce((sum, i) => sum + i.price * i.quantity, 0).toFixed(2)
)

function remove(id) {
  cart.value = cart.value.filter(i => i.id !== id)
  localStorage.setItem('cart', JSON.stringify(cart.value))
}
</script>
