<script setup>
import { inject } from 'vue';

    const sysapp = inject('sysapp')
</script>

<template>
    <div class="copyr">
        <p class="p-0 m-0">{{ sysapp.copy }}</p>
        <p class="p-0 m-0">Versão: {{ sysapp.version }}</p>
    </div>
</template>

<style scoped>
    .copyr{
        position: absolute;
        width: 200px;
        left: 40px;
        bottom: 40px;
    }

    .copyr p{
        font-size: 0.7rem;
        color: var(--color-text-secondary);
        opacity: 0.5;
    }

    @media (min-width: 991px) {
        .copyr{
            left: 200px;
        }
    }
</style>