<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import Alert from './components/Alert.vue';

const alert = ref({show: false, data:{type:'success', msg: 'Mensagem vinda do backend...'}})

</script>

<template>

  <div id="load-wall" class="load-wall d-none">
    <img id="load-img" class="load-img" src="./assets/imgs/load.svg">
  </div>

  <Alert :alert="alert" />

  <div class="container-fluid px-4">
      <RouterView />
  </div>
  
</template>

