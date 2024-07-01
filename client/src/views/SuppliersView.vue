<script setup>
import { onMounted, ref, watch } from 'vue'
import MainNav from '@/components/MainNav.vue'
import MainHeader from '@/components/MainHeader.vue'
import ListSuppliersGov from '@/components/ListSuppliersGov.vue'
import TableList from '@/components/TableList.vue'
import Ui from '@/utils/ui'
import CatalogsNav from '@/components/CatalogsNav.vue'
import masks from '@/utils/masks'
import Data from '@/services/data'

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const page = ref({
    baseURL: '/suppliers',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'FORNECEDOR', sub: [{ key: 'cnpj', title: 'CNPJ: ' }] },
        { key: 'modality', cast: 'title', title: 'DEFINIÇÃO', sub: [{ key: 'size', cast: 'title' }] },
        { key: 'address', title: 'ENDEREÇO' },
    ],
    selects: {
        modalities: [],
        sizes: [],
    },
    search: {},
    rules: {
        fields: {
            name: 'required',
            address: 'required',
            cnpj: 'required',
        },
        valids: {}
    }
})

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

const ui = new Ui(page, 'Fornecedores')
const data = new Data(page, emit, ui)

function populateWithGovData(govdata) {
    page.value.data.name = null
    page.value.data.cnpj = null
    page.value.data.address = null
    page.value.data.name = govdata.nome
    page.value.data.cnpj = govdata.cnpj
    page.value.data.address = govdata.address
}

onMounted(() => {
    data.list()
    data.selects()
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
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-8">
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
                            <div class="col-sm-12 col-md-4">
                                <label for="modality" class="form-label">Modalidade</label>
                                <select name="modality" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.modality }" id="modality"
                                    v-model="page.data.modality">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.modalities" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="size" class="form-label">Porte</label>
                                <select name="size" class="form-control" id="size" v-model="page.data.size">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.sizes" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>

                    <!--DATA LIST-->
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']" :casts="{
                            'size': page.selects.sizes,
                            'modality': page.selects.modalities,
                        }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <ListSuppliersGov class="mb-3" @resp-select="populateWithGovData" />
                    <form class="form-row" @submit.prevent="data.save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Fornecedor</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Nome do Fornecedor" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="modality" class="form-label">Modalidade</label>
                                <select name="modality" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.modality }" id="modality"
                                    v-model="page.data.modality">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.modalities" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
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
                            <div class="col-sm-12 col-md-4">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="size" class="form-label">Porte</label>
                                <select name="size" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.size }" id="size"
                                    v-model="page.data.size">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.sizes" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
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