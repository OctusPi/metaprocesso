<script setup>
import DropDownCalendarCmp from './DropDownCalendarCmp.vue';
import DropDownNotifyCmp from './DropDownNotifyCmp.vue';
import DropDownThemeCmp from './DropDownThemeCmp.vue';
import DropDownUserCmp from './DropDownUserCmp.vue';
import InputFastAccess from './inputs/InputFastAccess.vue'

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
                <InputFastAccess />
            </div>
            <div class="user-actions d-flex">
                <DropDownNotifyCmp />
                <DropDownCalendarCmp />
                <DropDownThemeCmp />
                <DropDownUserCmp />
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