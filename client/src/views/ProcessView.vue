<script setup>
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-journal-bookmark',
                title: 'Gestor de Processos',
                description: 'Registro de Processos de Aquisição de Bens e Serviços'
            }" />

            <div class="box box-main p-0 rounded-4">
                
            </div>
        </section>
    </main>
</template>

<style scoped>

</style>