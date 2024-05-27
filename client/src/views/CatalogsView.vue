<script setup>
import { onMounted, ref, watch } from 'vue'
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import Ui from '@/utils/ui';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import CatalogsNav from '@/components/CatalogsNav.vue';
import { useRouter } from 'vue-router';

const router = useRouter()
const emit   = defineEmits(['callAlert', 'callRemove'])
const props  = defineProps({ datalist: { type: Array, default: () => [] } })
const page   = ref({
    title: {},
    uiview: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'CATÁLOGO' },
        { key: 'comission_id', title: 'COMISSÃO', sub: [{ key: 'organ_id' }] },
        { key: 'law', title: 'DESCRIÇÃO', sub: [{ key: 'description' }] }
    ],
    search: {},
    selects: {
        organs: [],
        comissions: []
    },
    rules: {
        fields: {
            name: 'required',
            organ_id: 'required',
            comission_id: 'required'
        },
        valids: {}
    }
})

const ui = new Ui(page, 'Catálogos')

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url = page.value.data?.id ? '/catalogs/update' : '/catalogs/save'
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list();
    })
}

function update(id) {
    http.get(`/catalogs/details/${id}`, emit, (response) => {
        selects('organ_id', response.data?.unit)
        page.value.data = response.data
        ui.toggle('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: '/catalogs',
        search: page.value.search
    })
}

function list() {
    http.post('/catalogs/list', page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        ui.toggle('list')
    })
}

function selects(key = null, search = null) {

    const urlselect = (key && search) ? `/catalogs/selects/${key}/${search}` : '/catalogs/selects'

    http.get(urlselect, emit, (response) => {
        page.value.selects = response.data
    })
}

function selectCatalog(id){
    router.replace({ name: 'catalogitems', params: { id } })
}

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

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
                        <form @submit.prevent="list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Catálogo</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do catálogo">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ_id" class="form-label">Orgão</label>
                                <select name="organ_id" class="form-control" id="s-organ_id"
                                    v-model="page.search.organ_id" @change="selects('organ_id', page.search.organ_id)">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-comission_id" class="form-label">Comissão</label>
                                <select name="comission_id" class="form-control" id="s-comission_id"
                                    v-model="page.search.comission_id">
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
                    <TableList 
                    @action:update="update" 
                    @action:delete="remove" 
                    @action:items="selectCatalog"
                    :header="page.dataheader"
                    :body="page.datalist" :actions="['items', 'update', 'delete']"
                    :casts="{ 'organ_id': page.selects.organs, 'comission_id': page.selects.comissions }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            
                            <div class="col-sm-12 col-md-4">
                                <label for="organ_id" class="form-label">Orgão</label>
                                <select name="organ_id" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.organ_id }" id="organ_id"
                                    v-model="page.data.organ_id" @change="selects('organ_id', page.data.organ_id)">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.organs" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="comission_id" class="form-label">Comissão</label>
                                <select name="comission_id" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.comission_id }" id="comission_id"
                                    v-model="page.data.comission_id">
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
                                    placeholder="Breve resumo do objetivo do programa" v-model="page.data.description">
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