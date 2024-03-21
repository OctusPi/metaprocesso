<script setup>
    import { onMounted, ref } from 'vue';
    import { useJwt } from '@/stores/auth';

    const auth = useJwt()

    const menuitens = {
        'catalogs': {href: '/catalogs', icon:'bi-book-half', title:'Catálogos', description:'Catálogos de Itens GOV'},
        'suppliers': {href: '/suppliers', icon:'bi-person-lines-fill', title:'Fornecedores', description:'Lista de Fornecedores Habilitados'},
        'dfds': {href: '/dfds', icon:'bi-file-earmark-ruled-fill', title:'DFDs', description:'Formalização de Demandas'},
        'pricerecords': {href: '/pricerecords', icon:'bi-journal-album', title:'Registro de Preços', description:'Atas de Registro de Preços'},
        'contracts': {href: '/contracts', icon:'bi-file-earmark-text-fill', title:'Contratos', description:'Contratos de Bens e Serviços'},
        'purchases': {href: '/purchases', icon:'bi-basket-fill', title:'Compras', description:'Ordens de Compras de Contratos'},
        'stock': {href: '/stock', icon:'bi-inboxes-fill', title:'Estoque', description:'Controle de Entrada e Saída'},
        'ocurrencys': {href: '/ocurrencys', icon:'bi-exclamation-diamond-fill', title:'Ocorrências', description:'Livro de Ocorrências de Fornecimento'},
        'constructions': {href: '/constructions', icon:'bi-building-fill-gear', title:'Obras', description:'Fiscalização e Acompanhamento de Obras'},
        'lightings': {href: '/lightings', icon:'bi-lightning-charge-fill', title:'Iluminação', description:'Fiscalização Iluminação Pública'},
        'trashcollect': {href: '/trashcollect', icon:'bi-truck-front-fill', title:'Coleta e Limpeza', description:'Coleta e Limpeza de Lixo'},
        'sanctions': {href: '/sanctions', icon:'bi-x-octagon-fill', title:'Sanções', description:'Processos de Sançao e Penalização'},
        'reports': {href: '/reports', icon:'bi-clipboard-data-fill', title:'Relatórios', description:'Relatórios de Acompanhamento e Planejamento'},
        'management': {href: '/management', icon:'bi-house-gear-fill', title:'Gestão', description:'Dados Administrativos e Estruturais'}
    }


    const menu = ref([])

    onMounted(() => {
        menu.value = auth.getNavigation()
    })
</script>

<template>
    <nav class="navbar navbar-expand-lg rounded-4">
       
        <div class="navbar-container p-4">
            <div class="nav-header">
                <a class="brand" href="/">
                    <img src="../assets/imgs/logo.svg" alt="Bootstrap" height="38">
                </a>

                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-grid nav-toggle-icon"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li v-for="i in menu" class="nav-item"> 
                        <RouterLink v-if="menuitens[i.module]" class="nav-link nav-link-item" :to="menuitens[i.module].href">
                            <i class="bi nav-link-icon" :class="menuitens[i.module].icon"></i>
                            <div class="nav-link-body">
                                <span class="nav-link-title">{{ menuitens[i.module].title }}</span>
                                <span class="nav-link-description">{{ menuitens[i.module].description }}</span>
                            </div>
                        </RouterLink>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style>
    .navbar{
        z-index: 1000;
        height: calc(100% - 48px);
        top:24px;
        width: 120px;
        overflow: auto;
        padding: 0;
        position: fixed;
        align-items: start;
        justify-content: center;
        background-color: var(--color-nav);
        --bs-navbar-toggler-border-color:none;
        --bs-navbar-toggler-focus-width:none;
    }

    .nav-toggle-icon{
        color: var(--color-nav-itens) !important;
        font-size: 1.2rem;
    }

    .navbar-nav{
        width: 100%;
        display: block;
        margin-top: 20px;
    }

    .navbar-container{
        width: 100% !important;
    }

    .brand{
        display: block !important;
        margin: 0 auto;
        text-align: center;
        margin-bottom: 20px;
    }

    .nav-link-body{
        display: none;
    }

    .nav-link-item{
        text-align: center;
        margin: 10px;
        color: var(--color-nav-itens);
    }

    .nav-link-icon{
        font-size: 1.8rem;
    }

    .nav-link-item:hover{
        color: var(--color-base);
    }

    @media (max-width: 991px) {
        .navbar{
            width: calc(100% - 48px);
            height: auto;
            max-height: calc(100% - 48px);
            justify-content: start;
        }

        .nav-header{
            width: 100%;
            display: flex;
        }
        
        .brand{
            display: inline-flex !important;
            vertical-align: middle;
            margin: 0;
        }

        .nav-link-body{
            display: block;
        }

        .nav-link-item{
            display: flex;
            text-align: start;
        }

        .nav-link-item span{
            display: block;
            margin: 0;
            padding: 0;
        }

        .nav-link-icon{
            font-size: 1.8rem;
            margin-right: 10px;
        }

        .nav-link-title{
            font-weight: bold;
        }

        .nav-link-description{
            font-size: small;
        }
    }
</style>