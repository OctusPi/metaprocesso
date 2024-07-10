<script setup>
import { onMounted, ref, watch, createApp } from 'vue'
import utils from '@/utils/utils'
import dates from '@/utils/dates'
import Ui from '@/utils/ui'
import masks from '@/utils/masks'
import notifys from '@/utils/notifys'
import Tabs from '@/utils/tabs'
import Data from '@/services/data'
import http from '@/services/http'
import gpt from '@/services/gpt'
import exp from '@/services/export'

import MainNav from '@/components/MainNav.vue'
import MainHeader from '@/components/MainHeader.vue'
import TableList from '@/components/TableList.vue'
import TableListStatus from '@/components/TableListStatus.vue'
import TabNav from '@/components/TabNav.vue'
import DfdReport from '@/views/reports/DfdReport.vue'

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })

const page = ref({
    baseURL: '/dfds',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: { items: [] },
    datalist: props.datalist,
    dataheader: [
        { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { obj: 'demandant', key: 'name', title: 'DEMANDANTE' },
        { obj: 'ordinator', key: 'name', title: 'ORDENADOR' },
        { obj: 'unit', key: 'name', title: 'ORIGEM', sub: [{ obj: 'organ', key: 'name' }] },
        { title: 'OBJETO', sub: [{ key: 'description', utils: ['truncate'] }] },
        { key: 'status', cast: 'title', title: 'SITUAÇÃO' }
    ],
    search: {
    },
    selects: {
        organs: [],
        units: [],
        ordinators: [],
        demandants: [],
        comissions: [],
        prioritys: [],
        hirings: [],
        acquisitions: [],
        bonds: [],
        programs: [],
        dotations: [],
        categories: [],
        responsibilitys: [],
        status: []
    },
    rules: {
        fields: {
            organ: 'required',
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
        },
        valids: {}
    }
})
const tabs = ref([
    { id: 'origin', icon: 'bi-bounding-box', title: 'Origem', status: true },
    { id: 'infos', icon: 'bi-chat-square-dots', title: 'Infos', status: false },
    { id: 'items', icon: 'bi-boxes', title: 'Itens', status: false },
    { id: 'details', icon: 'bi-justify-left', title: 'Detalhes', status: false },
    { id: 'revisor', icon: 'bi-journal-check', title: 'Revisar', status: false }
])
const items = ref({
    search: null,
    search_list: [],
    headers_list: [
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
    ],
    selected_item: {
        item: null,
        program: null,
        dotation: null,
        quantity: 0
    }
})

const ui = new Ui(page, 'DFDs')
const data = new Data(page, emit, ui)
const navtab = new Tabs(tabs)

function search_items() {
    http.post(`${page.value.baseURL}/items`, { name: items.value.search }, emit, (resp) => {
        items.value.search_list = resp.data
        items.value.selected_item.item = null
    })
}

function select_item(item) {
    items.value.selected_item.item = item
    items.value.search = null
    items.value.search_list = []
}

function add_item() {
    if (!items.value.selected_item.quantity || items.value.selected_item.quantity == 0) {
        emit('callAlert', notifys.warning('A quantidade não pode ser zero!'))
        return
    }

    if (!page.value.data.items) {
        page.value.data.items = []
    }

    items.value.selected_item.id = `${items.value.selected_item.item?.id}:${items.value.selected_item.program ?? '0'}:${items.value.selected_item.dotation ?? '0'}`
    let item = page.value.data.items.find((obj) => obj.id === items.value.selected_item.id)

    if (item) {
        item = { ...items.value.selected_item }
    } else {
        page.value.data.items.push({ ...items.value.selected_item })
    }

    items.value.selected_item = {}
}

function update_item(id) {
    items.value.selected_item = page.value.data.items.find((obj) => obj.id === id)
}

function delete_item(id) {
    page.value.data.items = page.value.data.items.filter((obj) => obj.id !== id)
}

function generate(type) {
    let callresp = null
    const dfd = page.value.data
    const slc = page.value.selects
    const base = {
        organ: slc?.organs.find((o) => o.id === dfd.organ),
        unit: slc?.units.find((o) => o.id === dfd.unit),
        type: slc?.acquisitions.find((o) => o.id === dfd.acquisition_type),
        items: JSON.stringify(dfd.items),
        description: dfd.description,
        justification: dfd.justification
    }

    switch (type) {
        case 'dfd_description':
            callresp = (resp) => {
                page.value.data.description = resp.data?.choices[0]?.text
            }
            break
        case 'dfd_justification':
            callresp = (resp) => {
                page.value.data.justification = resp.data?.choices[0]?.text
            }
            break
        case 'dfd_quantitys':
            callresp = (resp) => {
                page.value.data.justification_quantity = resp.data?.choices[0]?.text
            }
            break
        default:
            break
    }

    const payload = gpt.make_payload(type, base)
    gpt.generate(`${page.value.baseURL}/generate`, payload, emit, callresp)
}

function rescue_members() {
    http.get(`${page.value.baseURL}/selects/comission/${page.value.data.comission}`, emit, (resp) => {
        page.value.data.comission_members = resp.data
    })
}

function update_dfd(id) {
    http.get(`${page.value.baseURL}/details/${id}`, emit, (response) => {
        page.value.data = response.data
        data.selects('unit', page.value.data.unit)
        ui.toggle('update')
    })
}

function export_dfd(id) {
    http.get(`${page.value.baseURL}/export/${id}`, emit, (resp) => {
        const dfd = resp.data
        const containerReport = document.createElement('div')
        const instanceReport = createApp(DfdReport, { dfd: dfd, selects: page.value.selects })
        instanceReport.mount(containerReport)
        exp.exportPDF(containerReport, `DFD-${dfd.protocol}`)
    })

}

function clone_dfd(id) {
    http.get(`${page.value.baseURL}/details/${id}`, emit, (response) => {
        response.data.id = null
        page.value.data = response.data
        data.selects('unit', page.value.data.unit)
        ui.toggle('update')
    })
}

watch(
    () => props.datalist,
    (newdata) => {
        page.value.datalist = newdata
    }
)

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
                icon: 'bi-journal-album',
                title: 'Formalização de Demandas',
                description: 'Registro de demandas solicitadas pelas as Unidades'
            }" />

            <div class="box box-main p-0 rounded-4">
                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center w-100 px-4 px-md-5 pt-5 mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ page.title.primary }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ page.title.secondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <button @click="ui.toggle('register')" type="button" class="btn btn-action btn-action-primary"
                            data-bs-toggle="dropdown" aria-expanded="false">
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
                                <label for="s-unit" class="form-label">Unidade</label>
                                <select name="unit" class="form-control" id="s-unit" v-model="page.search.unit">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.units" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-description" class="form-label">Objeto</label>
                                <input type="text" name="description" class="form-control" id="s-description"
                                    v-model="page.search.description"
                                    placeholder="Pesquise por partes do Objeto do DFD" />
                            </div>

                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">
                                    Aplicar <i class="bi bi-check2-circle"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- DATA LIST -->
                    <TableList @action:update="update_dfd" @action:delete="data.remove" @action:pdf="export_dfd"
                        @action:clone="clone_dfd" :casts="{ 'status': page.selects.status }" :header="page.dataheader"
                        :body="page.datalist" :actions="['export_pdf', 'clone', 'update', 'delete']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row">
                        <input type="hidden" name="id" v-model="page.data.id" />

                        <TabNav identify="dfdsTab" :tab-instance="navtab" />

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('origin') }"
                                id="origin-tab-pane" role="tabpanel" aria-labelledby="origin-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="organ" class="form-label">Orgão</label>
                                        <select name="organ" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.organ
                                        }" id="organ" v-model="page.data.organ"
                                            @change="data.selects('organ', page.data.organ)">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.organs" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="unit" class="form-label">Unidade</label>
                                        <select name="unit" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.unit
                                        }" id="unit" @change="data.selects('unit', page.data.unit)"
                                            v-model="page.data.unit">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.units" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="ordinator" class="form-label">Ordenador</label>
                                        <select name="ordinator" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.ordinator
                                        }" id="ordinator" v-model="page.data.ordinator">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.ordinators" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="demandant" class="form-label">Demandante</label>
                                        <select name="demandant" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.demandant
                                        }" id="demandant" v-model="page.data.demandant">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.demandants" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-8">
                                        <label for="comission" class="form-label">Comissão/Equipe de
                                            Planejamento</label>
                                        <select name="comission" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.comission
                                        }" id="comission" @change="rescue_members" v-model="page.data.comission">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                        <div id="comissionHelpBlock" class="form-text txt-color-sec">
                                            Ao selecionar a comissão/equipe de planejamento seus
                                            integrantes serão vinculados ao documento
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('infos') }"
                                id="infos-tab-pane" role="tabpanel" aria-labelledby="infos-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="date_ini" class="form-label">Data Envio Demanda</label>
                                        <VueDatePicker auto-apply v-model="page.data.date_ini"
                                            :input-class-name="page.rules.valids.date_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />

                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="estimated_date" class="form-label">Data Prevista Contratação</label>
                                        <VueDatePicker auto-apply v-model="page.data.estimated_date"
                                            :input-class-name="page.rules.valids.estimated_date ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="year_pca" class="form-label">Ano do PCA</label>
                                        <input type="text" name="year_pca" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.year_pca
                                        }" id="year_pca" placeholder="AAAA" v-maska:[masks.masknumbs]
                                            v-model="page.data.year_pca" />
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="acquisition_type" class="form-label">Tipo de Aquisição</label>
                                        <select name="acquisition_type" class="form-control" :class="{
                                            'form-control-alert':
                                                page.rules.valids.acquisition_type
                                        }" id="acquisition_type" v-model="page.data.acquisition_type">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.acquisitions" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="estimated_value" class="form-label">Valor Estimado</label>
                                        <input type="text" name="estimated_value" class="form-control" :class="{
                                            'form-control-alert':
                                                page.rules.valids.estimated_value
                                        }" id="estimated_value" placeholder="R$0,00" v-maska:[masks.maskmoney]
                                            v-model="page.data.estimated_value" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="priority" class="form-label">Prioridade</label>
                                        <select name="priority" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.priority
                                        }" id="priority" v-model="page.data.priority">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.prioritys" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="description" class="form-label d-flex justify-content-between">
                                            Descrição sucinta do objeto
                                            <a href="#" class="a-ia" @click="generate('dfd_description')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <textarea name="description" class="form-control" rows="4" :class="{
                                            'form-control-alert': page.rules.valids.description
                                        }" id="description" v-model="page.data.description"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="suggested_hiring" class="form-label">Forma de Contratação
                                            Sugerida</label>
                                        <select name="suggested_hiring" class="form-control" :class="{
                                            'form-control-alert':
                                                page.rules.valids.suggested_hiring
                                        }" id="suggested_hiring" v-model="page.data.suggested_hiring">
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
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="price_taking" v-model="page.data.price_taking">
                                            <label class="form-check-label" for="price_taking">Indique se a demanda se
                                                trata de registro de preços.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('items') }"
                                id="items-tab-pane" role="tabpanel" aria-labelledby="items-tab" tabindex="0">
                                <!-- search items -->
                                <div class="row mb-3 position-relative">
                                    <div class="col-sm-12">
                                        <label for="search-item" class="form-label">Localizar Item no Catálogo
                                            Padronizado</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="search-item"
                                                placeholder="Pesquise por parte do nome do item"
                                                aria-label="Pesquise por parte do nome do item"
                                                aria-describedby="btn-search-item" v-model="items.search"
                                                @keydown.enter.prevent="search_items" />
                                            <button class="btn btn-group-input" type="button" id="btn-search-item"
                                                @click="search_items">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                        <div id="search-item-HelpBlock" class="form-text txt-color-sec">
                                            Localize itens no catálogo padronizado de itens do Orgão
                                        </div>
                                    </div>

                                    <!-- List Search Items -->
                                    <div v-if="items.search && items.search_list.length"
                                        class="position-absolute my-2 top-100 start-0">
                                        <div class="form-control load-items-cat p-0 m-0">
                                            <ul class="search-list-items">
                                                <li v-for="i in items.search_list" :key="i.id" @click="select_item(i)"
                                                    class="d-flex align-items-center px-3 py-2">
                                                    <div class="me-3 item-type">
                                                        {{ i.type == '1' ? 'M' : 'S' }}
                                                    </div>
                                                    <div class="item-desc">
                                                        <h3 class="m-0 p-0 small">
                                                            {{ `${i.code} - ${i.name}` }}
                                                        </h3>
                                                        <p class="m-0 p-0 small">
                                                            {{
                                                                `Unidade: ${i.und} - Volume:
                                                            ${i.volume} - Categoria: ${page.selects.categories.find(
                                                                    (o) => o.id === i.category
                                                                )?.title
                                                                } `
                                                            }}
                                                        </p>
                                                        <p class="m-0 p-0 small">
                                                            {{ i.description }}
                                                        </p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- add item -->
                                <div v-if="items.selected_item.item">
                                    <div class="form-control d-flex align-items-center px-3 py-2 mb-3">
                                        <div class="me-3 item-type">
                                            {{ items.selected_item.item.type == '1' ? 'M' : 'S' }}
                                        </div>
                                        <div class="item-desc">
                                            <h3 class="m-0 p-0 small">
                                                {{
                                                    `${items.selected_item.item.code} -
                                                ${items.selected_item.item.name}`
                                                }}
                                            </h3>
                                            <p class="m-0 p-0 small">
                                                {{
                                                    `Unidade: ${items.selected_item.item.und} -
                                                Volume:
                                                ${items.selected_item.item.volume} - Categoria:
                                                ${page.selects.categories.find(
                                                        (o) =>
                                                            o.id ===
                                                            items.selected_item.item.category
                                                    )?.title
                                                    } `
                                                }}
                                            </p>
                                            <p class="m-0 p-0 small">
                                                {{ items.selected_item.item.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mb-3 g-3">
                                        <div class="col-sm-12 col-md-4">
                                            <label for="item-program" class="form-label">Programa</label>
                                            <select name="item-program" class="form-control" id="item-program"
                                                v-model="items.selected_item.program">
                                                <option value=""></option>
                                                <option v-for="p in page.selects.programs" :key="p.id" :value="p.id">
                                                    {{ p.title }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="item-dotation" class="form-label">Dotação</label>
                                            <select name="item-dotation" class="form-control" id="item-dotation"
                                                v-model="items.selected_item.dotation">
                                                <option value=""></option>
                                                <option v-for="d in page.selects.dotations" :key="d.id" :value="d.id">
                                                    {{ d.title }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="item-quantity" class="form-label">Quantidade</label>
                                            <div class="input-group">
                                                <input type="text" name="item-quantity" class="form-control"
                                                    id="item-quantity" v-maska:[masks.masknumbs]
                                                    v-model="items.selected_item.quantity"
                                                    @keydown.enter.prevent="add_item" />
                                                <button @click="add_item" class="btn btn-group-input" type="button"
                                                    id="btn-search-item">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- list items -->
                                <div v-if="page.data?.items">
                                    <TableList :smaller="true" :count="false" :header="items.headers_list"
                                        :body="page.data?.items" :actions="['update', 'fastdelete']" :casts="{
                                            type: [
                                                { id: 1, title: 'Material' },
                                                { id: 2, title: 'Serviço' }
                                            ],
                                            program: page.selects.programs,
                                            dotation: page.selects.dotations
                                        }" @action:update="update_item" @action:fastdelete="delete_item" />
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('details') }"
                                id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="justification" class="form-label d-flex justify-content-between">
                                            Justificativa da necessidade da contratação
                                            <a href="#" class="a-ia" @click="generate('dfd_justification')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <textarea name="justification" class="form-control" rows="4" :class="{
                                            'form-control-alert':
                                                page.rules.valids.justification
                                        }" id="justification" v-model="page.data.justification"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="justification_quantity"
                                            class="form-label d-flex justify-content-between">
                                            Justificativa dos quantitativos demandados
                                            <a href="#" class="a-ia" @click="generate('dfd_quantitys')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <textarea name="justification_quantity" class="form-control" rows="4"
                                            id="justification_quantity"
                                            v-model="page.data.justification_quantity"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade position-relative"
                                :class="{ 'show active': navtab.activate_tab('revisor') }" id="revisor-tab-pane"
                                role="tabpanel" aria-labelledby="revisor-tab" tabindex="0">

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
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.organs,
                                                            page.data.organ
                                                        )
                                                    }}
                                                </p>
                                            </div>
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
                                                <h4>Ordenador de Despesas</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.ordinators,
                                                            page.data.ordinator
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
                                                            page.data.demandant
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
                                            <div class="col-md-4 mb-4">
                                                <h4>Integrantes da Comissão</h4>
                                                <span class="p-0 m-0 small" v-for="m in page.data.comission_members"
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
                                        <div v-if="page.data?.items">
                                            <TableList :smaller="true" :count="false" :header="items.headers_list"
                                                :body="page.data?.items" :casts="{
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

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">
                                Cancelar <i class="bi bi-x-circle"></i>
                            </button>
                            <button @click="data.save({ status: 2 })" type="button"
                                class="btn btn-outline-primary me-2">
                                Enviar <i class="bi bi-check2-circle"></i>
                            </button>
                            <button @click="data.save({ status: 1 })" type="button"
                                class="btn btn-outline-secondary me-2">
                                Rascunho <i class="bi bi-receipt-cutoff"></i>
                            </button>
                            <button @click="navtab.navigate_tab('next')" type="button"
                                class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-right-circle"></i>
                            </button>
                            <button @click="navtab.navigate_tab('prev')" type="button"
                                class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left-circle"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
.search-list-items {
    list-style: none;
    margin: 0;
    padding: 0;
}

.search-list-items li {
    cursor: pointer;
    border-bottom: var(--border-box);
}

.search-list-items li h3 {
    font-weight: 700;
    color: var(--color-base);
}

.search-list-items li:hover {
    background-color: var(--color-contrast-hover);
}

.item-type {
    width: 35px;
    border-right: var(--border-box);
}

.item-desc h3 {
    color: var(--color-base);
}

.item-desc p {
    font-size: 0.7rem;
    color: var(--color-text-sec);
}

.nav-step {
    margin: 0 !important;
    padding: 0 !important;
    position: relative;
    height: 60px;
}

.nav-line-step {
    height: 3px;
    width: 100%;
    background-color: var(--color-shadow);
    position: absolute;
    top: 50%;
    z-index: 0;
}

.nav-step-txt {
    background-color: var(--color-shadow);
    color: var(--color-shadow-2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: calc(50% - 30px);
}

.nav-step-txt i {
    font-size: 1.3rem;
    margin: 0;
    padding: 0;
}

.nav-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--color-shadow-2);
}

.nav-item .active .nav-step-txt {
    background-color: var(--color-base);
    color: white;
    transition: 400ms;
}

.nav-item .active .nav-line-step {
    background: rgb(202, 201, 201);
    background: linear-gradient(90deg,
            var(--color-shadow) 0%,
            var(--color-base) 50%,
            var(--color-shadow) 100%);
    transition: 400ms;
}

.active-label {
    color: var(--color-base);
}

.box-revisor {
    border-bottom: var(--border-box);
}

.bar-revisor-title {
    width: 5px;
    background-color: var(--color-base);
    border-radius: 2px;
}

.box-revisor-title h3 {
    color: var(--color-base);
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.box-revisor-title p {
    color: var(--color-text-secondary);
    font-size: small;
    margin: 0;
    padding: 0;
}

.box-revisor-content {
    padding: 0 10px 0 10px;
}

.box-revisor-content h4 {
    margin: 0;
    padding: 0;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--color-text);
}

.box-revisor-content p {
    color: var(--color-text-secondary);
    font-size: 0.9rem;
}

.btn-abs {
    top: -20px;
    right: 0;
}

@media (max-width: 755px) {
    .nav-step {
        height: 50px;
    }

    .nav-step-txt {
        width: 50px;
        height: 50px;
        left: calc(50% - 25px);
    }

    .nav-step-txt i {
        font-size: 1.2rem;
    }

    .nav-label {
        display: none;
    }
}
</style>
