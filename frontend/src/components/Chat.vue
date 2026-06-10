<template>
  <div class="fixed bottom-6 right-6 z-50">
    <!-- Chat Window -->
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 translate-y-4 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-4 scale-95"
    >
      <div v-if="isOpen" class="mb-4 w-80 sm:w-96 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-zinc-900 to-zinc-800 dark:from-zinc-800 dark:to-zinc-700 p-4 flex justify-between items-center text-white">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
            </div>
            <div>
              <h3 class="font-medium text-sm">Shop Assistant</h3>
              <p class="text-xs text-zinc-300">Online</p>
            </div>
          </div>
          <button @click="isOpen = false" class="text-zinc-300 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Messages Area -->
        <div class="p-4 h-80 overflow-y-auto bg-zinc-50 dark:bg-zinc-950/50 flex flex-col gap-3" ref="chatContainer">
          <!-- Initial Welcome Message -->
          <div class="flex items-end gap-2 max-w-[85%] self-start">
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 p-3 rounded-2xl rounded-bl-sm shadow-sm text-sm text-zinc-800 dark:text-zinc-200">
              Hi there! 👋 How can I help you today?
            </div>
          </div>

          <!-- Dynamic Messages -->
          <div 
            v-for="(msg, index) in messages" 
            :key="index"
            class="flex items-end gap-2 max-w-[85%]"
            :class="msg.sender === 'user' ? 'self-end flex-row-reverse' : 'self-start'"
          >
            <div 
              class="p-3 shadow-sm text-sm"
              :class="[
                msg.sender === 'user' 
                  ? 'bg-zinc-900 text-white rounded-2xl rounded-br-sm' 
                  : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl rounded-bl-sm text-zinc-800 dark:text-zinc-200'
              ]"
            >
              {{ msg.text }}
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isLoading" class="flex items-end gap-2 max-w-[85%] self-start">
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 p-3 rounded-2xl rounded-bl-sm shadow-sm flex gap-1 items-center h-10">
              <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce"></div>
              <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
              <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
          <form @submit.prevent="sendMessage" class="flex items-center gap-2">
            <input 
              v-model="message" 
              type="text" 
              placeholder="Type your message..." 
              class="flex-1 bg-zinc-100 dark:bg-zinc-800 border-none rounded-full px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-500 outline-none transition-all"
              :disabled="isLoading"
            />
            <button 
              type="submit" 
              class="w-9 h-9 rounded-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:hover:scale-100"
              :disabled="!message.trim() || isLoading"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
            </button>
          </form>
        </div>
      </div>
    </transition>

    <!-- Floating Button -->
    <button 
      @click="isOpen = !isOpen"
      class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95"
      :class="isOpen ? 'bg-zinc-800 text-white rotate-90' : 'bg-gradient-to-tr from-zinc-900 to-zinc-700 dark:from-white dark:to-zinc-200 text-white dark:text-zinc-900'"
    >
      <svg v-if="isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import axios from 'axios'

const isOpen = ref(false)
const message = ref('')
const messages = ref([])
const isLoading = ref(false)
const chatContainer = ref(null)

const scrollToBottom = async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!message.value.trim() || isLoading.value) return

  const userMsg = message.value
  messages.value.push({ sender: 'user', text: userMsg })
  message.value = ''
  isLoading.value = true
  await scrollToBottom()

  try {
    const baseUrl = import.meta.env.VITE_CHATBOT_URL || 'http://localhost:8000'
    const res = await axios.post(`${baseUrl}/chat/`, {
      message: userMsg
    })
    messages.value.push({ sender: 'bot', text: res.data.reply })
  } catch (error) {
    messages.value.push({ sender: 'bot', text: "Sorry, I'm having trouble connecting right now." })
  } finally {
    isLoading.value = false
    await scrollToBottom()
  }
}
</script>
