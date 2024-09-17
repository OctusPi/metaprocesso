<script setup>
import { onBeforeMount } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/services/layout';

import HeaderProposalUi from '@/components/HeaderProposalUi.vue';
import NavProposalUi from '@/components/NavProposalUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import http from '@/services/http';

const emit = defineEmits(['callAlert', 'callUpdate'])
const route = useRoute()

const [page] = Layout.new(emit, {
    url: '/proposal_supplier',
    auth_view: false,
    proposal: {}
})

onBeforeMount(() => {
    http.get(`${page.url}/check/${route.params.token}`, emit, (resp) => {
        if (http.success(resp)) {
            page.auth_view = true
            page.proposal = resp.data
        }
    })
})

</script>

<template>
    <div class="page">

        <NavProposalUi :data="{
            protocol: page.proposal?.process.protocol,
            organ: page.proposal?.organ.name,
            date_ini: `${page.proposal?.date_ini} - ${page.proposal?.hour_ini}`,
            date_fin: page.proposal?.price_record.date_fin,
            description: page.proposal?.process.description
        }" />

        <main class="main">

            <HeaderProposalUi :supplier="page.proposal.supplier ?? {}" />

            <section class="main-section container-fluid p-4">

                <div v-if="page.auth_view">
                    <div role="heading" class="inside-title mb-4">
                        <div>
                            <h2>Registro de Coleta</h2>
                            <p>
                                Preencha os dados para enviar sua proposta de preços
                            </p>
                        </div>
                    </div>

                    <div class="container p-4">
                        Conteudo da View
                    </div>
                </div>

                <div v-else>asas</div>

            </section>

            <FooterMainUi />
        </main>
    </div>
</template>
