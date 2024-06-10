<script setup>
import { onMounted, ref, watch } from 'vue'
import Ui from '@/utils/ui';
import masks from '@/utils/masks';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import Data from '@/services/data';
import http from '@/services/http';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })

const page = ref({
    baseURL: '/dfds',
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
        categories: []
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
const tabs = ref([
    {id:'origin', icon: 'bi-bounding-box', title: 'Origem', status:true},
    {id:'infos', icon: 'bi-chat-square-dots', title: 'Infos', status:false},
    {id:'items', icon: 'bi-boxes', title: 'Itens', status:false},
    {id:'details', icon: 'bi-justify-left', title: 'Detalhes', status:false},
    {id:'revisor', icon: 'bi-journal-check', title: 'Revisar', status:false}
])
const items = ref({
    search: null,
    search_list: [],
    selected_item: null
})


const ui = new Ui(page, 'DFDs')
const data = new Data(page, emit, ui)

function generate(type) {

    const dfd = page.value.data
    const slc = page.value.selects
    const base = {
        organ: slc?.organs.find(o => o.id === dfd.organ),
        unit: slc?.units.find(o => o.id === dfd.unit),
        type: slc?.acquisitions.find(o => o.id === dfd.acquisition_type),
    }
    let payload = ''

    switch (type) {
        case 'object':
            payload = `Solicitação: gere DESCRIÇÃO DO OBJETO de contrataçao de empresa especializada para fornecimento de ${base.type?.title} para ${dfd?.description} para atender as necessidades da ${base.unit?.title} vinculado a ${base.organ?.title}. `
            break
        case 'jsutify':
            payload = {}
            break
        case 'quantify':
            payload = {}
            break
        default:
            break
    }

    http.post('/dfds/generate', { payload }, emit, (resp) => {
        console.log(resp)
        page.value.data.description = resp.data.choices[0].text.replaceAll('\n', ' ')
    })
}

function search_items() {
    http.post(`${page.value.baseURL}/items`, { name: items.value.search }, emit, (resp) => {
        items.value.search_list = resp.data
    })
}

function select_item(item){
    items.value.selected_item = item
    items.value.search = null
    items.value.search_list = null
}

function activate_tab(tab){
    return tabs.value.find(obj => obj.id === tab)?.status
}

function navigate_tab(flux, target = null){
    let index = 0

    for (let i = 0; i < tabs.value.length; i++) {
        const tab = tabs.value[i];
        if(tab.status){
            index = i
            break;
        }
    }

    tabs.value.forEach((t, i) => {
        if(target !== null){
            t.status = target === t.id
        }else{
            let active_index = flux === 'prev' 
            ? index > 0 ? index - 1 : index
            : index < (tabs.value.length - 1) ? index + 1 : index
            t.status = active_index === i
        }
    });
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
                        <div class="dropdown">
                            <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                                <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-item c-pointer" @click="ui.toggle('register')">
                                    <i class="bi bi-plus-circle me-1"></i> Novo em Branco
                                </li>
                                <li class="dropdown-item c-pointer">
                                    <i class="bi bi-journal-bookmark me-1"></i> Novo a partir de Contrato
                                </li>
                                <li class="dropdown-item c-pointer">
                                    <i class="bi bi-journal-album me-1"></i> Novo a partir de DFD
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
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do setor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control" id="s-organ" v-model="page.search.organ"
                                    @change="data.selects('organ', page.search.organ)">
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
                                @click="navigate_tab(null, tab.id)"
                                :class="{'active':tab.status}" 
                                :id="`${tab.id}-tab`" 
                                :aria-controls="`${tab.div}-tab-pane`" 
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
                            <div class="tab-pane fade" :class="{'show active':activate_tab('origin')}" id="origin-tab-pane" role="tabpanel" aria-labelledby="origin-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="organ" class="form-label">Orgão</label>
                                        <select name="organ" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.organ }" id="organ"
                                            v-model="page.data.organ" @change="data.selects('organ', page.data.organ)">
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
                                            @change="data.selects('unit', page.data.unit)" v-model="page.data.unit">
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
                                            <option v-for="s in page.selects.ordinators" :value="s.id" :key="s.id">{{
                                                s.title }}
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
                                            <option v-for="s in page.selects.demandants" :value="s.id" :key="s.id">{{
                                                s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-8">
                                        <label for="comission" class="form-label">Comissão/Equipe de
                                            Planejamento</label>
                                        <select name="comission" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.comission }"
                                            id="comission" v-model="page.data.comission">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">{{
                                                s.title }}
                                            </option>
                                        </select>
                                        <div id="comissionHelpBlock" class="form-text txt-color-sec">
                                            Ao selecionar a comissão/equipe de planejamento seus integrantes
                                            serão vinculados ao documento
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{'show active':activate_tab('infos')}" id="infos-tab-pane" role="tabpanel" aria-labelledby="infos-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="date_ini" class="form-label">Data Envio Demanda</label>
                                        <input type="text" name="date_ini" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.date_ini }" id="date_ini"
                                            placeholder="DD/MM/AAAA" v-model="page.data.date_ini"
                                            v-maska:[masks.maskdate]>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="year_pca" class="form-label">Ano do PCA</label>
                                        <input type="text" name="year_pca" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.year_pca }" id="year_pca"
                                            placeholder="AAAA" v-maska:[masks.masknumbs] v-model="page.data.year_pca">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="priority" class="form-label">Prioridade</label>
                                        <select name="priority" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.priority }" id="priority"
                                            v-model="page.data.priority">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.prioritys" :value="s.id" :key="s.id">{{
                                                s.title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="acquisition_type" class="form-label">Tipo de
                                            Aquisição</label>
                                        <select name="acquisition_type" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.acquisition_type }"
                                            id="acquisition_type" v-model="page.data.acquisition_type">
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
                                            id="estimated_value" placeholder="R$0,00" v-maska:[masks.maskmoney]
                                            v-model="page.data.estimated_value">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="estimated_date" class="form-label">Data Prevista
                                            Contratação</label>
                                        <input type="text" name="estimated_date" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.estimated_date }"
                                            id="estimated_date" placeholder="DD/MM/AAAA" v-maska:[masks.maskdate]
                                            v-model="page.data.estimated_date">
                                    </div>

                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="description" class="form-label d-flex justify-content-between">
                                            Descrição sucinta do objeto
                                            <a href="#" class="a-ia" @click="generate('object')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <textarea name="description" class="form-control" rows="4"
                                            :class="{ 'form-control-alert': page.rules.valids.description }"
                                            id="description" v-model="page.data.description"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12 col-md-6">
                                        <label for="suggested_hiring" class="form-label">Forma de Contratação
                                            Sugerida</label>
                                        <select name="suggested_hiring" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.suggested_hiring }"
                                            id="suggested_hiring" v-model="page.data.suggested_hiring">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.hirings" :value="s.id" :key="s.id">
                                                {{
                                                    s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label for="bonds" class="form-label">Vinculo ou Dependência</label>
                                        <select name="bonds" class="form-control"
                                            :class="{ 'form-control-alert': page.rules.valids.bonds }" id="bonds"
                                            v-model="page.data.bonds">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.bonds" :value="s.id" :key="s.id">{{
                                                s.title }}
                                            </option>
                                        </select>
                                        <div id="bondsHelpBlock" class="form-text txt-color-sec">
                                            Indicação de vinculação ou dependência com o objeto de outro
                                            documento de formalização de demanda
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{'show active':activate_tab('items')}" id="items-tab-pane" role="tabpanel" aria-labelledby="items-tab" tabindex="0">
                                <div class="row mb-3 position-relative">
                                    <div class="col-sm-12">
                                        <label for="search-item" class="form-label">Localizar Item no Catálogo Padronizado</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="search-item"
                                                placeholder="Pesquise por parte do nome do item"
                                                aria-label="Pesquise por parte do nome do item"
                                                aria-describedby="btn-search-item" v-model="items.search" @keydown.enter.prevent="search_items">
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
                                                    <div class="me-3 item-type">{{ i.type == '1' ? 'M' : 'S' }}</div>
                                                    <div class="item-desc">
                                                        <h3 class="m-0 p-0 small">{{ `${i.code} - ${i.name}` }}</h3>
                                                        <p class="m-0 p-0 small">{{ `Unidade: ${i.und} - Volume:
                                                            ${i.volume} - Categoria: ${page.selects.categories.find(o =>
                                                            o.id === i.category)?.title} ` }}</p>
                                                        <p class="m-0 p-0 small">{{ i.description }}</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="items.selected_item">
                                    <div class="row mb-3 g-3">
                                        <div class="col-sm-12 col-md-4">
                                            <label for="item-program" class="form-label">Programa</label>
                                            <select name="item-program" class="form-control" id="item-program">
                                                <option value=""></option>
                                                <option v-for="p in page.selects.programs" :key="p.id" :value="p.id">{{
                                                    p.title }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="item-dotation" class="form-label">Dotação</label>
                                            <select name="item-dotation" class="form-control" id="item-dotation">
                                                <option value=""></option>
                                                <option v-for="d in page.selects.dotations" :key="d.id" :value="d.id">{{
                                                    d.title }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label for="item-quantity" class="form-label">Quantidade</label>
                                            <input type="text" name="item-quantity" class="form-control" id="item-quantity"
                                                v-maska:[masks.masknumbs]>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{'show active':activate_tab('details')}" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="justification" class="form-label d-flex justify-content-between">
                                            Justificativa da necessidade da contratação
                                            <a href="#" class="a-ia" @click="generate('justify')"><i
                                                    class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <textarea name="justification" class="form-control" rows="4"
                                            :class="{ 'form-control-alert': page.rules.valids.justification }"
                                            id="justification" v-model="page.data.justification"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3 g-3">
                                    <div class="col-sm-12">
                                        <label for="justification_quantity"
                                            class="form-label d-flex justify-content-between">
                                            Justificativa dos quantitativos demandados
                                            <a href="#" class="a-ia"><i class="bi bi-cpu me-1"></i> Gerar com I.A</a>
                                        </label>
                                        <textarea name="justification_quantity" class="form-control" rows="4"
                                            :class="{ 'form-control-alert': page.rules.valids.justification_quantity }"
                                            id="justification_quantity"
                                            v-model="page.data.justification_quantity"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="{'show active':activate_tab('revisor')}" id="revisor-tab-pane" role="tabpanel" aria-labelledby="revisor-tab" tabindex="0">
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
                            <button @click="navigate_tab('next')" type="button" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-right-circle"></i>
                            </button>
                            <button @click="navigate_tab('prev')" type="button" class="btn btn-outline-secondary me-2">
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