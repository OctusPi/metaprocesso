<script setup>
import { onBeforeMount, ref, watch } from 'vue'
import { useRoute } from 'vue-router';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import Ui from '@/utils/ui';
import utils from '@/utils/utils';
import defines from '@/utils/defines';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ListCatGov from '@/components/ListCatGov.vue';

const router = useRoute()
const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const catalogID = router.params?.catalog
const page = ref({
    title: {},
    uiview: {},
    catalog: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'CATÁLOGO' },
        { key: 'comission', title: 'COMISSÃO', sub: [{ key: 'organ' }] },
        { key: 'law', title: 'DESCRIÇÃO', sub: [{ key: 'description' }] }
    ],
    search: {},
    selects: {
        unds: [],
        types: [],
        categories: [],
        subcategories: [],
        status: []
    },
    rules: {
        fields: {
            code: 'requierd',
            name: 'required',
            type: 'required',
            und: 'required',
            category:'required',
            status: 'required',
            description: 'required'
        },
        valids: {}
    }
})

const ui = new Ui(page, 'Itens do Catálogo')

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url = page.value.data?.id ? `/catalogitems/${catalogID}/update` : `/catalogitems/${catalogID}/save`
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list();
    })
}

function update(id) {
    http.get(`/catalogitems/${catalogID}/details/${id}`, emit, (response) => {
        selects('organ', response.data?.unit)
        page.value.data = response.data
        ui.toggle('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: `/catalogs/${catalogID}`,
        search: page.value.search
    })
}

function list() {
    http.post(`/catalogitems/${catalogID}/list`, page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        ui.toggle('list')
    })
}

function selects(key = null, search = null) {

    const urlselect = (key && search) ? `/catalogitems/${catalogID}/selects/${key}/${search}` : `/catalogitems/${catalogID}/selects`

    http.get(urlselect, emit, (response) => {
        page.value.selects = response.data
    })
}

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
    if (value === true) {
        page.value.data.code = utils.randCode()
        page.value.selects.unds = defines.unds
    }
})

onBeforeMount(() => {
    detailsCatalog()
    selects()
    list()
    
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
                        <RouterLink to="/subcategories" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-bookmarks"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Grupos</span>
                        </RouterLink>

                    </div>
                </div>

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
                        <form @submit.prevent="list" class="row g-3">
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
                                <label for="s-comission" class="form-label">Status</label>
                                <select name="comission" class="form-control" id="s-comission"
                                    v-model="page.search.comission">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-type" class="form-label">Tipo</label>
                                <select name="type" class="form-control" id="s-type"
                                    v-model="page.search.type">
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
                                    <option v-for="o in page.selects.subcategories" :key="o.id" :value="o.id">{{ o.title }}
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
                    <TableList @action:update="update" @action:delete="remove"
                        :header="page.dataheader" :body="page.datalist" :actions="['items', 'update', 'delete']"
                        :casts="{ 'organ': page.selects.organs, 'comission': page.selects.comissions }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">

                    <!-- IMPORT ITEM CATMAT/CATSER -->
                    <ListCatGov @resp-catgov="selectItem" />

                    <form class="form-row mt-3" @submit.prevent="save(page.data.id)">
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
                                    <option v-for="(t, i) in page.selects.types" :key="i" :value="i">{{ t }}</option>
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
                                    <option v-for="(c, i) in page.selects.categories" :key="i" :value="i">{{ c }}
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
                                    <option v-for="(s, i) in page.selects.status" :key="i" :value="i">{{ s }}</option>
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
    </main>
</template>

<style scoped>
.inside-box{
    height: calc(100% - 270px);
    overflow: scroll;
}

@media (max-width: 991px) {
    .inside-box{
        height: calc(100% - 315px);
    }
}
</style>