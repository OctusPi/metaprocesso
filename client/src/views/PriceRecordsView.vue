<script setup>
import { onBeforeMount, watch } from 'vue';

import TableList from '@/components/table/TableList.vue';
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TableListRadio from '@/components/table/TableListRadio.vue';
import TabNav from '@/components/TabNav.vue';
import ModalDfdDetails from '@/components/DfdDetails.vue';
import ModalProposalDetailsUi from '@/components/ModalProposalDetailsUi.vue';

import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import gpt from '@/services/gpt';
import Tabs from '@/utils/tabs';
import notifys from '@/utils/notifys';
import dates from '@/utils/dates'
import utils from '@/utils/utils';
import citys_tce from '@/data/citys_tce'

const emit = defineEmits(['callAlert', 'callUpdate', 'callRemove'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/pricerecords',
    datalist: props.datalist,
    collect: {},
    process: {
        search: {},
        data: [],
        headers: [
            { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'modality', title: 'TIPO', sub: [{ key: 'type' }] },
            { key: 'units.title', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
    dfds: {
        search: {},
        datalist: [],
        data: null,
        group_items: [],
        headers: [
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'demandant.name', title: 'DEMANDANTE' },
            { key: 'ordinator.name', title: 'ORDENADOR' },
            { key: 'unit.name', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
    suppliers: {
        data: [],
        search: '',
        headers: [
            { key: 'name', title: 'FORNECEDOR', sub: [{ key: 'cnpj', title: 'CNPJ: ' }] },
            { key: 'modality', title: 'DEFINIÇÃO', sub: [{ key: 'size' }] },
            { key: 'address', title: 'ENDEREÇO' },
        ],
    },
    proposals: {
        selected: 'emails',
        manual_insert: false,
        manual_insert_item: null,
        manual_insert_search: false,
        manual_insert_types_resource_selected: 'tce',
        manual_insert_types_resource: {
            'tce': { nav: 'TCE', title: 'TCE', subtitle: 'Consulta Processos Tribunal de Contas do Estado do Ceará' },
            'pncp': { nav: 'PNCP', title: 'PNCP', subtitle: 'Consulta Plano Nacional de Contrações Públicas' },
            'ecomerce': { nav: 'Ecomerce', title: 'Ecomerce', subtitle: 'Consultar através sites de varejo' }
        },
        manual_insert_search_items: {
            tce: {},
            pncp: {},
            ecomerce:{}
        },
        manual_insert_find_items: {
            tce: [],
            pncp:[]
        },
        types: {
            'emails': { nav: 'E-mails', title: 'Coletas por E-mail', subtitle: 'Situação das cotações solicitas por e-mail aos fornecedores' },
            'manual': { nav: 'Inserção Manual', title: 'Inserir Coletas Manualmente', subtitle: 'Adicionar coletas através de banco de preços do TCE ou sites de varejo online.' },
        },
        headers: [
            { title: 'DATA', key: 'date_ini', sub: [{ key: 'hour_ini' }] },
            { title: 'RESPOSTA', key: 'date_fin', sub: [{ key: 'hour_fin' }] },
            { title: 'FORNECEDOR', key: 'supplier.name', sub: [{ key: 'supplier.cnpj' }] },
            { title: 'MODALIDADE', key: 'modality' },
            { title: 'SITUAÇÃO', key: 'status' }
        ],
        manual_headers: [
            { title: 'DATA', key: 'date_ini', sub: [{ key: 'hour_ini' }] },
            { title: 'IDENTIFICAÇÃO', key: 'token', sub: [{ key: 'protocol' }] },
            { title: 'AUTOR', key: 'author.name', sub: [{ key: 'author.email' }] },
            { title: 'MODALIDADE', key: 'modality' },
            { title: 'SITUAÇÃO', key: 'status' }
        ],
        data: {
            'emails': [],
            'manual': []
        }
    },
    header: [
        { title: 'IDENTIFICAÇÃO', key: 'date_ini', sub: [{ key: 'protocol' }] },
        { title: 'PROCESSO', key: 'process.protocol', sub: [{ key: 'process.description' }] },
        { title: 'FORCECEDORES', key: 'suppliers.name' },
        { title: 'TIPO CÁLCULO', key: 'calctype' },
        { title: 'SITUAÇÃO', key: 'status' }
    ],
    rules: {
        process: 'required',
        protocol: 'required',
        date_ini: 'required',
        date_fin: 'required',
        comission_id: 'required',
        calctype: 'required'
    }
})

const tabs = new Tabs([
    { id: 'process', title: 'Processo' },
    { id: 'dfds', title: 'DFDs' },
    { id: 'infos', title: 'Informações' },
    { id: 'suppliers', title: 'Fornecedores' },
    { id: 'proposals', title: 'Coletas' }
])

function prepare_register() {
    pageData.ui('register')
    page.data.protocol = utils.dateProtocol(page.organ.id, '-', utils.randCode(6))
    page.data.date_ini = dates.getDateNow()
}

function prepare_update(id) {
    http.post('proposals/list', { price_record: id }, emit, (resp) => {
        page.proposals.data = resp.data
        pageData.update(id)
    })
}

function list_processes() {
    http.post(`${page.url}/list_processes`, page.process.search, emit, (resp) => {
        page.process.data = resp.data ?? []
    })
}

function list_suppliers() {
    http.post(`${page.url}/list_suppliers`, { supplier: page.suppliers.search }, emit, (resp) => {
        page.suppliers.data = resp.data
    })
}

function list_grouped_items() {
    if (page.data.process?.id) {
        http.get(`${page.url}/list_grouped_items/${page.data.process?.id}`, emit, (resp) => {
            page.dfds.group_items = resp.data
            page.proposals.manual_insert = true
        })
    }
}

function select_supplier(supplier) {
    if (!page.data.suppliers) {
        page.data.suppliers = []
    }

    if (page.data.suppliers && !page.data.suppliers.find(s => s.id == supplier?.id)) {
        page.data.suppliers.push(supplier)
        page.suppliers.search = ''
        page.suppliers.data = []

        if (!page.proposals.data.emails.find(o => o.supplier.id == supplier.id)) {
            page.proposals.data.emails.push({
                date_ini: page.data.date_ini,
                hour_ini: dates.getHourNow(),
                supplier: supplier,
                modality: 1,
                status: 0
            })
        }

        return
    }

    emit('callAlert', notifys.warning('Fornecedor já adicionado as coletas...'))
}

function remove_supplier(id) {
    page.data.suppliers = page.data.suppliers.filter(s => s.id !== id);
    page.proposals.data.emails = page.proposals.data.emails.filter(o => o.supplier.id !== id)

    if (page.data.id) {
        http.post('proposals/destroy', { price_record: page.data.id, supplier: id }, emit)
    }
}

function dfd_details(id) {
    if (page.data.process.dfds) {
        page.dfds.data = page.data.process.dfds.find(obj => obj.id === id)
        http.get(`${page.url}/list_dfd_items/${id}`, emit, (resp) => {
            page.dfds.data.items = resp.data
        })
    }
}

function view_colects(id) {
    http.post('proposals/list', { price_record: id }, emit, (resp) => {
        page.proposals.data = resp.data
        pageData.update(id)
        tabs.navigate('proposals')
    })
}

function resend_collect(id) {
    http.get(`${page.url}/send_collect/${id}`, emit);
}

function view_proposal(id) {
    http.get(`/proposals/details/${id}`, emit, (resp) => {
        page.collect = resp.data
    })
}

function open_search_manual_price(i) {
    page.proposals.manual_insert_item = i
    page.proposals.manual_insert_search = false
    page.proposals.manual_insert_find_items.tce = []

}

function set_manual_price(price) {
    Object.assign(page.proposals.manual_insert_item, price)
}

function save_manual_collect() {
    http.post(`${page.url}/save`, Object.assign(page.data, {manual_items:page.dfds.group_items}), emit, (resp) => {
        page.proposals.manual_insert = false
        if(resp.data?.instance_id){
            http.post('proposals/list', { price_record: resp.data?.instance_id }, emit, (resp) => {
                page.proposals.data = resp.data
            })
        }
    })
}

function update_manual_collert(id){
    http.get(`/proposals/details/${id}`, emit, (resp) => {
        page.dfds.group_items = resp.data?.items
        page.data.manual_proposal_id = resp.data?.id
        page.proposals.manual_insert = true
    })
}

function remove_manual_collert(id){
    http.get(`/proposals/fastdestroy/${id}`, emit, () => {
        page.proposals.data.manual = page.proposals.data.manual.filter(o => o.id !== id)
    })
}

function prices_tce() {

const params = {
    origin: page.proposals.manual_insert_search_items.tce.origin,
    year: page.proposals.manual_insert_search_items.tce.year,
    item: page.proposals.manual_insert_item
}
http.post(`${page.url}/prices_tce`, params, emit, (resp) => {
    page.proposals.manual_insert_find_items.tce = resp.data
    page.proposals.manual_insert_search = true
})
}

function prices_pncp() {
    console.log(' prices_pncp')
}

function generate(type) {

if (!page.data?.process && !page.data?.suppliers) {
    emit('callAlert', notifys.warning('É necessário inicialmente selecionar um processo e adicionar fornecedores...'))
    return
}

let callresp = null;
let payload = null;

switch (type) {
    case 'suppliers_justification':
        callresp = (resp) => {
            page.data.suppliers_justification = resp.data?.choices[0]?.message?.content
        }
        payload = (`
            Justifique a escolha desses fornecedores ${JSON.stringify(page.data?.suppliers ?? '')} 
            de acordo com esse objeto: ${page.data?.process?.description}. 
            Por favor gere a resposta em um único parágarfo, sem quebras de linha.
        `)
        break
    default:
        break
}

gpt.generate(`${page.url}/generate`, payload, emit, callresp)
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

onBeforeMount(() => {
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
                        <button @click="prepare_register" class="btn btn-action-primary">
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
                                :auto-position="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                                menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_fin" class="form-label">Data Final</label>
                            <VueDatePicker auto-apply v-model="page.search.date_f" :enable-time-picker="false"
                                :auto-position="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                                menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-protocol" class="form-label">Protocolo</label>
                            <input type="text" name="protocol" class="form-control" id="s-protocol"
                                v-model="page.search.protocol" placeholder="Número do Protocolo" />
                        </div>
                        <div class="col-sm-12 col-md-8">
                            <label for="s-suppliers" class="form-label">Forncedor</label>
                            <InputDropMultSelect :options="page.selects.suppliers" identify="search_suppliers"
                                label="name" v-model="page.search.suppliers" />

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
                            <button type="submit" class="btn btn-action-primary">
                                <ion-icon name="filter"></ion-icon>
                                Aplicar Filtro
                            </button>
                        </div>
                    </form>
                </div>
                <div role="list" class="container p-0">
                    <TableList :header="page.header" :body="page.datalist" :actions="[
                        Actions.Create('documents-outline', 'Coletas', view_colects),
                        Actions.Edit(prepare_update),
                        Actions.Delete(pageData.remove)
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        calctype: [Mounts.Cast(page.selects.calctypes)],
                        'process.description': [Mounts.Truncate()]
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
                    <form @submit.prevent="pageData.save({ process_id: page.data.process?.id })">
                        <!-- Tabs Register -->
                        <div class="content">

                            <!-- tab process -->
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
                                                                :auto-position="false" model-type="yyyy-MM-dd"
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
                                                                :auto-position="false" model-type="yyyy-MM-dd"
                                                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                                calendar-class-name="dp-custom-calendar"
                                                                calendar-cell-class-name="dp-custom-cell"
                                                                menu-class-name="dp-custom-menu" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="s-protocol" class="form-label">Protocolo</label>
                                                            <input type="text" name="protocol" class="form-control"
                                                                id="s-protocol" v-model="page.process.search.protocol"
                                                                placeholder="Número do Protocolo do Processo"
                                                                @keydown.enter.prevent="list_processes" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="s-unit" class="form-label">Unidades</label>
                                                            <InputDropMultSelect v-model="page.process.search.units"
                                                                :options="page.selects.units" identify="units" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-8">
                                                            <label for="s-description" class="form-label">Objeto</label>
                                                            <input type="text" name="description" class="form-control"
                                                                id="s-description"
                                                                v-model="page.process.search.description"
                                                                placeholder="Pesquise por partes do Objeto do Processo"
                                                                @keydown.enter.prevent="list_processes" />
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
                                                            modality: [Mounts.Cast(page.selects.process_modalities)],
                                                            type: [Mounts.Cast(page.selects.process_types)],
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
                                <div v-if="page.data.process" class="form-neg-box p-0">
                                    <TableList :count="false" :header="page.dfds.headers" :body="page.data.process.dfds"
                                        :mounts="{
                                            status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
                                            description: [Mounts.Truncate(100)]

                                        }" :actions="[
                                            Actions.ModalDetails(dfd_details),
                                        ]" />
                                </div>
                                <div v-else>
                                    <h2
                                        class="txt-color text-center m-0 d-flex justify-content-center align-items-center gap-1">
                                        <ion-icon name="warning" class="fs-5" />
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0 pb-3">
                                        É necessário selecionar um processo para visualizar as DFDs
                                    </p>
                                </div>
                            </div>

                            <!-- tab informations -->
                            <div class="tab-pane fade row m-0 p-4 pt-1 g-3"
                                :class="{ 'show active': tabs.is('infos') }">
                                <div class="col-sm-12 col-md-4">
                                    <label for="protocol" class="form-label">Número Protocolo</label>
                                    <input type="text" name="protocol" class="form-control" :class="{
                                        'form-control-alert': page.valids.protocol
                                    }" id="protocol" placeholder="000-000000-0000" v-model="page.data.protocol" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="date_ini" class="form-label">Data Inicio da Coleta</label>
                                    <VueDatePicker auto-apply v-model="page.data.date_ini" :auto-position="false"
                                        :input-class-name="page.valids.date_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                        :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                        locale="pt-br" calendar-class-name="dp-custom-calendar"
                                        calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />

                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="date_fin" class="form-label">Data Finalização da Coleta</label>
                                    <VueDatePicker auto-apply v-model="page.data.date_fin" :auto-position="false"
                                        :input-class-name="page.valids.date_fin ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                        :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                        locale="pt-br" calendar-class-name="dp-custom-calendar"
                                        calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="calctype" class="form-label">Tipo de Cálculo</label>
                                    <select name="calctype" class="form-control" :class="{'form-control-alert': page.valids.calctype}" id="calctype" v-model="page.data.calctype">
                                        <option value=""></option>
                                        <option v-for="s in page.selects.calctypes" :value="s.id" :key="s.id">
                                            {{ s.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-8">
                                    <label for="comission" class="form-label">Comissão</label>
                                    <select name="comission" class="form-control" :class="{
                                        'form-control-alert': page.valids.comission_id
                                    }" id="comission" v-model="page.data.comission_id">
                                        <option value=""></option>
                                        <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">
                                            {{ s.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- tab suppliers -->
                            <div class="tab-pane fade row m-0 p-4 pt-1 g-3"
                                :class="{ 'show active': tabs.is('suppliers') }">
                                <div class="col-sm-12">
                                    <label for="search-supplier" class="form-label">Localizar Fornecedores no
                                        Catálogo</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-supplier"
                                            placeholder="Pesquise por parte do nome ou pelo CNPJ do Fornecedor"
                                            aria-label="Pesquise por parte do nome ou pelo CNPJ do Fornecedor"
                                            aria-describedby="btn-search-supplier" v-model="page.suppliers.search"
                                            @keydown.enter.prevent="list_suppliers" />
                                        <button class="btn btn-action-primary" type="button" @click="list_suppliers">
                                            <ion-icon name="search" class="fs-5"></ion-icon>
                                        </button>
                                    </div>

                                    <div class="container-list position-relative bg-success">
                                        <div v-if="page.suppliers.search && page.suppliers.data.length"
                                            class="position-absolute w-100 my-2 top-0 start-0 z-3">
                                            <div class="form-control load-items-cat p-0 m-0">
                                                <ul class="search-list-items">
                                                    <li v-for="i in page.suppliers.data" :key="i.id"
                                                        @click="select_supplier(i)"
                                                        class="d-flex align-items-center px-3 py-2">
                                                        <div class="me-3 item-type">
                                                            <button type="button" class="btn btn-sm btn-action-close">
                                                                <ion-icon name="add-circle-outline"
                                                                    class="fs-5"></ion-icon>
                                                            </button>
                                                        </div>
                                                        <div class="item-desc">
                                                            <h3 class="m-0 p-0 small">
                                                                {{ `${i.cnpj} - ${i.name}` }}
                                                            </h3>
                                                            <p class="m-0 p-0 small">
                                                                {{ i.address }}
                                                            </p>
                                                            <p class="m-0 p-0 small">
                                                                {{ `${i.email} - ${i.phone}` }}
                                                            </p>
                                                            <p class="m-0 p-0 small">
                                                                {{ `${i.agent} - ${i.cpf}` }}
                                                            </p>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="page.data?.suppliers">
                                    <TableList secondary :count="false" :header="page.suppliers.headers"
                                        :body="page.data.suppliers" :mounts="{
                                            modality: [Mounts.Cast(page.selects.supplier_modalities)],
                                            size: [Mounts.Cast(page.selects.supplier_sizes)],
                                        }" :actions="[
                                            Actions.FastDelete(remove_supplier),
                                        ]" />
                                </div>

                                <div class="col-sm-12">
                                    <label for="justification" class="form-label d-flex justify-content-between">
                                        Justificativa da escolha de fornecedores
                                        <a href="#" class="a-ia d-flex align-items-center gap-1"
                                            @click="generate('suppliers_justification')">
                                            <ion-icon name="hardware-chip-outline" class="m-0"></ion-icon>
                                            Gerar com I.A
                                        </a>
                                    </label>
                                    <textarea name="justification" class="form-control" rows="4" id="justification"
                                        v-model="page.data.suppliers_justification"></textarea>
                                </div>
                            </div>

                            <!-- tab collects -->
                            <div class="tab-pane fade row m-0 p-4 pt-1 g-3"
                                :class="{ 'show active': tabs.is('proposals') }">

                                <div v-if="page.data.process">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="h-pane">
                                            <h2 class="txt-color m-0">
                                                {{ page.proposals.types[page.proposals.selected].title }}
                                            </h2>
                                            <p class="validation txt-color-sec small m-0">
                                                {{ page.proposals.types[page.proposals.selected].subtitle }}
                                            </p>
                                        </div>
                                        <div class="n-pane d-flex align-items-center">
                                            <div v-for="(c, i) in page.proposals.types" :key="i" class="ms-2">
                                                <input class="btn-check" :id="`type-collect-${i}`" type="radio"
                                                    name="type-collect" :value="i" v-model="page.proposals.selected">
                                                <label class="btn btn-action-primary-tls" :for="`type-collect-${i}`">{{
                                                    c.nav
                                                }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dashed-separator mt-2 mb-3"></div>

                                    <div v-if="page.proposals.selected === 'emails'">
                                        <TableList secondary :count="false" :header="page.proposals.headers"
                                            :body="page.proposals.data.emails" :actions="[
                                                Actions.Create('eye-outline', 'Visualizar', view_proposal, '#modalProposalDetails'),
                                                Actions.Create('send-outline', 'Reenviar', resend_collect),
                                            ]" :mounts="{
                                                modality: [Mounts.Cast(page.selects.proposal_modalities)],
                                                status: [Mounts.Cast(page.selects.proposal_status), Mounts.Status()]
                                            }" />
                                    </div>

                                    <div v-else>
                                        <div v-if="page.proposals.manual_insert">

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
                                                                <th class="pe-2">ORIGEM</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="i in page.dfds.group_items" :key="i.id">
                                                                <td class="align-middle">
                                                                    <div class="small">{{ i.item.code }}</div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <div class="small txt-color-sec">{{ i.item.name }}
                                                                    </div>
                                                                    <div class="small">{{ i.item.description }}</div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <div class="small txt-color-sec">{{ i.item.und }}
                                                                    </div>
                                                                    <div class="small">{{ i.item.volume }}</div>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <div class="small">{{ i.quantity }}</div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <div class="small"
                                                                        :class="i.value ? 'text-success' : 'text-danger'">
                                                                        {{ i.value ? utils.floatToCurrency(i.value) :
                                                                            '0,00' }}</div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <div class="small">{{
                                                                        utils.floatToCurrency((i.quantity *
                                                                            utils.currencyToFloat(i.value)).toFixed(2)) }}
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <div class="small">{{ i.origin }}</div>
                                                                </td>
                                                                <td class="align-middle text-end">
                                                                    <button type="button"
                                                                        @click="open_search_manual_price(i)"
                                                                        data-bs-target="#modalProposalManualConsult"
                                                                        data-bs-toggle="modal"
                                                                        class="btn btn-inline btn-action-quaternary">
                                                                        <ion-icon name="search-outline"
                                                                            class="ms-auto"></ion-icon>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="mt-4 d-flex align-items-center justify-content-center">
                                                <button type="button" class="mx-2 btn btn-action-primary"
                                                    @click="save_manual_collect">
                                                    <ion-icon name="document-text-outline" class="fs-5"></ion-icon>
                                                    Salvar Coleta
                                                </button>
                                                <button type="button" @click="page.proposals.manual_insert = false"
                                                    class="mx-2 btn btn-action-tertiary">
                                                    <ion-icon name="close-outline" class="fs-5"></ion-icon>
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <TableList secondary :header="page.proposals.manual_headers" :count="false"
                                                :body="page.proposals.data.manual" :actions="[
                                                Actions.Create('eye-outline', 'Visualizar', view_proposal, '#modalProposalDetails'),
                                                Actions.Create('create-outline', 'Editar', update_manual_collert),
                                                Actions.Create('trash-outline', 'Remover', remove_manual_collert),
                                            ]"
                                                :mounts="{
                                                modality: [Mounts.Cast(page.selects.proposal_modalities)],
                                                status: [Mounts.Cast(page.selects.proposal_status), Mounts.Status()]
                                            }" />

                                            <button type="button" class="mx-auto btn btn-action-primary mt-4"
                                                @click="list_grouped_items">
                                                <ion-icon name="add-circle-outline" class="fs-5"></ion-icon>
                                                Iniciar Coleta Manual
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-else>
                                    <h2
                                        class="txt-color text-center m-0 d-flex justify-content-center align-items-center gap-1">
                                        <ion-icon name="warning" class="fs-5" />
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0 pb-0">
                                        É necessário selecionar um processo para iniciar as coletas
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- nav buttons -->
                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button type="button" @click="pageData.ui('register')" class="btn btn-action-secondary">
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

    <!-- Modal DFDS Details -->
    <ModalDfdDetails :dfd="page.dfds.data" :selects="page.selects" />

    <!-- Modal Proposal Details -->
    <ModalProposalDetailsUi :collect="page.collect" :selects="page.selects"
        @callAlert="(data) => emit('callAlert', data)" />

    <!-- Modal Search Manual Price -->
    <div class="modal fade" id="modalProposalManualConsult" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered mx-auto">
            <div class="modal-content p-4 content">
                <div v-if="page.proposals.manual_insert_item" class="modal-body p-0 my-1">
                    <div role="heading" class="inside-title w-100 mb-3">

                        <div class="d-flex align-items-center justify-content-center">
                            <div v-for="(c, i) in page.proposals.manual_insert_types_resource" :key="i" class="ms-2">
                                <input class="btn-check" :id="`type-collect-${i}`" type="radio" name="type-collect"
                                    :value="i" v-model="page.proposals.manual_insert_types_resource_selected">
                                <label class="btn btn-action-primary-tls" :for="`type-collect-${i}`">{{
                                    c.nav
                                }}</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-action-close">
                                <ion-icon name="close" class="fs-5"></ion-icon>
                            </button>
                        </div>
                    </div>
                    <div class="dashed-separator mt-2 mb-3"></div>
                    <div class="mb-4">
                        <div class="me-2">
                            <h3 class="small txt-blue p-0 m-0">
                                {{ page.proposals.manual_insert_item?.item.code }} :
                                {{ page.proposals.manual_insert_item?.item.type == 1 ? 'Material' : 'Serviço' }} :
                                {{ page.proposals.manual_insert_item?.item.name }} :
                                {{ page.proposals.manual_insert_item?.item.und }} :
                                {{ page.proposals.manual_insert_item?.item.volume }}
                            </h3>
                            <p class="small p-0 m-0 text-justify">{{ page.proposals.manual_insert_item?.item.description
                                }}</p>
                        </div>
                    </div>

                    <!-- TCE -->
                    <div v-if="page.proposals.manual_insert_types_resource_selected === 'tce'">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-12 col-md-7">
                                <label for="city_tce_origem" class="form-label">Origem</label>
                                <select name="city_tce_origem" class="form-control" id="city_tce_origem"
                                    v-model="page.proposals.manual_insert_search_items.tce.origin">
                                    <option v-for="c in citys_tce" :key="c.codigo_municipio"
                                        :value="c.codigo_municipio">
                                        {{ c.nome_municipio }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="ano_tce_licitacao" class="form-label">Ano Base</label>
                                <select name="ano_tce_licitacao" class="form-control" id="ano_tce_licitacao"
                                    v-model="page.proposals.manual_insert_search_items.tce.year">
                                    <option v-for="d in dates.listYears()" :key="d" :value="d">{{ d }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-1 align-items-bottom">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="button" class="w-100 text-center btn btn-inline btn-action-primary"
                                    @click="prices_tce">
                                    <ion-icon name="search-outline" class="mx-auto fs-5"></ion-icon>
                                </button>
                            </div>
                        </div>
                        <div v-if="page.proposals.manual_insert_find_items.tce.length"
                            class="tablelist-modal table-responsive-sm">
                            <table class="m-0 table-borderless table-striped table-hover table">
                                <thead>
                                    <tr>
                                        <th>ORIGEM</th>
                                        <th>ITEM</th>
                                        <th>UNIT.</th>
                                        <th>VALOR UNIT.</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(i, k) in page.proposals.manual_insert_find_items.tce" :key="k">
                                        <td class="align-middle">
                                            <div class="small txt-color-sec">TCE</div>
                                            <div class="small">{{ i.numero_licitacao }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small">{{ i.descricao_item_licitacao }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small">{{ i.descricao_unidade_item_licitacao }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small">{{ utils.floatToCurrency(i.valor_unitario_item_licitacao)
                                                }}</div>
                                        </td>
                                        <td class="align-middle text-end">
                                            <button
                                                @click="set_manual_price({ origin: 'TCE', value: i.valor_unitario_item_licitacao, data: i })"
                                                type="button" class="btn btn-inline btn-action-quaternary"
                                                data-bs-dismiss="modal">
                                                <ion-icon name="checkmark-circle-outline" class="ms-auto"></ion-icon>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center txt-color-sec">
                            <ion-icon :name="page.proposals.manual_insert_search ? 'ellipsis-horizontal-outline' : 'search-outline'" class="fs-4"></ion-icon>
                            <p class="p-0 m-0 small">{{ page.proposals.manual_insert_search ? 'Não foram localizados itens.' : 'Aplique o filtro para localizar os itens.' }}</p>
                        </div>
                    </div>

                    <!-- PNCP -->
                    <div v-if="page.proposals.manual_insert_types_resource_selected === 'pncp'">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-12 col-md-5">
                                <label for="pncp_item_code" class="form-label">Cod. Item</label>
                                <input type="text" name="pncp_item_code" class="form-control" id="pncp_item_code"
                                    v-model="page.proposals.manual_insert_search_items.pncp.item_code">
                                    
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <label for="pnpc_start_vigency" class="form-label">Inicio Vigencia</label>
                                <select name="pnpc_start_vigency" class="form-control" id="pnpc_start_vigency"
                                    v-model="page.proposals.manual_insert_search_items.pncp.start_year">
                                    <option v-for="d in dates.listYears()" :key="d" :value="d">{{ d }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <label for="ano_tce_licitacao" class="form-label">Final Vigencia</label>
                                <select name="ano_tce_licitacao" class="form-control" id="ano_tce_licitacao"
                                    v-model="page.proposals.manual_insert_search_items.pncp.end_year">
                                    <option v-for="d in dates.listYears()" :key="d" :value="d">{{ d }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-1 align-items-bottom">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="button" class="w-100 text-center btn btn-inline btn-action-primary"
                                    @click="prices_pncp">
                                    <ion-icon name="search-outline" class="mx-auto fs-5"></ion-icon>
                                </button>
                            </div>
                        </div>
                        <div v-if="page.proposals.manual_insert_find_items.tce.length"
                            class="tablelist-modal table-responsive-sm">
                            <table class="m-0 table-borderless table-striped table-hover table">
                                <thead>
                                    <tr>
                                        <th>ORIGEM</th>
                                        <th>ITEM</th>
                                        <th>UNIT.</th>
                                        <th>VALOR UNIT.</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(i, k) in page.proposals.manual_insert_find_items.pncp" :key="k">
                                        <td class="align-middle">
                                            <div class="small txt-color-sec">TCE</div>
                                            <div class="small">{{ i.numero_licitacao }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small">{{ i.descricao_item_licitacao }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small">{{ i.descricao_unidade_item_licitacao }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small">{{ utils.floatToCurrency(i.valor_unitario_item_licitacao)
                                                }}</div>
                                        </td>
                                        <td class="align-middle text-end">
                                            <button
                                                @click="set_manual_price({ origin: 'TCE', value: i.valor_unitario_item_licitacao, data: i })"
                                                type="button" class="btn btn-inline btn-action-quaternary"
                                                data-bs-dismiss="modal">
                                                <ion-icon name="checkmark-circle-outline" class="ms-auto"></ion-icon>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center txt-color-sec">
                            <ion-icon :name="page.proposals.manual_insert_search ? 'ellipsis-horizontal-outline' : 'search-outline'" class="fs-4"></ion-icon>
                            <p class="p-0 m-0 small">{{ page.proposals.manual_insert_search ? 'Não foram localizados itens.' : 'Aplique o filtro para localizar os itens.' }}</p>
                        </div>
                    </div>

                    <!-- Ecomerce -->
                    <div v-if="page.proposals.manual_insert_types_resource_selected === 'ecomerce'">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-12 col-md-11">
                                <label for="ecomerce_url_item" class="form-label">URL Item</label>
                                <input type="text" name="ecomerce_url_item" class="form-control" id="ecomerce_url_item"
                                placeholder="https://sitevarejo.com.br/item"
                                    v-model="page.proposals.manual_insert_search_items.ecomerce.url_item">
                            </div>
                            <div class="col-sm-12 col-md-1 align-items-bottom">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="button" class="w-100 text-center btn btn-inline btn-action-primary">
                                    <ion-icon name="search-outline" class="mx-auto fs-5"></ion-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>