<script setup>
import { onBeforeMount, ref } from 'vue';
    import auth from '@/stores/auth';

const menu = ref([])

const menuitens = {
        'organs': {href: '/organs', icon:'bi-building-fill-gear', title:'Orgãos', description:'Dados do Orgão'},
        'units': {href: '/units', icon:'bi-house-gear-fill', title:'Unidades', description:'Gestão de Unidades / Secretarias'},
        'sectors': {href: '/sectors', icon:'bi-grid-fill', title:'Setores', description:'Gestão de Setores'},
        'ordinators': {href: '/ordinators', icon:'bi-person-raised-hand', title:'Ordenadores', description:'Gestão de Ordenadores'},
        'demandants': {href: '/demandants', icon:'bi-person-video2', title:'Demandantes', description:'Gestão de Demandantes'},
        'comissions': {href: '/comissions', icon:'bi-people-fill', title:'Comissões', description:'Gestão de Comissões'},
        'programs': {href: '/programs', icon:'bi-circle-square', title:'Programas', description:'Gestão de Programas'},
        'dotations': {href: '/dotations', icon:'bi-piggy-bank-fill', title:'Dotações', description:'Gestão de Dotações'},
        'users': {href: '/users', icon:'bi-person-bounding-box', title:'Usuarios', description:'Gestão de Usuarios'},
    }

onBeforeMount(() => {
    menu.value = auth.getNavigation()
})
</script>

<template>
    <div class="dropdown">
        <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Gestão</span>
        </button>
        <ul class="dropdown-menu">
            <li v-for="(m, i) in menuitens" :key="i">
                <RouterLink v-if="menu.find(m => m.module == i)" :to="m.href" class="dropdown-item">
                    <i class="bi me-1" :class="m.icon"></i> {{ m.title }}
                </RouterLink>
            </li>
        </ul>
    </div>
</template>