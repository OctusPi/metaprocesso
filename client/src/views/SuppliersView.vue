<script setup>
import { onMounted, ref, watch } from 'vue'
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import http from '@/services/http';

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import ListSuppliersGov from '@/components/ListSuppliersGov.vue';
import TableList from '@/components/TableList.vue';
import Ui from '@/utils/ui';
import CatalogsNav from '@/components/CatalogsNav.vue';
import masks from '@/utils/masks';

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
        { key: 'name', title: 'FORNECEDOR', sub: [{ key: 'cnpj' }] },
        { key: 'phone', title: 'CONTATO', sub: [{ key: 'email', title: 'Email: ' }] },
        { key: 'address', title: 'ENDEREÇO' },
    ],
    search: {},
    rules: {
        fields: {
            name: 'required',
            address: 'required',
            cnpj: 'required',
            email: 'required',
            phone: 'required',
        },
        valids: {}
    }
})

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

const ui = new Ui(page, 'Fornecedores')

function save() {
    const validation = forms.checkform(page.value.data, page.value.rules);
    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    const data = { ...page.value.data }
    const url = page.value.data?.id ? '/suppliers/update' : '/suppliers/save'
    const exec = page.value.data?.id ? http.put : http.post

    exec(url, data, emit, () => {
        list();
    })
}

function update(id) {
    http.get(`/suppliers/details/${id}`, emit, (response) => {
        page.value.data = response.data
        ui.toggle('update')
    })
}

function remove(id) {
    emit('callRemove', {
        id: id,
        url: '/suppliers',
        search: page.value.search
    })
}

function list() {
    http.post('/suppliers/list', page.value.search, emit, (response) => {
        page.value.datalist = response.data ?? []
        ui.toggle('list')
    })
}

onMounted(() => {
    list()
})

</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-person-vcard',
                title: 'Gerenciamento de Fornecedores',
                description: 'Registro de Fornecedores de Serviços'
            }" />

            <div class="box box-main p-0 rounded-4">
                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center w-100 px-4 px-md-5 pt-5 mb-4">
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
                                <label for="s-name" class="form-label">Fornecedor</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do Fornecedor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" class="form-control" id="s-cnpj"
                                    v-model="page.search.cnpj" placeholder="Pesquise por partes do CNPJ do Fornecedor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control" id="s-address"
                                    v-model="page.search.address"
                                    placeholder="Pesquise por partes do endereço do Fornecedor">
                            </div>

                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>

                    <!--DATA LIST-->
                    <TableList @action:update="update" @action:delete="remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <ListSuppliersGov />
                        </div>
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-8">
                                <label for="name" class="form-label">Fornecedor</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Nome do Fornecedor" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" class="form-control" v-maska:[masks.maskcnpj]
                                    :class="{ 'form-control-alert': page.rules.valids.cnpj }" id="cnpj"
                                    placeholder="XX.XXX.XXX/XXXX-XX" v-model="page.data.cnpj">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="agent" class="form-label">Agente</label>
                                <input type="text" name="agent" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.agent }" id="agent"
                                    placeholder="Nome do Agente" v-model="page.data.agent">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" name="cpf" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.cpf }" id="cpf"
                                    v-maska:[masks.maskcpf] placeholder="XXX.XXX.XXX-XX" v-model="page.data.cpf">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" name="phone" class="form-control" v-maska:[masks.maskphone]
                                    :class="{ 'form-control-alert': page.rules.valids.phone }" id="phone"
                                    placeholder="(00) 90000-0000" v-model="page.data.phone">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-8">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.email }" id="email"
                                    placeholder="Email de contato do Fornecedor" v-model="page.data.email">
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