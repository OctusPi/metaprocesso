<script setup>
import { watch } from 'vue';
import layout from '@/services/layout';
import utils from '@/utils/utils'

const emit = defineEmits(['callAlert'])

const props = defineProps({
    collect: { type: Object, default: () => { } },
    selects: { type: Object, default: () => { } },
    modal: { type: String, default: 'modalProposalDetails' }
})

const [page, pgData] = layout.new(emit, {
    url: '/proposals',
    data: props.collect ?? {},
    selects: props.selects,
    items_headers: [
        { key: 'item.code', title: 'COD', sub: [{ key: 'item.type' }] },
        { key: 'item.name', title: 'ITEM' },
        { key: 'item.description', title: 'DESCRIÇÃO' },
        { key: 'item.und', title: 'UDN', sub: [{ key: 'item.volume' }] },
        { key: 'program.name', title: 'VINC.', sub: [{ key: 'dotation.name' }] },
        { key: 'quantity', title: 'QUANT.' }
    ],
    dfds_headers: [
        { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { key: 'demandant.name', title: 'DEMANDANTE' },
        { key: 'ordinator.name', title: 'ORDENADOR' },
        { key: 'unit.name', title: 'ORIGEM' },
        { title: 'OBJETO', sub: [{ key: 'description' }] },
        { key: 'status', title: 'SITUAÇÃO' }
    ]
})

watch(() => props.collect, (newval) => {
    page.data = newval
})

watch(() => props.selects, (newval) => {
    page.selects = newval
})

</script>

<template>
    <div class="modal fade" :id="props.modal" tabindex="-1" :aria-labelledby="'#' + props.modal + 'Label'"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content p-4" v-if="!Object.is(page.data, {})">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-6 p-0 m-0 d-flex align-items-center gap-2"
                        :id="'#' + props.modal + 'Label'">
                        <ion-icon name="document-attach" class="icon-dfd" />
                        Coleta {{ page.data.protocol }}
                    </h1>
                    <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-action-close ms-auto">
                        <ion-icon name="close" class="fs-4"></ion-icon>
                    </button>
                </div>
                <div class="modal-body border-0">

                    <!-- organ -->
                    <div class="box-revisor mb-4 px-0">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Vinculo</h3>
                                <p>
                                    Dados do orgão e unidades vinculadas ao processo
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content px-0">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Orgão</h4>
                                    <p>
                                        {{ page.data?.organ?.name }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ page.data?.organ?.cnpj }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Unidades</h4>
                                    <p>
                                        {{ utils.extractTxt(page.data?.process?.units, 'title') }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Ordenadores</h4>
                                    <p>
                                        {{ utils.extractTxt(page.data?.process?.ordinators, 'name') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- origin -->
                    <div class="box-revisor mb-4 px-0">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Processo</h3>
                                <p>
                                    Dados do processo que originou a coleta de preço
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content px-0">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Protocolo</h4>
                                    <p>
                                        {{ page.data?.process?.date_hour_ini }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ page.data?.process?.protocol }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Modalidade</h4>
                                    <p>
                                        {{ utils.getTxt(page.selects.modalities, page.data?.process?.modality) }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ utils.getTxt(page.selects.process_types,
                                        page.data?.process?.type) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Tipo de Contratação</h4>
                                    <p>
                                        {{ page.data?.process?.acquisition_type == 1 ? 'Serviço' : 'Aquisição' }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ utils.getTxt(page.selects.acquisitions_dfd,
                                        page.data?.process?.acquisition_type) }}</p>
                                </div>
                                <div class="col-md-12">
                                    <h4>Descrição do Objeto</h4>
                                    <p>
                                        {{ page.data?.process?.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier -->
                    <div v-if="page.data.modality === 1" class="box-revisor mb-4 px-0">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Fornecedor</h3>
                                <p>
                                    Dados do fornecedor responsável por responder a solicitação
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content px-0">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Fornecedor</h4>
                                    <p>
                                        {{ page.data?.supplier?.name }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ page.data?.supplier?.cnpj }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Representante</h4>
                                    <p>
                                        {{ page.data?.supplier?.agent }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ page.data?.supplier?.cpf }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Contato</h4>
                                    <p>
                                        {{ page.data?.supplier?.email }} {{ page.data?.supplier?.phone }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ page.data?.supplier?.address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="box-revisor mb-4 px-0">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Coleta</h3>
                                <p>
                                    Informações da coleta lisgatem de itens e valores
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content px-0">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Modalidade Coleta</h4>
                                    <p>
                                        {{ utils.getTxt(page.selects?.proposal_modalities, page.data?.modality) }}
                                    </p>
                                    <p class="p-0 m-0 form-text">Prazo: {{ `${page.data?.pricerecord?.date_ini} - ${page.data?.pricerecord?.date_fin}`  }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Situação da Coleta</h4>
                                    <p>
                                        {{ utils.getTxt(page.selects?.proposal_status, page.data?.status) }}
                                    </p>
                                    <p class="p-0 m-0 form-text">
                                        <a v-if="page.data?.modality === 1 && page.data?.status == 4" href="#" @click.prevent="pgData.download(page.data?.id)" class="d-flex align-items-center">
                                            Proposta Assinada
                                            <ion-icon name="cloud-download-outline" class="fs-6 ms-2"></ion-icon>
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Data da Resposta</h4>
                                    <p>
                                       {{ page.data?.date_fin ?? '***' }} : {{ page.data?.hour_fin ?? '***' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- list items -->
                        <div v-if="page.data?.items && page.data?.items.length" class="table-responsive mt-4">
                                <table class="m-0 table-borderless table-striped table-hover table">
                                    <thead>
                                        <tr>
                                            <th>CÓDIGO</th>
                                            <th>ITEM</th>
                                            <th>UNIT.</th>
                                            <th class="text-center">QUANT.</th>
                                            <th>VALOR UNIT.</th>
                                            <th>TOTAL</th>
                                            <th v-if="page.data.modality !== 1">ORIGEM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="i in page.data?.items" :key="i.id">
                                            <td class="align-middle">
                                                <div class="small txt-color-sec">{{ i.item.type == 1 ? 'Material' : 'Serviço' }}</div>
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
                                                <div class="small">{{ utils.floatToCurrency(utils.currencyToFloat(i.value)) }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="small">{{ utils.floatToCurrency((i.quantity * utils.currencyToFloat(i.value)).toFixed(2)) }}</div>
                                            </td>
                                            <td v-if="page.data.modality !== 1" class="align-middle">
                                                <div class="small">{{ i.origin ?? '*****' }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.icon-dfd {
    background-color: var(--color-base-tls);
    color: var(--color-base);
    padding: 8px;
    border-radius: 8px;
    font-size: 1.2em;
}

p {
    margin: 0;
    padding: 0;
}
</style>