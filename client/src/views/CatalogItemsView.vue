<script setup>
import { onBeforeMount, watch } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import defines from '@/utils/defines';
import utils from '@/utils/utils';

import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TableList from '@/components/table/TableList.vue';
import ListCatGov from '@/components/ListCatGov.vue';

const route = useRoute()
const emit  = defineEmits(['callAlert', 'callUpdate'])
const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: `/catalogitems/${route.params.id}`,
    datalist: props.datalist,
    catalog: {},
    header: [
        { key: 'code', title: 'COD.', sub: [{ key: 'origin' }] },
        { key: 'name', title: 'ITEM', sub: [{ key: 'category' }] },
        { key: 'und', title: 'UND.', sub: [{ key: 'volume' }] },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] },
        { key: 'status', title: 'STATUS' }
    ],
    rules: {
        code: 'required',
        name: 'required',
        type: 'required',
        und: 'required',
        category: 'required',
        status: 'required',
        description: 'required'
    }
})

const [groups, groupsData] = Layout.new(emit, {
    url: `/catalogsubcategories/${page.catalog.organ?.id}`,
    data: {},
    header: [{ key: 'name', title: 'GRUPO' }],
    formRules: { name: 'required' },
    modal: 'subgroup',
})

function detailsCatalog() {
    http.get(`${page.url}/catalog`, emit, (response) => {
        page.catalog = response.data
        groups.url = `/catalogsubcategories/${page.catalog?.organ?.id}`
    })
}

function selectItem(data) {
    page.data.origin = 1
    if (data.category.tipo === 'M') {
        let description = ''
        data.item.buscaItemCaracteristica.forEach(element => {
            description += `${element.nomeCaracteristica}: ${element.nomeValorCaracteristica}; `
        });
        page.data.type = 1
        page.data.category = 1
        page.selects.unds = data.units
        page.data.code = data.item.codigoItem
        page.data.name = data.item.nomePdm
        page.data.description = description

    } else {
        const unds = data.units.map(obj => {
            return { "siglaUnidadeFornecimento": obj.siglaUnidadeMedida, "nomeUnidadeFornecimento": obj.nomeUnidadeMedida }
        })
        page.data.type = 2
        page.data.category = 2
        page.selects.unds = unds
        page.data.code = data.item.codigoServico
        page.data.name = data.item.descricaoServicoAcentuado
        page.data.description = data.item.nomeGrupo + '; ' + data.item.descricaoServicoAcentuado
    }
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

watch(() => page.ui.register, (value) => {
    if (value === true && !page.data.code) {
        page.data.origin = 2
        page.data.code = utils.randCode()
    }
})

onBeforeMount(() => {
    detailsCatalog()
    pageData.selects()
    pageData.list()
})

</script>

<template>
    <div class="page">

        <NavMainUi />

        <main class="main">
            <HeaderMainUi />

            <!-- List -->
            <section v-if="!page.ui.register && !page.ui.prepare" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Itens do Catálogo</h2>
                        <p>
                            Listagem dos itens do catálogo
                            <span class="txt-color">{{ page.catalog.name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="pageData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                        <button @click="groupsData.list" data-bs-toggle="modal" :data-bs-target="'#' + groups.modal"
                            class="btn btn-action-secondary">
                            <ion-icon name="grid-outline" class="fs-5"></ion-icon>
                            Grupos
                        </button>
                        <RouterLink to="/catalogs" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </RouterLink>
                    </div>
                </div>

                <!-- Search -->
                <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Item</label>
                            <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                placeholder="Pesquise por partes do nome do item">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-description" class="form-label">Descrição</label>
                            <input type="text" name="description" class="form-control" id="s-description"
                                v-model="page.search.description" placeholder="Pesquise pela descrição do item">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-status" class="form-label">Status</label>
                            <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                                <option value=""></option>
                                <option v-for="s in page.selects.status" :key="s.id" :value="s.id">
                                    {{ s.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-type" class="form-label">Tipo</label>
                            <select name="type" class="form-control" id="s-type" v-model="page.search.type">
                                <option value=""></option>
                                <option v-for="o in page.selects.types" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <label for="s-category" class="form-label">Categoria</label>
                            <select name="category" class="form-control" id="s-category" v-model="page.search.category">
                                <option value=""></option>
                                <option v-for="o in page.selects.categories" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <label for="s-subcategory" class="form-label">Grupo</label>
                            <select name="subcategory" class="form-control" id="s-subcategory"
                                v-model="page.search.subcategory">
                                <option value=""></option>
                                <option v-for="o in page.selects.groups" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="d-flex flex-row-reverse mt-4">
                            <button type="submit" class="btn btn-action-primary">
                                <ion-icon name="filter"></ion-icon>
                                Aplicar Filtro
                            </button>
                        </div>
                    </form>
                </div>

                <div role="list" class="container p-0">
                    <TableList :header="page.header" :body="page.datalist" :actions="[
                        Actions.Edit(pageData.update),
                        Actions.Delete(pageData.remove)
                    ]" :mounts="{
                        category: [Mounts.Cast(page.selects.categories)],
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        origin: [Mounts.Cast(page.selects.origins)]
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Comissão</h2>
                        <p>Registro das comissões do sistema</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container">
                    <div class="content p-4 pt-3 mb-3">
                        <ListCatGov @resp-catgov="selectItem" />
                    </div>
                    <form class="form-row" @submit.prevent="pageData.save">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">
                            <div class="col-sm-12 col-md-4">
                                <label for="organ" class="form-label">Código Item</label>
                                <input type="text" name="code" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.code }" id="code"
                                    v-model="page.data.code">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="name" class="form-label">Item</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="type" class="form-label">Tipo</label>
                                <select name="type" class="form-control" id="type"
                                    :class="{ 'form-control-alert': page.valids.type }" v-model="page.data.type">
                                    <option></option>
                                    <option v-for="t in page.selects.types" :key="t.id" :value="t.id">
                                        {{ t.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="und" class="form-label">Unidade</label>
                                <select name="und" class="form-control" id="und"
                                    :class="{ 'form-control-alert': page.valids.und }" v-model="page.data.und">
                                    <option></option>
                                    <option v-for="u in defines.unds" :key="u.siglaUnidadeFornecimento"
                                        :value="u.siglaUnidadeFornecimento">{{ `${u.siglaUnidadeFornecimento} -
                                        ${u.nomeUnidadeFornecimento}` }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="volume" class="form-label">Volume</label>
                                <input type="text" name="volume" class="form-control" id="volume"
                                    placeholder="Total de Gramas, UNDs, Pacotes..." v-model="page.data.volume">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="category" class="form-label">Categoria</label>
                                <select name="category" class="form-control" id="category"
                                    :class="{ 'form-control-alert': page.valids.category }"
                                    v-model="page.data.category">
                                    <option></option>
                                    <option v-for="c in page.selects.categories" :key="c.id" :value="c.id">
                                        {{ c.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="subcategory" class="form-label">Agrupamento</label>
                                <select name="subcategory" class="form-control" id="subcategory"
                                    :class="{ 'form-control-alert': page.valids.subcategory }"
                                    v-model="page.data.subcategory">
                                    <option></option>
                                    <option v-for="c in page.selects.groups" :key="c.id" :value="c.id">
                                        {{ c.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control" id="status"
                                    :class="{ 'form-control-alert': page.valids.status }" v-model="page.data.status">
                                    <option></option>
                                    <option v-for="s in page.selects.status" :key="s.id" :value="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    placeholder="Características do Item"
                                    :class="{ 'form-control-alert': page.valids.description }"
                                    v-model="page.data.description">
                            </div>

                        </div>
                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                                <ion-icon name="close-outline" class="fs-5"></ion-icon>
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="modal fade" :id="groups.modal" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered mx-auto">
                    <div class="modal-content p-4 content">
                        <div class="modal-body p-0 my-1">
                            <div v-if="!groups.ui.register" role="heading" class="inside-title mb-4 w-100">
                                <div>
                                    <h2>Agrupamentos</h2>
                                    <p>
                                        Grupos de itens pertencentes ao
                                        <span class="txt-color">{{ page.organ_name }}</span>
                                    </p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-action-close">
                                        <ion-icon name="close" class="fs-5"></ion-icon>
                                    </button>
                                </div>
                            </div>
                            <div v-if="groups.ui.register">
                                <div role="heading" class="inside-title mb-4 w-100">
                                    <div>
                                        <h2>Criar Agrupamento</h2>
                                        <p>
                                            Preencha o nome do grupo abaixo
                                        </p>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button @click="groupsData.ui('list')" class="btn btn-action-tertiary">
                                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                                            Voltar
                                        </button>
                                    </div>
                                </div>
                                <form @submit.prevent="groupsData.save({}, () => pageData.selects())"
                                    class="row p-0 g-3 py-1">
                                    <input type="hidden" name="id" v-model="groups.data.id">
                                    <div class="col-sm-12 col-md-12 m-0">
                                        <label for="name" class="form-label">Grupo</label>
                                        <input type="text" name="name" class="form-control"
                                            :class="{ 'form-control-alert': groups.valids.name }"
                                            v-model="groups.data.name">
                                    </div>
                                    <div class="d-flex flex-row-reverse mt-4 gap-2">
                                        <button type="submit" class="btn btn-action-primary">
                                            <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                            Salvar
                                        </button>
                                        <button @click="groupsData.ui('list')" class="btn btn-action-tertiary">
                                            <ion-icon name="close" class="fs-5"></ion-icon>
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div v-if="!groups.ui.register" class="modal-listage">
                                <TableList secondary :header="groups.header" :body="groups.datalist" :actions="[
                                    Actions.Edit((id) => groupsData.update(id, () => { pageData.selects() })),
                                    Actions.FastDelete((id) => groupsData.fastremove(id, () => { pageData.selects() }))
                                ]" />
                            </div>
                        </div>
                        <div v-if="!groups.ui.register" class="modal-footer border-0 p-0">
                            <button @click="groupsData.ui('register')" class="btn btn-action-primary">
                                <ion-icon name="add" class="fs-5"></ion-icon>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped></style>