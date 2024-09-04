<script setup>
import { createApp, onMounted, ref, watch } from 'vue';
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TableListStatus from '@/components/table/TableListStatus.vue';
import TableListRadio from '@/components/table/TableListRadio.vue';
import TabNav from '@/components/TabNav.vue';
import DfdReport from './reports/DfdReport.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import gpt from '@/services/gpt';
import exp from '@/services/export';
import masks from '@/utils/masks';
import utils from '@/utils/utils';
import dates from '@/utils/dates';
import Tabs from '@/utils/tabs';


const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/pricerecords',
    datalist: props.datalist,
    process: {
        search: {},
        data: [],
        headers: [
            { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'ordinators.name', title: 'ORDENADORES' },
            { key: 'units.title', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
    dfd: {
        data: {},
        headers: [
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'demandant.name', title: 'DEMANDANTE' },
            { key: 'ordinator.name', title: 'ORDENADOR' },
            { key: 'unit.name', title: 'ORIGEM', sub: [{ key: 'organ.name' }] },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
    header: [
        { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { key: 'demandant.name', title: 'DEMANDANTE' },
        { key: 'ordinator.name', title: 'ORDENADOR' },
        { key: 'unit.name', title: 'ORIGEM' },
        { title: 'OBJETO', sub: [{ key: 'description' }] },
        { key: 'status', title: 'SITUAÇÃO' }
    ],
    rules: {
        unit: 'required',
        ordinator: 'required',
        demandant: 'required',
        comission: 'required',
        date_ini: 'required',
        estimated_date: 'required',
        year_pca: 'required',
        acquisition_type: 'required',
        estimated_value: 'required',
        priority: 'required',
        description: 'required',
        suggested_hiring: 'required',
        justification: 'required'
    }
})

const items = ref({
    search: null,
    body: [],
    header: [
        { key: 'item.code', title: 'COD', sub: [{ key: 'item.type' }] },
        { key: 'item.name', title: 'ITEM' },
        { key: 'item.description', title: 'DESCRIÇÃO' },
        { key: 'item.und', title: 'UDN', sub: [{ key: 'item.volume' }] },
        { key: 'program', title: 'VINC.', sub: [{ key: 'dotation' }] },
        { key: 'quantity', title: 'QUANT.' }
    ],
    selected_item: {
        item: null,
        program: null,
        dotation: null,
        quantity: 0
    }
})

const tabs = new Tabs([
    { id: 'process', title: 'Processo' },
    { id: 'dfds', title: 'DFDs' },
    { id: 'infos', title: 'Informações' },
    { id: 'suppliers', title: 'Fornecedores' },
    { id: 'proposals', title: 'Coletas' }
])

function generate(type) {
    const dfd = page.data
    const slc = page.selects
    const base = {
        organ: page.organ,
        unit: slc?.units.find((o) => o.id === dfd.unit),
        type: slc?.acquisitions.find((o) => o.id === dfd.acquisition_type),
        items: JSON.stringify(dfd.items),
        description: dfd.description,
        justification: dfd.justification
    }

    let callresp = null;
    let payload = null;

    switch (type) {
        case 'dfd_description':
            callresp = (resp) => {
                page.data.description = resp.data?.choices[0]?.message?.content
            }
            payload = (`
                Estou elaborando um DFD, por favor crie a descrição sucinta do 
                objeto de contratação de uma empresa especializada para fornecimento de 
                ${base.type?.title} para ${base?.description} para atender as necessidades da 
                ${base.unit?.title} vinculado a ${base.organ?.title}. 
                Por favor gere a resposta em um único parágarfo, sem quebras de linha.
            `)
            break
        case 'dfd_justification':
            callresp = (resp) => {
                page.data.justification = resp.data?.choices[0]?.message?.content
            }
            payload = (`
                Justifique a necessidade de contratação para ${base.description}. 
                Por favor gere a resposta em um único parágarfo, sem quebras de linha.
            `)
            break
        case 'dfd_quantitys':
            callresp = (resp) => {
                page.data.justification_quantity = resp.data?.choices[0]?.message?.content
            }
            payload = (`
                Justifique a quantidade demandada para esses itens ${base.items} 
                de acordo com esse objeto: ${base.description} com base nessa justificativa ${base.justification}. 
                Por favor gere a resposta em um único parágarfo, sem quebras de linha.
            `)
            break
        default:
            break
    }

    gpt.generate(`${page.url}/generate`, payload, emit, callresp)
}

function update_dfd(id) {
    http.get(`${page.url}/details/${id}`, emit, (response) => {
        page.data = response.data
        pageData.selects('filter', `${page.data.unit},${page.data.comission}`)
        pageData.ui('update')
    })
}

function export_dfd(id) {
    http.get(`${page.url}/export/${id}`, emit, (resp) => {
        const dfd = resp.data
        const containerReport = document.createElement('div')
        const instanceReport = createApp(DfdReport, { organ: page.organ, dfd: dfd, selects: page.selects })
        instanceReport.mount(containerReport)
        exp.exportPDF(containerReport, `DFD-${dfd.protocol}`)
    })
}

function clone_dfd(id) {
    http.get(`${page.url}/details/${id}`, emit, (response) => {
        response.data.id = null
        page.data = response.data
        page.data.clone = true
        pageData.selects('filter', `${page.data.unit},${page.data.comission}`)
        pageData.ui('update')
    })
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

onMounted(() => {
    pageData.selects()
    pageData.list()
})

</script>

<template>
    <div class="page">
        <NavMainUi />
        <main class="main">
            <HeaderMainUi />

            <!-- List -->
            <section v-if="!page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Coleta de Precos</h2>
                        <p>
                            Listagem das ultimas coletas de preco realizadas
                            <span class="txt-color">{{ page.organ_name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="pageData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                    </div>
                </div>
                <!-- Search -->
                <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_ini" class="form-label">Data Inicial</label>
                            <VueDatePicker auto-apply v-model="page.search.date_i" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_fin" class="form-label">Data Final</label>
                            <VueDatePicker auto-apply v-model="page.search.date_f" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-protocol" class="form-label">Protocolo</label>
                            <input type="text" name="protocol" class="form-control" id="s-protocol"
                                v-model="page.search.protocol" placeholder="Número do Protocolo" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-unit" class="form-label">Unidade</label>
                            <select name="unit" class="form-control" id="s-unit" v-model="page.search.unit">
                                <option value=""></option>
                                <option v-for="o in page.selects.units" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-8">
                            <label for="s-description" class="form-label">Objeto</label>
                            <input type="text" name="description" class="form-control" id="s-description"
                                v-model="page.search.description" placeholder="Pesquise por partes do Objeto do DFD" />
                        </div>
                        <div class="d-flex flex-row-reverse mt-4">
                            <button type="submit" class="btn btn-action-primary">
                                <ion-icon name="filter"></ion-icon>
                                Aplicar Filtro
                            </button>
                        </div>
                    </form>
                </div>
                <div role="list" class="container p-0">
                    <TableList :header="page.header" :body="page.datalist" :actions="[
                        Actions.Edit(update_dfd),
                        Actions.Delete(pageData.remove),
                        Actions.Export('document-text-outline', export_dfd),
                        Actions.Create('documents-outline', 'Clonar', clone_dfd),
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        description: [Mounts.Truncate()],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registro de Coletas</h2>
                        <p>Processo de registro de coletas de precos para o Processo</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <TabNav :tabs="tabs" identify="tabbed" />
                    <form @submit.prevent="pageData.save({ status: 2 })">
                        <div class="content">
                            
                            <!-- tab proccess -->
                            <div class="tab-pane fade row m-0 g-3" :class="{ 'show active': tabs.is('process') }">
                                <div class="accordion mb-3" id="accordion-process">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="accordionSearchProcessHeadId">
                                            <button class="w-100 text-center px-2 py-3" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#accordionSearchColapseId"
                                                aria-expanded="true" aria-controls="accordionSearchColapseId">
                                                <h2 class="txt-color text-center m-0">
                                                    <i class="bi bi-journal-bookmark me-1"></i>
                                                    Localizar Processo
                                                </h2>
                                                <p class="validation txt-color-sec small text-center m-0" :class="{
                                                    'text-danger': page.valids.process
                                                }">
                                                    Aplique os filtros abaixo para localizar os Processos
                                                </p>
                                            </button>
                                        </h2>
                                        <div id="accordionSearchColapseId" class="accordion-collapse collapse show"
                                            :class="{ 'show': page.data.id }" aria-labelledby="accordion-processHeadId"
                                            data-bs-parent="#accordion-process">
                                            <div class="accordion-body p-0 m-0">
                                                <div class="p-4 pt-0 mx-2">
                                                    <div class="dashed-separator mb-3"></div>
                                                    <div class="row g-3">
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="date_s_ini" class="form-label">Data
                                                                Inicial</label>
                                                            <VueDatePicker auto-apply
                                                                v-model="page.process.search.date_i"
                                                                :enable-time-picker="false" format="dd/MM/yyyy"
                                                                model-type="yyyy-MM-dd"
                                                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                                calendar-class-name="dp-custom-calendar"
                                                                calendar-cell-class-name="dp-custom-cell"
                                                                menu-class-name="dp-custom-menu" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="date_s_fin" class="form-label">Data
                                                                Final</label>
                                                            <VueDatePicker auto-apply
                                                                v-model="page.process.search.date_f"
                                                                :enable-time-picker="false" format="dd/MM/yyyy"
                                                                model-type="yyyy-MM-dd"
                                                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                                calendar-class-name="dp-custom-calendar"
                                                                calendar-cell-class-name="dp-custom-cell"
                                                                menu-class-name="dp-custom-menu" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="s-protocol" class="form-label">Protocolo</label>
                                                            <input type="text" name="protocol" class="form-control"
                                                                id="s-protocol" v-model="page.process.search.protocol"
                                                                placeholder="Número do Protocolo do Processo" />
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="s-description" class="form-label">Objeto</label>
                                                            <input type="text" name="description" class="form-control"
                                                                id="s-description"
                                                                v-model="page.process.search.description"
                                                                placeholder="Pesquise por partes do Objeto do Processo" />
                                                        </div>
                                                        <div class="d-flex flex-row-reverse mt-4">
                                                            <button type="button" @click="list_processes"
                                                                class="btn btn-action-primary">
                                                                <ion-icon name="search" class="fs-5"></ion-icon>
                                                                Localizar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-4 pt-0 mx-2">
                                                    <TableListRadio secondary identify="process"
                                                        v-model="page.data.process" :header="page.process.headers"
                                                        :body="page.process.data" :mounts="{
                                                            status: [Mounts.Cast(page.selects.process_status), Mounts.Status()],
                                                            description: [Mounts.Truncate(200)],
                                                        }" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- tab dfds -->
                            <div class="tab-pane fade row m-0 g-3" :class="{ 'show active': tabs.is('dfds') }">
                                <div v-if="page.data.process" class="form-neg-box">
                                    <TableList :count="false" :header="page.dfd.headers" :body="page.data.process.dfds"
                                        :mounts="{
                                            status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
                                        }" :actions="[
                                        Actions.ModalDetails(dfd_details),
                                    ]" />
                                </div>
                                <div class="pb-3" v-else>
                                    <h2
                                        class="txt-color text-center m-0 d-flex justify-content-center align-items-center gap-1">
                                        <ion-icon name="warning" class="fs-5" />
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0">
                                        É necessário selecionar um processo para visualizar as DFDs
                                    </p>
                                </div>
                            </div>

                            <!-- tab informacoes -->
                            <div class="tab-pane fade row m-0 p-4 pt-1 g-3"
                                :class="{ 'show active': tabs.is('infos') }">
                                <div class="col-sm-12 col-md-4">
                                    <label for="date_ini" class="form-label">Data Envio Demanda</label>
                                    <VueDatePicker auto-apply v-model="page.data.date_ini"
                                        :input-class-name="page.valids.date_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                        :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                        locale="pt-br" calendar-class-name="dp-custom-calendar"
                                        calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="estimated_date" class="form-label">Data Prevista
                                        Contratação</label>
                                    <VueDatePicker auto-apply v-model="page.data.estimated_date"
                                        :input-class-name="page.valids.estimated_date ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                        :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                        locale="pt-br" calendar-class-name="dp-custom-calendar"
                                        calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="year_pca" class="form-label">Ano do PCA</label>
                                    <input maxlength="4" type="text" name="year_pca" class="form-control"
                                        :class="{ 'form-control-alert': page.valids.year_pca }" id="year_pca"
                                        placeholder="AAAA" v-maska:[masks.masknumbs] v-model="page.data.year_pca" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="acquisition_type" class="form-label">Tipo de Aquisição</label>
                                    <select name="acquisition_type" class="form-control"
                                        :class="{ 'form-control-alert': page.valids.acquisition_type }"
                                        id="acquisition_type" v-model="page.data.acquisition_type">
                                        <option value=""></option>
                                        <option v-for="s in page.selects.acquisitions" :value="s.id" :key="s.id">
                                            {{ s.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="estimated_value" class="form-label">Valor Estimado</label>
                                    <input type="text" name="estimated_value" class="form-control"
                                        :class="{ 'form-control-alert': page.valids.estimated_value }"
                                        id="estimated_value" placeholder="R$0,00" v-maska:[masks.maskmoney]
                                        v-model="page.data.estimated_value" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="priority" class="form-label">Prioridade</label>
                                    <select name="priority" class="form-control"
                                        :class="{ 'form-control-alert': page.valids.priority }" id="priority"
                                        v-model="page.data.priority">
                                        <option value=""></option>
                                        <option v-for="s in page.selects.prioritys" :value="s.id" :key="s.id">
                                            {{ s.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="description" class="form-label d-flex justify-content-between">
                                        Descrição sucinta do objeto
                                        <a href="#" class="a-ia d-flex align-items-center gap-1"
                                            @click="generate('dfd_description')">
                                            <ion-icon name="hardware-chip-outline" class="m-0"></ion-icon>
                                            Gerar com I.A
                                        </a>
                                    </label>
                                    <textarea name="description" class="form-control" rows="4"
                                        :class="{ 'form-control-alert': page.valids.description }" id="description"
                                        v-model="page.data.description"></textarea>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="suggested_hiring" class="form-label">
                                        Forma de Contratação Sugerida
                                    </label>
                                    <select name="suggested_hiring" class="form-control"
                                        :class="{ 'form-control-alert': page.valids.suggested_hiring }"
                                        id="suggested_hiring" v-model="page.data.suggested_hiring">
                                        <option value=""></option>
                                        <option v-for="s in page.selects.hirings" :value="s.id" :key="s.id">
                                            {{ s.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="bonds" class="form-label">Vinculo ou Dependência</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="bonds"
                                            v-model="page.data.bonds">
                                        <label class="form-check-label" for="bonds">Indicação de vinculação ou
                                            dependência com o objeto de outro documento de formalização de
                                            demanda.</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label class="form-label" for="price_taking">Registro de Preço</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="price_taking"
                                            v-model="page.data.price_taking">
                                        <label class="form-check-label" for="price_taking">Indique se a demanda
                                            se
                                            trata de registro de preços.</label>
                                    </div>
                                </div>
                            </div>

                            <!-- tab detalhes -->
                            <div class="tab-pane fade row m-0 p-4 pt-1 g-3"
                                :class="{ 'show active': tabs.is('suppliers') }">
                                <div class="col-sm-12">
                                    <label for="justification" class="form-label d-flex justify-content-between">
                                        Justificativa da necessidade da contratação
                                        <a href="#" class="a-ia d-flex align-items-center gap-1"
                                            @click="generate('dfd_justification')">
                                            <ion-icon name="hardware-chip-outline" class="m-0"></ion-icon>
                                            Gerar com I.A
                                        </a>
                                    </label>
                                    <textarea name="justification" class="form-control" rows="4" :class="{
                                        'form-control-alert': page.valids.justification
                                    }" id="justification" v-model="page.data.justification"></textarea>
                                </div>
                                <div class="col-sm-12">
                                    <label for="justification_quantity"
                                        class="form-label d-flex justify-content-between">
                                        Justificativa dos quantitativos demandados
                                        <a href="#" class="a-ia d-flex align-items-center gap-1"
                                            @click="generate('dfd_quantitys')">
                                            <ion-icon name="hardware-chip-outline" class="m-0"></ion-icon>
                                            Gerar com I.A
                                        </a>
                                    </label>
                                    <textarea name="justification_quantity" class="form-control" rows="4"
                                        id="justification_quantity"
                                        v-model="page.data.justification_quantity"></textarea>
                                </div>
                            </div>

                            <!-- tab revisar -->
                            <div class="tab-pane fade row m-0 p-4 pt-1 g-3"
                                :class="{ 'show active': tabs.is('proposals') }">

                                <!-- origin -->
                                <div class="box-revisor mb-4">
                                    <div class="box-revisor-title mb-4">
                                        <h3>Origem da Demanda</h3>
                                        <p>
                                            Dados referentes a origem e responsabilidade pela
                                            Demanda
                                        </p>
                                    </div>
                                    <div class="box-revisor-content">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h4>Unidade</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.units,
                                                            page.data.unit
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <h4>Comissão / Equipe de Planejamento</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.comissions,
                                                            page.data.comission
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <h4>Ordenador de Despesas</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.ordinators,
                                                            page.data.ordinator,
                                                            'id', 'name'
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h4>Demadantes</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.demandants,
                                                            page.data.demandant, 'id', 'name'
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <h4>Integrantes da Comissão</h4>
                                                <span class="p-0 m-0 small" v-for="m in page.data.comission_members"
                                                    :key="m.id">
                                                    {{ `${utils.getTxt(page.selects.responsibilitys,
                                                        m.responsibility)}
                                                    : ${m.name}; ` }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Infos -->
                                <div class="box-revisor mb-4">
                                    <div class="box-revisor-title mb-4">
                                        <h3>Informações Gerais</h3>
                                        <p>
                                            Dados de prioridade, previsão de contratação e
                                            detalhamento de Objeto
                                        </p>
                                    </div>
                                    <div class="box-revisor-content">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h4>Data Envio</h4>
                                                <p>{{ page.data.date_ini ?? '*****' }}</p>
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
                                                        page.selects.prioritys,
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
                                                            page.selects.acquisitions,
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
                                                            page.selects.hirings,
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
                                    <div class="box-revisor-title mb-4">
                                        <h3>Lista de Itens</h3>
                                        <p>
                                            Lista de materiais ou serviços vinculados a Demanda
                                        </p>
                                    </div>
                                    <div>
                                        <TableList secondary :count="false" :header="items.header"
                                            :body="page.data.items ?? []" :mounts="{
                                                'item.type': [Mounts.Cast(page.selects.items_types)],
                                                'dotation': [Mounts.Cast(page.selects.dotations)],
                                                'program': [Mounts.Cast(page.selects.programs)],
                                            }" />
                                    </div>
                                </div>

                                <!-- details -->
                                <div class="box-revisor">
                                    <div class="box-revisor-title mb-4">
                                        <h3>Detalhamento da Necessidade</h3>
                                        <p>
                                            Justificativas para necessidade e quantitativo de
                                            itens demandados
                                        </p>
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
                                                <p class="m-0 p-0">
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

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                                <ion-icon name="close-outline" class="fs-5"></ion-icon>
                                Cancelar
                            </button>
                            <button @click="tabs.next()" type="button" class="btn btn-action-secondary me-auto">
                                <ion-icon name="arrow-forward" class="fs-5"></ion-icon>
                            </button>
                            <button @click="tabs.prev()" type="button" class="btn btn-action-secondary">
                                <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>