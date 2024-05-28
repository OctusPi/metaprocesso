<script setup>
import { onMounted, ref, watch } from 'vue'

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import ManagementNav from '@/components/ManagementNav.vue';
import TableList from '@/components/TableList.vue';
import InputMultSelect from '@/components/inputs/InputMultSelect.vue';
import Ui from '@/utils/ui';

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
        { key: 'name', title: 'NOME' },
        { key: 'email', title: 'E-MAIL' },
        { key: 'profile', cast:'title', title: 'PERFIL', sub: [{ key: 'passchange', cast:'title', title: 'Sitiação Senha:' }] },
        { key: 'status', cast:'title', title: 'STATUS', sub: [{ key: 'lastlogin', title: 'Ultimo Acesso:' }] }
    ],
    search: {},
    selects: {
        status: [],
        profiles: [],
        organs: [],
        units: [],
        sectors: [],
        modules: []
    },
    rules: {
        fields: {
            name: 'required',
            email: 'required|email',
            status: 'required',
            profile: 'required',
            modules: 'required'
        },
        valids: {}
    }
})

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

const ui = new Ui(page, 'Usuários')

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const dataform = { ...page.value.data }
    dataform.modules = JSON.stringify((page.value.data.modules).map(i => page.value.selects.modules[i]))
    dataform.organs = JSON.stringify(page.value.data.organs)
    dataform.units = JSON.stringify(page.value.data.units)
    dataform.sectors = JSON.stringify(page.value.data.sectors)

    if (page.value.data?.id) {
        http.put('/management/update', dataform, emit, () => {
            list()
        })

        return
    }

    http.post('/management/save', dataform, emit, () => {
        list()
    })

}

function update(id) {
    http.get(`/management/details/${id}`, emit, (response) => {
        page.value.data = response.data
        page.value.data.modules = (response.data?.modules ?? {}).map(obj => obj['id'])
        ui.toggle('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: '/management',
        search: page.value.search
    })
}

function list() {
    http.post('/management/list', page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        ui.toggle('list')
    })
}

function selects() {
    http.get('/management/selects', emit, (response) => {
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
                icon: 'bi-person-bounding-box',
                title: 'Gestão e Controle Organizacional',
                description: 'Estrutura e Perminissionamento de Usuários'
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
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">

                    <!--SEARCH BAR-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do usuário">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" id="s-email"
                                    v-model="page.search.email" placeholder="user@mail.com">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-profile" class="form-label">Perfil</label>
                                <select name="profile" class="form-control" id="s-profile"
                                    v-model="page.search.profile">
                                    <option></option>
                                    <option v-for="s in page.selects.profiles" :value="s.id" :key="s.id">{{ s.title }}
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
                        :body="page.datalist" :actions="['update', 'delete']" :casts="{
                            'profile': page.selects.profiles,
                            'status': page.selects.status,
                            'passchange': [{ id: 0, title: 'Ativa' }, { id: 1, title: 'Mudança de Senha' }]
                        }" />
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
                                    placeholder="Sr. Snake" v-model="page.data.name">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.email }" id="email"
                                    placeholder="user@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="profile" class="form-label">Perfil</label>
                                <select name="profile" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.profile }" id="profile"
                                    v-model="page.data.profile">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.profiles" :value="s.id" :key="s.id">{{ s.title }}
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
                            <div class="col-sm-12">
                                <label for="modules" class="form-label">Modulos</label>
                                <InputMultSelect v-model="page.data.modules" :options="page.selects.modules"
                                    identify="modules" />
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="organs" class="form-label">Orgãos</label>
                                <InputMultSelect v-model="page.data.organs" :options="page.selects.organs"
                                    identify="organs" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="units" class="form-label">Unidades</label>
                                <InputMultSelect v-model="page.data.units" :options="page.selects.units"
                                    identify="units" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="sectors" class="form-label">Setores</label>
                                <InputMultSelect v-model="page.data.sectors" :options="page.selects.sectors"
                                    identify="sectors" />
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