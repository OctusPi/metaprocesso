<script setup>
import { onMounted, ref, watch } from 'vue'
import Ui from '@/utils/ui';
import masks from '@/utils/masks';
import Data from '@/services/data';
import Tabs from '@/utils/tabs';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import InputRichText from '@/components/inputs/InputRichText.vue';
import InputMultSelect from '@/components/inputs/InputMultSelect.vue';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })

const page = ref({
    baseURL: '/etps',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'number', title: 'NÚMERO' },
        { key: 'necessity', title: 'NECESSIDADE' },
        { key: 'status', title: 'STATUS' },
    ],
    search: {},
    selects: {
        organs: [],
        status: [],
    },
    rules: {
        fields: {
            organ: 'required',
            unit: 'required',
            ordinator: 'required',
            demandant: 'required',
            comission: 'required',
        },
        valids: {}
    }
})

const ui = new Ui(page, 'ETPs')
const data = new Data(page, emit, ui)
const tabs = ref([
    { id: 'dfds', icon: 'bi-journal-album', title: 'DFDs', status: true },
    { id: 'infos_gerais', icon: 'bi-journal-album', title: 'Geral', status: false },
    { id: 'necessity', icon: 'bi-file-earmark-text', title: 'Necessidade', status: false },
    { id: 'solution', icon: 'bi-file-earmark-text', title: 'Solução', status: false },
    { id: 'revisor', icon: 'bi-journal-check', title: 'Revisar', status: false },
])
const tabSwitch = new Tabs(tabs)

function generate(type) {

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
                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- DATA LIST -->
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save()">
                        <input type="hidden" name="id" v-model="page.data.id">

                        <ul class="nav nav-fill mb-5" id="dfdTab" role="tablist">
                            <li v-for="tab in tabs" :key="tab.id" class="nav-item" role="presentation">
                                <button class="nav-link nav-step" data-bs-toggle="tab" type="button" role="tab"
                                    @click="tabSwitch.navigate_tab(null, tab.id)" :class="{ 'active': tab.status }"
                                    :id="`${tab.id}-tab`" :aria-controls="`${tab.div}-tab-pane`"
                                    :aria-selected="tab.status ? 'true' : 'false'">
                                    <div class="nav-line-step"></div>
                                    <div class="nav-step-txt mx-auto">
                                        <i class="bi" :class="tab.icon"></i>
                                        <span>{{ tab.title }}</span>
                                    </div>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="dfdTabContent">
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('dfds') }"
                                id="dfds-tab-pane" role="tabpanel" aria-labelledby="dfds-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="dfds" class="form-label">Selecione as DFDs</label>
                                        <InputMultSelect v-model="page.data.dfds" :options="page.selects.dfds"
                                            identify="dfds" />
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('infos_gerais') }"
                                id="infos_gerais-tab-pane" role="tabpanel" aria-labelledby="infos_gerais-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_description" class="form-label">Descrição do Objeto</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_classification" class="form-label">Classificação do Objeto</label>
                                        <InputRichText />
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('necessity') }"
                                id="necessity-tab-pane" role="tabpanel" aria-labelledby="necessity-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="object_description" class="form-label">Descrição da Necessidade</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_requirements" class="form-label">Descrição dos Requisitos da Contratação</label>
                                        <InputRichText />
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('solution') }"
                                id="solution-tab-pane" role="tabpanel" aria-labelledby="solution-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-12">
                                        <label for="market_survey" class="form-label">Levantamento de Mercado</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="solution_full_description" class="form-label">Descrição da Solução como um Todo</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="solution_full_description" class="form-label">Estimativa das Quantidades Contratadas</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_expected_price" class="form-label">Estimativa do Preço da Contratação</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="contract_expected_price" class="form-label">Justificativa para o Parcelamento ou Não</label>
                                        <InputRichText />
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="correlated_contracts" class="form-label">Contratações Correlatas e/ou Interdependentes</label>
                                        <InputRichText />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" :class="{ 'show active': tabSwitch.activate_tab('revisor') }"
                                id="revisor-tab-pane" role="tabpanel" aria-labelledby="revisor-tab" tabindex="0">
                                Revisor
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">
                                Cancelar <i class="bi bi-x-circle"></i>
                            </button>
                            <button type="submit" class="btn btn-outline-primary me-2">
                                Salvar <i class="bi bi-check2-circle"></i>
                            </button>
                            <button @click="tabSwitch.navigate_tab('next')" type="button" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-right-circle"></i>
                            </button>
                            <button @click="tabSwitch.navigate_tab('prev')" type="button" class="btn btn-outline-secondary me-2">
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
    height: 80px;
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
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: calc(50% - 40px);
}

.nav-step-txt i {
    font-size: 1.3rem;
    margin: 0;
    padding: 0;
}

.nav-step-txt span {
    font-size: 0.7rem;
    font-weight: 600;
}

.nav-item .active .nav-step-txt {
    background-color: var(--color-base);
    color: white;
    transition: 400ms;
}

.nav-item .active .nav-line-step {
    background: rgb(202, 201, 201);
    background: linear-gradient(90deg, var(--color-shadow) 0%, var(--color-base) 50%, var(--color-shadow) 100%);
    transition: 400ms;
}

@media (max-width: 755px) {

    .nav-step {
        height: 60px;
    }

    .nav-step-txt {
        width: 60px;
        height: 60px;
        left: calc(50% - 30px);
    }

    .nav-step-txt i {
        font-size: 1.2rem;
    }

    .nav-step-txt span {
        display: none;
    }
}
</style>