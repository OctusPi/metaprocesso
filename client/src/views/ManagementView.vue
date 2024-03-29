<script setup>
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';

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

                <ul class="nav justify-content-end mb-4">
                    <li class="nav-item">
                        <RouterLink to="/organs" class="submenu-item btn btn-sm btn-outline-primary small fw-medium ms-2" href="#">
                            <i class="bi bi-building-fill-gear me-1"></i> Orgãos
                        </RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/units" class="submenu-item btn btn-sm btn-outline-primary small fw-medium ms-2" href="#">
                            <i class="bi bi-house-gear-fill me-1"></i> Unidades
                        </RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/management" class="submenu-item btn btn-sm btn-outline-primary small fw-medium ms-2" href="#">
                            <i class="bi bi-people-fill me-1"></i> Usuários
                        </RouterLink>
                    </li>
                </ul>

            <div class="box p-4 mb-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">Info Principal</h2>
                        <p class="small txt-color-sec p-0 m-0">Info Secundária</p>
                    </div>
                    <div class="action-buttons d-flex">
                        <button type="button"
                        title="Adicionar Usuário"
                        class="btn btn-sm btn-action btn-action-primary ms-2"><i class="bi bi-plus-circle"></i></button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-action btn-action-primary ms-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-search"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-sm btn-action btn-action-primary ms-2"><i class="bi bi-file-richtext"></i></button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>