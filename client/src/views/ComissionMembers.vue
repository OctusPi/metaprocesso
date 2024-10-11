<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import { useRoute } from 'vue-router';
import { onMounted } from 'vue';
import { watch } from 'vue';
import FileInput from '@/components/inputs/FileInput.vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const route = useRoute()

const [page, pageData] = Layout.new(emit, {
    url: `/comissionmembers/${route.params.id}`,
    datalist: props.datalist,
    comission: {},
    header: [
        { key: 'name', title: 'NOME' },
        { key: 'responsibility', title: 'RESPONSABILIDADE' },
        { title: 'PLEITO', sub: [{ title: 'De ', key: 'start_term' }, { title: 'à ', key: 'end_term', err: 'Agora' }] },
        { key: 'status', title: 'STATUS' },
    ],
    rules: {
        name: 'required',
        responsibility: 'required',
        start_term: 'required',
        status: 'required',
    },
})

function fetchComission() {
    http.get(`${page.url}/comission`, emit, (res) => {
        page.comission = res.data
        page.search.comission_id = res.data?.id
    })
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

onMounted(() => {
    fetchComission()
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
            <section v-if="!page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Membros</h2>
                        <p>
                            Membros atrelados à comissão
                            <span class="txt-color">{{ page.comission.name ?? '-' }}</span>
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
                        <RouterLink to="/comissions" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </RouterLink>
                    </div>
                </div>
                <!-- Search -->
                <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                placeholder="Pesquise por partes do nome do setor">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-responsibility" class="form-label">Responsabilidade</label>
                            <select name="responsibility" class="form-control" id="s-responsibility"
                                v-model="page.search.responsibility">
                                <option value=""></option>
                                <option v-for="o in page.selects.responsibilities" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-status" class="form-label">Status</label>
                            <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                                <option value=""></option>
                                <option v-for="o in page.selects.status" :key="o.id" :value="o.id">
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
                        Actions.Delete(pageData.remove),
                        Actions.Dowload((id) => pageData.download(id, 'Amparo')),
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        responsibility: [Mounts.Cast(page.selects.responsibilities)],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Comissão</h2>
                        <p>Registro das dotações do sistema</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row" @submit.prevent="pageData.save()">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">
                            <input type="hidden" name="id" v-model="page.id">
                            <div class="col-sm-12">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Nome do Membro" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="start_term" class="form-label">Início Pleito</label>
                                <VueDatePicker auto-apply v-model="page.data.start_term"
                                    :input-class-name="page.valids.start_term ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    :auto-position="false"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Término Pleito</label>
                                <VueDatePicker auto-apply v-model="page.data.end_term"
                                    :input-class-name="page.valids.end_term ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    :auto-position="false"
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
                            <div class="col-sm-12 col-md-4">
                                <label for="responsibility" class="form-label">Responsabilidade</label>
                                <select name="responsibility" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.responsibility }" id="responsibility"
                                    v-model="page.data.responsibility">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.responsibilities" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="number_doc" class="form-label">Número Portaria</label>
                                <input type="text" name="number_doc" class="form-control" id="number_doc"
                                    placeholder="00000" v-model="page.data.number_doc">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <FileInput label="Anexar Amparo Legal" identify="document" v-model="page.data.document"
                                    :valid="page.valids.document" />
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

            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped></style>