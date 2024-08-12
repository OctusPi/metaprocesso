<script setup>
import { onMounted, ref } from 'vue'
import { RouterView } from 'vue-router'
import AlertUi from './components/AlertUi.vue';

const alert = ref({ show: true, data: {type: 'danger', msg:'Agora foi com Deus...'} })

onMounted(() => {
  const screen = document.getElementById('screen')
  if(screen){ screen.classList.add('dark')}
})
</script>

<template>
    <AlertUi :alert="alert" />
    <RouterView />
</template>