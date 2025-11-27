import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import axios from 'axios'
import './style.css'

const apiBaseUrl = import.meta.env.VITE_API_URL || window.location.origin

axios.defaults.baseURL = apiBaseUrl
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.withCredentials = true

axios.interceptors.request.use(async (config) => {
  if (['post', 'put', 'delete', 'patch'].includes(config.method)) {
    try {
      await axios.get('/sanctum/csrf-cookie')
    } catch (error) {
      console.log('CSRF cookie obtained')
    }
  }
  return config
})

window.axios = axios

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.mount('#app')
