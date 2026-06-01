<template>
  <div class="checkout-page">
    <h2>Checkout</h2>
    <div v-if="success" class="success">Order placed successfully!</div>
    <div v-else>
      <input v-model="address" placeholder="Shipping address" />
      <div v-for="item in cart" :key="item.id" class="cart-item">
        <span>{{ item.name }} x{{ item.quantity }} = ${{ (item.price * item.quantity).toFixed(2) }}</span>
      </div>
      <p><strong>Total: ${{ total }}</strong></p>
      <button @click="placeOrder" class="btn-primary">Place Order</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '../../axios'

const cart = ref(JSON.parse(localStorage.getItem('cart') || '[]'))
const address = ref('')
const success = ref(false)

const total = computed(() =>
  cart.value.reduce((sum, i) => sum + i.price * i.quantity, 0).toFixed(2)
)

async function placeOrder() {
  try {
    await api.post('/orders', {
      items: cart.value,
      shipping_address: address.value,
      total_amount: total.value
    })
    localStorage.removeItem('cart')
    success.value = true
  } catch (e) {
    alert('Order failed. Please try again.')
  }
}
</script>
