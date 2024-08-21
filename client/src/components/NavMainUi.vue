<script setup>
// import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import auth from '@/stores/auth';

const router = useRoute()
const menu = auth.get_user()?.navigation
const menuitens = {
    'management': {
        href: '/management', icon: 'cog', title: 'Gestão', description: 'Dados Administrativos e Estruturais', sub: {
            'organs': { href: '/organs', icon: 'business', title: 'Orgãos', description: 'Dados do Orgão' },
            'units': { href: '/units', icon: 'home', title: 'Unidades', description: 'Gestão de Unidades / Secretarias' },
            'sectors': { href: '/sectors', icon: 'logo-buffer', title: 'Setores', description: 'Gestão de Setores' },
            'ordinators': { href: '/ordinators', icon: 'id-card', title: 'Ordenadores', description: 'Gestão de Ordenadores' },
            'demandants': { href: '/demandants', icon: 'magnet', title: 'Demandantes', description: 'Gestão de Demandantes' },
            'comissions': { href: '/comissions', icon: 'people-circle', title: 'Comissões', description: 'Gestão de Comissões' },
            'programs': { href: '/programs', icon: 'grid', title: 'Programas', description: 'Gestão de Programas' },
            'dotations': { href: '/dotations', icon: 'diamond', title: 'Dotações', description: 'Gestão de Dotações' },
            'users': { href: '/users', icon: 'person-circle', title: 'Usuarios', description: 'Gestão de Usuarios' },
        }
    },
    'catalogs': { href: '/catalogs', icon: 'bookmarks', title: 'Catálogos', description: 'Catálogos de Itens GOV' },
    'dfds': { href: '/dfds', icon: 'document-attach', title: 'DFDs', description: 'Formalização de Demandas' },
    'processes': { href: '/processes', icon: 'document-text', title: 'Processos', description: 'Formalização de Processos' },
    'etps': { href: '/etps', icon: 'documents', title: 'ETPs', description: 'Estudos Técnicos Preliminares' },
    'pricerecords': { href: '/pricerecords', icon: 'pricetags', title: 'Preços', description: 'Mapa de Registro de Preços' },
    'riskiness': { href: '/riskiness', icon: 'map', title: 'Mapa de Riscos', description: 'Mapa de Riscos' },
    'refterms': { href: '/refterms', icon: 'newspaper', title: 'Termos', description: 'Termos de Referência' },
    'trasmission': { href: '/trasmission', icon: 'navigate', title: 'Publicar', description: 'Publicação e Transmissão' },
    'reports': { href: '/reports', icon: 'stats-chart', title: 'Relatórios', description: 'Relatórios de Acompanhamento e Planejamento' }
}

</script>

<template>
    <nav class="main-nav p-4">
        <div class="app-title d-flex align-items-center">
            <img src="@/assets/imgs/logo.svg" alt="logomarca">
            <h1 class="fs-5 p-0 m-0 ms-2">Metaprocesso</h1>
        </div>
        <div class="nav-items">
            <ul class="navbar-nav" v-if="menu != null && Object.keys(menu).length > 0">
                <li v-for="(i, j) in menuitens" :key="j" class="nav-item">
                    <template v-if="i.sub">
                        <div class="accordion" :id="`accordion_${j}`">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        :data-bs-target="`#collapseOne_${j}`" aria-expanded="true"
                                        :aria-controls="`collapseOne_${j}`">
                                        <ion-icon :name="i.icon" class="nav-icon"></ion-icon>
                                        <div class="nav-link-body">
                                            <span class="nav-link-title">{{ i.title }}</span>
                                        </div>
                                    </button>
                                </h2>
                                <div :id="`collapseOne_${j}`" class="accordion-collapse collapse"
                                    :data-bs-parent="`#accordion_${j}`">
                                    <div class="accordion-body">
                                        <ul class="accordion-submenu">
                                            <li v-for="(h, l) in i.sub" :key="l" class="my-1">
                                                <RouterLink class="nav-subitem d-flex align-items-center"
                                                    :to="h.href">
                                                    <ion-icon :name="h.icon" class="sub-nav-icon"></ion-icon>
                                                    <span class="sub-nav-title">{{ h.title }}</span>
                                                </RouterLink>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <RouterLink v-if="menu.find(m => m.module == j)" :to="i.href" class="nav-link nav-link-item"
                            :class="{ 'active-nav': router.name === i.href.replace('/', '') }">
                            <ion-icon :name="i.icon" class="nav-icon"></ion-icon>
                            <div class="nav-link-body">
                                <span class="nav-link-title">{{ i.title }}</span>
                            </div>
                        </RouterLink>
                    </template>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style scoped>



.accordion, .accordion-item, .accordion-item, .accordion-body{
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
}

.accordion-button {
  background-color: transparent;
  color: inherit;
  box-shadow: none;
}

.accordion-button::after {
  display: none;
}

.accordion-body {
  border: none;
  background-color: transparent;
}

.accordion {
  margin: 0;
  padding: 0;
}

.accordion-item {
  margin: 0;
  padding: 0;
}

.accordion-header {
  margin: 0;
  padding: 0;
}

.accordion-button {
  margin: 0;
  padding: 0;
}

.accordion-body {
  margin: 0;
  padding: 0;
}

</style>