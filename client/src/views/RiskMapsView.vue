<script setup>
import { onMounted, ref, watch } from 'vue'
// import masks from '@/utils/masks'
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';
import PseudoData from '@/services/pseudodata';

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
        status: [],
        risk_actions: [],
    },
    rules: {
        fields: {

        },
        valids: {}
    },
})

const ui = new Ui(page, 'Mapas de Risco')
const data = new Data(page, emit, ui)

const riskiness = ref({
    datalist: [],
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    dataheader: [
        { key: 'id', title: 'ID' },
        { key: 'risk_name', title: 'RISCO' },
        { key: 'risk_related', title: 'RELAÇÃO' },
        { key: 'risk_probability', cast: 'value', title: 'PROBABILIDADE' },
        { key: 'risk_impact', cast: 'value', title: 'IMPACTO' },
        { key: 'risk_level', title: 'NÍVEL DE RISCO' }
    ],
    selects: {
        status: [],
        risk_actions: [],
    },
    data: {},
    search: {},
    rules: {
        fields: {
            'risk_name': 'required',
            'risk_related': 'required',
            'risk_probability': 'required',
            'risk_impact': 'required',
            'risk_level': 'required',
        },
        valids: {}
    },
})

const risknessUi = new Ui(riskiness, 'Riscos')

const pseudoData = new PseudoData(riskiness, emit, risknessUi, (data) => {
    function getRiskVal(val) {
        return page.value.selects.risk_actions.find(({id}) => id == val).value
    }

    return {
        'risk_level': getRiskVal(data.risk_impact) * getRiskVal(data.risk_probability),
    }
})

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
                        <div>
                            <div class="mb-4">
                                <h2 class="txt-color text-center m-0">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    {{ riskiness.title.primary }}
                                </h2>
                                <p class="txt-color-sec small text-center m-0">
                                    {{ riskiness.title.secondary }}
                                </p>
                            </div>
                            <!--BOX REGISTER-->
                            <div v-if="riskiness.uiview.register" id="register-box" class="mb-4 p-0">
                                <input type="hidden" name="id" v-model="riskiness.data.id">

                                <div class="col-sm-12 col-md-4">
                                    <label for="risk_name" class="form-label">Risco</label>
                                    <input type="text" name="risk_name" class="form-control"
                                        :class="{ 'form-control-alert': riskiness.rules.valids.risk_name }"
                                        id="risk_name" v-model="riskiness.data.risk_name" placeholder="Nome do risco">
                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <label for="risk_related" class="form-label">Relacionado à(ao)</label>
                                    <input type="text" name="risk_related" class="form-control"
                                        :class="{ 'form-control-alert': riskiness.rules.valids.risk_related }"
                                        id="risk_related" v-model="riskiness.data.risk_related" placeholder="Nome do risco">
                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <label for="risk_impact" class="form-label">Impacto</label>
                                    <select name="risk_impact" class="form-control"
                                        :class="{ 'form-control-alert': riskiness.rules.valids.risk_impact }"
                                        id="risk_impact" v-model="riskiness.data.risk_impact">
                                        <option value=""></option>
                                        <option v-for="o in page.selects.risk_actions" :key="o.id" :value="o.id">
                                            {{ o.title }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <label for="risk_probability" class="form-label">Probabilidade</label>
                                    <select name="risk_probability" class="form-control"
                                        :class="{ 'form-control-alert': riskiness.rules.valids.risk_probability }"
                                        id="risk_probability" v-model="riskiness.data.risk_probability">
                                        <option value=""></option>
                                        <option v-for="o in page.selects.risk_actions" :key="o.id" :value="o.id">
                                            {{ o.title }}
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex flex-row-reverse justify-content-center mt-4">
                                    <button @click="risknessUi.toggle('list')" type="button"
                                        class="btn btn-warning">Cancelar <i class="bi bi-x-circle"></i></button>
                                    <button @click="pseudoData.save()" type="button" class="btn btn-primary mx-2">Enviar
                                        <i class="bi bi-check2-circle"></i></button>
                                </div>
                            </div>
                            <div v-if="!riskiness.uiview.register">
                                <div class="inside-box mb-4 form-neg-box">
                                    <TableList @action:update="pseudoData.update" @action:fastdelete="pseudoData.remove"
                                        :header="riskiness.dataheader" :body="riskiness.datalist"
                                        :actions="['update', 'delete']" :casts="{
                                            'impact': page.selects.risk_actions,
                                            'probability': page.selects.risk_actions,
                                        }" />
                                </div>
                                <div class="action-buttons d-flex">
                                    <button @click="risknessUi.toggle('register')" type="button"
                                        class="btn btn-action btn-action-primary mx-auto">
                                        <i class="bi bi-map"></i>
                                        <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Novo
                                            Risco</span>
                                    </button>
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
    </main>
</template>