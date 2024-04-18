<script setup>
import { ref } from 'vue';

const menu = ref([
    { title: 'Bens e Serviços', icon: 'bi-book-half', route: '/catalogs' },
    { title: 'Fornecedores', icon: 'bi-person-lines-fill', route: '/suppliers' },
])
</script>

<template>
    <div class="dropdown">
        <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Catalógos</span>
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