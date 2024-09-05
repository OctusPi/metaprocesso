<script setup>
import { createApp, onMounted, watch } from 'vue';
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TabNav from '@/components/TabNav.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import gpt from '@/services/gpt';
import exp from '@/services/export';
import notifys from '@/utils/notifys';
import masks from '@/utils/masks';
import utils from '@/utils/utils';
import dates from '@/utils/dates';
import Tabs from '@/utils/tabs';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [main, mainData] = Layout.new(emit, {
    url: '/riskiness',
    datalist: props.datalist,
    header: [
        { key: 'version', title: 'VERSÃO E DATA', sub: [{ key: 'date_version' }] },
        { key: 'comission.name', title: 'ORIGEM', sub: [{ key: 'organ.name' }] },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] },
        { key: 'phase', title: 'FASE' }
    ],
    rules: {
        comission: 'required',
        phase: 'required',
        description: 'required',
        process: 'required',
    },
    process: {
        search: {},
        data: [],
        headers: [
            { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'ordinators.name', title: 'ORDENADORES' },
            { key: 'units.title', title: 'ORIGEM', sub: [{ key: 'organ.name' }] },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
})

const [risks, risksData] = Layout.pseudo(emit, {
    risk_actions: [],
    risk_damage: [],
    header: [
        { key: 'verb_id', title: 'ID' },
        { key: 'risk_name', title: 'RISCO', sub: [{ key: 'risk_related' }] },
        { key: 'risk_treatment', title: 'TRATAMENTO' },
        { key: 'risk_probability', title: 'PROBABILIDADE' },
        { key: 'risk_impact', title: 'IMPACTO' },
        { key: 'risk_level', title: 'NÍVEL DE RISCO' }
    ],
    rules: {
        risk_name: 'required',
        risk_related: 'required',
        risk_probability: 'required',
        risk_impact: 'required',
        risk_treatment: 'required',
    },
})

const [actions, actionsData] = Layout.pseudo(emit, {
    risk: {},
    header: [
        { key: 'verb_id', title: 'ID', sub: [{ key: 'risk_action_type' }] },
        { title: 'AÇÃO', sub: [{ key: 'risk_action_name' }] },
        { key: 'risk_action_responsability', title: 'RESPONSABILIDADE' },
    ],
    rules: {
        risk_action_name: 'required',
        risk_action_type: 'required',
        risk_action_responsability: 'required',
    }
})

const [damage, damageData] = Layout.pseudo(emit, {
    risk: {},
    header: [
        { key: 'risk_damage', title: 'DANO' },
    ],
    rules: {
        risk_damage: 'required',
    }
})

const [accomp, accompData] = Layout.pseudo(emit, {
    header: [
        { key: 'verb_id', title: 'ID', sub: [{ key: 'accomp_date' }] },
        { key: 'accomp_risk', title: 'RISCO' },
        { key: 'accomp_action', title: 'AÇÃO' },
        { key: 'accomp_treatment', title: 'TRATAMENTO' },
    ],
    rules: {
        accomp_date: 'required',
        accomp_risk: 'required',
        accomp_action: 'required',
        accomp_treatment: 'required',
    },
})


risksData.setAfterSave((data) => {
    return { 'verb_id': 'R' + data.verb_id }
})


risksData.setBeforeSave((data) => {
    const getSelect = (select, name) =>
        main.selects[select].find(({ id }) => id === data[name])

    return {
        risk_actions: data.risk_actions ?? [],
        risk_damage: data.risk_damage ?? [],
        risk_level: getSelect('risk_impacts', 'risk_impact').value
            * getSelect('risk_probabilities', 'risk_probability').value,
    }
})

actionsData.setAfterSave((data, index) => {
    const sep = actionsData.separate('risk_action_type', index)
    const act = main.selects.risk_actions
        .find(({ id }) => id == data.risk_action_type)
    return {
        verb_id: act.code + sep[data.risk_action_type] ?? 0
    }
})

function swithToModal(id, reference, ds, key) {
    reference.risk = ds.datalist.find((item) => item.id === id)
    reference.datalist = reference.risk[key] ?? []
}

function search_process() {
    http.post('/pricerecords/list_processes', main.process.search, emit, (resp) => {
        main.process.data = resp.data ?? []
    })
}

function export_riskiness(id) {
    http.get(`${main.url}/export/${id}`, emit, (resp) => {
        const instance = resp.data
        const containerReport = document.createElement('div')
        const instanceReport = createApp(RiskMapReport, { riskmap: instance, selects: main.selects })
        instanceReport.mount(containerReport)
        exp.exportPDF(containerReport, `Mapa_De_Risco-${instance.date_version}-v${instance.version}`)
    })

}

function copyFromDataset(dsval, key, newval) {
    if (Array.isArray(newval) && newval.length > 0) {
        const instance = risks.datalist
            .find((item) => item.id === dsval.risk.id)
        instance[key] = newval
    }
}

watch(() => actions.datalist, (newval) => {
    copyFromDataset(actions, 'risk_actions', newval)
})

watch(() => damage.datalist, (newval) => {
    copyFromDataset(damage, 'risk_damage', newval)
})

watch(() => accomp.data, (newval) => {
    accompData.select(
        risks.datalist,
        'risk_actions', 'id',
        newval.accomp_risk
    )
})

watch(() => props.datalist, (newdata) => {
    main.datalist = newdata
})

watch(() => main.ui.register, (newdata) => {
    if (newdata) {
        risks.datalist = []
        main.process.data = []
    }
    if (newdata && mainData.id) {
        risks.datalist = mainData.riskiness ?? []
        accomp.datalist = mainData.accompaniments ?? []
    }
})

const tabs = new Tabs([
    { id: 'process', icon: 'bi-bounding-box', title: 'Processo' },
    { id: 'infos', icon: 'bi-bounding-box', title: 'Infos' },
    { id: 'risks', icon: 'bi-journal-bookmark', title: 'Riscos' },
    { id: 'accompaniments', icon: 'bi-check', title: 'Acompanhamentos' },
])

watch(() => props.datalist, (newdata) => {
    main.datalist = newdata
})

onMounted(() => {
    mainData.selects()
    mainData.list()
})

</script>

<template>
    <div class="page">
        <NavMainUi />
        <main class="main">
            <HeaderMainUi />

            <!-- List -->
            <section v-if="!main.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>DFDs</h2>
                        <p>
                            Listagem das DFDs atreladas ao
                            <span class="txt-color">{{ main.organ_name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="mainData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="mainData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                    </div>
                </div>
                <!-- Search -->
                <div v-if="main.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="mainData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_ini" class="form-label">Data Inicial</label>
                            <VueDatePicker auto-apply v-model="main.search.date_i" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_fin" class="form-label">Data Final</label>
                            <VueDatePicker auto-apply v-model="main.search.date_f" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-phase" class="form-label">Fase</label>
                            <select name="phase" class="form-control" id="s-phase" v-model="main.search.phase">
                                <option value=""></option>
                                <option v-for="o in main.selects.phases" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-8">
                            <label for="s-description" class="form-label">Descrição</label>
                            <input type="text" name="description" class="form-control" id="s-description"
                                v-model="main.search.description" placeholder="Descrição" />
                        </div>
                        <div class="col-sm-12">
                            <div class="accordion" id="s-accordionSearchProcess">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="s-accordionSearchProcessHeadId">
                                        <button class="w-100 text-center px-2 py-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#s-accordionSearchColapseId"
                                            aria-expanded="true" aria-controls="s-accordionSearchColapseId">
                                            <h2 class="txt-color text-center m-0">
                                                <i class="bi bi-journal-bookmark me-1"></i>
                                                Localizar Processo
                                            </h2>
                                            <p class="validation small text-center m-0 txt-color-sec">
                                                Aplique os filtros abaixo para localizar os Processos
                                            </p>
                                        </button>
                                    </h2>
                                    <div id="s-accordionSearchColapseId" class="accordion-collapse collapse"
                                        aria-labelledby="accordionSearchProcessHeadId"
                                        data-bs-parent="#accordionSearchProcess">
                                        <div class="accordion-body p-0 m-0">
                                            <div class="row g-3 p-4">
                                                <div class="col-sm-12 col-md-4">
                                                    <label for="date_s_ini" class="form-label">Data Inicial</label>
                                                    <VueDatePicker auto-apply v-model="main.process.search.date_i"
                                                        :enable-time-picker="false" format="dd/MM/yyyy"
                                                        model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                                        locale="pt-br" calendar-class-name="dp-custom-calendar"
                                                        calendar-cell-class-name="dp-custom-cell"
                                                        menu-class-name="dp-custom-menu" />
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <label for="date_s_fin" class="form-label">Data Final</label>
                                                    <VueDatePicker auto-apply v-model="main.process.search.date_f"
                                                        :enable-time-picker="false" format="dd/MM/yyyy"
                                                        model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                                        locale="pt-br" calendar-class-name="dp-custom-calendar"
                                                        calendar-cell-class-name="dp-custom-cell"
                                                        menu-class-name="dp-custom-menu" />
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <label for="s-protocol" class="form-label">Protocolo</label>
                                                    <input type="text" name="protocol" class="form-control"
                                                        id="s-protocol" v-model="main.process.search.protocol"
                                                        placeholder="Número do Protocolo do Processo" />
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <label for="s-organ" class="form-label">Orgão</label>
                                                    <select name="organ" class="form-control" id="s-organ"
                                                        v-model="main.process.search.organ"
                                                        @change="mainData.selects('organ', main.process.search.organ)">
                                                        <option value=""></option>
                                                        <option v-for="o in main.selects.organs" :key="o.id"
                                                            :value="o.id">
                                                            {{ o.title }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <label for="s-unit" class="form-label">Unidades</label>
                                                    <InputDropMultSelect v-model="main.process.search.units"
                                                        :options="main.selects.units" identify="p_units" />
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <label for="s-description" class="form-label">Objeto</label>
                                                    <input type="text" name="description" class="form-control"
                                                        id="s-description" v-model="main.process.search.description"
                                                        placeholder="Pesquise por partes do Objeto do Processo" />
                                                </div>
                                                <div class="mt-4">
                                                    <button @click="search_process" type="button"
                                                        class="btn btn-primary">
                                                        <i class="bi bi-search"></i> Localizar Processos
                                                    </button>
                                                </div>
                                            </div>

                                            <TableListRadio identify="process" v-model="mainData.process"
                                                :header="main.process.headers" :body="main.process.data" :mounts="{
                                                    status: [Mounts.Cast(main.selects.status_process), Mounts.Status()],
                                                    description: [Mounts.Truncate(200)],
                                                }" />
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                    <TableList :header="main.header" :body="main.datalist" :actions="[
                        Actions.Edit(mainData.update),
                        Actions.Delete(mainData.remove),
                        Actions.Export('file', export_riskiness),
                    ]" :mounts="{
                        phase: [Mounts.Cast(main.selects.phases)]
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="main.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar DFD</h2>
                        <p>Registro de Documento de Formalização de Demanda</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="mainData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <TabNav :tabs="tabs" identify="tabbed" />
                    <form @submit.prevent="mainData.save">
                        <div class="tab-pane fade row m-0 p-4 pt-1 g-3" :class="{ 'show active': tabs.is('origin') }">

                        </div>
                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button @click="mainData.ui('register')" class="btn btn-action-secondary">
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