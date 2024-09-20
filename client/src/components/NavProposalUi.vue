<script setup>

defineProps({
    data:{type:Object, required:true}
})

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
            <ul class="navbar-nav">
                <li class="mb-3">
                    <span class="strong d-block">Processo: </span> {{ data.protocol }}
                </li>
                <li class="mb-3">
                    <span class="strong d-block">Solicitante: </span>{{ data.organ }}
                </li>
                <li class="mb-3">
                    <span class="strong d-block">Data Solicitação: </span>{{ data.date_ini}}
                </li>
                <li class="mb-3">
                    <span class="strong d-block">Prazo Resposta: </span>{{ data.date_fin}} - 00:00:00
                </li>
                <li class="mb-3">
                    <span class="strong d-block">Objeto: </span>{{ data.description }}
                </li>
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

.strong{
    font-weight: 600 !important;
    color: var(--color-text)
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
