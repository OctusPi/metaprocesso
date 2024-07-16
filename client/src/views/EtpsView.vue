<script setup>
import { onMounted, ref, watch } from 'vue'
import Ui from '@/utils/ui';
import utils from '@/utils/utils';
import Tabs from '@/utils/tabs';
import Data from '@/services/data';
import http from '@/services/http';
import gpt from '@/services/gpt';

import MainNav from '@/components/MainNav.vue';
import TabNav from '@/components/TabNav.vue';
import AttachmentsList from '@/components/AttachmentsList.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import InputRichText from '@/components/inputs/InputRichText.vue';
import TableListSelectRadio from '@/components/TableListSelectRadio.vue';
import TableListStatus from '@/components/TableListStatus.vue';
import dates from '@/utils/dates';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })

const page = ref({
    baseURL: '/etps',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'emission', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { key: 'name', obj: 'organ', title: 'ÓRIGEM', sub: [{ obj: 'comission', key: 'name' }] },
        { title: 'NECESSIDADE', sub: [{ key: 'necessity', utils: ['truncate', 'html'] }] },
        { key: 'status', cast: 'title', title: 'STATUS' },
    ],
    search: {},
    selects: {
        organs: [],
        units: [],
        comissions: [],
        prioritys_dfd: [],
        hirings_dfd: [],
        acquisitions_dfd: [],
        status: [],
        status_process: [],
        status_dfds: [],
        programs: [],
        dotations: [],
        responsibilitys: []
    },
    rules: {
        fields: {
            process: 'required',
            organ: 'required',
            comission: 'required',
            protocol: 'required',
            emission: 'required',
            status: 'required',
            object_description: 'required',
            object_classification: 'required',
            necessity: 'required',
            contract_forecast: 'required',
            contract_requirements: 'required',
            market_survey: 'required',
            contract_calculus_memories: 'required',
            contract_expected_price: 'required',
            solution_full_description: 'required',
            solution_parcel_justification: 'required',
            correlated_contracts: 'required',
            contract_alignment: 'required',
            expected_results: 'required',
            contract_previous_actions: 'required',
            ambiental_impacts: 'required',
            viability_declaration: 'required',
        },
        valids: {}
    },
    process: {
        search: {},
        data: [],
        headers: [
            { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { obj: 'ordinators', key: 'name', title: 'ORDENADORES' },
            { obj: 'units', key: 'title', title: 'ORIGEM', sub: [{ obj: 'organ', key: 'name' }] },
            { title: 'OBJETO', sub: [{ key: 'description', utils: ['truncate'] }] },
            { key: 'status', cast: 'title', title: 'SITUAÇÃO' }
        ],
    },
    dfd: {
        data: null,
        items: [],
        headers: [
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { obj: 'demandant', key: 'name', title: 'DEMANDANTE' },
            { obj: 'ordinator', key: 'name', title: 'ORDENADOR' },
            { obj: 'unit', key: 'name', title: 'ORIGEM', sub: [{ obj: 'organ', key: 'name' }] },
            { title: 'OBJETO', sub: [{ key: 'description', utils: ['truncate'] }] },
            { key: 'status', cast: 'title', title: 'SITUAÇÃO' }
        ],
        items_headers: [
            {
                obj: 'item',
                key: 'code',
                title: 'COD',
                sub: [{ obj: 'item', cast: 'title', key: 'type' }]
            },
            { obj: 'item', key: 'name', title: 'ITEM' },
            { obj: 'item', key: 'description', title: 'DESCRIÇÃO' },
            { obj: 'item', key: 'und', title: 'UDN', sub: [{ obj: 'item', key: 'volume' }] },
            {
                key: 'program',
                cast: 'title',
                title: 'VINC.',
                sub: [{ key: 'dotation', cast: 'title' }]
            },
            { key: 'quantity', title: 'QUANT.' }
        ]
    }
})

function list_processes() {
    http.post('/etps/list_processes', page.value.process.search, emit, (resp) => {
        page.value.process.data = resp.data ?? []
    })
}

const tabs = ref([
    { id: 'info', icon: 'bi-chat-square-dots', title: 'Infos', status: true },
    { id: 'process', icon: 'bi-journal-album', title: 'Processo', status: false },
    { id: 'dfds', icon: 'bi-journal-album', title: 'DFDs', status: false },
    { id: 'necessidade', icon: 'bi-question-circle', title: 'Necessidade', status: false },
    { id: 'solucao', icon: 'bi-card-checklist', title: 'Solução', status: false },
    { id: 'planejamento', icon: 'bi-journal-check', title: 'Planejamento', status: false },
    { id: 'viabilidade', icon: 'bi-check-all', title: 'Viabilidade', status: false },
    { id: 'anexos', icon: 'bi-file-pdf', title: 'Anexos', status: false },
])

const ui = new Ui(page, 'ETPs')
const data = new Data(page, emit, ui)
const tabSwitch = new Tabs(tabs)

function setProtocol() {
    data.selects('organ', page.value.data.organ)
    page.value.data.protocol = utils.dateProtocol(page.value.data.organ, '-')
}

function generate(type) {
    const etp = page.value.data
    const slc = page.value.selects
    const base = {
        organ: slc?.organs.find((o) => o.id === etp.organ),
        comission: slc?.comissions.find((o) => o.id === etp.comission),
        object_description: etp.object_description,
    }

    let callresp, payload = null

    function setValuesAndPayload(key, mPayload) {
        callresp = (resp) => {
            page.value[key] = resp.data?.choices[0]?.text;
        };
        payload = mPayload;
    }

    switch (type) {
        case 'object_description':
            setValuesAndPayload('object_description', 'Create: Please provide a concise description of the contracting object.');
            break;
        case 'object_classification':
            setValuesAndPayload('object_classification', `Create: Please provide the classification of the contracting object. ${base.object_description}`);
            break;
        case 'necessity':
            setValuesAndPayload('necessity', 'Create: Please provide the necessity description.');
            break;
        case 'contract_forecast':
            setValuesAndPayload('contract_forecast', 'Create: Please provide the forecast for the contract.');
            break;
        case 'contract_requirements':
            setValuesAndPayload('contract_requirements', 'Create: Please provide the contract requirements.');
            break;
        case 'market_survey':
            setValuesAndPayload('market_survey', 'Create: Please provide the market survey description.');
            break;
        case 'contract_calculus_memories':
            setValuesAndPayload('contract_calculus_memories', 'Create: Please provide the memories of contract calculations.');
            break;
        case 'contract_expected_price':
            setValuesAndPayload('contract_expected_price', 'Create: Please provide the expected price for the contract.');
            break;
        case 'solution_full_description':
            setValuesAndPayload('solution_full_description', 'Create: Please provide a full description of the solution.');
            break;
        case 'solution_parcel_justification':
            setValuesAndPayload('solution_parcel_justification', 'Create: Please provide justification for solution parcel.');
            break;
        case 'correlated_contracts':
            setValuesAndPayload('correlated_contracts', 'Create: Please provide description of correlated contracts.');
            break;
        case 'contract_alignment':
            setValuesAndPayload('contract_alignment', 'Create: Please provide contract alignment details.');
            break;
        case 'expected_results':
            setValuesAndPayload('expected_results', 'Create: Please provide expected results description.');
            break;
        case 'contract_previous_actions':
            setValuesAndPayload('contract_previous_actions', 'Create: Please provide previous contract actions.');
            break;
        case 'ambiental_impacts':
            setValuesAndPayload('ambiental_impacts', 'Create: Please provide possible environmental impacts.');
            break;
        default:
            break;
    }

    gpt.generate(`${page.value.baseURL}/generate`, payload, emit, callresp)
}

const attachmentTypes = [
    { id: 0, title: 'Memória de Cálculo' },
    { id: 1, title: 'Levantamento de Mercado' },
]

function dfd_details(id) {
    if (page.value.data.process.dfds) {
        page.value.dfd.data = (page.value.data.process.dfds).find(obj => obj.id === id)
        http.get(`${page.value.baseURL}/list_dfd_items/${id}`, emit, (resp) => {
            page.value.dfd.items = resp.data
        })
    }
}

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

onMounted(() => {
    data.selects()
    data.list()
})

</script>

<template>
    <main class="container-primary">
        <MainNav />

        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-journal-medical',
                title: 'Gerenciamento de Estudos Técnicos Preliminares',
                description: 'Registro dos ETPs elaborados pelos Órgãos'
            }" />

            <div class="box box-main p-0 rounded-4">
                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center w-100 px-4 px-md-5 pt-5 mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ page.title.primary }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ page.title.secondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <button @click="ui.toggle('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                        <button @click="ui.toggle('search')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-search"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Localizar</span>
                        </button>
                    </div>
                </div>

                <!--BOX LIST-->
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">

                    <!--SEARCH BAR-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-protocol" class="form-label">Protocolo</label>
                                <input type="text" name="protocol" class="form-control" id="s-protocol"
                                    v-model="page.search.protocol" placeholder="Pesquise por partes do protocolo" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control" id="s-organ" v-model="page.search.organ"
                                    @change="data.selects('organ', page.search.organ)">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-comission" class="form-label">Comissão</label>
                                <select name="comission" class="form-control" id="s-comission"
                                    v-model="page.search.comission">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="emission" class="form-label">Emissão</label>
                                <VueDatePicker auto-apply v-model="page.search.emission" :enable-time-picker="false"
                                    format="dd/MM/yyyy" model-type="dd/MM/yyyy" input-class-name="dp-custom-input-dtpk"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-necessity" class="form-label">Necessidade</label>
                                <input type="text" name="necessity" class="form-control" id="s-necessity"
                                    v-model="page.search.necessity" placeholder="Pesquise por partes da necessidade" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-status" class="form-label">Situação</label>
                                <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.status" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- DATA LIST -->
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']" :casts="{
                            'status': page.selects.status
                        }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save({'process': page.data.process.id})">
                        <input type="hidden" name="id" v-model="page.data.id">

                        <TabNav :tab-instance="tabSwitch" identify="etps-nav" />

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('info') }"
                                id="dfds-tab-pane" role="tabpanel" aria-labelledby="dfds-tab" tabindex="0">
                                <div class="row mb-2 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="emission" class="form-label">Emissão</label>
                                        <VueDatePicker auto-apply v-model="page.data.emission"
                                            :input-class-name="page.rules.valids.emission ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="organ" class="form-label">Órgão</label>
                                        <select name="organ" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.organ }" id="organ"
                                            v-model="page.data.organ" @change="setProtocol">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                        <div class="form-text txt-color-sec">
                                            Ao selecionar o órgão, o protocolo será automaticamente preenchido.
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="protocol" data-tooltip="É necessário selecionar um Órgão"
                                            :class="{ 'active': page.data.organ }" class="form-label custom-tooltip">
                                            <i v-if="page.data.protocol" class="bi bi-check-circle text-success"></i>
                                            <i v-if="!page.data.protocol" class="bi bi-info-circle text-warning"></i>
                                            Protocolo
                                        </label>
                                        <input disabled autocomplete="off" type="text" name="protocol"
                                            class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.protocol }" id="protocol"
                                            placeholder="000-00000000-0000" v-model="page.data.protocol">
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-8">
                                        <label for="comission" class="form-label">Comissão</label>
                                        <select name="comission" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.comission }"
                                            id="comission" v-model="page.data.comission">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="status" class="form-label">Situação Atual</label>
                                        <select name="status" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.status }" id="status"
                                            v-model="page.data.status">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.status" :value="s.id" :key="s.id">{{
                                                s.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_description"
                                            class="form-label d-flex justify-content-between">
                                            Descrição sucinta do objeto
                                            <a href="#" class="a-ia" @click="generate('object_description')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.object_description"
                                            placeholder="Descrição do Objeto" identifier="object_description"
                                            v-model="page.data.object_description" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_classification"
                                            class="form-label d-flex justify-content-between">
                                            Classificação do objeto
                                            <a href="#" class="a-ia" @click="generate('object_classification')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.object_classification"
                                            placeholder="Classificação do Objeto" identifier="object_classification"
                                            v-model="page.data.object_classification" />
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('process') }"
                                id="origin-tab-pane" role="tabpanel" aria-labelledby="origin-tab" tabindex="0">

                                <div v-if="page.data.process" class="mb-4 form-neg-box">
                                    <TableList :header="page.process.headers" :body="[page.data.process]"
                                        :casts="{ 'status': page.selects.status_process }" :count="false"
                                        :order="false" />
                                </div>

                                <div class="accordion mb-3" id="accordionSearchProcess">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="accordionSearchProcessHeadId">
                                            <button class="w-100 text-center px-2 py-3" type="button"
                                                :data-bs-toggle="[(page.data.organ && page.data.comission) && 'collapse']"
                                                data-bs-target="#accordionSearchColapseId" aria-expanded="true"
                                                aria-controls="accordionSearchColapseId">
                                                <h2 class="txt-color text-center m-0">
                                                    <i class="bi bi-journal-bookmark me-1"></i>
                                                    Localizar Processo
                                                </h2>
                                                <p class="validation txt-color-sec small text-center m-0" :class="{
                                                    'text-danger': page.rules.valids.process || !(page.data.organ && page.data.comission)
                                                }">
                                                    {{
                                                        (page.data.organ && page.data.comission)
                                                            ? `Aplique os filtros abaixo para localizar os Processos`
                                                            : `Selecione o órgão e a comissão`
                                                    }}
                                                </p>
                                            </button>
                                        </h2>
                                        <div id="accordionSearchColapseId" class="accordion-collapse collapse"
                                            aria-labelledby="accordionSearchProcessHeadId"
                                            data-bs-parent="#accordionSearchProcess">
                                            <div class="accordion-body p-0 m-0">
                                                <div class="row g-3 p-4">
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="date_s_ini" class="form-label">Data Inicial</label>
                                                        <VueDatePicker auto-apply v-model="page.process.search.date_i"
                                                            :enable-time-picker="false" format="dd/MM/yyyy"
                                                            model-type="yyyy-MM-dd"
                                                            input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                            calendar-class-name="dp-custom-calendar"
                                                            calendar-cell-class-name="dp-custom-cell"
                                                            menu-class-name="dp-custom-menu" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="date_s_fin" class="form-label">Data Final</label>
                                                        <VueDatePicker auto-apply v-model="page.process.search.date_f"
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
                                                    <div class="col-sm-12 col-md-12">
                                                        <label for="s-description" class="form-label">Objeto</label>
                                                        <input type="text" name="description" class="form-control"
                                                            id="s-description" v-model="page.process.search.description"
                                                            placeholder="Pesquise por partes do Objeto do Processo" />
                                                    </div>
                                                    <div class="mt-4">
                                                        <button @click="list_processes" type="button"
                                                            class="btn btn-primary mx-2">
                                                            <i class="bi bi-search"></i> Localizar Processos
                                                        </button>
                                                    </div>
                                                </div>
                                                <TableListSelectRadio v-model="page.data.process" identify="process"
                                                    :header="page.process.headers" :body="page.process.data"
                                                    :casts="{ status: page.selects.status_process }" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('dfds') }"
                                id="items-tab-pane" role="tabpanel" aria-labelledby="items-tab" tabindex="0">
                                <div v-if="page.data.process" class="form-neg-box">
                                    <TableList :header="page.dfd.headers" :body="page.data.process.dfds"
                                        :casts="{ status: page.selects.status_dfds }" :actions="['modaldetails']"
                                        @action:modaldetails="dfd_details" />
                                </div>
                                <div v-else>
                                    <h2 class="txt-color text-center m-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0">
                                        É necessário selecionar um processo para visualizar os DFDs
                                    </p>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('necessidade') }"
                                id="necessity-tab-pane" role="tabpanel" aria-labelledby="necessity-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="necessity" class="form-label d-flex justify-content-between">
                                            Necessidade
                                            <a href="#" class="a-ia" @click="generate('necessity')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.necessity"
                                            placeholder="Descrição da Necessidade" identifier="necessity"
                                            v-model="page.data.necessity" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_requirements"
                                            class="form-label d-flex justify-content-between">
                                            Descrição dos Requisitos da Contratação
                                            <a href="#" class="a-ia" @click="generate('contract_requirements')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.contract_requirements"
                                            placeholder="Descrição dos Requisitos da Contratação"
                                            identifier="contract_requirements"
                                            v-model="page.data.contract_requirements" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_forecast"
                                            class="form-label d-flex justify-content-between">
                                            Previsão de Realização da Contratação
                                            <a href="#" class="a-ia" @click="generate('contract_forecast')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.contract_forecast"
                                            placeholder="Previsão de Realização da Contratação"
                                            identifier="contract_forecast" v-model="page.data.contract_forecast" />
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('solucao') }"
                                id="solution-tab-pane" role="tabpanel" aria-labelledby="solution-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="market_survey"
                                            class="form-label d-flex justify-content-between">Levantamento de Mercado
                                            <a href="#" class="a-ia" @click="generate('market_survey')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.market_survey"
                                            placeholder="Levantamento de Mercado" identifier="market_survey"
                                            v-model="page.data.market_survey" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="solution_full_description"
                                            class="form-label d-flex justify-content-between">Descrição da Solução como
                                            um Todo
                                            <a href="#" class="a-ia" @click="generate('solution_full_description')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.solution_full_description"
                                            placeholder="Descrição da Solução como um Todo"
                                            identifier="solution_full_description"
                                            v-model="page.data.solution_full_description" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_calculus_memories"
                                            class="form-label d-flex justify-content-between">Estimativa das Quantidades
                                            Contratadas
                                            <a href="#" class="a-ia" @click="generate('contract_calculus_memories')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.contract_calculus_memories"
                                            placeholder="Estimativa das Quantidades Contratadas"
                                            identifier="contract_calculus_memories"
                                            v-model="page.data.contract_calculus_memories" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_expected_price"
                                            class="form-label d-flex justify-content-between">Estimativa do Preço da
                                            Contratação
                                            <a href="#" class="a-ia" @click="generate('contract_expected_price')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.contract_expected_price"
                                            placeholder="Estimativa do Preço da Contratação"
                                            identifier="contract_expected_price"
                                            v-model="page.data.contract_expected_price" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="solution_parcel_justification"
                                            class="form-label d-flex justify-content-between">Justificativa para o
                                            Parcelamento ou Não
                                            <a href="#" class="a-ia"
                                                @click="generate('solution_parcel_justification')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.solution_parcel_justification"
                                            placeholder="Justificativa para o Parcelamento ou Não"
                                            identifier="solution_parcel_justification"
                                            v-model="page.data.solution_parcel_justification" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="correlated_contracts"
                                            class="form-label d-flex justify-content-between">Contratações Correlatas
                                            e/ou Interdependentes
                                            <a href="#" class="a-ia" @click="generate('correlated_contracts')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.correlated_contracts"
                                            placeholder="Contratações Correlatas e/ou Interdependentes"
                                            identifier="correlated_contracts"
                                            v-model="page.data.correlated_contracts" />
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade"
                                :class="{ 'show active': tabSwitch.activate_tab('planejamento') }"
                                id="solution-tab-pane" role="tabpanel" aria-labelledby="solution-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="expected_results"
                                            class="form-label d-flex justify-content-between">Resultados Pretendidos
                                            <a href="#" class="a-ia" @click="generate('expected_results')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.expected_results"
                                            placeholder="Resultados Pretendidos" identifier="expected_results"
                                            v-model="page.data.expected_results" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_previous_actions"
                                            class="form-label d-flex justify-content-between">Providências a Serem
                                            Tomadas
                                            <a href="#" class="a-ia" @click="generate('contract_previous_actions')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.contract_previous_actions"
                                            placeholder="Providências a Serem Tomadas"
                                            identifier="contract_previous_actions"
                                            v-model="page.data.contract_previous_actions" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_alignment"
                                            class="form-label d-flex justify-content-between">Alinhamento de Contrato
                                            <a href="#" class="a-ia" @click="generate('contract_alignment')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.contract_alignment"
                                            placeholder="Alinhamento de Contrato" identifier="contract_alignment"
                                            v-model="page.data.contract_alignment" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="ambiental_impacts"
                                            class="form-label d-flex justify-content-between">Possíveis Impactos
                                            Ambientais
                                            <a href="#" class="a-ia" @click="generate('ambiental_impacts')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.rules.valids.ambiental_impacts"
                                            placeholder="Possíveis Impactos Ambientais" identifier="ambiental_impacts"
                                            v-model="page.data.ambiental_impacts" />
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('viabilidade') }"
                                id="solution-tab-pane" role="tabpanel" aria-labelledby="solution-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div>
                                        <h2 class="txt-color text-center m-0">
                                            <i class="bi bi-check-all me-1"></i>
                                            Declaração de Viabilidade
                                        </h2>
                                        <p class="txt-color-sec small text-center m-0"
                                            :class="{ 'text-danger': page.rules.valids.viability_declaration }">
                                            Selecione uma das opções abaixo
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input class="btn-check" id="viability-1" type="radio" name="viability"
                                            value="1" v-model="page.data.viability_declaration">
                                        <label class="btn btn-outline-base" for="viability-1">Esta equipe de
                                            planejamento
                                            declara viável esta contratação com base neste ETP, consoante o inciso
                                            XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.</label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input class="btn-check" id="viability-2" type="radio" name="viability"
                                            value="0" v-model="page.data.viability_declaration">
                                        <label class="btn btn-outline-secondary" for="viability-2">Esta equipe de
                                            planejamento
                                            declara inviável esta contratação com base neste ETP, consoante o inciso
                                            XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.</label>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('anexos') }"
                                id="solution-tab-pane" role="tabpanel" aria-labelledby="solution-tab" tabindex="0">
                                <div v-if="!page.data.protocol">
                                    <h2 class="txt-color text-center m-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0">
                                        É necessário selecionar um órgão para continuar
                                    </p>
                                </div>
                                <AttachmentsList v-else @callAlert="(m) => emit('callAlert', m)" origin="5"
                                    :protocol="page.data.protocol" :types="attachmentTypes" />
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">
                                Cancelar <i class="bi bi-x-circle"></i>
                            </button>
                            <button type="submit" class="btn btn-outline-primary me-2">
                                Salvar <i class="bi bi-check2-circle"></i>
                            </button>
                            <button @click="tabSwitch.navigate_tab('next')" type="button"
                                class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-right-circle"></i>
                            </button>
                            <button @click="tabSwitch.navigate_tab('prev')" type="button"
                                class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left-circle"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <div class="modal fade" id="modalDetails" tabindex="-1" aria-labelledby="modalDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content p-4" v-if="page.dfd.data">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-6 p-0 m-0" id="modalDetailsLabel">DFD: {{ page.dfd.data.protocol }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body border-0">
                    <!-- origin -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="bar-revisor-title me-2"></div>
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
                                        {{ page.dfd.data.organ.name }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Unidade</h4>
                                    <p>
                                        {{ page.dfd.data.unit.name }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Ordenador de Despesas</h4>
                                    <p>
                                        {{ page.dfd.data.ordinator.name }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Demadantes</h4>
                                    <p>
                                        {{ page.dfd.data.demandant.name }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Comissão / Equipe de Planejamento</h4>
                                    <p>
                                        {{ page.dfd.data.comission.name }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <h4>Integrantes da Comissão</h4>
                                    <span class="p-0 m-0 small" v-for="m in page.dfd.data.comission_members"
                                        :key="m.id">
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
                            <div class="bar-revisor-title me-2"></div>
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
                                    <p>{{ page.dfd.data.date_ini }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h4>Previsão Contratação</h4>
                                    <p>
                                        {{
                                            dates.getMonthYear(page.dfd.data.estimated_date)
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-2">
                                    <h4>Ano PCA</h4>
                                    <p>{{ page.dfd.data.year_pca ?? '*****' }}</p>
                                </div>
                                <div class="col-md-2">
                                    <h4>Prioridade</h4>
                                    <p>
                                        <TableListStatus :data="utils.getTxt(
                                            page.selects.prioritys_dfd,
                                            page.dfd.data.priority
                                        )" />
                                    </p>
                                </div>
                                <div class="col-md-2">
                                    <h4>Valor Estimado</h4>
                                    <p>R${{ page.dfd.data.estimated_value ?? '*****' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h4>Tipo de Aquisição</h4>
                                    <p>
                                        {{
                                            utils.getTxt(
                                                page.selects.acquisitions_dfd,
                                                page.dfd.data.acquisition_type
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
                                                page.dfd.data.suggested_hiring
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
                                            page.dfd.data.bonds ? 'Sim Possui' : 'Não Possui'
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
                                            page.dfd.data.price_taking ? 'Sim' : 'Não'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Descrição sucinta do Objeto</h4>
                                    <p>{{ page.dfd.data.description ?? '*****' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="bar-revisor-title me-2"></div>
                            <div class="txt-revisor-title">
                                <h3>Lista de Itens</h3>
                                <p>
                                    Lista de materiais ou serviços vinculados a Demanda
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <!-- list items -->
                            <div v-if="page.dfd?.items">
                                <TableList :smaller="true" :count="false" :header="page.dfd.items_headers"
                                    :body="page.dfd?.items" :casts="{
                                        type: [
                                            { id: 1, title: 'Material' },
                                            { id: 2, title: 'Serviço' }
                                        ],
                                        program: page.selects.programs,
                                        dotation: page.selects.dotations
                                    }" />
                            </div>
                        </div>
                    </div>

                    <!-- details -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="bar-revisor-title me-2"></div>
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
                                    <p>{{ page.dfd.data.justification ?? '*****' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Justificativa dos quantitativos demandados</h4>
                                    <p>
                                        {{
                                            page.dfd.data.justification_quantity ?? '*****'
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
.custom-tooltip {
    width: 100%;
    position: relative;
}

.custom-tooltip i {
    font-size: 1em;
}

.custom-tooltip:hover::before {
    top: -28px;
    left: 0;
    content: attr(data-tooltip);
    position: absolute;
    background-color: var(--color-shadow);
    padding: 4px 12px;
    border-radius: 4px;
    display: flex;
    width: fit-content;
}

.custom-tooltip.active:hover::before {
    content: '';
    display: none;
}
</style>