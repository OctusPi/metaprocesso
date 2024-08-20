<script setup>
import DropDownCalendarCmp from './DropDownCalendarCmp.vue';
import DropDownNotifyCmp from './DropDownNotifyCmp.vue';
import DropDownThemeCmp from './DropDownThemeCmp.vue';
import DropDownUserCmp from './DropDownUserCmp.vue';


</script>

<template>
    <header class="main-header py-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="search-bar flex-grow-1 me-2">
                <input class="form-control" placeholder="Acesso Rápido">
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