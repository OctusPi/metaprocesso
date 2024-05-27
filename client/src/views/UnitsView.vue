<script setup>
import { onMounted, ref, watch } from 'vue'
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';
import masks from '@/utils/masks'

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ManagementNav from '@/components/ManagementNav.vue';
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
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cnpj' }] },
        { key: 'phone', title: 'CONTATO', sub: [{ key: 'email' }] },
        { key: 'organ_id', title: 'VINCULO', sub: [{ key: 'address' }] }
    ],
    search: {},
    selects: {
        organs: [],
        status: []
    },
    rules: {
        fields: {
            organ_id: 'required',
            name: 'required',
            cnpj: 'required',
            phone: 'required',
            email: 'required|email',
            address: 'required',
        },
        valids: {}
    }
})

const ui = new Ui(page, 'Unidades', 'a')

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url = page.value.data?.id ? '/units/update' : '/units/save'
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list();
    })
}

function update(id) {
    http.get(`/units/details/${id}`, emit, (response) => {
        page.value.data = response.data
        ui.toggle('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: '/units',
        search: page.value.search
    })
}

function list() {
    http.post('/units/list', page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        ui.toggle('list')
    })
}

function selects() {
    http.get('/units/selects', emit, (response) => {
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
                icon: 'bi-house-gear',
                title: 'Gerenciamento de Unidades',
                description: 'Registro de secretarias, departamentos e repartições do Orgão'
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

                    <!-- SEARCH BAR -->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome da unidade">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-cnpj" class="form-label">CNPJ</label>
                                <input type="cnpj" name="cnpj" class="form-control" id="s-cnpj"
                                    v-model="page.search.cnpj" placeholder="000.000.00/0000-00"
                                    v-maska:[masks.maskcnpj]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ_id" class="form-label">Orgão</label>
                                <select name="organ_id" class="form-control" id="s-organ_id"
                                    v-model="page.search.organ_id">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">{{ o.title }}
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
                        :body="page.datalist" :actions="['update', 'delete']"
                        :casts="{ 'status': page.selects.status, 'organ_id': page.selects.organs }" />
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
                                    placeholder="Nome Secretaria, Departamento, Unidade" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.cnpj }" id="cnpj"
                                    placeholder="00.000.000/0000-00" v-model="page.data.cnpj" v-maska:[masks.maskcnpj]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="organ_id" class="form-label">Orgão</label>
                                <select name="organ_id" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.organ_id }" id="organ_id"
                                    v-model="page.data.organ_id">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.organs" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" name="phone" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.phone }" id="phone"
                                    placeholder="(00)9.0000-0000" v-model="page.data.phone" v-maska:[masks.maskphone]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.email }" id="email"
                                    placeholder="unidade@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
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