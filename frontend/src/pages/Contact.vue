<template>
  <div class="min-h-screen bg-white dark:bg-zinc-950 pt-32 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-16 border-b border-zinc-200 dark:border-zinc-800 pb-8 max-w-3xl">
        <h1 class="text-4xl sm:text-5xl font-display font-medium text-zinc-900 dark:text-white tracking-tight mb-4">Contact Us</h1>
        <p class="text-zinc-500 dark:text-zinc-400 text-lg font-light leading-relaxed">
          Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-5 gap-16">
        <!-- Info -->
        <div class="lg:col-span-2 space-y-10">
          <div v-for="info in contactInfo" :key="info.title" class="group">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center shrink-0 border border-zinc-200/50 dark:border-zinc-800/50 text-zinc-900 dark:text-zinc-50 group-hover:bg-zinc-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-zinc-900 transition-colors duration-300">
                <component :is="info.icon" class="w-5 h-5 stroke-[1.5]" />
              </div>
              <div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-50 mb-1">{{ info.title }}</h3>
                <p class="text-zinc-500 dark:text-zinc-400 font-light leading-relaxed whitespace-pre-line">{{ info.value }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="lg:col-span-3">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Name</label>
                <input id="name" v-model="form.name" type="text" required placeholder="Your name"
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-zinc-900 dark:text-zinc-50 transition-all placeholder:text-zinc-400" />
              </div>
              <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Email</label>
                <input id="email" v-model="form.email" type="email" required placeholder="you@example.com"
                  class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-zinc-900 dark:text-zinc-50 transition-all placeholder:text-zinc-400" />
              </div>
            </div>
            <div class="space-y-2">
              <label for="subject" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Subject</label>
              <input id="subject" v-model="form.subject" type="text" required placeholder="What's this about?"
                class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-zinc-900 dark:text-zinc-50 transition-all placeholder:text-zinc-400" />
            </div>
            <div class="space-y-2">
              <label for="message" class="block text-sm font-medium text-zinc-900 dark:text-zinc-50">Message</label>
              <textarea id="message" v-model="form.message" rows="6" required placeholder="Your message..."
                class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-zinc-900 dark:text-zinc-50 transition-all placeholder:text-zinc-400 resize-none"></textarea>
            </div>
            <button type="submit" class="btn-primary w-full sm:w-auto px-10 py-4 text-base">
              Send Message
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { MapPin as MapPinIcon, Phone as PhoneIcon, Mail as MailIcon } from '@lucide/vue'

const toast = inject('toast')
const form = ref({ name: '', email: '', subject: '', message: '' })

const contactInfo = [
  { title: 'Location', value: 'Royal University of Phnom Penh (RUPP)\nPhnom Penh, Cambodia', icon: MapPinIcon },
  { title: 'Phone', value: '+855 12 345 678\nMon-Fri from 8am to 5pm', icon: PhoneIcon },
  { title: 'Email', value: 'support@ruppshop.com\ncontact@ruppshop.com', icon: MailIcon },
]

function handleSubmit() {
  toast('Message sent! We will get back to you soon.', 'success')
  form.value = { name: '', email: '', subject: '', message: '' }
}
</script>
