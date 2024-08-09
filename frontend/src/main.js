import './assets/tailwind.css'

import { createApp, markRaw } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedState from 'pinia-plugin-persistedstate'
import axios from 'axios'
import App from './App.vue'
import router from './router'
import { createHead } from "@vueuse/head"
import PrimeVue from 'primevue/config';
// import dayjs from 'dayjs';

window.axios = axios
window.axios.defaults.baseURL = import.meta.env.VITE_APP_API_URL
window.axios.defaults.headers.common['Accept'] = 'application/json'
window.axios.defaults.headers.common['Content-Type'] = 'application/json'
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.default.withCredentials = true

const pinia = createPinia()
pinia.use(({store}) => {
    store.router = markRaw(router)
})
// pinia.use(createPersistedState)
pinia.use(piniaPluginPersistedState)

const app = createApp(App)

const head = createHead()

app.use(pinia)
app.use(router)
app.use(head)
app.use(PrimeVue, {
    unstyled: true
})
// app.config.globalProperties.$dayjs = dayjs
app.mount('#app')
