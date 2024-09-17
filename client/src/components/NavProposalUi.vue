<script setup>

defineProps({
    process:{type:Object, required:true}
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
                <li>
                    <span class="strong">Processo: </span> {{ process.protocol }}
                </li>
                <li>
                    <span class="strong">Orgão: </span>{{ process.protocol }}
                </li>
                <li>
                    <span class="strong">Data Solicitação: </span>{{ process.protocol }}
                </li>
                <li>
                    <span class="strong">Objeto: </span>{{ process.protocol }}
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

@media(max-width:760px) {
    .main-nav {
        box-shadow: 2px 3px 30px 20px #31313113;
    }

    .hide-menu {
        display: block;
    }
}
</style>
