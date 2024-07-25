<script setup>
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-view-list',
                title: 'Dashboard',
                description: 'Panorama de contratações e estágios de processos.'
            }" />

            <div class="box box-main p-0 rounded-4">
                <h1 class="fs-5 text-center my-5">DashBoard</h1>
            </div>
            </section>
            </main>
</template>