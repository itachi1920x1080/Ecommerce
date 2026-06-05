import { ref, watch } from 'vue'

// Read from localStorage ONCE at module load time
const stored = localStorage.getItem('theme')
const isDark = ref(stored === 'dark')

// Apply immediately before Vue mounts
if (isDark.value) {
  document.documentElement.classList.add('dark')
} else {
  document.documentElement.classList.remove('dark')
}

// Only save when USER toggles — not on init
let ready = false
setTimeout(() => { ready = true }, 100)

watch(isDark, (val) => {
  if (!ready) return
  document.documentElement.classList.toggle('dark', val)
  localStorage.setItem('theme', val ? 'dark' : 'light')
})

export function useTheme() {
  function toggleTheme() {
    isDark.value = !isDark.value
  }
  function initTheme() {}
  return { isDark, toggleTheme, initTheme }
}