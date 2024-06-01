<script setup>
import { onMounted, ref, watch } from 'vue'
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import Ui from '@/utils/ui';
import masks from '@/utils/masks';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const page = ref({
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'IDENTIFICAÇÃO' },
        { obj: 'unit', key: 'name', title: 'VINCULO', sub: [{ obj: 'organ', key: 'name' }] },
        { key: 'description', title: 'DESCRIÇÃO' },
    ],
    search: {},
    selects: {
        organs: [],
        units: []
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

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

const ui = new Ui(page, 'DFDs')

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url = page.value.data?.id ? '/dfds/update' : '/dfds/save'
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list();
    })
}

function update(id) {
    http.get(`/dfds/details/${id}`, emit, (response) => {
        selects('organ', response.data?.unit)
        page.value.data = response.data
        ui.toggle('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: '/dfds',
        search: page.value.search
    })
}

function list() {
    // http.post('/dfds/list', page.value.search, emit, (response) => {
    //     page.value.datalist = response.data ?? []
    //     ui.toggle('list')
    // })
    ui.toggle('list')
}

function selects(key = null, search = null) {

    const urlselect = (key && search) ? `/dfds/selects/${key}/${search}` : '/dfds/selects'

    http.get(urlselect, emit, (response) => {
        page.value.selects = response.data
    })
}

onMounted(() => {
    selects()
    list()
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
                        <div class="dropdown">
                            <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                                <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-item c-pointer" @click="ui.toggle('register')">
                                    <i class="bi bi-plus-circle me-1" ></i> Novo em branco
                                </li>
                                <li class="dropdown-item c-pointer">
                                    <i class="bi bi-journal-bookmark me-1" ></i> Novo a partir de contrato
                                </li>
                                <li class="dropdown-item c-pointer">
                                    <i class="bi bi-journal-album me-1" ></i> Novo a partir de DFD anterior
                                </li>
                            </ul>
                        </div>
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
                        <form @submit.prevent="list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do setor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control" id="s-organ" v-model="page.search.organ"
                                    @change="selects('organ', page.search.organ)">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-unit" class="form-label">Unidade</label>
                                <select name="unit" class="form-control" id="s-unit" v-model="page.search.unit">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.units" :key="o.id" :value="o.id">{{ o.title }}
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
                    <TableList @action:update="update" @action:delete="remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">

                        <div class="accordion" id="accordionDfd">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="bi bi-bounding-box me-2"></i> Origem
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionDfd">
                                    <div class="accordion-body">
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12 col-md-4">
                                                <label for="organ" class="form-label">Orgão</label>
                                                <select name="organ" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.organ }"
                                                    id="organ" v-model="page.data.organ"
                                                    @change="selects('organ', page.data.organ)">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.organs" :value="s.id" :key="s.id">
                                                        {{ s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <label for="unit" class="form-label">Unidade</label>
                                                <select name="unit" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.unit }" id="unit"
                                                    @change="selects('unit', page.data.unit)"
                                                    v-model="page.data.unit">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.units" :value="s.id" :key="s.id">{{
                                                        s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <label for="ordinator" class="form-label">Ordenador</label>
                                                <select name="ordinator" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.ordinator }"
                                                    id="ordinator" v-model="page.data.ordinator">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.ordinators" :value="s.id"
                                                        :key="s.id">{{ s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12 col-md-4">
                                                <label for="demandant" class="form-label">Demandante</label>
                                                <select name="demandant" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.demandant }"
                                                    id="demandant" v-model="page.data.demandant">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.demandants" :value="s.id"
                                                        :key="s.id">{{ s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-8">
                                                <label for="comission" class="form-label">Comissão/Equipe de Planejamento</label>
                                                <select name="comission" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.comission }"
                                                    id="comission" v-model="page.data.comission">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.comissions" :value="s.id"
                                                        :key="s.id">{{ s.title }}
                                                    </option>
                                                </select>
                                                <div id="comissionHelpBlock" class="form-text txt-color-sec">
                                                    Ao selecionar a comissão/equipe de planejamento seus integrantes serão vinculados ao documento
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="bi bi-chat-square-dots me-2"></i> Informações
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDfd">
                                    <div class="accordion-body">
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12 col-md-4">
                                                <label for="date_ini" class="form-label">Data Envio Demanda</label>
                                                <input type="text" name="date_ini" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.date_ini }"
                                                    id="date_ini" placeholder="DD/MM/AAAA" v-model="page.data.date_ini"
                                                    v-maska:[masks.maskdate]>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <label for="year_pca" class="form-label">Ano do PCA</label>
                                                <input type="text" name="year_pca" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.year_pca }"
                                                    id="year_pca" placeholder="AAAA" v-maska:[masks.masknumbs]
                                                    v-model="page.data.year_pca">
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <label for="priority" class="form-label">Prioridade</label>
                                                <select name="priority" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.priority }"
                                                    id="priority" v-model="page.data.priority">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.prioritys" :value="s.id"
                                                        :key="s.id">{{ s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12 col-md-4">
                                                <label for="acquisition_type" class="form-label">Tipo de Aquisição</label>
                                                <select name="acquisition_type" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.acquisition_type }" id="acquisition_type"
                                                    v-model="page.data.acquisition_type">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.acquisitions" :value="s.id" :key="s.id">{{
                                                        s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <label for="estimated_value" class="form-label">Valor Estimado</label>
                                                <input type="text" name="estimated_value" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.estimated_value }"
                                                    id="estimated_value" placeholder="R$0,00"
                                                    v-maska:[masks.maskmoney] v-model="page.data.estimated_value">
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <label for="estimated_date" class="form-label">Data Prevista Contratação</label>
                                                <input type="text" name="estimated_date" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.estimated_date }"
                                                    id="estimated_date" placeholder="DD/MM/AAAA"
                                                    v-maska:[masks.maskdate] v-model="page.data.estimated_date">
                                            </div>
                                            
                                        </div>
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12">
                                                <label for="description" class="form-label d-flex justify-content-between">
                                                    Descrição sucinta do objeto
                                                    <a href="#" class="a-ia"><i class="bi bi-cpu me-1"></i> Gerar Automáticamente</a>
                                                </label>
                                                <textarea name="description" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.description }"
                                                    id="description" v-model="page.data.description"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12 col-md-6">
                                                <label for="suggested_hiring" class="form-label">Forma de Contratação Sugerida</label>
                                                <select name="suggested_hiring" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.suggested_hiring }"
                                                    id="suggested_hiring" v-model="page.data.suggested_hiring">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.hirings" :value="s.id" :key="s.id">{{
                                                        s.title }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <label for="bonds" class="form-label">Vinculo ou Dependência</label>
                                                <select name="bonds" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.bonds }"
                                                    id="bonds" v-model="page.data.bonds">
                                                    <option value=""></option>
                                                    <option v-for="s in page.selects.bonds" :value="s.id" :key="s.id">{{
                                                        s.title }}
                                                    </option>
                                                </select>
                                                <div id="bondsHelpBlock" class="form-text txt-color-sec">
                                                    Indicação de vinculação ou dependência com o objeto de outro documento de formalização de demanda
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        <i class="bi bi-boxes me-2"></i> Lista de Itens
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDfd">
                                    <div class="accordion-body">
                                        ...
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFor" aria-expanded="false" aria-controls="collapseFor">
                                        <i class="bi bi-file-earmark-text me-2"></i> Justificativas
                                    </button>
                                </h2>
                                <div id="collapseFor" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDfd">
                                    <div class="accordion-body">
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12">
                                                <label for="justification" class="form-label d-flex justify-content-between">
                                                    Justificativa da necessidade da contratação
                                                    <a href="#" class="a-ia"><i class="bi bi-cpu me-1"></i> Gerar Automáticamente</a>
                                                </label>
                                                <textarea name="justification" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.justification }"
                                                    id="justification" v-model="page.data.justification"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3 g-3">
                                            <div class="col-sm-12">
                                                <label for="justification_quantity" class="form-label d-flex justify-content-between">
                                                    Justificativa dos quantitativos demandados
                                                    <a href="#" class="a-ia"><i class="bi bi-cpu me-1"></i> Gerar Automáticamente</a>
                                                </label>
                                                <textarea name="justification_quantity" class="form-control"
                                                    :class="{ 'form-control-alert': page.rules.valids.justification_quantity }"
                                                    id="justification_quantity"
                                                    v-model="page.data.justification_quantity"></textarea>
                                            </div>
                                        </div>
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
    </main>
</template>