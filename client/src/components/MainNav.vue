<script setup>
    import { ref } from 'vue';

    const menu = ref([
    {href: '/', icon:'bi-cake2-fill', title:'Bolo de Niver', description:'Vamos Comemorar Again'},
    {href: '/about', icon:'bi-backpack3-fill', title:'Lets Travel', description:'O Mundo é minha morada'},
    {href: '/cart', icon:'bi-box-seam-fill', title:'Controle de Estoque', description:'Entrada e Saída de Mercadorias'},
    {href: '/login', icon:'bi-browser-firefox', title:'Navigation', description:'World Wide Web is Coming'},
    {href: '/logout', icon:'bi-chat-square-heart-fill', title:'Notifications', description:'Visualize suas mensagens'},])
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
                        <RouterLink class="nav-link nav-link-item" :to="i.href">
                            <i class="bi nav-link-icon" :class="i.icon"></i>
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