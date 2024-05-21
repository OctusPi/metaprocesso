<script setup>
import { onMounted, ref, watch } from 'vue'
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import masks from '@/utils/masks';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ManagementNav from '@/components/ManagementNav.vue';

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
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cpf' }]},
        { key: 'unit_id', title: 'VINCULO', sub: [{ key: 'organ_id' }] },
        { key: 'status', title: 'STATUS' , sub: [{ key: 'start_term' }, { key: 'end_term' }] },
    ],
    search: {},
    selects: {
        organs:  [],
        units:   [],
        sectors: [],
        status:  []
    },
    rules: {
        fields: {
            name: 'required',
            cpf: 'required',
            organ_id: 'required',
            unit_id: 'required',
            status: 'required',
            'start_term': 'required'
        },
        valids: {}
    }
})

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

function toggleUI(mode = null) {

    switch (mode) {
        case 'register':
            page.value.uiview.search = false
            page.value.uiview.register = !page.value.uiview.register
            page.value.data = {}
            break;
        case 'update':
            page.value.uiview.search = false
            page.value.uiview.register = !page.value.uiview.register
            break;
        case 'search':
            page.value.uiview.search = !page.value.uiview.search
            page.value.uiview.register = false
            break;
        default:
            page.value.uiview.register = false
            break;
    }

    if (page.value.uiview.register) {
        page.value.title.primary = 'Registro de Demandantes'
        page.value.title.secondary = 'Insira os dados de Demandantes vinculados as Unidades do Orgão'
    } else {
        page.value.title.primary = 'Lista de Demandantes'
        page.value.title.secondary = 'Dados dos demandantes inseridos no sistema'
    }
}

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url  = page.value.data?.id ? '/demandants/update' : '/demandants/save'
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list();
    })
}

function update(id) {
    http.get(`/demandants/details/${id}`, emit, (response) => {
        selects('organ_id', response.data?.unit)
        page.value.data = response.data
        toggleUI('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: '/demandants',
        search: page.value.search
    })
}

function list() {
    http.post('/demandants/list', page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        toggleUI('list')
    })
}

function selects(key = null, search = null) {

    const urlselect = (key && search) ? `/demandants/selects/${key}/${search}` : '/demandants/selects'

    http.get(urlselect, emit, (response) => {
        page.value.selects = response.data
    })
}

function handleFile(event){
    const file = event.target.files[0]
    if(file){
        page.value.data.document = file
    }
}

function download(id){
    http.download(`/demandants/download/${id}`, emit, (response) => {
        if(response.headers['content-type'] !== 'application/pdf'){
            emit('callAlert', notifys.warning('Arquivo Indisponível'))
            return
        }
        
        const url  = URL.createObjectURL(new Blob([response.data], {type: 'application/pdf'}))
        const link = document.createElement('a')
        link.href = url;
        link.download = `Amparo-${id}.pdf`
        document.body.appendChild(link)
        link.click()
        window.URL.revokeObjectURL(url);
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
                icon: 'bi-person-video2',
                title: 'Gerenciamento de Demandantes',
                description: 'Registro de Demandantes vinculados as Unidades do Orgão'
            }" />

            <div class="box box-main p-0 rounded-4">
                
                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center w-100 px-4 px-md-5 pt-5 mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ page.title.primary }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ page.title.secondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <ManagementNav />
                        <button @click="toggleUI('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                        <button @click="toggleUI('search')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-search"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Pesquisar</span>
                        </button>
                    </div>
                </div>

                <!--BOX LIST-->
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">
                    
                    <!-- SEARCH BAR -->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                    placeholder="Pesquise por partes do nome do setor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ_id" class="form-label">Orgão</label>
                                <select name="organ_id" class="form-control" id="s-organ_id" v-model="page.search.organ_id"
                                    @change="selects('organ_id', page.search.organ_id)">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">{{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-unit_id" class="form-label">Unidade</label>
                                <select name="unit_id" class="form-control" id="s-unit_id" v-model="page.search.unit_id">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.units" :key="o.id" :value="o.id">{{ o.title }}</option>
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
                        @action:download="download"
                        :header="page.dataheader"
                        :body="page.datalist" :actions="['download', 'update', 'delete']"
                        :casts="{ 'organ_id': page.selects.organs, 'unit_id': page.selects.units, 'status':page.selects.status}" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Nome do Ordenador" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" name="cpf" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.cpf }" id="cpf"
                                    placeholder="000.000.000-00" v-maska:[masks.maskcpf] v-model="page.data.cpf">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="registration" class="form-label">Matrícula</label>
                                <input type="text" name="registration" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.registration }" id="registration"
                                    placeholder="Número da Matrícula Servidor" v-model="page.data.registration">
                            </div>
                        </div>

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
                            <div class="col-sm-12 col-md-4">
                                <label for="unit_id" class="form-label">Unidade</label>
                                <select name="unit_id" class="form-control"
                                    @change="selects('unit_id', page.data.unit_id)"
                                    :class="{ 'form-control-alert': page.rules.valids.unit_id }" id="unit_id"
                                    v-model="page.data.unit_id">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.units" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="sector_id" class="form-label">Setor</label>
                                <select name="sector_id" class="form-control" id="sector_id"
                                    v-model="page.data.sector_id">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.sectors" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="start_term" class="form-label">Inicio Cargo</label>
                                <input type="text" name="start_term" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.start_term }" id="start_term"
                                    placeholder="dd/mm/aaaa" v-maska:[masks.maskdate] v-model="page.data.start_term">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Término Cargo</label>
                                <input type="text" name="end_term" class="form-control" id="end_term"
                                    placeholder="dd/mm/aaaa" v-maska:[masks.maskdate] v-model="page.data.end_term">
                            </div>
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
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="document" class="form-label">Anexar Amparo Legal</label>
                                <input @change="handleFile" type="file" name="document" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="toggleUI('list')" type="button" class="btn btn-outline-warning">Cancelar <i
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