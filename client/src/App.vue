<script setup>
import { provide, ref } from 'vue'
import { RouterView } from 'vue-router'
import Alert from './components/Alert.vue';

provide('sysapp', {
  name: import.meta.env.VITE_APP_NAME ?? 'Gestor Contratos',
  desc: import.meta.env.VITE_APP_DESC ?? 'Gestão e Fiscalização de Contratos',
  copy: import.meta.env.VITE_APP_COPY ?? 'OctusPi 2024'
})

const alert = ref({show: false, data:{type:'success', msg: ''}})

function showAlert(data){
  alert.value = data
}

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

