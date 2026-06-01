<template>
  <div class="auth-container">
    <div class="auth-box">
      <h2>Register</h2>
      <div v-if="error" class="error">{{ error }}</div>
      <input v-model="name" type="text" placeholder="Name" />
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Password" />
      <input v-model="password_confirmation" type="password" placeholder="Confirm Password" />
      <button @click="register" class="btn-primary">Register</button>
      <p>Have account? <router-link to="/login">Login</router-link></p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')

async function register() {
  try {
    await auth.register(name.value, email.value, password.value, password_confirmation.value)
    router.push('/')
  } catch (e) {
    error.value = 'Registration failed'
  }
}
</script>
