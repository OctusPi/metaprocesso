<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import organ from '@/stores/organ';
import { onMounted, watch } from 'vue';
import Mounts from '@/services/mounts';
import { useRouter } from 'vue-router';
import http from '@/services/http';
import utils from '@/utils/utils';
import masks from '@/utils/masks';
import FileInput from '@/components/inputs/FileInput.vue';

const router = useRouter();

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/comissions',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'type' }] },
        { key: 'unit.name', title: 'VINCULO' },
        { key: 'status', title: 'STATUS', sub: [{ key: 'start_term' }, { key: 'end_term', err: 'Presente' }] },
    ],
    rules: {
        name: 'required',
        unit: 'required',
        type: 'required',
        status: 'required',
        start_term: 'required'
    }
})

const [extinct, extinctData] = Layout.new(emit, {
    url: '/endedcomissions',
    rules: {
        end_term: 'required',
        description: 'required',
        organ: 'required',
        unit: 'required',
        comission: 'required',
    },
})

function extinction(id) {
    http.get(`/comissions/details/${id}`, emit, (response) => {
        extinct.data.comission = response.data.id
        extinct.data.organ = response.data.organ
        extinct.data.unit = response.data.unit
        extinct.data.end_term = utils.dateNow()
        pageData.ui('prepare')
    })
}

function members(id) {
    router.replace({ name: 'comissionmembers', params: { id } })
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

onMounted(() => {
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
                        <h2>Comissões</h2>
                        <p>
                            Listagem das comissões atreladas ao
                            <span class="txt-color">{{ organ.get_organ()?.name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button @click="pageData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="pageData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                    </div>
                </div>
                <!-- Search -->
                <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                placeholder="Pesquise por partes do nome da comissão">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Tipo</label>
                            <select name="type" class="form-control" id="type" v-model="page.search.type">
                                <option value=""></option>
                                <option v-for="s in page.selects.types" :value="s.id" :key="s.id">
                                    {{ s.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-description" class="form-label">Descrição</label>
                            <input type="text" name="description" class="form-control" id="s-description"
                                v-model="page.search.description" placeholder="Pesquise por partes da descrição">
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
                        Actions.Delete(pageData.remove),
                        Actions.Dowload((id) => pageData.download(id, 'Comissão')),
                        Actions.Create('calendar-clear-outline', 'Extinguir', extinction),
                        Actions.Create('people-outline', 'Membros', members),
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        type: [Mounts.Cast(page.selects.status)]
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
                    <div class="d-flex gap-2">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row" @submit.prevent="pageData.save">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">
                            <input type="hidden" name="id" v-model="page.id">
                            <div class="col-sm-12">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Nome de Identificação da Comissão" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="unit" class="form-label">Unidade</label>
                                <select name="unit" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.unit }" id="unit"
                                    v-model="page.data.unit">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.units" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <FileInput label="Documento" identify="document" v-model="page.data.document"
                                    :valid="extinct.valids.document" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="start_term" class="form-label">Início Pleito</label>
                                <VueDatePicker auto-apply v-model="page.data.start_term"
                                    :input-class-name="page.valids.start_term ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Início Pleito</label>
                                <VueDatePicker auto-apply v-model="page.data.end_term"
                                    :input-class-name="page.valids.end_term ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.status }" id="status"
                                    v-model="page.data.status">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.status" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="type" class="form-label">Tipo de Comissão</label>
                                <select name="type" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.type }" id="type"
                                    v-model="page.data.type">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.types" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição das Atividades</label>
                                <textarea name="description" class="form-control" id="description"
                                    v-model="page.data.description"></textarea>
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

            <section v-if="page.ui.prepare" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Comissões</h2>
                        <p>
                            Listagem das comissões atreladas ao
                            <span class="txt-color">{{ organ.get_organ()?.name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button @click="pageData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="pageData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row" @submit.prevent="() => { extinctData.save(null, pageData.list) }">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">

                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Data de Extinção</label>
                                <input type="text" name="end_term" class="form-control" id="end_term"
                                    :class="{ 'form-control-alert': extinct.valids.end_term }" placeholder="dd/mm/aaaa"
                                    v-maska:[masks.maskdate] v-model="extinct.data.end_term">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <FileInput label="Documento" identify="document" v-model="extinct.data.document"
                                    :valid="extinct.valids.document" />
                            </div>
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição da Extinção</label>
                                <textarea name="description" class="form-control" id="description"
                                    :class="{ 'form-control-alert': extinct.valids.description }"
                                    v-model="extinct.data.description"></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button type="submit" class="btn btn-action-primary">
                                <ion-icon name="calendar-clear-outline" class="fs-5"></ion-icon>
                                Extinguir
                            </button>
                            <button @click="pageData.ui('prepare')" class="btn btn-action-secondary">
                                <ion-icon name="close-outline" class="fs-5"></ion-icon>
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped></style>