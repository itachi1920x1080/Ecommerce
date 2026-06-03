<template>
  <div>
    <section class="py-20 px-6 bg-white">
      <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
          <span class="inline-block px-3 py-1 bg-primary-50 text-primary-600 text-xs font-semibold rounded-full mb-3 uppercase tracking-wider">Get in Touch</span>
          <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-3">Contact Us</h1>
          <p class="text-sm text-slate-400 max-w-md mx-auto">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
          <!-- Info Cards -->
          <div class="lg:col-span-2 space-y-4">
            <div v-for="info in contactInfo" :key="info.title"
              class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-200/50 hover:shadow-md transition-all duration-300 group">
              <div class="w-10 h-10 rounded-xl mb-3 flex items-center justify-center transition-colors duration-300" :class="info.bg">
                <svg class="w-5 h-5" :class="info.iconColor" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="info.icon"/>
                </svg>
              </div>
              <h3 class="text-sm font-semibold text-slate-800 mb-1">{{ info.title }}</h3>
              <p class="text-sm text-slate-400">{{ info.value }}</p>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="lg:col-span-3">
            <form @submit.prevent="handleSubmit" class="space-y-5">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium text-slate-500 mb-1.5">Name</label>
                  <input v-model="form.name" type="text" required placeholder="Your name"
                    class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-slate-500 mb-1.5">Email</label>
                  <input v-model="form.email" type="email" required placeholder="you@example.com"
                    class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Subject</label>
                <input v-model="form.subject" type="text" required placeholder="What's this about?"
                  class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Message</label>
                <textarea v-model="form.message" rows="5" required placeholder="Your message..."
                  class="w-full px-4 py-3 rounded-xl text-sm bg-slate-50 border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all resize-none"></textarea>
              </div>
              <button type="submit"
                class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/25 transition-all duration-300 hover:-translate-y-0.5 active:scale-95">
                Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
    <Footer />
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import Footer from '@/components/shop/Footer.vue'

const toast = inject('toast')
const form = ref({ name: '', email: '', subject: '', message: '' })

const contactInfo = [
  { title: 'Location', value: 'Royal University of Phnom Penh (RUPP)', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', bg: 'bg-blue-100', iconColor: 'text-blue-600' },
  { title: 'Phone', value: '012 345 678', icon: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', bg: 'bg-emerald-100', iconColor: 'text-emerald-600' },
  { title: 'Email', value: 'support@ruppshop.com', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', bg: 'bg-violet-100', iconColor: 'text-violet-600' },
]

function handleSubmit() {
  toast('Message sent! We will get back to you soon.', 'success')
  form.value = { name: '', email: '', subject: '', message: '' }
}
</script>
