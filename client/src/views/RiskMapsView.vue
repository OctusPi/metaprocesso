<script setup>
import { onMounted, ref, watch } from 'vue'
// import masks from '@/utils/masks'
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';
import PseudoData from '@/services/pseudodata';
import TabNav from '@/components/TabNav.vue';
import Tabs from '@/utils/tabs';
import TableListSelectRadio from '@/components/TableListSelectRadio.vue';
import http from '@/services/http';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const page = ref({
    baseURL: '/riskiness',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    search: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cnpj' }] },
        { key: 'phone', title: 'CONTATO', sub: [{ key: 'email' }] },
        { key: 'postalcity', title: 'LOCALIZAÇÃO', sub: [{ key: 'address' }] },
        { key: 'status', cast: 'title', title: 'STATUS' }
    ],
    selects: {
        phases: [],
        risk_impacts: [],
        risk_probabilities: [],
        risk_actions: [],
        status_process: [],
    },
    rules: {
        fields: {

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
})

const tabs = ref([
    { id: 'infos', icon: 'bi-bounding-box', title: 'Origem', status: true },
    { id: 'process', icon: 'bi-bounding-box', title: 'Processo', status: false },
    { id: 'risks', icon: 'bi-journal-bookmark', title: 'Riscos', status: false },
    { id: 'accomp', icon: 'bi-journal-album', title: 'Acompanhamentos', status: false },
    { id: 'revisar', icon: 'bi-journal-check', title: 'Revisar', status: false },
])

const ui = new Ui(page, 'Mapas de Risco')
const data = new Data(page, emit, ui)
const tabSwitch = new Tabs(tabs)

const riskiness = ref({
    datalist: [],
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    dataheader: [
        { key: 'id', title: 'ID' },
        { key: 'risk_name', title: 'RISCO', sub: [{ key: 'risk_related' }] },
        { key: 'risk_probability', cast: 'title', title: 'PROBABILIDADE' },
        { key: 'risk_impact', cast: 'title', title: 'IMPACTO' },
        { key: 'risk_level', title: 'NÍVEL DE RISCO' }
    ],
    selects: {},
    data: {},
    search: {},
    rules: {
        fields: {
            'risk_name': 'required',
            'risk_related': 'required',
            'risk_probability': 'required',
            'risk_impact': 'required',
            'risk_treatment': 'required',
        },
        valids: {}
    },
})

const risknessUi = new Ui(riskiness, 'Riscos')
const pseudoData = new PseudoData(riskiness, emit, risknessUi, (data) => {
    const getSelect = (select, name) =>
        page.value.selects[select].find(({ id }) => id === data[name])

    return {
        'risk_level': getSelect('risk_impacts', 'risk_impact').value
            * getSelect('risk_probabilities', 'risk_probability').value,
    }
})

const actions = ref({
    datalist: [],
    risk: {},
    uiview: {},
    title: { primary: '', secondary: '' },
    data: {},
    search: {},
    dataheader: [
        { key: 'id', title: 'ID', sub: [{ key: 'risk_action_type', cast: 'title' }] },
        { key: 'risk_action_name', title: 'AÇÃO' },
        { key: 'risk_action_responsability', title: 'RESPONSABILIDADE' },
    ],
    rules: {
        fields: {
            'risk_action_name': 'required',
            'risk_action_type': 'required',
            'risk_action_responsability': 'required',
        },
        valids: {}
    },
})

const damage = ref({
    datalist: [],
    risk: {},
    uiview: {},
    title: { primary: '', secondary: '' },
    data: {},
    search: {},
    dataheader: [
        { key: 'risk_damage', title: 'DANO' },
    ],
    rules: {
        fields: {
            'risk_damage': 'required',
        },
        valids: {}
    },
})

const actionsUi = new Ui(actions, 'Ações')
const actionsData = new PseudoData(actions, emit, actionsUi)
const damageUi = new Ui(damage, 'Danos')
const damageData = new PseudoData(damage, emit, damageUi)

function swithToModal(id, reference, key) {
    reference.risk = riskiness.value.datalist.find((item) => item.id == id)
    reference.datalist = reference.risk[key] ?? []
}

function save_actions(reference, data, key) {
    data.save()
    const index = riskiness.value.datalist
        .findIndex((item) => item.id == reference.risk.id)
    riskiness.value.datalist[index][key] = reference.datalist
}

function search_process() {
    http.post('/pricerecords/list_processes', page.value.process.search, emit, (resp) => {
        page.value.process.data = resp.data ?? []
    })
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
                icon: 'bi-map',
                title: 'Gerenciamento de Mapas de Risco',
                description: 'Registro dos Mapas de Risco'
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
                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- DATA LIST -->
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']"
                        :casts="{ 'phases': page.selects.phases }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save()">
                        <input type="hidden" name="id" v-model="page.data.id">

                        <TabNav :tab-instance="tabSwitch" identify="process-nav" />

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('infos') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
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
                                        <div class="form-text txt-color-sec">
                                            Ao selecionar a comissão/equipe de planejamento seus
                                            integrantes serão vinculados ao documento
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="phase" class="form-label">Fase</label>
                                        <select name="phase" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.phase
                                        }" id="phase" v-model="page.data.phases">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.phases" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="description">Descrição do mapeamento</label>
                                        <textarea name="description" class="form-control" rows="4" :class="{
                                            'form-control-alert': page.rules.valids.description
                                        }" id="description" v-model="page.data.description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('process') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
                                <div v-if="page.data.process" class="mb-4 form-neg-box">
                                    <TableList :header="page.process.headers" :body="[page.data.process]"
                                        :casts="{ 'status': page.selects.status_process }" :count="false"
                                        :order="false" />
                                </div>
                                <div class="accordion mb-3" id="accordionSearchProcess">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="accordionSearchProcessHeadId">
                                            <button class="w-100 text-center px-2 py-3" type="button"
                                                :data-bs-toggle="[page.data.comission && 'collapse']"
                                                data-bs-target="#accordionSearchColapseId" aria-expanded="true"
                                                aria-controls="accordionSearchColapseId">
                                                <h2 class="txt-color text-center m-0">
                                                    <i class="bi bi-journal-bookmark me-1"></i>
                                                    Localizar Processo
                                                </h2>
                                                <p class="validation small text-center m-0"
                                                    :class="[page.data.comission ? 'txt-color-sec' : 'text-danger']">
                                                    {{
                                                        page.data.comission
                                                            ? `Aplique os filtros abaixo para localizar os Processos`
                                                            : `Selecione uma comissão`
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
                                                        <button @click="search_process" type="button"
                                                            class="btn btn-primary">
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
                        </div>

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('risks') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
                                <div class="mb-4">
                                    <h2 class="txt-color text-center m-0">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        {{ riskiness.title.primary }}
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0">
                                        {{ riskiness.title.secondary }}
                                    </p>
                                </div>
                                <div v-if="riskiness.uiview.register" id="register-box" class="mb-4 p-0">
                                    <input type="hidden" name="id" v-model="riskiness.data.id">

                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-12 col-md-8">
                                            <label for="risk_name" class="form-label">Risco</label>
                                            <input type="text" name="risk_name" class="form-control"
                                                :class="{ 'form-control-alert': riskiness.rules.valids.risk_name }"
                                                id="risk_name" v-model="riskiness.data.risk_name"
                                                placeholder="Nome do risco">
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="risk_treatment" class="form-label">Tratamento</label>
                                            <input type="text" name="risk_treatment" class="form-control"
                                                :class="{ 'form-control-alert': riskiness.rules.valids.risk_treatment }"
                                                id="risk_treatment" v-model="riskiness.data.risk_treatment"
                                                placeholder="Tratamento do risco">
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-12 col-md-4">
                                            <label for="risk_impact" class="form-label">Impacto</label>
                                            <select name="risk_impact" class="form-control"
                                                :class="{ 'form-control-alert': riskiness.rules.valids.risk_impact }"
                                                id="risk_impact" v-model="riskiness.data.risk_impact">
                                                <option value=""></option>
                                                <option v-for="o in page.selects.risk_impacts" :key="o.id"
                                                    :value="o.id">
                                                    {{ o.value }} - {{ o.title }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="risk_probability" class="form-label">Probabilidade</label>
                                            <select name="risk_probability" class="form-control"
                                                :class="{ 'form-control-alert': riskiness.rules.valids.risk_probability }"
                                                id="risk_probability" v-model="riskiness.data.risk_probability">
                                                <option value=""></option>
                                                <option v-for="o in page.selects.risk_probabilities" :key="o.id"
                                                    :value="o.id">
                                                    {{ o.value }} - {{ o.title }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="risk_related" class="form-label">Relacionado ao(à)</label>
                                            <input type="text" name="risk_related" class="form-control"
                                                :class="{ 'form-control-alert': riskiness.rules.valids.risk_related }"
                                                id="risk_related" v-model="riskiness.data.risk_related"
                                                placeholder="Entidade relacionada ao risco">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row-reverse justify-content-center mt-4">
                                        <button @click="risknessUi.toggle('list')" type="button"
                                            class="btn btn-warning">Cancelar <i class="bi bi-x-circle"></i></button>
                                        <button @click="pseudoData.save()" type="button"
                                            class="btn btn-primary mx-2">Enviar
                                            <i class="bi bi-check2-circle"></i></button>
                                    </div>
                                </div>
                                <div v-if="!riskiness.uiview.register">
                                    <div class="action-buttons d-flex">
                                        <button @click="risknessUi.toggle('register')" type="button"
                                            class="btn btn-action btn-action-primary mx-auto">
                                            <i class="bi bi-map"></i>
                                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Novo
                                                Risco</span>
                                        </button>
                                    </div>
                                    <div class="inside-box mb-4 form-neg-box">
                                        <TableList @action:update="pseudoData.update"
                                            @action:damage="(id) => swithToModal(id, damage, 'risk_damage')"
                                            @action:actions="(id) => swithToModal(id, actions, 'risk_actions')"
                                            @action:fastdelete="pseudoData.remove" :header="riskiness.dataheader"
                                            :body="riskiness.datalist"
                                            :actions="['update', 'fastdelete', 'damage', 'actions']" :casts="{
                                                'risk_impact': page.selects.risk_impacts,
                                                'risk_probability': page.selects.risk_probabilities,
                                            }" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">Cancelar <i
                                    class="bi bi-x-circle"></i></button>
                            <button type="submit" class="btn btn-outline-primary mx-2">Salvar <i
                                    class="bi bi-check2-circle"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!--MODAL Actions-->
        <div class="modal fade" id="modalDamage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered mx-auto">
                <div class="modal-content p-0 box">
                    <div class="modal-header border border-0 p-4">
                        <h1 class="modal-title">
                            <i class="bi bi-bookmarks me-2"></i>
                            Danos
                        </h1>
                        <button type="button" class="txt-color ms-auto" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="text-center mb-3">
                            <h2 class="txt-color p-0 m-0">{{ damage.title.primary }}</h2>
                            <p class="small txt-color-sec p-0 m-0">{{ damage.title.secondary }}</p>
                        </div>
                        <div v-if="damage.uiview.register">
                            <form @submit.prevent="() => save_actions(damage, damageData, 'risk_damage')"
                                class="row g-3 p-4 pt-0">
                                <div class="col-sm-12 col-md-12">
                                    <label for="risk_damage" class="form-label">Dano</label>
                                    <input type="text" name="risk_damage" class="form-control"
                                        :class="{ 'form-control-alert': damage.rules.valids.risk_damage }"
                                        id="risk_damage" v-model="damage.data.risk_damage" placeholder="Dano causado">
                                </div>
                                <div class="d-flex flex-row-reverse mt-4">
                                    <button type="submit" class="btn btn-outline-primary">Salvar <i
                                            class="bi bi-check2-circle"></i></button>
                                    <button type="button" @click="damageUi.toggle('list')"
                                        class="btn btn-outline-warning mx-2">Cancelar
                                        <i class="bi bi-check2-circle"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-listage">
                            <TableList @action:update="damageData.update" @action:fastdelete="damageData.remove"
                                :header="damage.dataheader" :body="damage.datalist" :actions="['update', 'fastdelete']"
                                :casts="{
                                    'risk_action_type': page.selects.risk_actions
                                }" />
                        </div>
                    </div>
                    <div v-if="!damage.uiview.register" class="modal-footer border-0">
                        <button @click="damageUi.toggle('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-md-block d-lg-inline">Adicionar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL Actions-->
        <div class="modal fade" id="modalActions" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered mx-auto">
                <div class="modal-content p-0 box">
                    <div class="modal-header border border-0 p-4">
                        <h1 class="modal-title">
                            <i class="bi bi-bookmarks me-2"></i>
                            Tomada de ações
                        </h1>
                        <button type="button" class="txt-color ms-auto" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="text-center mb-3">
                            <h2 class="txt-color p-0 m-0">{{ actions.title.primary }}</h2>
                            <p class="small txt-color-sec p-0 m-0">{{ actions.title.secondary }}</p>
                        </div>
                        <div v-if="actions.uiview.register">
                            <form @submit.prevent="() => save_actions(actions, actionsData, 'risk_actions')"
                                class="row g-3 p-4 pt-0">
                                <div class="col-sm-12 col-md-12">
                                    <label for="risk_action_name" class="form-label">Ação</label>
                                    <input type="text" name="risk_action_name" class="form-control"
                                        :class="{ 'form-control-alert': actions.rules.valids.risk_action_name }"
                                        id="risk_action_name" v-model="actions.data.risk_action_name"
                                        placeholder="Nome do risco">
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <label for="risk_action_type" class="form-label">Tipo</label>
                                    <select name="risk_action_type" class="form-control"
                                        :class="{ 'form-control-alert': actions.rules.valids.risk_action_type }"
                                        id="risk_action_type" v-model="actions.data.risk_action_type">
                                        <option value=""></option>
                                        <option v-for="o in page.selects.risk_actions" :key="o.id" :value="o.id">
                                            {{ o.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-8">
                                    <label for="risk_action_responsability" class="form-label">Responsabilidade</label>
                                    <input type="text" name="risk_action_responsability" class="form-control"
                                        :class="{ 'form-control-alert': actions.rules.valids.risk_action_responsability }"
                                        id="risk_action_responsability"
                                        v-model="actions.data.risk_action_responsability"
                                        placeholder="Responsáveis pela ação">
                                </div>
                                <div class="d-flex flex-row-reverse mt-4">
                                    <button type="submit" class="btn btn-outline-primary">Salvar <i
                                            class="bi bi-check2-circle"></i></button>
                                    <button type="button" @click="actionsUi.toggle('list')"
                                        class="btn btn-outline-warning mx-2">Cancelar
                                        <i class="bi bi-check2-circle"></i></button>
                                </div>
                            </form>
                        </div>
                        <div v-if="!actions.uiview.register" class="modal-listage">
                            <TableList @action:update="actionsData.update" @action:fastdelete="actionsData.remove"
                                :header="actions.dataheader" :body="actions.datalist"
                                :actions="['update', 'fastdelete']" :casts="{
                                    'risk_action_type': page.selects.risk_actions
                                }" />
                        </div>
                    </div>
                    <div v-if="!actions.uiview.register" class="modal-footer border-0">
                        <button @click="actionsUi.toggle('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-md-block d-lg-inline">Adicionar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>