<script setup>
// import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import auth from '@/stores/auth';

const router = useRoute()
const menu = auth.getNavigation()
const menuitens = {
    'catalogs': { href: '/catalogs', icon: 'catalog.svg', title: 'Catálogos', description: 'Catálogos de Itens GOV' },
    'processes': { href: '/processes', icon: 'processes.svg', title: 'Processos', description: 'Formalização de Processos' },
    'dfds': { href: '/dfds', icon: 'dfds.svg', title: 'DFDs', description: 'Formalização de Demandas' },
    'etps': { href: '/etps', icon: 'etps.svg', title: 'ETPs', description: 'Estudos Técnicos Preliminares' },
    'pricerecords': { href: '/pricerecords', icon: 'prices.svg', title: 'Preços', description: 'Mapa de Registro de Preços' },
    'riskiness': { href: '/riskiness', icon: 'map.svg', title: 'Mapa de Riscos', description: 'Mapa de Riscos' },
    'refterms': { href: '/refterms', icon: 'terms.svg', title: 'Termos', description: 'Termos de Referência' },
    'trasmission': { href: '/trasmission', icon: 'share.svg', title: 'Publicar', description: 'Publicação e Transmissão' },
    'reports': { href: '/reports', icon: 'reports.svg', title: 'Relatórios', description: 'Relatórios de Acompanhamento e Planejamento' },
    'management': { href: '/management', icon: 'manager.svg', title: 'Gestão', description: 'Dados Administrativos e Estruturais' }

    // 'purchases': {href: '/purchases', icon:'bi-basket-fill', title:'Compras', description:'Ordens de Compras de Contratos'},
    // 'ocurrencys': {href: '/ocurrencys', icon:'bi-exclamation-diamond-fill', title:'Ocorrências', description:'Livro de Ocorrências de Fornecimento'},
    // 'constructions': {href: '/constructions', icon:'bi-building-fill-gear', title:'Obras', description:'Fiscalização e Acompanhamento de Obras'},
    // 'lightings': {href: '/lightings', icon:'bi-lightning-charge-fill', title:'Iluminação', description:'Fiscalização Iluminação Pública'},
    // 'trashcollect': {href: '/trashcollect', icon:'bi-truck-front-fill', title:'Coleta e Limpeza', description:'Coleta e Limpeza de Lixo'},
    // 'sanctions': {href: '/sanctions', icon:'bi-x-octagon-fill', title:'Sanções', description:'Processos de Sançao e Penalização'},
}

</script>

<template>
    <nav class="navbar navbar-expand-lg">

        <div class="navbar-container p-4">
            <div class="nav-header">
                <a class="brand" href="/">
                    <img src="../assets/imgs/logo.svg" alt="Bootstrap" height="38">
                </a>

                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="bi bi-grid nav-toggle-icon"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav" v-if="menu != null && Object.keys(menu).length > 0">
                    <li v-for="(i, j) in menuitens" :key="j" class="nav-item">
                        <RouterLink v-if="menu.find(m => m.module == j)" :to="i.href" class="nav-link nav-link-item"
                            :class="{ 'active-nav': router.name === i.href.replace('/', '') }">
                            <img :src="`/assets/icons/${i.icon}`" :alt="i.title" class="nav-icon">
                            <div class="nav-link-body">
                                <span class="nav-link-title">{{ i.title }}</span>
                                <span class="nav-link-description">{{ i.description }}</span>
                            </div>
                        </RouterLink>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style>
.navbar {
    z-index: 1000;
    height: calc(100% - 48px);
    top: 24px;
    width: 130px;
    overflow: auto;
    padding: 0;
    position: fixed;
    align-items: start;
    justify-content: center;
    border: var(--border-box);
    background-color: var(--color-nav);
    --bs-navbar-toggler-border-color: none;
    --bs-navbar-toggler-focus-width: none;
    border-radius: 12px !important;
}

.nav-toggle-icon {
    color: var(--color-nav-itens) !important;
    font-size: 1.2rem;
}

.navbar-nav {
    width: 100%;
    display: block;
    margin: 0;
    padding: 0;
    margin-top: 20px;
}

.navbar-container {
    width: 100% !important;
}

.brand {
    display: block !important;
    margin: 0 auto;
    text-align: center;
    margin-bottom: 20px;
}

.nav-link-item {
    width: 100%;
    text-align: center;
    color: var(--color-nav-itens);
}

.nav-link-icon {
    font-size: 1.6rem;
}

.nav-link-item:hover {
    color: var(--color-base);
}

.nav-link-title {
    display: block;
    font-weight: bold;
    font-size: 0.7rem;
}

.nav-link-description {
    display: none;
}

.active-nav {
    color: var(--color-base)
}

.nav-icon {
    width: 32px;
    margin-bottom: 5px;
}

@media (max-width: 991px) {
    .navbar {
        width: calc(100% - 48px);
        height: auto;
        max-height: calc(100% - 48px);
        justify-content: start;
    }

    .nav-header {
        width: 100%;
        display: flex;
    }

    .brand {
        display: inline-flex !important;
        vertical-align: middle;
        margin: 0;
    }

    .nav-link-body {
        display: block;
    }

    .nav-link-item {
        display: flex;
        text-align: start;
        margin: 10px;
    }

    .nav-link-item span {
        display: block;
        margin: 0;
        padding: 0;
    }

    .nav-link-icon {
        font-size: 1.8rem;
        margin-right: 10px;
    }

    .nav-link-title {
        font-size: 0.9rem;
    }

    .nav-link-description {
        display: block;
        font-size: small;
    }

    .nav-icon {
        width: 32px;
        margin-bottom: 0;
        margin-right: 10px;
    }
}
</style>