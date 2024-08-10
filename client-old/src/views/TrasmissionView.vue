<script setup>
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-send-check',
                title: 'Publicação de Dados',
                description: 'Adicionar descrição do modulo aqui'
            }" />

            <div class="box box-main p-0 rounded-4">
                <h1 class="fs-5 text-center my-5">Trasmitir pra Deus e o Mundo aqui...</h1>
            </div>
            </section>
            </main>
</template>