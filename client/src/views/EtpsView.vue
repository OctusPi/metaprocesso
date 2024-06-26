<script setup>
import { onMounted, ref, watch } from 'vue'
import Ui from '@/utils/ui';
import masks from '@/utils/masks';
import Data from '@/services/data';
import Tabs from '@/utils/tabs';

import MainNav from '@/components/MainNav.vue';
import TabNav from '@/components/TabNav.vue';
import AttachmentsList from '@/components/AttachmentsList.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import InputRichText from '@/components/inputs/InputRichText.vue';
import DfdsSelect from '@/components/DfdsSelect.vue';

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
        status: [],
    },
    rules: {
        fields: {
            dfds: 'required',
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
    }
})

const tabs = ref([
    { id: 'info', icon: 'bi-info-circle', title: 'Infos', status: true },
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

function autoProtocol(organId) {
    if (!organId) {
        return null
    }

    const d = new Date();

    const date = (
        d.getDay().toString().padStart(2, '0')
        + d.getMonth().toString().padStart(2, '0')
        + d.getFullYear().toString().padStart(4, '0')
    )

    const mili = (
        d.getMilliseconds().toString().padStart(4, '0')
    )

    return `${String(organId).padStart(3, '0')}-${date}-${mili}`
}

function setProtocol() {
    page.value.data.protocol = autoProtocol(page.value.data.organ)
}

const attachmentTypes = [
    { id: 0, title: 'Memória de Cálculo' },
    { id: 1, title: 'Levantamento de Mercado' },
]

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
                        <button @click="ui.toggle('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                        <button @click="ui.toggle('search')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-search"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Pesquisar</span>
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
                                    format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
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
                    <form class="form-row" @submit.prevent="data.save()">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <TabNav :tab-instance="tabSwitch" identify="etps-nav" />
                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('info') }"
                                id="dfds-tab-pane" role="tabpanel" aria-labelledby="dfds-tab" tabindex="0">
                                <div class="row mb-2 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="emission" class="form-label">Emissão</label>
                                        <VueDatePicker auto-apply v-model="page.data.emission"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                            input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                            calendar-class-name="dp-custom-calendar"
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
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_description" class="form-label">Descrição do Objeto</label>
                                        <InputRichText :valid="page.rules.valids.object_description"
                                            placeholder="Descrição do Objeto" identifier="object_description"
                                            v-model="page.data.object_description" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_classification" class="form-label">Classificação do
                                            Objeto</label>
                                        <InputRichText :valid="page.rules.valids.object_classification"
                                            placeholder="Classificação do Objeto" identifier="object_classification"
                                            v-model="page.data.object_classification" />
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('dfds') }"
                                id="dfds-tab-pane" role="tabpanel" aria-labelledby="dfds-tab" tabindex="0">
                                <div v-if="!page.data.protocol">
                                    <h2 class="txt-color text-center m-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0">
                                        É necessário selecionar um órgão para continuar
                                    </p>
                                </div>
                                <DfdsSelect v-else :organ="page.data.organ" :valid="page.rules.valids.dfds"
                                    identifier="dfds" v-model="page.data.dfds"
                                    @callAlert="(msg) => emit('callAlert', msg)" />
                            </div>

                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('necessidade') }"
                                id="necessity-tab-pane" role="tabpanel" aria-labelledby="necessity-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="necessity" class="form-label">Descrição da
                                            Necessidade</label>
                                        <InputRichText :valid="page.rules.valids.necessity"
                                            placeholder="Descrição da Necessidade" identifier="necessity"
                                            v-model="page.data.necessity" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_requirements" class="form-label">Descrição dos Requisitos
                                            da Contratação</label>
                                        <InputRichText :valid="page.rules.valids.contract_requirements"
                                            placeholder="Descrição dos Requisitos da Contratação"
                                            identifier="contract_requirements"
                                            v-model="page.data.contract_requirements" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_forecast" class="form-label">Previsão de Realização
                                            da Contratação</label>
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
                                        <label for="market_survey" class="form-label">Levantamento de Mercado</label>
                                        <InputRichText :valid="page.rules.valids.market_survey"
                                            placeholder="Levantamento de Mercado" identifier="market_survey"
                                            v-model="page.data.market_survey" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="solution_full_description" class="form-label">Descrição da Solução
                                            como um Todo</label>
                                        <InputRichText :valid="page.rules.valids.solution_full_description"
                                            placeholder="Descrição da Solução como um Todo"
                                            identifier="solution_full_description"
                                            v-model="page.data.solution_full_description" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_calculus_memories" class="form-label">Estimativa das
                                            Quantidades Contratadas</label>
                                        <InputRichText :valid="page.rules.valids.contract_calculus_memories"
                                            placeholder="Estimativa das Quantidades Contratadas"
                                            identifier="contract_calculus_memories"
                                            v-model="page.data.contract_calculus_memories" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_expected_price" class="form-label">Estimativa do Preço da
                                            Contratação</label>
                                        <InputRichText :valid="page.rules.valids.contract_expected_price"
                                            placeholder="Estimativa do Preço da Contratação"
                                            identifier="contract_expected_price"
                                            v-model="page.data.contract_expected_price" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="solution_parcel_justification" class="form-label">Justificativa para
                                            o
                                            Parcelamento ou Não</label>
                                        <InputRichText :valid="page.rules.valids.solution_parcel_justification"
                                            placeholder="Justificativa para o Parcelamento ou Não"
                                            identifier="solution_parcel_justification"
                                            v-model="page.data.solution_parcel_justification" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="correlated_contracts" class="form-label">Contratações Correlatas
                                            e/ou Interdependentes</label>
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
                                        <label for="expected_results" class="form-label">Resultados Pretendidos</label>
                                        <InputRichText :valid="page.rules.valids.expected_results"
                                            placeholder="Resultados Pretendidos" identifier="expected_results"
                                            v-model="page.data.expected_results" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_previous_actions" class="form-label">Providências a Serem
                                            Tomadas</label>
                                        <InputRichText :valid="page.rules.valids.contract_previous_actions"
                                            placeholder="Providências a Serem Tomadas"
                                            identifier="contract_previous_actions"
                                            v-model="page.data.contract_previous_actions" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_alignment" class="form-label">Alinhamento de
                                            Contrato</label>
                                        <InputRichText :valid="page.rules.valids.contract_alignment"
                                            placeholder="Alinhamento de Contrato" identifier="contract_alignment"
                                            v-model="page.data.contract_alignment" />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="ambiental_impacts" class="form-label">Possíveis Impactos
                                            Ambientais</label>
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