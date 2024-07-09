<script setup>
import { onMounted, ref, watch } from 'vue'

import Ui from '@/utils/ui'
import Tabs from '@/utils/tabs'
import Data from '@/services/data'
import http from '@/services/http'


import MainNav from '@/components/MainNav.vue'
import MainHeader from '@/components/MainHeader.vue'
import TableList from '@/components/TableList.vue'
import TabNav from '@/components/TabNav.vue'
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue'
import TableListSelectRadio from '@/components/TableListSelectRadio.vue'

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })

const page = ref({
    baseURL: '/pricerecords',
    title: { primary: 'Registro de Coletas de Preços', secondary: 'Insira os dados para registro de Coletas de Preços' },
    uiview: { register: true, search: false },
    data: {},
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
        comissions: [],
        status: [],
        process_status:[]
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
    },
    process:{
        search:{},
        data:[],
        headers:[
            { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { obj: 'ordinators', key: 'name', title: 'ORDENADORES' },
            { obj: 'units', key: 'title', title: 'ORIGEM', sub: [{ obj: 'organ', key: 'name' }] },
            { title: 'OBJETO', sub: [{ key: 'description', utils: ['truncate'] }] },
            { key: 'status', cast:'title', title: 'SITUAÇÃO' }
        ],
        dfds_headers:[
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { obj: 'demandant', key: 'name', title: 'DEMANDANTE' },
            { obj: 'ordinator', key: 'name', title: 'ORDENADOR' },
            { obj: 'unit', key: 'name', title: 'ORIGEM', sub: [{ obj: 'organ', key: 'name' }] },
            { title: 'OBJETO', sub: [{ key: 'description', utils: ['truncate'] }] },
            { key: 'status', cast: 'title', title: 'SITUAÇÃO' }
        ],
        items_dfds_headers:[
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
const tabs = ref([
    { id: 'process', icon: 'bi-journal-bookmark', title: 'Processo', status: true },
    { id: 'infos', icon: 'bi-chat-square-dots', title: 'Infos', status: false },
    { id: 'dfds', icon: 'bi-boxes', title: 'DFDs', status: false },
    { id: 'suppliers', icon: 'bi-person-lines-fill', title: 'Fornecedores', status: false },
    { id: 'proposals', icon: 'bi-journal-check', title: 'Propostas', status: false }
])

const ui = new Ui(page, 'Coletas de Preços')
const data = new Data(page, emit, ui)
const navtab = new Tabs(tabs)

function search_process(){
    http.post('/pricerecords/list_processes', page.value.process.search, emit, (resp) => {
        page.value.process.data = resp.data ?? []
        console.log(resp.data)
    })
}

function dfd_details(id){
    console.log(id)
}

watch(
    () => props.datalist,
    (newdata) => {
        page.value.datalist = newdata
    }
)

onMounted(() => {
    data.selects()
    // data.list()
})

</script>

<template>
    <div class="modal fade" id="modalDetails" tabindex="-1" aria-labelledby="modalDetailsLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalDetailsLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

    <main class="container-primary">
        <MainNav />

        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-cash-coin',
                title: 'Coleta de Preços',
                description: 'Registro de Cotações de Preços para o Mapa'
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
                                    v-model="page.search.protocol" placeholder="Número do Protocolo da Coleta" />
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
                    <TableList @action:update="data.update" @action:delete="data.remove" 
                    :casts="{ 'status': page.selects.status }" :header="page.dataheader"
                    :body="page.datalist" :actions="['export_pdf', 'clone', 'update', 'delete']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row">
                        <input type="hidden" name="id" v-model="page.data.id" />

                        <TabNav identify="dfdsTab" :tab-instance="navtab" />

                        <div class="tab-content" id="priceRecordTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('process') }"
                                id="origin-tab-pane" role="tabpanel" aria-labelledby="origin-tab" tabindex="0">
                                
                                <div v-if="page.data.process" class="mb-3">
                                    <TableList 
                                    :header="page.process.headers" 
                                    :body="[page.data.process]" 
                                    :casts="{'status':page.selects.status_process}"
                                    :smaller="true"
                                    :count="false"
                                    :order="false"
                                     />
                                </div>

                                <div class="accordion mb-3" id="accordionSearchProcess">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="accordionSearchProcessHeadId">
                                            <button class="w-100 text-center px-2 py-3" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#accordionSearchColapseId"
                                                aria-expanded="true" aria-controls="accordionSearchColapseId">
                                                <h2 class="txt-color text-center m-0">
                                                    <i class="bi bi-journal-bookmark me-1"></i>
                                                    Localizar Processo
                                                </h2>
                                                <p class="validation txt-color-sec small text-center m-0"
                                                    :class="{ 'text-danger': props.valid }">
                                                    Aplique os filtros abaixo para localizar os Processos
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
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="s-organ" class="form-label">Orgão</label>
                                                        <select name="organ" class="form-control" id="s-organ"
                                                            v-model="page.process.search.organ"
                                                            @change="data.selects('organ', page.process.search.organ)">
                                                            <option value=""></option>
                                                            <option v-for="o in page.selects.organs" :key="o.id"
                                                                :value="o.id">
                                                                {{ o.title }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="s-unit" class="form-label">Unidades</label>
                                                        <InputDropMultSelect v-model="page.process.search.units"
                                                            :options="page.selects.units" identify="p_units" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="s-description" class="form-label">Objeto</label>
                                                        <input type="text" name="description" class="form-control"
                                                            id="s-description" v-model="page.process.search.description"
                                                            placeholder="Pesquise por partes do Objeto do Processo" />
                                                    </div>
                                                    <div class="mt-4">
                                                        <button @click="search_process" type="button"
                                                            class="btn btn-primary mx-2">
                                                            <i class="bi bi-search"></i> Localizar Processos
                                                        </button>
                                                    </div>
                                                </div>
                                                <TableListSelectRadio 
                                                v-model="page.data.process"
                                                identify="process"
                                                :header="page.process.headers" 
                                                :body="page.process.data"
                                                :casts="{status: page.selects.status_process}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('infos') }"
                                id="infos-tab-pane" role="tabpanel" aria-labelledby="infos-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="protocol" class="form-label">Número Protocolo</label>
                                        <input type="text" name="protocol" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.protocol
                                        }" id="protocol" placeholder="000-000000-0000" v-model="page.data.protocol" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="date_ini" class="form-label">Data Inicio da Coleta</label>
                                        <VueDatePicker auto-apply v-model="page.data.date_ini"
                                            :input-class-name="page.rules.valids.date_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />

                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="date_fin" class="form-label">Data Finalização da Coleta</label>
                                        <VueDatePicker auto-apply v-model="page.data.date_fin"
                                            :input-class-name="page.rules.valids.date_fin ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu"
                                            disabled />
                                        <div id="comissionHelpBlock" class="form-text txt-color-sec">
                                            A data final será preenchida automaticamente.
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="comission" class="form-label">Comissão</label>
                                        <select name="comission" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.comission
                                        }" id="comission" v-model="page.data.comission">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">
                                                {{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('dfds') }"
                                id="items-tab-pane" role="tabpanel" aria-labelledby="items-tab" tabindex="0">
                                <div v-if="page.data.process">
                                    <TableList 
                                    :header="page.process.dfds_headers"
                                    :body="page.data.process.dfds"
                                    :casts="{}"
                                    :actions="['modaldetails']"
                                    :smaller="true"
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

                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('suppliers') }"
                                id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                                <p>Adicionar Fornecedores: Localizar por cnpj, nome fantasia, nome social ou atividade
                                </p>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': navtab.activate_tab('proposals') }"
                                id="revisor-tab-pane" role="tabpanel" aria-labelledby="revisor-tab" tabindex="0">
                                <p>Solicitaçao ou Inclusao de Propostas</p>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">
                                Cancelar <i class="bi bi-x-circle"></i>
                            </button>
                            <button @click="data.save" type="button" class="btn btn-outline-primary me-2">
                                Salvar <i class="bi bi-check2-circle"></i>
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
