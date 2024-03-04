<script setup>
import { RouterLink, RouterView } from 'vue-router'
import MainNav from '@/components/MainNav.vue'

const menu = [
  {href: '/', icon:'bi-cake2-fill', title:'Bolo de Niver', description:'Vamos Comemorar Again'},
  {href: '/about', icon:'bi-backpack3-fill', title:'Lets Travel', description:'O Mundo é minha morada'},
  {href: '/cart', icon:'bi-box-seam-fill', title:'Controle de Estoque', description:'Entrada e Saída de Mercadorias'},
  {href: '/login', icon:'bi-browser-firefox', title:'Navigation', description:'World Wide Web is Coming'},
  {href: '/logout', icon:'bi-chat-square-heart-fill', title:'Notifications', description:'Visualize suas mensagens'},
];

</script>

<template>
  <div class="container-fluid container-primary p-4">
      <MainNav v-bind="{active:true, menu:menu}" />
    <main>
      <RouterView />
    </main>
  </div>
  
</template>

