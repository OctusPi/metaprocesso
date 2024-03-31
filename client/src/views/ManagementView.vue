<script setup>
import { onMounted, ref } from 'vue'

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';

const showSearch = ref(false)
const showRegister = ref(false)

onMounted(() => {

})

</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-house-gear',
                title: 'Gestão e Controle Organizacional',
                description: 'Estrutura e Perminissionamento de Usuários'
            }" />

            <div class="box p-4 mb-4 rounded-4">
                <div class="d-md-flex justify-content-between align-items-center mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">Info Principal</h2>
                        <p class="small txt-color-sec p-0 m-0">Info Secundária</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <div class="dropdown">
                            <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                                <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Gestão</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <RouterLink to="/organs" class="dropdown-item">
                                        <i class="bi bi-building-fill-gear me-1"></i> Orgãos
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/units" class="dropdown-item">
                                        <i class="bi bi-house-gear-fill me-1"></i> Unidades
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/sectors" class="dropdown-item">
                                        <i class="bi bi-grid-fill me-1"></i> Setores
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/programs" class="dropdown-item">
                                        <i class="bi bi-circle-square me-1"></i> Programas
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/dotations" class="dropdown-item">
                                        <i class="bi bi-piggy-bank-fill me-1"></i> Dotações
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/management" class="dropdown-item">
                                        <i class="bi bi-person-bounding-box me-1"></i> Usuários
                                    </RouterLink>
                                </li>
                            </ul>
                        </div>
                        <button @click="showRegister = !showRegister" type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                        <button @click="showSearch = !showSearch" type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-search"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Pesquisar</span>
                        </button>
                        <button type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-file-richtext"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Exportar</span>
                        </button>
                        
                    </div>
                </div>

                <div v-if="showSearch" id="search-box" class="container">
                    sdasda
                </div>

                <div v-if="showRegister" id="register-box" class="container">
                    adicionar
                </div>
            </div>
        </section>
    </main>
</template>