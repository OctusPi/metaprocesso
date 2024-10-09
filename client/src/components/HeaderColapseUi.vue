<script setup>
import DropDownThemeCmp from './DropDownThemeCmp.vue';
</script>

<template>
    <header class="colapsed-header px-3 py-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="app-complete-header d-flex align-items-center ms-2 cursor-pointer">
                <img src="@/assets/imgs/logo.svg" alt="logomarca" />
                <h1 class="app-complete-header-title fs-5 p-0 m-0 ms-2">Metaprocesso</h1>
            </div>
            <div class="user-actions d-flex">
                <DropDownThemeCmp />
            </div>
        </div>
    </header>
</template>

<style scoped>

.app-complete-header img {
    width: 40px;
}

@media(max-width:760px) {
    .search-bar {
        display: none;
    }

    .app-title-header-main {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .app-title-header-main:hover {
        color: var(--color-base);
    }

    .app-complete-header-title {
        display: none;
    }
}
</style>