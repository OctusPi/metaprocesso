<script setup>
import { createApp, inject, onBeforeMount, watch } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/services/layout';
import http from '@/services/http';
import masks from '@/utils/masks';
import utils from '@/utils/utils';
import exp from '@/services/export';

import HeaderProposalUi from '@/components/HeaderProposalUi.vue';
import NavProposalUi from '@/components/NavProposalUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import FileInput from '@/components/inputs/FileInput.vue';
import ProposalReport from './reports/ProposalReport.vue';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';

const sysapp = inject('sysapp')
const emit = defineEmits(['callAlert', 'callUpdate'])
const route = useRoute()


const [page, pageData] = Layout.new(emit, {
    url: '/proposal_supplier',
    auth_view: false,
    data_logo:null,
    proposal: {},
    rules: {
        logomarca:'required',
        representation: 'required',
        cpf: 'required'
    }
})

function sendProposal() {
    pageData.save({
        status:4, 
        items:page.proposal.items ?? page.proposal.dfd_items
    }, checkProposal, false)
}

function sendPatialProposal() {
    pageData.save({
        status:3, 
        items:page.proposal.items ?? page.proposal.dfd_items
    }, null, false)
}

function checkProposal(){
    http.get(`${page.url}/check/${route.params.token}`, emit, (resp) => {
        if (http.success(resp)) {
            page.auth_view = true
            page.proposal = resp.data
            page.data = {
                id: resp.data?.id,
                logomarca:resp.data?.logomarca,
                representation:resp.data?.representation,
                cpf:resp.data?.cpf
            }
        }
    }, () => {
        page.auth_view = false
        page.proposal = {}
        page.data = {}
    })
}

function exportProposal(){
    const checkform = forms.checkform(page.data, {
        fields: page.rules,
        valids: page.valids
    })
    if(!checkform.isvalid){
        emit('callAlert', notifys.warning(checkform.message))
        return
    }

    const containerReport = document.createElement('div')
    const instanceReport = createApp(ProposalReport, {
        qrdata:sysapp, 
        logomarca:page.data.logomarca,
        supplier:page.proposal.supplier,
        process: {
            protocol: page.proposal?.process.protocol,
            organ: page.proposal?.organ,
            date_ini: `${page.proposal?.date_ini} - ${page.proposal?.hour_ini}`,
            date_fin: page.proposal?.pricerecord.date_fin,
            description: page.proposal?.process.description
        },
        items: page.proposal.items ?? page.proposal.dfd_items,
        representation:{
            name:page.data.representation,
            cpf: page.data.cpf
        }
     })
    instanceReport.mount(containerReport)
    exp.exportPDF(containerReport, `Coleta-${page.proposal.protocol}`)
}

watch(() => page.data_logo, (new_value) => {
    if(new_value != null){
        const reader = new FileReader()
        reader.readAsDataURL(new_value)
        reader.onloadend = () => {
            page.data.logomarca = reader.result
        }
    }
})

onBeforeMount(() => {
    checkProposal()
})

</script>

<template>

    <div class="modal fade" id="modalConfirmProposal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content box content p-4">

                <div class="modal-body py-0">
                    <div class="text-center text-success">
                        <ion-icon name="checkmark-done-outline" class="fs-1" />
                    </div>
                    <p class="text-center px-3">Confirmar envio de proposta? A coleta será finalizada e não será
                        possível realizar novas edições.</p>
                    <div class="row">
                        <div class="col">
                            <label for="representation" class="form-label">Código de validação</label>
                            <input placeholder="000-000000-0000" type="text" name="code" class="form-control"
                                :class="{ 'form-control-alert': page.valids.logomarca }" id="code-validation"
                                v-model="page.data.code" @keydown.enter.prevent="sendProposal">
                            <div id="codeHelpBlock" class="form-text txt-color-sec">
                                Informe o código de validação recebido pelo e-mail.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-action-tertiary" data-bs-dismiss="modal">
                        <ion-icon name="close" />
                        Cancelar
                    </button>
                    <button @click="sendProposal" type="button" class="btn btn-action-quaternary"
                        :data-bs-dismiss="page.data.code ? 'modal' : null">
                        <ion-icon name="checkmark-done-outline" />
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page" v-if="page.auth_view">

        <NavProposalUi :data="{
            protocol: page.proposal?.process.protocol,
            organ: page.proposal?.organ.name,
            date_ini: `${page.proposal?.date_ini} - ${page.proposal?.hour_ini}`,
            date_fin: page.proposal?.pricerecord.date_fin,
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
                                Preencha os dados para enviar sua proposta de preços, adicione a logo da sua empresa,
                                baixe a proposta assine e anexe ao envio.
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
                                            <th>UNIT.</th>
                                            <th class="text-center">QUANT.</th>
                                            <th>VALOR UNIT.</th>
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
                                                <div class="small txt-color-sec">{{ i.item.und }}</div>
                                                <div class="small">{{ i.item.volume }}</div>
                                            </td>
                                            <td class="align-middle text-center">
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

                    <div class="container content p-4 pt-1">
                        <form class="form-row" @submit.prevent="() => false">
                            <div class="row m-0 g-3">
                                <div class="col-sm-12 col-md-3">
                                    <label for="representation" class="form-label">Nome do Representante</label>
                                    <input type="text" name="representation" class="form-control" id="representation"
                                        placeholder="Nome completo do representante" v-model="page.data.representation" 
                                        :class="{ 'form-control-alert': page.valids.representation }">

                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <label for="cpf" class="form-label">CPF do Representante</label>
                                    <input type="text" name="cpf" class="form-control" id="cpf"
                                        placeholder="000.000.000-00" v-maska:[masks.maskcpf] v-model="page.data.cpf"
                                        :class="{ 'form-control-alert': page.valids.cpf }" >

                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <FileInput label="Selecionar logomarca" identify="logomarca"
                                        v-model="page.data_logo" :valid="page.valids.logomarca" accept="image/*" />
                                    <div id="logomarcaHelpBlock" class="form-text txt-color-sec">
                                        Logomarca ou timbre da empresa
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <FileInput label="Anexar documento assinado" identify="document"
                                        v-model="page.data.document" :valid="page.valids.document" />
                                    <div id="docHelpBlock" class="form-text txt-color-sec">
                                        Anexar proposta assinada pelo representante
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row-reverse gap-2 mt-4">
                                <button type="button" class="btn btn-action-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalConfirmProposal">
                                    <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                    Enviar Proposta
                                </button>
                                <button type="button" class="btn btn-action-quaternary" @click="sendPatialProposal">
                                    <ion-icon name="sparkles-outline" class="fs-5"></ion-icon>
                                    Salvar Parcialmente
                                </button>
                                <button type="button" class="btn btn-action-tertiary" @click="exportProposal">
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
