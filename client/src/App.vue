<script setup>
import { onMounted, ref } from 'vue'
import { RouterView } from 'vue-router'
import style from '@/stores/theme';
import UiAlert from './components/UiAlert.vue';
import ModalDelete from './components/ModalDelete.vue';

const datalist = ref([])
const alert    = ref({show: false, data:{type:'success', msg: ''}})
const remove   = ref({})



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

  <ModalDelete 
  :params="remove" 
  @callUpdate="(data) => { datalist = data}"
  @callAlert="(data) => { alert = data}" />
  <UiAlert :alert="alert" />

  <div class="container-fluid px-4">
      <RouterView 
      :datalist = "datalist"
      @callAlert="(data) => { alert = data}" 
      @callRemove="(data) => { remove = data }" />
  </div>
  
</template>

