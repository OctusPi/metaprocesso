<script setup>
import { createApp, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import Data from '@/services/data';
import http from '@/services/http';
import Ui from '@/utils/ui';
import exp from '@/services/export';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import CatalogsNav from '@/components/CatalogsNav.vue';
import CatalogReport from '@/views/reports/CatalogReport.vue';

const router = useRouter()
const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const page = ref({
    baseURL: '/catalogs',
    title: {},
    uiview: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { obj: 'comission', key: 'name', title: 'ORIGEM', sub: [{ obj: 'comission', key: 'name' }] },
        { key: 'name', title: 'CATÁLOGO' },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] }
    ],
    search: {},
    selects: {
        organs: [],
        comissions: [],
        items_status: [],
        items_categories: [],
        items_origins: [],
    },
    rules: {
        fields: {
            name: 'required',
            organ: 'required',
            comission: 'required'
        },
        valids: {}
    }
})

const ui = new Ui(page, 'Catálogos')
const data = new Data(page, emit, ui)

function export_catalog(id) {
    http.get(`${page.value.baseURL}/export/${id}`, emit, (resp) => {
        const containerReport = document.createElement('div')
        const instanceReport = createApp(CatalogReport, { catalog: resp.data, selects: page.value.selects })
        instanceReport.mount(containerReport)
        exp.exportPDF(containerReport, `CATÁLOGO-${resp.data.name}`)
    })

}

function selectCatalog(id) {
    router.replace({ name: 'catalogitems', params: { catalog: id } })
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
                icon: 'bi-book-half',
                title: 'Catálogo Bens, Serviços e Fornecedores',
                description: 'Gestão de Catálogo Padronizado de Itens'
            }" />

            <div class="box box-main p-0 rounded-4">

                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center px-4 px-md-5 pt-5 mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ page.title.primary }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ page.title.secondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <CatalogsNav />
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
                        <!-- <button type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-arrow-up"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Exportar</span>
                        </button> -->
                    </div>
                </div>

                <!--BOX LIST-->
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">
                    <!--SEARCH BAR-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Catálogo</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do catálogo">
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
                                <label for="s-comission" class="form-label">Comissão</label>
                                <select name="comission" class="form-control" id="s-comission"
                                    v-model="page.search.comission">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">{{ o.title }}
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
                    <TableList @action:update="data.update" @action:delete="data.remove" @action:items="selectCatalog"
                        @action:pdf="export_catalog" :header="page.dataheader" :body="page.datalist"
                        :actions="['items', 'update', 'delete', 'export_pdf']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.organ }" id="organ"
                                    v-model="page.data.organ" @change="data.selects('organ', page.data.organ)">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.organs" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="comission" class="form-label">Comissão</label>
                                <select name="comission" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.comission }" id="comission"
                                    v-model="page.data.comission">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Catálogo</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="Identificação do Catálogo" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    placeholder="Breve resumo do descrito sobre o catálogo"
                                    v-model="page.data.description">
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