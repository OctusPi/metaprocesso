<script setup>
import { onMounted, ref, watch } from 'vue'
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';
import masks from '@/utils/masks';
import DfdsSelect from '@/components/DfdsSelect.vue';
import dates from '@/utils/dates';
import Tabs from '@/utils/tabs';
import TabNav from '@/components/TabNav.vue';
import utils from '@/utils/utils';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const page = ref({
    baseURL: '/process',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    search: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'protocol', title: 'PROTOCOLO' },
    ],
    selects: {
        organs: [],
        comissions: [],
        types: [],
        modalities: [],
        situations: [],
        dfds: [],
        units: [],
        ordinators: [],
    },
    rules: {
        fields: {
            protocol: 'required',
            date_ini: 'required',
            hour_ini: 'required',
            year_pca: 'required',
            type: 'required',
            modality: 'required',
            organ: 'required',
            units: 'required',
            ordinators: 'required',
            comission: 'required',
            object: 'required',
            situation: 'required',
            initial_value: 'required',
            winner_value: 'required',
            dfds: 'required'
        },
        valids: {}
    }
})

const tabs = ref([
    { id: 'origem', icon: 'bi-bounding-box', title: 'Origem', status: true },
    { id: 'processo', icon: 'bi-journal-bookmark', title: 'Processo', status: false },
    { id: 'dfds', icon: 'bi-journal-album', title: 'DFDs', status: false },
    { id: 'revisar', icon: 'bi-journal-check', title: 'Revisar', status: false },
])

const ui = new Ui(page, 'Processos')
const data = new Data(page, emit, ui)
const tabSwitch = new Tabs(tabs)

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

watch(() => page.value.uiview.register, (newval) => {
    if (newval && !page.value.data.id) {
        const { date, time } = dates.now_utc()
        page.value.data.date_ini = date
        page.value.data.hour_ini = time
    }
})

// Metadata from DFDs select
const dfds = ref({})

function unselect_dfd(id) {
    page.value.data.dfds = [...page.value.data.dfds || []]
        .filter((item) => item.id != id)
}

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

    return `${String(organId).padStart(3, '0')}${date}${mili}`
}

function setProtocol() {
    data.selects('organ', page.value.data.organ)
    page.value.data.protocol = autoProtocol(page.value.data.organ)
}

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
                icon: 'bi-journal-bookmark',
                title: 'Gestor de Processos',
                description: 'Registro de Processos de Aquisição de Bens e Serviços'
            }" />

            <div class="box box-main p-0 rounded-4">
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

                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-12">
                                <label for="s-protocol" class="form-label">Protocolo</label>
                                <input type="text" name="protocol" class="form-control" id="s-protocol"
                                    v-model="page.search.protocol" placeholder="Pesquise por partes do protocolo">
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
                                <label for="s-date_ini" class="form-label">Data Inicial</label>
                                <VueDatePicker id="s-date_ini" auto-apply v-model="page.search.date_ini"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                    input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                    calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                                    menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-situation" class="form-label">Situação</label>
                                <select name="situation" class="form-control" id="s-situation"
                                    v-model="page.search.situation">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.situations" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-modality" class="form-label">Modalidade</label>
                                <select name="modality" class="form-control" id="s-modality"
                                    v-model="page.search.modality">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.modalities" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-type" class="form-label">Tipo</label>
                                <select name="type" class="form-control" id="s-type" v-model="page.search.type">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.types" :key="o.id" :value="o.id">
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
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']"
                        :casts="{ 'status': page.selects.status }" />
                </div>

                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save()">
                        <input type="hidden" name="id" v-model="page.data.id">

                        <TabNav :tab-instance="tabSwitch" identify="process-nav" />

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('origem') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="organ" class="form-label">Orgão</label>
                                        <select name="organ" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.organ }" id="organ"
                                            v-model="page.data.organ" @change="setProtocol">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                        <div class="form-text txt-color-sec">
                                            Ao selecionar o órgão o protocolo é automaticamente preenchido
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="unit" class="form-label">Unidades</label>
                                        <InputDropMultSelect :valid="page.rules.valids.units" v-model="page.data.units"
                                            :options="page.selects.units" identify="units" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="protocol" class="form-label">Protocolo</label>
                                        <input type="text" name="protocol" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.protocol }" id="protocol"
                                            v-model="page.data.protocol" placeholder="XXXXXXXXXXXXXXX">
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-8">
                                        <label for="comission" class="form-label">Comissão</label>
                                        <select name="comission" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.comission }"
                                            id="comission" v-model="page.search.comission"
                                            @change="data.selects('comission')">
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
                                        <label for="year_pca" class="form-label">Ano do PCA</label>
                                        <input maxlength="4" type="text" name="year_pca" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.year_pca }" id="year_pca"
                                            v-model="page.data.year_pca" placeholder="AAAA">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('processo') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="date_ini" class="form-label">Data Inicial</label>
                                        <VueDatePicker id="date_ini" auto-apply v-model="page.data.date_ini"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                            :input-class-name="page.rules.valids.date_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="hour_ini" class="form-label">Hora Inicial</label>
                                        <VueDatePicker time-picker id="hour_ini" auto-apply v-model="page.data.hour_ini"
                                            :enable-time-picker="false" model-type="HH:mm"
                                            :input-class-name="page.rules.valids.hour_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            locale="pt-br" calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="situation" class="form-label">Situação</label>
                                        <select name="situation" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.situation }"
                                            id="situation" v-model="page.data.situation">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.situations" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="initial_value" class="form-label">Valor Inicial</label>
                                        <input type="text" name="initial_value" class="form-control"
                                            v-maska:[masks.maskmoney]
                                            :class="{ 'form-control-alert': page.rules.valids.initial_value }"
                                            id="initial_value" v-model="page.data.initial_value" placeholder="0,00">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="winner_value" class="form-label">Valor Vencedor</label>
                                        <input type="text" name="winner_value" class="form-control"
                                            v-maska:[masks.maskmoney]
                                            :class="{ 'form-control-alert': page.rules.valids.winner_value }"
                                            id="winner_value" v-model="page.data.winner_value" placeholder="0,00">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="modality" class="form-label">Modalidade</label>
                                        <select name="modality" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.modality
                                        }" id="modality" v-model="page.data.modality">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.modalities" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="type" class="form-label">Tipo</label>
                                        <select name="type" class="form-control" :class="{
                                            'form-control-alert': page.rules.valids.type
                                        }" id="type" v-model="page.data.type">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.types" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="description" class="form-label">Descrição</label>
                                        <textarea name="description" class="form-control" rows="4" :class="{
                                            'form-control-alert': page.rules.valids.description
                                        }" id="description" v-model="page.data.description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('dfds') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <DfdsSelect :organ="page.data.organ" :valid="page.rules.valids.dfds"
                                        identifier="dfds" v-model="page.data.dfds"
                                        @callAlert="(msg) => emit('callAlert', msg)" @getMeta="(meta) => dfds = meta" />
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('revisar') }"
                                id="processes-tab-pane" role="tabpanel" aria-labelledby="processes-tab" tabindex="0">
                                <div class="box-revisor mb-4">
                                    <div class="box-revisor-title d-flex mb-4">
                                        <div class="bar-revisor-title me-2"></div>
                                        <div class="txt-revisor-title">
                                            <h3>Informações Gerais</h3>
                                            <p>Dados referentes ao processo</p>
                                        </div>
                                    </div>
                                    <div class="box-revisor-content">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h4>Data Inicial</h4>
                                                <p>{{ dates.toPtBr(page.data.date_ini) }}</p>
                                            </div>
                                            <div class="col-md-3">
                                                <h4>Horário Inicial</h4>
                                                <p>{{ page.data.hour_ini }}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <h4>Valor Inicial</h4>
                                                <p>R${{ page.data.initial_value ?? '*****' }}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <h4>Valor Vencedor</h4>
                                                <p>R${{ page.data.winner_value ?? '*****' }}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <h4>Ano PCA</h4>
                                                <p>{{ page.data.year_pca ?? '*****' }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h4>Modalidade</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.modalities,
                                                            page.data.modality
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <h4>Situação</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.situations,
                                                            page.data.situation
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <h4>Tipo de processo</h4>
                                                <p>
                                                    {{
                                                        utils.getTxt(
                                                            page.selects.types,
                                                            page.data.type
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4>Descrição do Processo</h4>
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
                                            <h3>DFDs</h3>
                                            <p>
                                                Lista das DFDs atreladas ao processo
                                            </p>
                                        </div>
                                    </div>
                                    <div class="box-revisor-content">
                                        <!-- list items -->
                                        <div v-if="page.data.dfds && page.data.dfds?.length > 0">
                                            <TableList :smaller="true" :count="false" @action:fastdelete="unselect_dfd"
                                                :casts="{ 'status': dfds.selects.status }" :header="dfds.dataheader"
                                                :body="page.data.dfds" :actions="['fastdelete']" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped></style>