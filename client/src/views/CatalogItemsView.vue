<script setup>
import { onBeforeMount, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router';
import http from '@/services/http';
import Ui from '@/utils/ui';
import utils from '@/utils/utils';
import defines from '@/utils/defines';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ListCatGov from '@/components/ListCatGov.vue';
import Data from '@/services/data';

const router = useRoute()
const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const catalogID = router.params?.catalog
const page = ref({
    baseURL: `/catalogitems/${catalogID}`,
    title: {},
    uiview: {},
    catalog: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'code', title: 'COD.', sub: [{ key: 'origin', cast: 'title' }] },
        { key: 'name', title: 'ITEM', sub: [{ key: 'category', cast: 'title' }] },
        { key: 'und', title: 'UND.', sub: [{ key: 'volume' }] },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] },
        { key: 'status', cast: 'title', title: 'STATUS' }
    ],
    search: {},
    selects: {
        unds: [],
        types: [],
        categories: [],
        subcategories: [],
        status: [],
        origins: []
    },
    rules: {
        fields: {
            code: 'requierd',
            name: 'required',
            type: 'required',
            und: 'required',
            category: 'required',
            status: 'required',
            description: 'required'
        },
        valids: {}
    }
})

const ui = new Ui(page, 'Itens do Catálogo')
const data = new Data(page, emit, ui)

function detailsCatalog() {
    http.get(`/catalogitems/${catalogID}/catalog`, emit, (response) => {
        page.value.catalog = response.data
    })
}

function selectItem(data) {
    page.value.data.origin = 1
    if (data.category.tipo === 'M') {
        let description = ''
        data.item.buscaItemCaracteristica.forEach(element => {
            description += `${element.nomeCaracteristica}: ${element.nomeValorCaracteristica}; `
        });
        page.value.data.type = 1
        page.value.data.category = 1
        page.value.selects.unds = data.units
        page.value.data.code = data.item.codigoItem
        page.value.data.name = data.item.nomePdm
        page.value.data.description = description

    } else {

        const unds = data.units.map(obj => {
            return { "siglaUnidadeFornecimento": obj.siglaUnidadeMedida, "nomeUnidadeFornecimento": obj.nomeUnidadeMedida }
        })

        page.value.data.type = 2
        page.value.data.category = 2
        page.value.selects.unds = unds
        page.value.data.code = data.item.codigoServico
        page.value.data.name = data.item.descricaoServicoAcentuado
        page.value.data.description = data.item.nomeGrupo + '; ' + data.item.descricaoServicoAcentuado
    }
}

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

watch(() => page.value.uiview.register, (value) => {
    if (value === true && !page.value.data.code) {
        page.value.data.origin = 2
        page.value.data.code = utils.randCode()
        page.value.selects.unds = defines.unds
    }
})

const modal = ref({
    baseURL: '/catalogsubcategories',
    uiview: {},
    title: {},
    datalist: [],
    data: {},
    dataheader: [
        { key: 'name', title: 'GRUPO' },
    ],
    rules: {
        fields: {
            name: 'required',
            organ: 'required',
        },
        valids: {}
    },
    search: {
        organ: 1
    }
})

const modalUi = new Ui(modal, 'Grupos dos Itens')
const modalData = new Data(modal, emit, modalUi)
const modalElement = ref(null)
const modalId = 'subgroup-modal'

watch(() => modal.value.uiview.register, (value) => {
    if (value === true) {
        modal.value.data.organ = page.value.catalog.organ?.id || 1
    }
})

onBeforeMount(() => {
    detailsCatalog()
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
                        <button ref="modalElement" @click="modalData.list" data-bs-toggle="modal"
                            :data-bs-target="`#${modalId}`" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-bookmarks"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Grupos</span>
                        </button>
                    </div>
                </div>

                <!-- DETAILS CATALOG -->
                <div class="px-4 px-md-5 mb-4">
                    <h2 class="m-0 p-0">Catálogo: {{ page.catalog.name }}</h2>
                    <p class="small m-0 p-0 txt-color-sec">{{ page.catalog.description }}</p>
                    <p class="small m-0 p-0 txt-color-sec">Orgão: {{ page.catalog?.organ?.name }}</p>
                    <p class="small m-0 p-0 txt-color-sec">Comissão: {{ page.catalog?.comission?.name }}</p>
                </div>

                <!--BOX LIST-->
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">
                    <!--SEARCH BAR-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Item</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do item">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="s-description"
                                    v-model="page.search.description" placeholder="Pesquise pela descrição do item">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-status" class="form-label">Status</label>
                                <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.status" :key="s.id" :value="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-type" class="form-label">Tipo</label>
                                <select name="type" class="form-control" id="s-type" v-model="page.search.type">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.types" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <label for="s-category" class="form-label">Categoria</label>
                                <select name="category" class="form-control" id="s-category"
                                    v-model="page.search.category">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.categories" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <label for="s-subcategory" class="form-label">Grupo</label>
                                <select name="subcategory" class="form-control" id="s-subcategory"
                                    v-model="page.search.subcategory">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.subcategories" :key="o.id" :value="o.id">
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
                            'category': page.selects.categories,
                            'status': page.selects.status,
                            'origin': page.selects.origins
                        }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">

                    <!-- IMPORT ITEM CATMAT/CATSER -->
                    <ListCatGov @resp-catgov="selectItem" />

                    <form class="form-row mt-3" @submit.prevent="data.save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">

                            <div class="col-sm-12 col-md-4">
                                <label for="organ" class="form-label">Código Item</label>
                                <input type="text" name="code" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.code }" id="code"
                                    v-model="page.data.code">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="name" class="form-label">Item</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    v-model="page.data.name">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="type" class="form-label">Tipo</label>
                                <select name="type" class="form-control" id="type"
                                    :class="{ 'form-control-alert': page.rules.valids.type }" v-model="page.data.type">
                                    <option></option>
                                    <option v-for="t in page.selects.types" :key="t.id" :value="t.id">{{ t.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="und" class="form-label">Unidade</label>
                                <select name="und" class="form-control" id="und"
                                    :class="{ 'form-control-alert': page.rules.valids.und }" v-model="page.data.und">
                                    <option></option>
                                    <option v-for="u in page.selects.unds" :key="u.siglaUnidadeFornecimento"
                                        :value="u.siglaUnidadeFornecimento">{{ `${u.siglaUnidadeFornecimento} -
                                        ${u.nomeUnidadeFornecimento}` }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="volume" class="form-label">Volume</label>
                                <input type="text" name="volume" class="form-control" id="volume"
                                    placeholder="Total de Gramas, UNDs, Pacotes..." v-model="page.data.volume">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="category" class="form-label">Categoria</label>
                                <select name="category" class="form-control" id="category"
                                    :class="{ 'form-control-alert': page.rules.valids.category }"
                                    v-model="page.data.category">
                                    <option></option>
                                    <option v-for="c in page.selects.categories" :key="c.id" :value="c.id">{{ c.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="subcategory" class="form-label">Agrupamento</label>
                                <select name="subcategory" class="form-control" id="subcategory"
                                    v-model="page.data.subcategory">
                                    <option></option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control" id="status"
                                    :class="{ 'form-control-alert': page.rules.valids.status }"
                                    v-model="page.data.status">
                                    <option></option>
                                    <option v-for="s in page.selects.status" :key="s.id" :value="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    placeholder="Características do Item"
                                    :class="{ 'form-control-alert': page.rules.valids.description }"
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

        <!--MODAL SUBCATEGORIES-->
        <div class="modal fade" :id="modalId" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content box">
                    <div class="modal-header border border-0">
                        <h1 class="modal-title">
                            <i class="bi bi-bookmarks me-2"></i>
                            Grupos
                        </h1>
                        <button type="button" class="txt-color ms-auto" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="text-center mb-3">
                            <h2 class="txt-color p-0 m-0">{{ modal.title.primary }}</h2>
                            <p class="small txt-color-sec p-0 m-0">{{ modal.title.secondary }}</p>
                        </div>
                        <div v-if="modal.uiview.register">
                            <form @submit.prevent="modalData.save(modal.data.id)" class="row g-3 px-5 py-2">
                                <div class="col-sm-12 col-md-12 m-0">
                                    <label for="name" class="form-label">Grupo</label>
                                    <input type="text" name="name" class="form-control"
                                        :class="{ 'form-control-alert': modal.rules.valids.name }"
                                        v-model="modal.data.name">
                                </div>
                                <div class="d-flex flex-row-reverse mt-4">
                                    <button type="submit" class="btn btn-outline-primary">Salvar <i
                                            class="bi bi-check2-circle"></i></button>
                                    <button type="button" @click="modalUi.toggle('list')"
                                        class="btn btn-outline-warning mx-2">Cancelar
                                        <i class="bi bi-check2-circle"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-listage">
                            <TableList @action:update="modalData.update"
                                @action:delete="(id) => modalData.remove(id, () => modalElement.click())"
                                :header="modal.dataheader" :body="modal.datalist" :actions="['update', 'delete']" />
                        </div>
                    </div>
                    <div v-if="!modal.uiview.register" class="modal-footer border-0">
                        <button @click="modalUi.toggle('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
.inside-box {
    height: calc(100% - 270px);
    overflow: scroll;
}

@media (max-width: 991px) {
    .inside-box {
        height: calc(100% - 315px);
    }
}

.modal-dialog {
    max-width: 900px;
    width: 100%;
}

.modal-listage {
    max-height: 400px;
    overflow: scroll;
}
</style>