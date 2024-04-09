<script setup>
import { onMounted, provide, ref } from 'vue'
import { RouterView } from 'vue-router'
import style from '@/stores/theme';
import UiAlert from './components/UiAlert.vue';

const alert = ref({show: false, data:{type:'success', msg: ''}})

provide('sysapp', {
  name: import.meta.env.VITE_APP_NAME ?? 'Gestor Contratos',
  desc: import.meta.env.VITE_APP_DESC ?? 'Gestão e Fiscalização de Contratos',
  copy: import.meta.env.VITE_APP_COPY ?? 'OctusPi 2024'
})

onMounted(() => {
  const screen = document.getElementById('screen')
  
  if(screen){

    //remove theme
    screen.classList.forEach(cl => {
      screen.classList.remove(cl)
    })

    //set theme
    screen.classList.add(style.theme)
  }
})

</script>

<template>

  <div id="load-wall" class="load-wall d-none">
    <img id="load-img" class="load-img" src="./assets/imgs/load.svg">
  </div>

  <UiAlert :alert="alert" />

  <div class="container-fluid px-4">
      <RouterView @callAlert="(data) => { alert = data}"/>
  </div>
  
</template>

