import './assets/css/main.css'
import 'bootstrap/dist/js/bootstrap.bundle.min'


import { createApp } from 'vue'
import { vMaska } from "maska"

import App from './App.vue'
import router from './router'


const app = createApp(App)
app.use(router)
app.directive('maska', vMaska)
app.provide('sysapp', {
  name: import.meta.env.VITE_APP_NAME ?? 'Gestor Contratos',
  desc: import.meta.env.VITE_APP_DESC ?? 'Gestão e Fiscalização de Contratos',
  copy: import.meta.env.VITE_APP_COPY ?? 'OctusPi 2024'
})
app.mount('#app')


