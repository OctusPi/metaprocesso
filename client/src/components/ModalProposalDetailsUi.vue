<script setup>
import { watch } from 'vue';
import TableList from './table/TableList.vue';
import Mounts from '@/services/mounts';
import dates from '@/utils/dates';
import utils from '@/utils/utils';
import layout from '@/services/layout';
import TableListStatus from './table/TableListStatus.vue';

const props = defineProps({
    dfd: { type: Object, default: () => ({}) },
    selects: { type: Object, default: () => ({}) },
    modal: { type: String, default: 'modalDetails' }
})

const [page, ] = layout.new(null, {
    data: props.dfd ?? {},
    items_headers: [
        { key: 'item.code', title: 'COD', sub: [{ key: 'item.type' }] },
        { key: 'item.name', title: 'ITEM' },
        { key: 'item.description', title: 'DESCRIÇÃO' },
        { key: 'item.und', title: 'UDN', sub: [{ key: 'item.volume' }] },
        { key: 'program.name', title: 'VINC.', sub: [{ key: 'dotation.name' }] },
        { key: 'quantity', title: 'QUANT.' }
    ]
})

watch(() => props.dfd, (newval) => {
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
                    <!-- origin -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Origem da Demanda</h3>
                                <p>
                                    Dados referentes a origem e responsabilidade pela
                                    Demanda
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Orgão</h4>
                                    <p>
                                        {{ page.data.organ?.name }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Unidade</h4>
                                    <p>
                                        {{ page.data.unit?.name }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Ordenador de Despesas</h4>
                                    <p>
                                        {{ page.data.ordinator?.name }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Demadantes</h4>
                                    <p>
                                        {{ page.data.demandant?.name }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Comissão / Equipe de Planejamento</h4>
                                    <p>
                                        {{ page.data.comission?.name }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <h4>Integrantes da Comissão</h4>
                                    <span class="p-0 m-0 small" v-for="m in page.data.comission_members" :key="m.id">
                                        {{ `${utils.getTxt(page.selects.responsibilitys, m.responsibility)}
                                        : ${m.name}; ` }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Infos -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Informações Gerais</h3>
                                <p>
                                    Dados de prioridade, previsão de contratação e
                                    detalhamento de Objeto
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row">
                                <div class="col-md-3">
                                    <h4>Data Envio</h4>
                                    <p>{{ page.data.date_ini }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h4>Previsão Contratação</h4>
                                    <p>
                                        {{
                                            dates.getMonthYear(page.data.estimated_date)
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-2">
                                    <h4>Ano PCA</h4>
                                    <p>{{ page.data.year_pca ?? '*****' }}</p>
                                </div>
                                <div class="col-md-2">
                                    <h4>Prioridade</h4>
                                    <p>
                                        <TableListStatus :data="utils.getTxt(
                                            page.selects.prioritys_dfd,
                                            page.data.priority
                                        )" />
                                    </p>
                                </div>
                                <div class="col-md-2">
                                    <h4>Valor Estimado</h4>
                                    <p>R${{ page.data.estimated_value ?? '*****' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h4>Tipo de Aquisição</h4>
                                    <p>
                                        {{
                                            utils.getTxt(
                                                page.selects.acquisitions_dfd,
                                                page.data.acquisition_type
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <h4>Forma Sugerida</h4>
                                    <p>
                                        {{
                                            utils.getTxt(
                                                page.selects.hirings_dfd,
                                                page.data.suggested_hiring
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <h4>Vinculo ou Dependência</h4>
                                    <p class="txt-very-small p-0 m-0">
                                        Dependência com o
                                        objeto de outro documento de formalização de
                                        demanda
                                    </p>
                                    <p>
                                        {{
                                            page.data.bonds ? 'Sim Possui' : 'Não Possui'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <h4>Registro de Preço</h4>
                                    <p class="txt-very-small p-0 m-0">
                                        Indique se a demanda se trata de registro de preços.
                                    </p>
                                    <p>
                                        {{
                                            page.data.price_taking ? 'Sim' : 'Não'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Descrição sucinta do Objeto</h4>
                                    <p>{{ page.data.description ?? '*****' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Lista de Itens</h3>
                                <p>
                                    Lista de materiais ou serviços vinculados a Demanda
                                </p>
                            </div>
                        </div>
                        <div>
                            <!-- list items -->
                            <div>
                                <TableList secondary :count="false" :order="false" :header="page.items_headers"
                                    :body="page.data.items" :mounts="{
                                        'item.type': [Mounts.Cast(page.selects.items_types)],
                                    }" />
                            </div>
                        </div>
                    </div>

                    <!-- details -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Detalhamento da Necessidade</h3>
                                <p>
                                    Justificativas para necessidade e quantitativo de
                                    itens demandados
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Justificativa da necessidade da contratação</h4>
                                    <p>{{ page.data.justification ?? '*****' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Justificativa dos quantitativos demandados</h4>
                                    <p>
                                        {{
                                            page.data.justification_quantity ?? '*****'
                                        }}
                                    </p>
                                </div>
                            </div>
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
</style>