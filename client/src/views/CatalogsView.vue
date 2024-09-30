<script setup>
import { createApp, inject, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import http from '@/services/http';
import exp from '@/services/export';

import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import CatalogReport from './reports/CatalogReport.vue';
import TableList from '@/components/table/TableList.vue';

const sysapp = inject('sysapp')
const router = useRouter()
const emit = defineEmits(['callAlert', 'callUpdate'])
const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/catalogs',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'CATÁLOGO' },
        { key: 'comission.name', title: 'ORIGEM', sub: [{ key: 'organ.name' }] },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] }
    ],
    search: {
        sent: false
    },
    rules: {
        name: 'required',
        comission_id: 'required'
    }
})

function export_catalog(id) {
    http.get(`${page.url}/export/${id}`, emit, (resp) => {
        const containerReport = document.createElement('div')
        const instanceReport = createApp(CatalogReport, { qrdata: sysapp, catalog: resp.data, selects: page.selects })
        instanceReport.mount(containerReport)
        exp.exportPDF(containerReport, `CATÁLOGO-${resp.data.name}`)
    })
}

function catalog(id) {
    router.replace({ name: 'catalogitems', params: { id } })
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

onMounted(() => {
    pageData.selects()
    pageData.list()
})

</script>

<template>
    <div class="page">

        <NavMainUi />

        <main class="main">
            <HeaderMainUi />

            <!-- List -->
            <section v-if="!page.ui.register && !page.ui.prepare" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Catálogos</h2>
                        <p>
                            Listagem dos catálogos de itens atrelados ao
                            <span class="txt-color">{{ page.organ_name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="pageData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                placeholder="Pesquise por partes do nome da comissão">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-description" class="form-label">Descrição</label>
                            <input type="text" name="description" class="form-control" id="s-description"
                                v-model="page.search.description" placeholder="Pesquise por partes da descrição">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Comissão</label>
                            <select name="type" class="form-control" id="type" v-model="page.search.comission_id">
                                <option value=""></option>
                                <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">
                                    {{ s.title }}
                                </option>
                            </select>
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
                    <TableList :header="page.header" :body="page.datalist" :actions="[
                        Actions.Create('cube-outline', 'Itens', catalog),
                        Actions.Edit(pageData.update),
                        Actions.Delete(pageData.remove),
                        Actions.Export('document-text-outline', export_catalog),
                    ]" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Catálogo</h2>
                        <p>Registro de um catálogo de items</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row" @submit.prevent="pageData.save">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">
                            <input type="hidden" name="id" v-model="page.id">
                            <div class="col-sm-12 col-md-6">
                                <label for="name" class="form-label">Catálogo</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    :class="{ 'form-control-alert': page.valids.name }"
                                    placeholder="Identificação do Catálogo" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label for="comission" class="form-label">Comissão</label>
                                <select name="comission" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.comission_id }" id="comission"
                                    v-model="page.data.comission_id">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-md-12">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    placeholder="Breve resumo do descrito sobre o catálogo"
                                    v-model="page.data.description">
                            </div>
                        </div>
                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                                <ion-icon name="close-outline" class="fs-5"></ion-icon>
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped></style>