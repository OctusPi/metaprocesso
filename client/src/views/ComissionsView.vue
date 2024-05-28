<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import masks from '@/utils/masks';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ManagementNav from '@/components/ManagementNav.vue';
import Ui from '@/utils/ui';
import utils from '@/utils/utils';

const router = useRouter();
const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const page = ref({
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false, prepare: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'type', cast: 'title' }] },
        { obj: 'unit', key: 'name', title: 'VINCULO', sub: [{ obj: 'organ', key: 'name' }] },
        { key: 'status', cast: 'title', title: 'STATUS', sub: [{ key: 'start_term' }, { key: 'end_term' }] },
    ],
    search: {},
    selects: {
        organs: [],
        units: [],
        types: [],
        status: []
    },
    rules: {
        fields: {
            name: 'required',
            organ: 'required',
            unit: 'required',
            type: 'required',
            status: 'required',
            'start_term': 'required'
        },
        valids: {}
    }
})


watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

const ui = new Ui(page, 'Comissões', 'a')

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url = page.value.data?.id ? '/comissions/update' : '/comissions/save'
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list()
    })
}

function update(id) {
    http.get(`/comissions/details/${id}`, emit, (response) => {
        selects('organ', response.data?.unit)
        page.value.data = response.data
        ui.toggle('update')
    })
}

const extinct = ref({
    data: {},
    rules: {
        fields: {
            end_term: 'required',
            description: 'required',
            organ: 'required',
            unit: 'required',
            comission: 'required',
        },
        valids: {}
    }
})

function extinction(id) {
    http.get(`/comissions/details/${id}`, emit, (response) => {
        extinct.value.data.comission = response.data.id
        extinct.value.data.organ = response.data.organ
        extinct.value.data.unit = response.data.unit
        extinct.value.data.end_term = utils.dateNow()
        ui.toggle('prepare')
    })
}

function saveExtinction() {
    const validation = forms.checkform(extinct.value.data, extinct.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    console.log(extinct.value.data)

    http.post('/comissionsends/save', extinct.value.data, emit, () => {
        list()
    })
}

function list() {
    http.post('/comissions/list', page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        ui.toggle('list')
    })
}

function selects(key = null, search = null) {

    const urlselect = (key && search) ? `/comissions/selects/${key}/${search}` : '/comissions/selects'

    http.get(urlselect, emit, (response) => {
        page.value.selects = response.data
    })
}

function handleFile(event) {
    const file = event.target.files[0]
    if (file) page.value.data.document = file
}

function handleExtinctFile(event) {
    const file = event.target.files[0]
    if (file) extinct.value.data.document = file
}

function download(id) {
    http.download(`/comissions/download/${id}`, emit, (response) => {
        if (response.headers['content-type'] !== 'application/pdf') {
            emit('callAlert', notifys.warning('Arquivo Indisponível'))
            return
        }

        const url = URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
        const link = document.createElement('a')
        link.href = url;
        link.download = `Amparo-${id}.pdf`
        document.body.appendChild(link)
        link.click()
        window.URL.revokeObjectURL(url);

    })
}

function members(id) {
    router.replace({ name: 'comissionsmembers', params: { id: id } })
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
                icon: 'bi-people-fill',
                title: 'Gerenciamento de Comissões',
                description: 'Registro de Comissões vinculados as Unidades do Orgão'
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
                <div v-if="!page.uiview.register && !page.uiview.prepare" id="list-box" class="inside-box mb-4">

                    <!--BOX SEARCH-->
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
                    <TableList @action:update="update" @action:extinction="extinction" @action:download="download"
                        @action:members="members" :header="page.dataheader" :body="page.datalist"
                        :actions="['members', 'download', 'update', 'extinction']" :casts="{
                            'organ': page.selects.organs,
                            'unit': page.selects.units,
                            'status': page.selects.status,
                            'type': page.selects.types
                        }" />
                </div>

                <!-- BOX PREPARE -->
                <div v-if="page.uiview.prepare" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="saveExtinction">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Data de Extinção</label>
                                <input type="text" name="end_term" class="form-control" id="end_term"
                                    :class="{ 'form-control-alert': extinct.rules.valids.end_term }"
                                    placeholder="dd/mm/aaaa" v-maska:[masks.maskdate] v-model="extinct.data.end_term">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="document" class="form-label">Anexar Documento de Extinção</label>
                                <input @change="handleExtinctFile" type="file" name="document" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição da Extinção</label>
                                <textarea name="description" class="form-control" id="description"
                                    :class="{ 'form-control-alert': extinct.rules.valids.description }"
                                    v-model="extinct.data.description"></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle()" type="button" class="btn btn-outline-warning">Cancelar <i
                                    class="bi bi-x-circle"></i></button>
                            <button type="submit" class="btn btn-outline-primary mx-2">Extinguir <i
                                    class="bi bi-calendar2-minus"></i></button>
                        </div>
                    </form>
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Nome de Identificação da Comissão" v-model="page.data.name">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.organ }" id="organ"
                                    v-model="page.data.organ" @change="selects('organ', page.data.organ)">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.organs" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="unit" class="form-label">Unidade</label>
                                <select name="unit" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.unit }" id="unit"
                                    v-model="page.data.unit">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.units" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
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
                            <div class="col-sm-12 col-md-4">
                                <label for="document" class="form-label">Anexar Amparo Legal</label>
                                <input @change="handleFile" type="file" name="document" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="type" class="form-label">Tipo de Comissão</label>
                                <select name="type" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.type }" id="type"
                                    v-model="page.data.type">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.types" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
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