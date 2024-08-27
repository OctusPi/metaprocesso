<script setup>

import { ref } from 'vue'

const menuitens = [
    { href: '/organs', icon: 'business', title: 'Orgãos', description: 'Dados do Orgão' },
    { href: '/units', icon: 'home', title: 'Unidades', description: 'Gestão de Unidades / Secretarias' },
    { href: '/sectors', icon: 'logo-buffer', title: 'Setores', description: 'Gestão de Setores' },
    { href: '/ordinators', icon: 'id-card', title: 'Ordenadores', description: 'Gestão de Ordenadores' },
    { href: '/demandants', icon: 'magnet', title: 'Demandantes', description: 'Gestão de Demandantes' },
    { href: '/comissions', icon: 'people-circle', title: 'Comissões', description: 'Gestão de Comissões' },
    { href: '/programs', icon: 'grid', title: 'Programas', description: 'Gestão de Programas' },
    { href: '/dotations', icon: 'diamond', title: 'Dotações', description: 'Gestão de Dotações' },
    { href: '/users', icon: 'person-circle', title: 'Usuarios', description: 'Gestão de Usuarios' },
    { href: '/catalogs', icon: 'bookmarks', title: 'Catálogos', description: 'Catálogos de Itens GOV' },
    { href: '/suppliers', icon: 'storefront', title: 'Fornecedores', description: 'Catálogos de Fornecedores' },
    { href: '/dfds', icon: 'document-attach', title: 'DFDs', description: 'Formalização de Demandas' },
    { href: '/processes', icon: 'document-text', title: 'Processos', description: 'Formalização de Processos' },
    { href: '/etps', icon: 'documents', title: 'ETPs', description: 'Estudos Técnicos Preliminares' },
    { href: '/pricerecords', icon: 'pricetags', title: 'Preços', description: 'Mapa de Registro de Preços' },
    { href: '/riskiness', icon: 'map', title: 'Mapa de Riscos', description: 'Mapa de Riscos' },
    { href: '/refterms', icon: 'newspaper', title: 'Termos', description: 'Termos de Referência' },
    { href: '/trasmission', icon: 'navigate', title: 'Publicar', description: 'Publicação e Transmissão' },
    { href: '/reports', icon: 'stats-chart', title: 'Relatórios', description: 'Relatórios de Acompanhamento e Planejamento' },
];

const comp = ref({
    search: '',
    list: []
})

function search_item() {
    const value = comp.value.search
    if (value.length) {
        comp.value.list = menuitens.filter(obj => obj.description.includes(comp.value.search))
    } else {
        comp.value.list = []
    }
}

</script>

<template>
    <div class="container-input">
        <input class="form-control" :class="{'active' : comp.list.length}" placeholder="Acesso Rápido" v-model="comp.search" @keyup="search_item">
        <div class="container-list" v-if="comp.list.length">
            <ul class="fast-link">
                <li v-for="i in comp.list" :key="i.title">
                    <RouterLink :to="i.href" class="d-flex align-items-center">
                        <ion-icon :name="i.icon" class="fs-5 me-2 txt-color-sec"></ion-icon>
                        <div>
                            <p class="p-0 m-0 txt-color">{{ i.title }}</p>
                            <p class="p-0 m-0 txt-color-sec">{{ i.description }}</p>
                        </div>
                    </RouterLink>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.container-input{
    position: relative;
}

.container-list{
    position: absolute;
    background-color: var(--color-input-focus);
    border-radius: 0 0 10px 10px;
    width: 100%;
    max-height: 300px;
    padding: 20px;
    overflow: auto;
}

.active{
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.fast-link{
    list-style: none;
    margin: 0;
    padding: 0;
}

.fast-link li{
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.8rem !important;
}

.fast-link li:hover{
    background-color: var(--color-base-trans-hover);
}
</style>