<script setup>
import { onMounted, provide, ref } from 'vue'
import { RouterView } from 'vue-router'
import { useTheme } from '@/stores/theme';
import Alert from './components/Alert.vue';

const alert = ref({show: false, data:{type:'success', msg: ''}})

function showAlert(data){
  alert.value = data
}

function setTheme()
{
  const screen = document.getElementById('screen')
  if(screen){
    const style = useTheme()
    //remove theme
    screen.classList.forEach(cl => {
      screen.classList.remove(cl)
    })

    //set theme
    screen.classList.add(style.theme)
  }
}

provide('sysapp', {
  name: import.meta.env.VITE_APP_NAME ?? 'Gestor Contratos',
  desc: import.meta.env.VITE_APP_DESC ?? 'Gestão e Fiscalização de Contratos',
  copy: import.meta.env.VITE_APP_COPY ?? 'OctusPi 2024'
})

onMounted(() => {
  setTheme()
})

</script>

<template>

  <div id="load-wall" class="load-wall d-none">
    <img id="load-img" class="load-img" src="./assets/imgs/load.svg">
  </div>

  <Alert :alert="alert" />

  <div class="container-fluid px-4">
      <RouterView @callAlert="showAlert"/>
  </div>
  
</template>

