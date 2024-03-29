<script setup>
import { ref } from 'vue';
import Notifications from './Notifications.vue';
import DarkMode from './DarkMode.vue';
import UserID from './UserID.vue';
import SearchBar from './SearchBar.vue';

const props = defineProps({
    header:Object
})

const icon          = ref(props?.header?.icon)
const title         = ref(props?.header?.title)
const description   = ref(props?.header?.description)

</script>

<template>
    <header class="header-main d-md-flex justify-content-between align-items-center flex-row-reverse z-3">
        <div class="header-main-action d-flex flex-fill justify-content-end mb-2">
            <Notifications />
            <DarkMode />
            <UserID />
        </div>
        <div class="header-main-title d-flex align-items-center flex-fill mb-2">
            <i class="bi fs-3 me-2 rounded-circle txt-color-base" :class="icon"></i>
            <div>
                <h1 class="p-0 m-0 txt-color">{{ title }}</h1>
                <span class="m-0 p-0 small txt-color-sec">{{ description }}</span>
            </div>
        </div>
    </header>
</template>