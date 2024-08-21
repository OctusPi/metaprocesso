<script setup>
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';

</script>

<template>
  <div class="page">
        <NavMainUi />

        <main class="main">
            <HeaderMainUi />

            <section class="main-section container-fluid p-4">
                
                <header class="inside-title mb-2">
                    <h2 class="p-0 m-0">Organs</h2>
                    <p class="p-0 m-0">Descricao das funcionalidade da view organs</p>
                </header>

                <div class="container p-4">
                    Conteudo da View
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>
