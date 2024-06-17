<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router';
import http from '@/services/http';
import masks from '@/utils/masks';
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';

const router = useRouter()
const route  = useRoute()
const comissionID = route.params?.id
const comission = ref({})
const emit  = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const page  = ref({
    baseURL: `/comissionsmembers/${comissionID}`,
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'NOME' },
        { key: 'responsibility', cast: 'title', title: 'RESPONSABILIDADE', sub: [{ key: 'status', cast: 'title' }] },
        { key: 'start_term', title: 'INÍCIO PLEITO' },
        { key: 'end_term', title: 'TÉRMINO PLEITO' },
    ],
    search: {},
    selects: {
        responsibilities: [],
        status: []
    },
    rules: {
        fields: {
            name: 'required',
            responsibility: 'required',
            start_term: 'required',
            status: 'required',
        },
        valids: {}
    }
})

const ui   = new Ui(page, 'Membros das Comissões')
const data = new Data(page, emit, ui)

function handleFile(event) {
    const file = event.target.files[0]
    if (file) {
        page.value.data.document = file
    }
}

function backToComission() {
    
    router.replace({ name: 'comissions' })
}

function fetchComission() {
    http.get(`/comissions/details/${route.params.id}?display=1`, emit, (response) => {
        comission.value = response.data
    })
}

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

onMounted(() => {
    fetchComission()
    data.selects()
    data.list()
})

</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-people-fill',
                title: 'Gerenciamento de Membros da Comissões',
                description: 'Registro de Comissões vinculados as Unidades do Orgão'
            }" />

            <div class="box box-main p-0 rounded-4">

                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center px-4 px-md-5 pt-5 mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ page.title.primary }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ page.title.secondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <button @click="backToComission" type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-arrow-left-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Voltar</span>
                        </button>
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

                    <!-- COMISSION BOX -->
                    <div v-if="!page.uiview.register && !page.uiview.search"
                        class="justify-content-between align-items-center px-5 mb-4">
                        <div class="row mb-3 g-3 w-100">
                            <div class="col-sm-12 col-md-4">
                                <strong class="d-flex gap-2"><i
                                        class="bi bi-people txt-color-base"></i>Comissão</strong>
                                <p class="m-0">{{ comission.name }}</p>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <strong class="d-flex gap-2"><i
                                        class="bi bi-building-gear txt-color-base"></i>Órgão</strong>
                                <p class="m-0">{{ comission.organ?.name }}</p>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <strong class="d-flex gap-2"><i
                                        class="bi bi-house-gear txt-color-base"></i>Unidade</strong>
                                <p class="m-0">{{ comission.unit?.name }}</p>
                            </div>
                        </div>
                        <div class="row mb-3 g-3 w-100">
                            <div class="col-sm-12 col-md-4">
                                <strong class="d-flex gap-2"><i class="bi bi-people txt-color-base"></i>Status</strong>
                                <p class="m-0">{{ comission.status }}</p>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <strong class="d-flex gap-2"><i
                                        class="bi bi-house-gear txt-color-base"></i>Tipo</strong>
                                <p class="m-0">{{ comission.type }}</p>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <strong class="d-flex gap-2"><i
                                        class="bi bi-calendar2 txt-color-base"></i>Pleito</strong>
                                <p class="m-0">{{ `${comission.start_term} - ${comission.end_term || 'Presente'}` }}</p>
                            </div>
                        </div>
                    </div>

                    <!--BOX SEARCH-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do setor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-responsibility" class="form-label">Responsabilidade</label>
                                <select name="responsibility" class="form-control" id="s-responsibility"
                                    v-model="page.search.responsibility"
                                    @change="selects('responsibility', page.search.responsibility)">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.responsibilities" :key="o.id" :value="o.id">{{
                                        o.title
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-status" class="form-label">Status</label>
                                <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.status" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>
                    <TableList @action:update="data.update" @action:delete="data.remove"
                        @action:download="(id) => data.download(id, 'Amparo')" :header="page.dataheader"
                        :body="page.datalist" :actions="['download', 'update', 'delete']" :casts="{
                            'status': page.selects.status,
                            'responsibility': page.selects.responsibilities,
                        }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Nome do Membro" v-model="page.data.name">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.status }" id="status"
                                    v-model="page.data.status">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.status" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="start_term" class="form-label">Inicio Pleito</label>
                                <input type="text" name="start_term" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.start_term }" id="start_term"
                                    placeholder="dd/mm/aaaa" v-maska:[masks.maskdate] v-model="page.data.start_term">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Término Pleito</label>
                                <input type="text" name="end_term" class="form-control" id="end_term"
                                    placeholder="dd/mm/aaaa" v-maska:[masks.maskdate] v-model="page.data.end_term">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="responsibility" class="form-label">Responsabilidade</label>
                                <select name="responsibility" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.responsibility }"
                                    id="responsibility" v-model="page.data.responsibility">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.responsibilities" :value="s.id" :key="s.id">{{
                                        s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="document" class="form-label">Anexar Amparo Legal</label>
                                <input @change="handleFile" type="file" name="document" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição das Atividades</label>
                                <textarea name="description" class="form-control" id="description"
                                    v-model="page.data.description"></textarea>
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