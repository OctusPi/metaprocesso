<script setup>
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-journal-plus',
                title: 'Termos de Referência',
                description: 'Adicionar descrição do modulo aqui'
            }" />

            <div class="box box-main p-0 rounded-4">
                <h1 class="fs-5 text-center my-5">Termos de Referencia</h1>
            </div>
            </section>
            </main>
</template>