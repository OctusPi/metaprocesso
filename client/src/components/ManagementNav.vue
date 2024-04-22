<script setup>
import { ref } from 'vue';

const menu = ref([
    { title: 'Orgãos', icon: 'bi-building-fill-gear', route: '/organs' },
    { title: 'Unidades', icon: 'bi-house-gear-fill', route: '/units' },
    { title: 'Setores', icon: 'bi-grid-fill', route: '/sectors' },
    { title: 'Ordenadores', icon: 'bi-person-raised-hand', route: '/ordinators' },
    { title: 'Demandantes', icon: 'bi-person-video2', route: '/demandants' },
    { title: 'Comissões', icon: 'bi-people-fill', route: '/comissions' },
    { title: 'Programas', icon: 'bi-circle-square', route: '/programs' },
    { title: 'Dotações', icon: 'bi-piggy-bank-fill', route: '/dotations' },
    { title: 'Usuários', icon: 'bi-person-bounding-box', route: '/management' }
])
</script>

<template>
    <div class="dropdown">
        <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Gestão</span>
        </button>
        <ul class="dropdown-menu">
            <li>
                <RouterLink v-for="m in menu" :key="m.title" :to="m.route" class="dropdown-item">
                    <i class="bi me-1" :class="m.icon"></i> {{ m.title }}
                </RouterLink>
            </li>
        </ul>
    </div>
</template>