<script setup>
import { onBeforeMount } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/services/layout';
import http from '@/services/http';
import masks from '@/utils/masks';
import utils from '@/utils/utils';

import HeaderProposalUi from '@/components/HeaderProposalUi.vue';
import NavProposalUi from '@/components/NavProposalUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import FileInput from '@/components/inputs/FileInput.vue';

const emit = defineEmits(['callAlert', 'callUpdate'])
const route = useRoute()

const [page, pageData] = Layout.new(emit, {
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
    <div class="page" v-if="page.auth_view">

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

                <div>
                    <div role="heading" class="inside-title mb-4">
                        <div>
                            <h2>Registro de Coleta</h2>
                            <p>
                                Preencha os dados para enviar sua proposta de preços
                            </p>
                        </div>
                    </div>

                    <div class="container content p-0 mb-4">
                        <div class="tablelist">
                            <div class="table-responsive-md">
                                <table class="m-0 table-borderless table-striped table-hover table">
                                    <thead>
                                        <tr>
                                            <th>CÓDIGO</th>
                                            <th>ITEM</th>
                                            <th>QUANTIDADE</th>
                                            <th>VALOR UNITÁRIO</th>
                                            <th class="pe-2">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="i in page.proposal.items ?? page.proposal?.dfd_items" :key="i.id">
                                            <td class="align-middle">
                                                <div class="small">{{ i.item.code }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="small txt-color-sec">{{ i.item.name }}</div>
                                                <div class="small">{{ i.item.description }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="small">{{ i.quantity }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <input type="text" class="form-control input-table"
                                                    v-maska:[masks.maskmoney] v-model="i.value">
                                            </td>
                                            <td class="align-middle">
                                                <div class="small">{{ utils.floatToCurrency((i.quantity *
                                                    utils.currencyToFloat(i.value)).toFixed(2)) }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div role="heading" class="inside-title mb-4">
                        <div>
                            <h2>Enviar Proposta</h2>
                            <p>
                                Adicione a sua logomarca, faça o download da proposta, adicione sua assinatura escrita
                                ou digital e anexe o documento assinado.
                            </p>
                        </div>
                    </div>
                    <div class="container content p-4 pt-1">
                        <form class="form-row" @submit.prevent="pageData.save()">
                            <div class="row m-0 g-3">
                                <div class="col-sm-12 col-md-4">
                                    <label for="code" class="form-label">Código Validação</label>
                                    <input type="text" name="code" class="form-control" id="code"
                                        placeholder="00000" v-model="page.data.code">
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <FileInput label="Selecionar logomarca" identify="logomarca"
                                        v-model="page.data.logomarca" :valid="page.valids.logomarca" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <FileInput label="Anexar documento assinado" identify="document"
                                        v-model="page.data.document" :valid="page.valids.document" />
                                </div>
                            </div>
                            <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Enviar Proposta
                            </button>
                            <button type="button" class="btn btn-action-tertiary">
                                <ion-icon name="cloud-download-outline" class="fs-5"></ion-icon>
                                Download Proposta
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>
