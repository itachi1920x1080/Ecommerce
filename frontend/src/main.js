/**
 * RUPP Shop — Main Entry Point
 * Initializes Vue 3 app with Pinia (state) and Vue Router (navigation).
 */
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
