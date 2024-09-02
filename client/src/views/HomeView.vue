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
                
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Dashboard</h2>
                        <p>
                            Panorama resultados ano corrente
                        </p>
                    </div>
                </div>

                <div class="container p-4">
                    Conteudo da View
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>
