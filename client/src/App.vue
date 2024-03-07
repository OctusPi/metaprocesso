<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import Alert from './components/Alert.vue';

const alert = ref({show: true, data:{type:'success', msg: 'Mensagem vinda do backend...'}})

</script>

<template>

  <Alert :alert="alert" />

  <div class="container-fluid px-4">
      <RouterView />
  </div>
  
</template>

