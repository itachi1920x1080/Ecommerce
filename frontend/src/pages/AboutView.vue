<template>
  <div class="about-page">

    <!-- HERO -->
    <section class="text-center py-20 px-4">
      <span class="inline-block text-xs font-semibold tracking-widest uppercase text-pink-500 bg-pink-50 dark:bg-pink-500/10 dark:text-pink-300 px-4 py-1.5 rounded-full mb-6">
        Our Story
      </span>
      <h1 class="font-serif text-5xl md:text-6xl font-normal leading-tight text-zinc-900 dark:text-zinc-50 mb-5">
        Beauty That <em class="italic text-pink-500">Feels</em><br>Like You
      </h1>
      <p class="text-zinc-500 dark:text-zinc-400 text-lg max-w-xl mx-auto leading-relaxed font-light">
        At Tea Tik Kok Shop, we believe skincare is a ritual, not a routine — curated products that celebrate your natural glow.
      </p>
      <div class="w-14 h-px bg-pink-300 mx-auto mt-10"></div>
    </section>

    <!-- STORY -->
    <section class="max-w-5xl mx-auto px-4 pb-20 grid md:grid-cols-2 gap-12 items-center">
      <div>
        <h2 class="font-serif text-3xl font-medium text-zinc-900 dark:text-zinc-50 mb-5 leading-snug">
          Born from a passion for clean beauty
        </h2>
        <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed font-light mb-4">
          We started Tea Tik Kok Shop with one simple belief: everyone deserves to feel confident in their own skin. Our journey began with a small collection of handpicked skincare essentials and has grown into a full beauty destination.
        </p>
        <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed font-light">
          Every product in our collection is carefully selected for its quality, effectiveness, and kindness to skin. From hydrating serums to brightening masks — we've got your glow covered.
        </p>
      </div>
      <div class="rounded-2xl bg-gradient-to-br from-pink-50 via-pink-100 to-pink-200 dark:from-pink-900/30 dark:via-pink-800/20 dark:to-pink-700/10 h-72 flex flex-col items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1.5 3-4 4.5-4 7a4 4 0 008 0c0-2.5-2.5-4-4-7z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v7m0 0c-1.5 0-3 .5-3 1.5S10.5 20 12 20s3-.5 3-1.5S13.5 17 12 17z"/>
        </svg>
        <span class="font-serif italic text-pink-600 dark:text-pink-300 text-lg">Clean · Gentle · Radiant</span>
      </div>
    </section>

    <!-- STATS -->
    <section class="max-w-5xl mx-auto px-4 pb-20 grid grid-cols-3 gap-4">
      <div v-for="stat in stats" :key="stat.label"
        class="text-center py-8 rounded-2xl bg-pink-50 dark:bg-pink-500/10">
        <div class="font-serif text-4xl md:text-5xl font-medium text-pink-600 dark:text-pink-400">{{ stat.number }}</div>
        <div class="text-xs text-pink-700 dark:text-pink-300 mt-2 tracking-wide">{{ stat.label }}</div>
      </div>
    </section>

    <!-- VALUES -->
    <section class="max-w-5xl mx-auto px-4 pb-20">
      <h2 class="font-serif text-3xl font-medium text-center text-zinc-900 dark:text-zinc-50 mb-10">What we stand for</h2>
      <div class="grid md:grid-cols-3 gap-4">
        <div v-for="value in values" :key="value.title"
          class="bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800 rounded-2xl p-6 text-center">
          <div class="w-12 h-12 rounded-full bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center mx-auto mb-4">
            <component :is="value.icon" class="w-5 h-5 text-pink-500" stroke-width="1.5" />
          </div>
          <h3 class="font-medium text-zinc-900 dark:text-zinc-50 mb-2">{{ value.title }}</h3>
          <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed font-light">{{ value.desc }}</p>
        </div>
      </div>
    </section>

    <!-- TEAM -->
    <section class="max-w-5xl mx-auto px-4 pb-20">
      <div class="text-center mb-12">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-pink-500 bg-pink-50 dark:bg-pink-500/10 dark:text-pink-300 px-4 py-1.5 rounded-full mb-4">
          The Team
        </span>
        <h2 class="font-serif text-3xl font-medium text-zinc-900 dark:text-zinc-50">
          People behind the platform
        </h2>
      </div>
      <div class="grid md:grid-cols-2 gap-6">
        <div
          v-for="member in team"
          :key="member.name"
          class="bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-2xl overflow-hidden hover:shadow-lg transition-shadow"
        >
          <!-- Photo -->
          <div class="w-full h-95 bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
            <img
              v-if="member.photo"
              :src="member.photo"
              :alt="member.name"
              class="w-full h-full object-cover"
              style="object-position: 50% 20%;"
            />
            <div
              v-else
              :class="['w-full h-full flex items-center justify-center font-serif text-5xl font-medium', member.color]"
            >
              {{ member.initial }}
            </div>
          </div>

          <!-- Info -->
          <div class="p-6">
            <h4 class="font-semibold text-zinc-900 dark:text-zinc-50 text-lg mb-1">{{ member.name }}</h4>
            <p class="text-pink-500 dark:text-pink-400 text-sm font-medium mb-3">{{ member.role }}</p>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed font-light mb-5">{{ member.desc }}</p>

            <!-- Social Links -->
            <div class="flex items-center gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">

              <!-- GitHub -->
              <a v-if="member.github" :href="member.github" target="_blank"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-pink-100 hover:text-pink-500 dark:hover:bg-pink-500/20 dark:hover:text-pink-400 transition-colors"
                title="GitHub">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
              </a>

              <!-- LinkedIn -->
              <a v-if="member.linkedin" :href="member.linkedin" target="_blank"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-pink-100 hover:text-pink-500 dark:hover:bg-pink-500/20 dark:hover:text-pink-400 transition-colors"
                title="LinkedIn">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
              </a>

              <!-- Telegram -->
              <a v-if="member.telegram" :href="member.telegram" target="_blank"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-pink-100 hover:text-pink-500 dark:hover:bg-pink-500/20 dark:hover:text-pink-400 transition-colors"
                title="Telegram">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
              </a>

              <!-- TikTok -->
              <a v-if="member.tikTok" :href="member.tikTok" target="_blank"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-pink-100 hover:text-pink-500 dark:hover:bg-pink-500/20 dark:hover:text-pink-400 transition-colors"
                title="TikTok">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.75a4.85 4.85 0 01-1.01-.06z"/>
                </svg>
              </a>

              <!-- Facebook -->
              <a v-if="member.facebook" :href="member.facebook" target="_blank"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-pink-100 hover:text-pink-500 dark:hover:bg-pink-500/20 dark:hover:text-pink-400 transition-colors"
                title="Facebook">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.235 2.686.235v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                </svg>
              </a>

            </div><!-- end social links -->
          </div><!-- end info -->
        </div><!-- end member card -->
      </div><!-- end grid -->
    </section>

    <!-- CTA -->
    <section class="max-w-5xl mx-auto px-4 pb-20">
      <div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/30 dark:to-pink-800/20 rounded-3xl p-12 text-center border border-pink-100 dark:border-pink-800/30">
        <h2 class="font-serif text-3xl font-normal text-zinc-900 dark:text-zinc-50 mb-3">
          Ready to start your skincare journey?
        </h2>
        <p class="text-pink-700 dark:text-pink-300 font-light mb-8">
          Browse our curated collection and find your perfect routine.
        </p>
        <router-link
          to="/shop"
          class="inline-block bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium px-8 py-3 rounded-full transition-colors"
        >
          Shop now →
        </router-link>
      </div>
    </section>

  </div>
</template>

<script setup>
import { Leaf, Heart, Award } from '@lucide/vue'
import BunNengImage from '@/assets/BunNengImage.png'
import VisalImage from '@/assets/VisalImage.png'

const stats = [
  { number: '500+', label: 'Products curated' },
  { number: '10K+', label: 'Happy customers' },
  { number: '3+',   label: 'Years of glow' },
]

const values = [
  { title: 'Clean ingredients', desc: 'No harsh chemicals. Every product is skin-friendly and thoughtfully formulated.', icon: Leaf },
  { title: 'Cruelty-free',      desc: 'We only carry brands that respect animals and the planet we share.',              icon: Heart },
  { title: 'Curated quality',   desc: 'Each item is hand-tested and approved before it lands on our shelves.',           icon: Award },
]

const team = [
  {
    name: 'Mach Bunneng',
    role: 'UI/UX Designer & Frontend Developer',
    desc: 'Crafts the visual language of the platform — from wireframes to polished interfaces that delight and convert.',
    photo: BunNengImage,
    initial: 'B',
    color: 'bg-pink-100 dark:bg-pink-900/40 text-pink-700 dark:text-pink-300',
    github:   'https://github.com/',
    linkedin: 'https://linkedin.com/',
    telegram: 'https://t.me/',
    tikTok:   'https://www.tiktok.com/',
    facebook: 'https://www.facebook.com/',
  },
  {
    name: 'Mao Visal',
    role: 'Backend Developer',
    desc: 'Focused on API integration, database management, reusable components, user interaction, and improving performance for a seamless shopping experience.',
    photo: VisalImage,
    initial: 'V',
    color: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
    github:   'https://github.com/',
    linkedin: 'https://linkedin.com/',
    telegram: 'https://t.me/',
    tikTok:   'https://www.tiktok.com/',
    facebook: 'https://www.facebook.com/',
  },
]
</script>

<style scoped>
.font-serif {
  font-family: 'Georgia', 'Times New Roman', serif;
}
</style>