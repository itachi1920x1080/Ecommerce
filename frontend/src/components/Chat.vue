<template>
  <div>
    <input v-model="message" />
    <button @click="sendMessage">Send</button>

    <div v-for="(msg, index) in messages" :key="index">
      <p>{{ msg }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const message = ref('')
const messages = ref([])

const sendMessage = async () => {
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  const res = await axios.post(
    `${baseUrl}/chat/`,
    {
      message: message.value
    }
  )

  messages.value.push("You: " + message.value)
  messages.value.push("Bot: " + res.data.reply)

  message.value = ''
}
</script>
