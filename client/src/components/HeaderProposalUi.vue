<script setup>
import dates from '@/utils/dates';

defineProps({
    supplier:{type:Object, required: true}
})

const data_now = dates.dateTxtNow().split(',')

function open_menu() {
    const menu = document.getElementById('nav-primary')
    if (menu) {
        menu.style.display = 'block'
    }
}

</script>

<template>
    <header class="main-header py-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="app-title-header-main me-2" @click="open_menu">
                <img src="@/assets/imgs/logo.svg" alt="logomarca" class="logo-header" />
                <ion-icon name="chevron-forward-outline" class="fs-6"></ion-icon>
            </div>
            <div class="search-bar flex-grow-1 me-2">
                <h1 class="p-0 m-0">{{ supplier.name }}</h1>
                <p class="p-0 m-0">{{ supplier.cnpj }}</p>

            </div>
            <div class="user-actions">
                <h1 class="p-0 m-0">{{ data_now[0] }}</h1>
                <p class="p-0 m-0">{{ data_now[1] }}</p>
            </div>
        </div>
    </header>
</template>

<style scoped>
.logo-header{
    height: 34px;
}

.app-title-header-main{
    display: none;
}

@media(max-width:760px){
    .search-bar{
        display: none;
    }

    .app-title-header-main{
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .app-title-header-main:hover{
        color: var(--color-base);
    }
}
</style>