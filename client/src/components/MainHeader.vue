<script setup>
import Notifications from './Notifications.vue';
import DarkMode from './DarkMode.vue';
import UserID from './UserID.vue';
import SearchBar from './SearchBar.vue';

</script>

<template>
    <header class="header-main d-md-flex justify-content-between align-items-center flex-row-reverse">
        
        <div class="header-main-action d-flex flex-fill justify-content-end mb-2">
            <SearchBar />
            <Notifications />
            <DarkMode />
            <UserID />
        </div>
        <div class="header-main-title d-flex align-items-center flex-fill mb-2">
            <i class="bi nav-link-icon bi-grid me-2 rounded-circle txt-color-base"></i>
            <div>
                <h1 class="p-0 m-0 txt-color">Title Page</h1>
                <span class="m-0 p-0 small txt-color-sec">Description about functionality page</span>
            </div>
        </div>
    </header>
</template>