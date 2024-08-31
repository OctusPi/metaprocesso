<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import { onMounted, watch } from 'vue';
import Mounts from '@/services/mounts';
import masks from '@/utils/masks';
import FileInput from '@/components/inputs/FileInput.vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/ordinators',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cpf' }] },
        { obj: 'unit', key: 'name', title: 'VINCULO', sub: [{ obj: 'organ', key: 'name' }] },
        { key: 'status', title: 'STATUS', sub: [{ title: 'De ', key: 'start_term' }, { title: 'à ', key: 'end_term', err: 'Agora' }] },
    ],
    rules: {
        name: 'required',
        cpf: 'required',
        status: 'required',
        start_term: 'required',
    }
})

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
            <section v-if="!page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Ordenadores</h2>
                        <p>
                            Listagem dos ordenadores atrelados ao
                            <span class="txt-color">{{ page.organ_name }}</span>
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
                            <label for="s-unit" class="form-label">Unidade</label>
                            <select name="unit" class="form-control" id="s-unit" v-model="page.search.unit">
                                <option value=""></option>
                                <option v-for="o in page.selects.units" :key="o.id" :value="o.id">
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
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()]
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Ordenador</h2>
                        <p>Preencha os dados abaixo para realizar o registro</p>
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
                            <div class="col-sm-12 col-md-8">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Nome do Ordenador" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" name="cpf" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.cpf }" id="cpf"
                                    placeholder="000.000.000-00" v-maska:[masks.maskcpf] v-model="page.data.cpf">
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
                            <div class="col-sm-12 col-md-4">
                                <label for="registration" class="form-label">Matrícula</label>
                                <input type="text" name="registration" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.registration }" id="registration"
                                    placeholder="Número da Matrícula Servidor" v-model="page.data.registration">
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
                                <label for="start_term" class="form-label">Início Pleito</label>
                                <VueDatePicker auto-apply v-model="page.data.start_term"
                                    :input-class-name="page.valids.start_term ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="end_term" class="form-label">Término Pleito</label>
                                <VueDatePicker auto-apply v-model="page.data.end_term"
                                    :input-class-name="page.valids.end_term ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <FileInput label="Documento" identify="document" v-model="page.data.document"
                                    :valid="page.valids.document" />
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