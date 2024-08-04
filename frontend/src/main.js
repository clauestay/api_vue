import './assets/main.css'
import './assets/tailwind.css'

import { createApp, markRaw } from 'vue'
import { createPinia } from 'pinia'
import { createPersistedState } from 'pinia-plugin-persistedstate'
// import axiosPlugin from './plugins/axios'
import axios from 'axios'
import App from './App.vue'
import router from './router'

window.axios = axios

window.axios.default.baseURL = import.meta.env.VITE_APP_API_URL
window.axios.defaults.headers.common['Accept'] = 'application/json'
window.axios.defaults.headers.common['Content-Type'] = 'application/json'
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.default.withCredentials = true

const pinia = createPinia()
pinia.use(({store}) => {
    store.router = markRaw(router)
})
pinia.use(createPersistedState)

const app = createApp(App)

app.use(pinia)
app.use(router)

app.mount('#app')
