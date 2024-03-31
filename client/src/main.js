import './assets/css/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import bootstrap from  'bootstrap/dist/js/bootstrap.bundle.min'

const app = createApp(App)
app.use(bootstrap)
app.use(createPinia())
app.use(router)
app.mount('#app')
