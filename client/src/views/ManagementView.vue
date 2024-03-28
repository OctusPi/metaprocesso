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
                
            </div>
        </section>
    </main>
</template>