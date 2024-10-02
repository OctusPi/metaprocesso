<script setup>
import { onMounted, ref } from 'vue'
import { RouterView } from 'vue-router'
import theme from '@/stores/theme'

import AlertUi from './components/AlertUi.vue';
import ModalDeleteUi from './components/ModalDeleteUi.vue';
import ModalManualUi from './components/manual/ModalManualUi.vue';

const alert = ref({ show: false, data: {} })
const list = ref([])
const erase = ref({})

onMounted(() => {
    theme.apply_theme()
})
</script>

<template>

    <div id="load-wall" class="load-wall d-none">
        <img id="load-img" class="load-img" src="./assets/imgs/load.svg">
    </div>

    <ModalManualUi/>

    <AlertUi :alert="alert" />

    <RouterView 
        :datalist="list" 
        @callAlert="(data) => { alert = data }" 
        @callRemove="(data) => { erase = data }" />

    <ModalDeleteUi 
        :params="erase" 
        @callUpdate="(data) => { list = data }" 
        @callAlert="(data) => { alert = data }" />
</template>
