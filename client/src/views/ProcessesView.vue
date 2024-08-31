<script setup>
import { onMounted, watch } from 'vue';
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TableListStatus from '@/components/table/TableListStatus.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import masks from '@/utils/masks';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import utils from '@/utils/utils';
import TabNav from '@/components/TabNav.vue';
import Tabs from '@/utils/tabs';
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';
import DfdDetails from '@/components/DfdDetails.vue';
import TableListSelect from '@/components/table/TableListSelect.vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/processes',
    datalist: props.datalist,
    header: [
        { key: 'date_hour_ini', title: 'ABERTURA', sub: [{ key: 'protocol' }] },
        { key: 'units.title', title: 'ORIGEM' },
        { key: 'modality', title: 'CLASSIFICAÇÃO', sub: [{ key: 'type' }] },
        { title: 'OBJETO', sub: [{ key: 'description' }] },
        { key: 'status', title: 'SITUAÇÃO' }
    ],
    rules: {
        date_hour_ini: 'required',
        year_pca: 'required',
        type: 'required',
        modality: 'required',
        units: 'required',
        comission: 'required',
        description: 'required',
        status: 'required',
        dfds: 'required',
    },
    dfds: {
        search: {},
        datalist: [],
        data: null,
        headers: [
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'demandant.name', title: 'DEMANDANTE' },
            { key: 'ordinator.name', title: 'ORDENADOR' },
            { key: 'unit.name', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
})


const tabs = new Tabs([
    { id: 'origem', title: 'Origem' },
    { id: 'processo', title: 'Processo' },
    { id: 'dfds', title: 'DFDs' },
    { id: 'revisar', title: 'Revisar' },
])

function listDfds() {
    http.post(`${page.url}/list_dfds`, page.dfds.search, emit, (resp) => {
        page.dfds.datalist = resp.data ?? []
    })
}

function dfd_details(id) {
    if (page.data.dfds) {
        page.dfds.data = (page.data.dfds).find(obj => obj.id === id)
        http.get(`${page.url}/list_dfd_items/${id}`, emit, (resp) => {
            page.dfds.data.items = resp.data
        })
    }
}

function unselect_dfd(id) {
    page.data.dfds = [...page.data.dfds || []]
        .filter((item) => item.id != id)
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

watch(() => page.ui.register, (newdata) => {
    if (newdata && page.data.id == null) {
        page.data.protocol = utils.dateProtocol(page.organ?.id)
    }
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
                        <h2>Processos</h2>
                        <p>
                            Listagem dos processos atreladas ao
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
                        <div class="col-sm-12 col-md-8">
                            <label for="s-unit" class="form-label">Unidades</label>
                            <InputDropMultSelect v-model="page.search.units" :options="page.selects.units"
                                identify="units" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-date_hour_ini" class="form-label">Data de Abertura</label>
                            <VueDatePicker id="s-date_hour_ini" auto-apply v-model="page.search.date_hour_ini"
                                :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy 00:00"
                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                                menu-class-name="dp-custom-menu" />
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <label for="s-protocol" class="form-label">Protocolo</label>
                            <input type="text" name="protocol" class="form-control" id="s-protocol"
                                v-model="page.search.protocol" placeholder="Pesquise por partes do protocolo">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-status" class="form-label">Situação</label>
                            <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                                <option value=""></option>
                                <option v-for="o in page.selects.status" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-object" class="form-label">Objeto</label>
                            <input type="text" name="object" class="form-control" id="s-object"
                                v-model="page.search.object" placeholder="Pesquise por partes do objeto">
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
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        type: [Mounts.Cast(page.selects.types)],
                        modality: [Mounts.Cast(page.selects.modalities)],
                        description: [Mounts.Truncate(200)],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Processo</h2>
                        <p>Registro dos processos do sistema</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <TabNav :tabs="tabs" identify="tabbed" />
                    <form @submit.prevent="pageData.save">
                        <input type="hidden" name="id" v-model="page.id">
                        <div class="tab-pane fade row content m-0 g-3 p-4 pt-1"
                            :class="{ 'show active': tabs.is('origem') }">
                            <div class="col-sm-12 col-md-4">
                                <label for="unit" class="form-label">Unidades</label>
                                <InputDropMultSelect :valid="page.valids.units" v-model="page.data.units"
                                    :options="page.selects.units" identify="units" />
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <label for="year_pca" class="form-label">Ano do PCA</label>
                                <input maxlength="4" type="text" name="year_pca" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.year_pca }" id="year_pca"
                                    v-model="page.data.year_pca" placeholder="AAAA">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="comission" class="form-label">Comissão</label>
                                <select name="comission" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.comission }" id="comission"
                                    v-model="page.data.comission">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                                <div class="form-text txt-color-sec">
                                    Ao selecionar a comissão/equipe de planejamento seus
                                    integrantes serão vinculados ao documento
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade row content m-0 g-3 p-4 pt-1"
                            :class="{ 'show active': tabs.is('processo') }">
                            <div class="col-sm-12 col-md-4">
                                <label for="date_hour_ini" class="form-label">Data e Hora de Abertura</label>
                                <VueDatePicker id="date_hour_ini" time-picker-inline model-type="dd/MM/yyyy HH:mm"
                                    v-model="page.data.date_hour_ini" auto-apply
                                    :input-class-name="page.valids.date_hour_ini ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="modality" class="form-label">Modalidade</label>
                                <select name="modality" class="form-control" :class="{
                                    'form-control-alert': page.valids.modality
                                }" id="modality" v-model="page.data.modality">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.modalities" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="type" class="form-label">Tipo</label>
                                <select name="type" class="form-control" :class="{
                                    'form-control-alert': page.valids.type
                                }" id="type" v-model="page.data.type">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.types" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="initial_value" class="form-label">Valor Inicial (R$)</label>
                                <input type="text" name="initial_value" class="form-control" v-maska:[masks.maskmoney]
                                    :class="{ 'form-control-alert': page.valids.initial_value }" id="initial_value"
                                    v-model="page.data.initial_value" placeholder="R$ 0.00">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="winner_value" class="form-label">Valor Vencedor (R$)</label>
                                <input type="text" name="winner_value" class="form-control" v-maska:[masks.maskmoney]
                                    :class="{ 'form-control-alert': page.valids.winner_value }" id="winner_value"
                                    v-model="page.data.winner_value" placeholder="R$ 0.00">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Situação</label>
                                <select name="status" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.status }" id="status"
                                    v-model="page.data.status">
                                    <option v-for="o in page.selects.status" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <label for="description" class="form-label">Descrição do Objeto</label>
                                <textarea name="description" class="form-control" rows="4" :class="{
                                    'form-control-alert': page.valids.description
                                }" id="description" v-model="page.data.description"></textarea>
                            </div>
                        </div>
                        <div class="tab-pane fade row m-0 g-3 content m-0 g-3"
                            :class="{ 'show active': tabs.is('dfds') }">
                            <div class="accordion" id="accordion-dfds">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="accordion-dfds-header">
                                        <button class="w-100 text-center py-3" type="button"
                                            :data-bs-toggle="[page.data.units && 'collapse']"
                                            data-bs-target="#accordion-dfds-collapse" aria-expanded="false"
                                            aria-controls="accordion-dfds-collapse">
                                            <h2 class="txt-color text-center m-0">
                                                <i class="bi bi-journal-album me-1"></i>
                                                Localizar DFDs
                                            </h2>
                                            <p class="validation txt-color-sec small text-center m-0"
                                                :class="{ 'text-danger': page.valids.dfds || !page.data.units }">
                                                {{ page.data.units
                                                    ? `Preencha os campos abaixo para localizar as DFDs`
                                                    : `É necessário selecionar uma unidade para continuar`
                                                }}
                                            </p>
                                        </button>
                                    </h2>
                                    <div id="accordion-dfds-collapse" class="accordion-collapse collapse"
                                        :class="[page.data.dfds && 'show']" aria-labelledby="accordion-dfds-header"
                                        data-bs-parent="#accordion-dfds">
                                        <div class="accordion-body">
                                            <div class="p-4 pt-0 mx-2">
                                                <div class="dashed-separator mb-3"></div>
                                                <div class="row g-3">
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="date_s_ini" class="form-label">Data
                                                            Inicial</label>
                                                        <VueDatePicker auto-apply v-model="page.dfds.search.date_i"
                                                            :enable-time-picker="false" format="dd/MM/yyyy"
                                                            model-type="yyyy-MM-dd"
                                                            input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                            calendar-class-name="dp-custom-calendar"
                                                            calendar-cell-class-name="dp-custom-cell"
                                                            menu-class-name="dp-custom-menu" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="date_s_fin" class="form-label">Data
                                                            Final</label>
                                                        <VueDatePicker auto-apply v-model="page.dfds.search.date_f"
                                                            :enable-time-picker="false" format="dd/MM/yyyy"
                                                            model-type="yyyy-MM-dd"
                                                            input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                            calendar-class-name="dp-custom-calendar"
                                                            calendar-cell-class-name="dp-custom-cell"
                                                            menu-class-name="dp-custom-menu" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="s-protocol" class="form-label">Protocolo</label>
                                                        <input type="text" name="protocol" class="form-control"
                                                            id="s-protocol" v-model="page.dfds.search.protocol"
                                                            placeholder="Número do Protocolo" />
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <label for="s-description" class="form-label">Descrição
                                                            do objeto</label>
                                                        <input type="text" name="description" class="form-control"
                                                            id="s-description" v-model="page.dfds.search.description"
                                                            placeholder="Pesquise por partes do Objeto do DFD" />
                                                    </div>

                                                    <div class="d-flex flex-row flex-row-reverse mt-4">
                                                        <button type="button" @click="listDfds"
                                                            class="btn btn-action-primary">
                                                            <ion-icon name="search" class="fs-5"></ion-icon>
                                                            Localizar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-4 pt-0 mx-2">
                                                <TableListSelect secondary identify="dfds" v-model="page.data.dfds"
                                                    :header="page.dfds.headers" :body="page.dfds.datalist" :mounts="{
                                                        status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
                                                        description: [Mounts.Truncate(160)],
                                                    }" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade row position-relative content m-0 g-3 p-4 pt-3"
                            :class="{ 'show active': tabs.is('revisar') }">
                            <div class="box-revisor">
                                <div class="box-revisor-title d-flex mb-4">
                                    <div class="bar-revisor-title me-2"></div>
                                    <div class="txt-revisor-title">
                                        <h3>Informações Gerais</h3>
                                        <p>Dados referentes ao processo</p>
                                    </div>
                                </div>
                                <div class="box-revisor-content">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h4>Data e Hora de Abertura</h4>
                                            <p>{{ page.data.date_hour_ini ?? '*****' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4>Valor Inicial</h4>
                                            <p>R${{ page.data.initial_value ?? '*****' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4>Valor Vencedor</h4>
                                            <p>R${{ page.data.winner_value ?? '*****' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4>Ano PCA</h4>
                                            <p>{{ page.data.year_pca ?? '*****' }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h4>Modalidade</h4>
                                            <p>
                                                {{
                                                    utils.getTxt(
                                                        page.selects.modalities,
                                                        page.data.modality
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <h4>Situação</h4>
                                            <TableListStatus :data="utils.getTxt(
                                                page.selects.status,
                                                page.data.status
                                            )" />
                                        </div>
                                        <div class="col-md-3">
                                            <h4>Tipo de processo</h4>
                                            <p>
                                                {{
                                                    utils.getTxt(
                                                        page.selects.types,
                                                        page.data.type
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>Descrição do Processo</h4>
                                            <p>{{ page.data.description ?? '*****' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="box-revisor">
                                <div class="box-revisor-title d-flex mb-4">
                                    <div class="bar-revisor-title me-2"></div>
                                    <div class="txt-revisor-title">
                                        <h3>DFDs</h3>
                                        <p>
                                            Lista das DFDs atreladas ao processo
                                        </p>
                                    </div>
                                </div>
                                <div class="box-revisor-content p-0">
                                    <!-- list items -->
                                    <TableList secondary :count="false" :header="page.dfds.headers"
                                        :body="page.data.dfds" :mounts="{
                                            status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
                                            description: [Mounts.Truncate()],
                                        }" :actions="[
                                                Actions.ModalDetails(dfd_details),
                                                Actions.FastDelete(unselect_dfd),
                                            ]" />
                                </div>
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
                            <button @click="tabs.next()" type="button" class="btn btn-action-secondary me-auto">
                                <ion-icon name="arrow-forward" class="fs-5"></ion-icon>
                            </button>
                            <button @click="tabs.prev()" type="button" class="btn btn-action-secondary">
                                <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            </button>
                        </div>
                    </form>
                </div>
            </section>
            <DfdDetails :dfd="page.dfds.data" :selects="page.selects" />
            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped>
.search-list-items {
    list-style: none;
    margin: 0;
    padding: 0;
}

.search-list-items li {
    cursor: pointer;
    border-bottom: var(--border-box);
}

.search-list-items li h3 {
    font-weight: 700;
    color: var(--color-base);
}

.search-list-items li:hover {
    background-color: var(--color-contrast-hover);
}

.item-type {
    width: 35px;
    border-right: var(--border-box);
}

.item-desc h3 {
    color: var(--color-base);
}

.item-desc p {
    font-size: 0.7rem;
    color: var(--color-text-sec);
}

.nav-step {
    margin: 0 !important;
    padding: 0 !important;
    position: relative;
    height: 60px;
}

.nav-line-step {
    height: 3px;
    width: 100%;
    background-color: var(--color-shadow);
    position: absolute;
    top: 50%;
    z-index: 0;
}

.nav-step-txt {
    background-color: var(--color-shadow);
    color: var(--color-shadow-2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: calc(50% - 30px);
}

.nav-step-txt i {
    font-size: 1.3rem;
    margin: 0;
    padding: 0;
}

.nav-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--color-shadow-2);
}

.nav-item .active .nav-step-txt {
    background-color: var(--color-base);
    color: white;
    transition: 400ms;
}

.nav-item .active .nav-line-step {
    background: rgb(202, 201, 201);
    background: linear-gradient(90deg,
            var(--color-shadow) 0%,
            var(--color-base) 50%,
            var(--color-shadow) 100%);
    transition: 400ms;
}

.active-label {
    color: var(--color-base);
}

.box-revisor {
    border-bottom: var(--border-box);
}

.bar-revisor-title {
    width: 5px;
    background-color: var(--color-base);
    border-radius: 2px;
}

.box-revisor-title h3 {
    color: var(--color-base);
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.box-revisor-title p {
    color: var(--color-text-secondary);
    font-size: small;
    margin: 0;
    padding: 0;
}

.box-revisor-content {
    padding: 0 10px 0 10px;
}

.box-revisor-content h4 {
    margin: 0;
    padding: 0;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--color-text);
}

.box-revisor-content p {
    color: var(--color-text-secondary);
    font-size: 0.9rem;
}

.btn-abs {
    top: -20px;
    right: 0;
}

@media (max-width: 755px) {
    .nav-step {
        height: 50px;
    }

    .nav-step-txt {
        width: 50px;
        height: 50px;
        left: calc(50% - 25px);
    }

    .nav-step-txt i {
        font-size: 1.2rem;
    }

    .nav-label {
        display: none;
    }
}
</style>