<script setup>
import { useRoute } from 'vue-router';
import auth from '@/stores/auth';

const router = useRoute();
const menu = auth.get_user()?.navigation;

const menuitens = {
    management: {
        href: '/management', icon: 'apps', title: 'Gestão', description: 'Dados Administrativos e Estruturais', sub: {
            organs: { href: '/organs', icon: 'business', title: 'Orgãos', description: 'Dados do Orgão' },
            units: { href: '/units', icon: 'home', title: 'Unidades', description: 'Gestão de Unidades / Secretarias' },
            sectors: { href: '/sectors', icon: 'logo-buffer', title: 'Setores', description: 'Gestão de Setores' },
            ordinators: { href: '/ordinators', icon: 'id-card', title: 'Ordenadores', description: 'Gestão de Ordenadores' },
            demandants: { href: '/demandants', icon: 'magnet', title: 'Demandantes', description: 'Gestão de Demandantes' },
            comissions: { href: '/comissions', icon: 'people-circle', title: 'Comissões', description: 'Gestão de Comissões' },
            programs: { href: '/programs', icon: 'grid', title: 'Programas', description: 'Gestão de Programas' },
            dotations: { href: '/dotations', icon: 'diamond', title: 'Dotações', description: 'Gestão de Dotações' },
            users: { href: '/users', icon: 'person-circle', title: 'Usuarios', description: 'Gestão de Usuarios' },
        },
    },
    catalogs: { href: '/catalogs', icon: 'bookmarks', title: 'Catálogos', description: 'Catálogos de Itens GOV' },
    suppliers: { href: '/suppliers', icon: 'storefront', title: 'Fornecedores', description: 'Catálogos de Fornecedores' },
    dfds: { href: '/dfds', icon: 'document-attach', title: 'DFDs', description: 'Formalização de Demandas' },
    processes: { href: '/processes', icon: 'document-text', title: 'Processos', description: 'Formalização de Processos' },
    etps: { href: '/etps', icon: 'documents', title: 'ETPs', description: 'Estudos Técnicos Preliminares' },
    pricerecords: { href: '/pricerecords', icon: 'pricetags', title: 'Preços', description: 'Mapa de Registro de Preços' },
    riskiness: { href: '/riskiness', icon: 'map', title: 'Mapa de Riscos', description: 'Mapa de Riscos' },
    refterms: { href: '/refterms', icon: 'newspaper', title: 'Termos', description: 'Termos de Referência' },
    trasmission: { href: '/trasmission', icon: 'navigate', title: 'Publicar', description: 'Publicação e Transmissão' },
    reports: { href: '/reports', icon: 'stats-chart', title: 'Relatórios', description: 'Relatórios de Acompanhamento e Planejamento' },
};

function close_menu() {
    const menu = document.getElementById('nav-primary')
    if (menu) {
        menu.style.display = 'none'
    }
}
</script>

<template>
    <nav class="main-nav p-4" id="nav-primary">
        <div class="app-title d-flex align-items-center ms-2">
            <RouterLink to="/home"><img src="@/assets/imgs/logo.svg" alt="logomarca" /></RouterLink>
            <h1 class="fs-5 p-0 m-0 ms-2">Metaprocesso</h1>
            <ion-icon name="chevron-back-outline" class="fs-6 hide-menu ms-2" @click="close_menu"></ion-icon>
        </div>
        <div class="nav-items">
            <ul class="navbar-nav" v-if="menu && Object.keys(menu).length">
                <template v-for="(item, key) in menuitens" :key="key">
                    <li v-if="menu.find(m => m.module === key)" class="items">
                        <div v-if="item.sub" class="accordion" :id="`accordion_${key}`">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button navmain-item" type="button"
                                        data-bs-toggle="collapse" :data-bs-target="`#collapseOne_${key}`"
                                        aria-expanded="true" :aria-controls="`collapseOne_${key}`">
                                        <ion-icon :name="item.icon" class="navmain-icon"></ion-icon>
                                        <span class="navmain-title">{{ item.title }}</span>
                                    </button>
                                </h2>
                                <div :id="`collapseOne_${key}`" class="accordion-collapse collapse"
                                    :data-bs-parent="`#accordion_${key}`">
                                    <div class="accordion-body">
                                        <ul class="accordion-submenu">
                                            <li v-for="(subItem, subKey) in item.sub" :key="subKey" class="subitems">
                                                <RouterLink class="navmain-item" :to="subItem.href">
                                                    <ion-icon :name="subItem.icon" class="navmain-icon"></ion-icon>
                                                    <span class="navmain-title">{{ subItem.title }}</span>
                                                </RouterLink>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <RouterLink v-else :to="item.href" class="navmain-item"
                            :class="{ 'active-nav': router.name === item.href.replace('/', '') }">
                            <ion-icon :name="item.icon" class="navmain-icon"></ion-icon>
                            <span class="navmain-title">{{ item.title }}</span>
                        </RouterLink>
                    </li>
                </template>
            </ul>
        </div>
    </nav>
</template>


<style scoped>
.hide-menu {
    display: none;
    cursor: pointer;
}

.hide-menu:hover {
    color: var(--color-base);
}

.items {
    margin-bottom: 25px;
}

.subitems {
    margin-top: 20px;
}

.accordion-submenu {
    margin: 0 0 0 20px;
    list-style: none;
    padding: 0;
}

.navmain-item {
    font-family: 'Inter', 'Roboto', Arial, Helvetica, sans-serif !important;
    color: var(--color-input-txt) !important;
    font-weight: 500 !important;
    font-size: 0.90rem !important;
    display: flex;
    align-items: center;
}

.navmain-item:hover {
    color: var(--color-base) !important;
}

.navmain-icon {
    margin-right: 12px;
    font-size: 1.1rem !important;
}

@media(max-width:760px) {
    .main-nav {
        box-shadow: 2px 3px 30px 20px #31313113;
    }

    .hide-menu {
        display: block;
    }
}
</style>
