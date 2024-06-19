<script setup>
import { onBeforeMount, ref } from 'vue';
import auth from '@/stores/auth';

import MainNav from '@/components/MainNav.vue'
import MainHeader from '@/components/MainHeader.vue'

const menu = ref([])

const menuitens = {
    'organs': { href: '/organs', icon: 'bi-building-fill-gear', title: 'Orgãos', description: 'Dados do Orgão' },
    'units': { href: '/units', icon: 'bi-house-gear-fill', title: 'Unidades', description: 'Gestão de Unidades / Secretarias' },
    'sectors': { href: '/sectors', icon: 'bi-grid-fill', title: 'Setores', description: 'Gestão de Setores' },
    'ordinators': { href: '/ordinators', icon: 'bi-person-raised-hand', title: 'Ordenadores', description: 'Gestão de Ordenadores' },
    'demandants': { href: '/demandants', icon: 'bi-person-video2', title: 'Demandantes', description: 'Gestão de Demandantes' },
    'comissions': { href: '/comissions', icon: 'bi-people-fill', title: 'Comissões', description: 'Gestão de Comissões' },
    'programs': { href: '/programs', icon: 'bi-circle-square', title: 'Programas', description: 'Gestão de Programas' },
    'dotations': { href: '/dotations', icon: 'bi-piggy-bank-fill', title: 'Dotações', description: 'Gestão de Dotações' },
    'users': { href: '/users', icon: 'bi-person-bounding-box', title: 'Usuarios', description: 'Gestão de Usuarios' },
}

onBeforeMount(() => {
    menu.value = auth.getNavigation()
})
</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-gear-wide-connected',
                title: 'Gestão Administrativa',
                description: 'Ferramentas administrativas estruturais do Sistema'
            }" />

            <div class="box box-main p-0 rounded-4">

                <div class="inside-box">
                    <nav class="row p-0 m-0 mt-5">
                        <template v-for="(m, i) in menuitens" :key="i">
                            <div class="col-lg-3 col-sm-4 mb-4 text-center" v-if="menu.find(m => m.module == i)">
                                <RouterLink :to="m.href" class="man-link">
                                    <i class="bi me-1 mx-auto" :class="m.icon"></i>
                                    <p class="l-title">{{ m.title }}</p>
                                    <p class="l-desc">{{ m.description }}</p>
                                </RouterLink>
                            </div>
                        </template>
                    </nav>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
.inside-box {
    height: calc(100% - 20px);
}

.man-link {
    color: var(--color-text);
}

.man-link:hover {
    color: var(--color-base);
}

i {
    font-size: 2rem;
}

p {
    margin: 0;
    padding: 0;
}

.l-title {
    font-size: 0.9rem;
    font-weight: 700;
}

.l-desc {
    font-size: 0.7rem;
    color: var(--color-text-sec);
}
</style>