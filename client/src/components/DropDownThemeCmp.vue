<script setup>

import theme from '@/stores/theme';

function changeTheme(mode) {
    theme.set_theme(mode)
    theme.apply_theme()
}
</script>

<template>
    <div class="dropdown">
        <button class="btn header-main-btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <ion-icon name="contrast-outline" class="fs-5"></ion-icon>
        </button>
        <ul class="dropdown-menu shadow-lg">
            <li><a class="dropdown-item d-flex align-items-center mb-1" href="#" @click="changeTheme('ligth')">
                    <ion-icon name="sunny-outline" class="me-2 fs-5"></ion-icon>
                    Modo Claro
                </a></li>
            <li><a class="dropdown-item d-flex align-items-center" href="#" @click="changeTheme('dark')">
                    <ion-icon name="moon-outline" class="me-2 fs-6"></ion-icon>
                    Modo Escuro
                </a></li>
        </ul>
    </div>
</template>