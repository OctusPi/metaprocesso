<script setup>
import { onMounted, ref, watch } from 'vue'
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ManagementNav from '@/components/ManagementNav.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })

const page = ref({
    baseURL: '/sectors',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'IDENTIFICAÇÃO' },
        { obj: 'unit', key: 'name', title: 'VINCULO', sub: [{ obj: 'organ', key: 'name' }] },
        { key: 'description', title: 'DESCRIÇÃO' },
    ],
    search: {},
    selects: {
        organs: [],
        units: []
    },
    rules: {
        fields: {
            name: 'required',
            organ: 'required',
            unit: 'required'
        },
        valids: {}
    }
})

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

const ui = new Ui(page, 'Setores')
const data = new Data(page, emit, ui)

onMounted(() => {
    data.selects()
    data.list()
})
</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-grid',
                title: 'Gerenciamento de Setor',
                description: 'Registro de setores vinculados as Unidades do Orgão'
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
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Localizar</span>
                        </button>
                    </div>
                </div>

                <!--BOX LIST-->
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">

                    <!--SEARCH BAR-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do setor">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control" id="s-organ" v-model="page.search.organ"
                                    @change="data.selects('organ', page.search.organ)">
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
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save(page.data.id)">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Nome de identificação do Setor" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="organ" class="form-label">Orgão</label>
                                <select name="organ" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.organ }" id="organ"
                                    v-model="page.data.organ" @change="data.selects('organ', page.data.organ)">
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
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    placeholder="Tipo de Setor: Escola Municipal, UBS, Setor Administrativo"
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