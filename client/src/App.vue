<script setup>
import { onMounted, ref } from 'vue'
import { RouterView } from 'vue-router'
import theme from '@/stores/theme'

import AlertUi from './components/AlertUi.vue';
import ModalDeleteUi from './components/ModalDeleteUi.vue';

const alert = ref({ show: false, data: {} })
const list = ref([])
const erase = ref({})

onMounted(() => {
    theme.apply_theme()
})
</script>

<template>
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