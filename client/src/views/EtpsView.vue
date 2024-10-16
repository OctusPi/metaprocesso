<script setup>

import { createApp, inject, onMounted, ref, watch } from 'vue';

// Services
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import http from '@/services/http';
import gpt from '@/services/gpt';
import exp from '@/services/export';
import axsi from '@/services/axsi';

// Utils
import utils from '@/utils/utils';
import TabNav from '@/components/TabNavValidated.vue';
import Tabs from '@/utils/tabsValidated';

// Components
import TableList from '@/components/table/TableList.vue';
import TableListRadio from '@/components/table/TableListRadio.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import InputRichText from '@/components/inputs/InputRichText.vue';
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';
import DfdDetails from '@/components/DfdDetails.vue';
import AttachmentsCmp from '@/components/AttachmentsCmp.vue';

// Local Components
import EtpReport from './reports/EtpReport.vue';
import PDFMerger from 'pdf-merger-js';
import notifys from '@/utils/notifys';
import forms from '@/services/forms';


const ORIGIN_ETPS = "1";

const sysapp = inject('sysapp')

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/etps',
    datalist: props.datalist,
    header: [
        { key: 'emission', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { key: 'comission.name', title: 'ORIGEM' },
        { title: 'NECESSIDADE', sub: [{ key: 'necessity' }] },
        { key: 'status', title: 'STATUS' },
    ],
    rules: {
        process: 'required',
        comission_id: 'required',
        protocol: 'required',
        emission: 'required',
        status: 'required',
        object_description: 'required',
        object_classification: 'required',
        necessity: 'required',
        contract_forecast: 'required',
        contract_requirements: 'required',
        market_survey: 'required',
        contract_calculus_memories: 'required',
        contract_expected_price: 'required',
        solution_full_description: 'required',
        solution_parcel_justification: 'required',
        correlated_contracts: 'required',
        contract_alignment: 'required',
        expected_results: 'required',
        contract_previous_actions: 'required',
        ambiental_impacts: 'required',
        viability_declaration: 'required',
        installment_type: 'required'
    },
    process: {
        search: {},
        data: [],
        headers: [
            { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'ordinators.name', title: 'ORDENADORES' },
            { key: 'units.title', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    },
    dfd: {
        data: {},
        headers: [
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'demandant.name', title: 'DEMANDANTE' },
            { key: 'ordinator.name', title: 'ORDENADOR' },
            { key: 'unit.name', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
    }
})

const tabs = new Tabs({
    process: { title: 'Processo' },
    dfds: { title: 'DFDs' },
    info: { title: 'Informações' },
    necessidade: { title: 'Necessidade' },
    solucao: { title: 'Solução' },
    planejamento: { title: 'Planejamento' },
    viabilidade: { title: 'Viabilidade' },
    anexos: { title: 'Anexos' },
})

function list_processes() {
    http.post(`${page.url}/list_processes`, page.process.search, emit, (resp) => {
        page.process.data = resp.data ?? []
    })
}

function export_etp(id) {
    http.get(`${page.url}/export/${id}`, emit, async (resp) => {
        const etp = resp.data

        const merger = new PDFMerger()

        const containerReport = document.createElement('div')
        const instanceReport = createApp(EtpReport, {
            qrdata: sysapp,
            organ: page.organ,
            etp: etp,
            selects: page.selects,
        })
        instanceReport.mount(containerReport)

        const pdf = await exp.generatePDF(containerReport)

        await merger.add(pdf)

        for (const item of etp.attachments) {
            await axsi.axiosInstanceAuth.request({
                method: 'GET',
                url: `/attachments/${ORIGIN_ETPS}/${etp?.protocol}/download/${item.id}`,
                responseType: 'blob',
                headers: {
                    'Content-Type': 'application/json',
                }
            }).then(async res => {
                await merger.add(res.data)
            }).catch(e => console.error(e))
        }

        await merger.save(`ETP-${etp.protocol}`)
    })
}

const partial = ref({})

function savePartially() {
    Object.keys(page.valids).forEach(k => {
        page.valids[k] = false
    })

    const validation = forms.checkform(page.data, {
        valids: page.valids,
        fields: {
            process: 'required',
            comission_id: 'required',
        },
    });

    if (!validation.isvalid) {
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    page.data.process_id = page.data.process?.id

    http.post(`${page.url}/save_part`, page.data, emit, () => {
        pageData.list()
    })
}

function generate(type) {
    const base = {
        organ: page.organ,
        comission: page.selects.comissions?.find(o => o.id === page.data.comission_id),
        object_description: page.data.object_description,
        process: page.process
    }

    let callresp, payload = null

    function setValuesAndPayload(key, mPayload) {
        payload = mPayload
        callresp = (resp) => {
            page.data[key] = `<p>${resp?.data?.choices[0]?.message.content ?? ''}</p>`
            console.log(`<p>${resp?.data?.choices[0]?.message.content ?? ''}</p>`)
        };
    }

    switch (type) {
        case 'object_description':
            setValuesAndPayload(type, `
            Crie uma descrição concisa para um Estudo Técnico preliminar pelo órgão ${base.organ?.name}
            baseado no descrição do processo '${base.process?.description}' em plain text
        `);
            break;
        case 'object_classification':
            setValuesAndPayload(type, `
            Classifique o objeto de um Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'necessity':
            setValuesAndPayload(type, `
            Descreva a necessidade da criação de um Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'contract_forecast':
            setValuesAndPayload(type, `
            Descreva uma data de previsão por extenso, com base na complexidade o Estudo Técnico preliminar descrito
            no texto '${base.object_description}' em plain text
        `);
            break;
        case 'contract_requirements':
            setValuesAndPayload(type, `
            Elabore os requisitos para o Estudo técnico preliminar descrito no texto '${base.object_description}' em plain text
        `);
            break;
        case 'market_survey':
            setValuesAndPayload(type, `
            Crie uma descrição para a pesquisa de mercado do Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'contract_calculus_memories':
            setValuesAndPayload(type, `
            Crie uma descrição para as memórias de cálculos do contrato relacionadas ao Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'contract_expected_price':
            setValuesAndPayload(type, `
            Crie uma descrição para o preço esperado do contrato com base no Estudo Técnico preliminar descrito no texto '${base.object_description}' em plain text
        `);
            break;
        case 'solution_full_description':
            setValuesAndPayload(type, `
            Crie uma descrição completa da solução para o Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'solution_parcel_justification':
            setValuesAndPayload(type, `
            Crie uma justificativa para as parcelas da solução descrita no Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'correlated_contracts':
            setValuesAndPayload(type, `
            Crie uma descrição dos contratos correlacionados ao Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'contract_alignment':
            setValuesAndPayload(type, `
            Crie uma descrição para o alinhamento do contrato com o Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'expected_results':
            setValuesAndPayload(type, `
            Crie uma descrição dos resultados esperados com base no Estudo Técnico preliminar descrito no texto '${base.object_description}' em plain text
        `);
            break;
        case 'contract_previous_actions':
            setValuesAndPayload(type, `
            Crie uma descrição das ações anteriores relacionadas ao contrato e ao Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;
        case 'ambiental_impacts':
            setValuesAndPayload(type, `
            Crie uma descrição dos possíveis impactos ambientais relacionados ao Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input da descrição do processo '${base.process?.description}' e na descrição '${base.object_description}' em plain text
        `);
            break;

        case 'installment_justification':
            setValuesAndPayload(type, `
            Crie uma justificativa de parcelamento do tipo ${utils.getTxt(page.selects.installment_types, base.installment_type)}
            relacionados ao Estudo Técnico preliminar do órgão ${base.organ?.name}
            baseado no input prévio da justificastiva  '${base.installment_justification}' em plain text
        `);
            break;
        default:
            break;
    }

    gpt.generate(`${page.url}/generate`, payload, emit, callresp)
}

function fetchProcess(e) {
    http.post(`${page.url}/${page.data.id ?? 0}/fetch_process/${e.target._value?.id}`, {}, emit, (res) => {
        page.data.installment_type = res.data.installment_type
        page.data.installment_justification = res.data.installment_justification
    }, () => {
        page.data.process = null
    })
}

function dfd_details(id) {
    if (page.data.process.dfds) {
        page.dfd.data = page.data.process.dfds.find(obj => obj.id === id)
        http.get(`${page.url}/list_dfd_items/${id}`, emit, (resp) => {
            page.dfd.data.items = resp.data
        })
    }
}

watch(() => page.ui.register, (newdata) => {
    if (newdata && page.data.id == null) {
        page.data.protocol = utils.dateProtocol(page.organ?.id)
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
                        <h2>ETPs</h2>
                        <p>
                            Listagem das ETPs atreladas ao
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
                            <label for="s-protocol" class="form-label">Protocolo</label>
                            <input type="text" name="protocol" class="form-control" id="s-protocol"
                                v-model="page.search.protocol" placeholder="Pesquise por partes do protocolo" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_ini" class="form-label">Data Inicial</label>
                            <VueDatePicker auto-apply v-model="page.search.date_i" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                :auto-position="false" locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_fin" class="form-label">Data Final</label>
                            <VueDatePicker auto-apply v-model="page.search.date_f" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                :auto-position="false" locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-8">
                            <label for="s-necessity" class="form-label">Necessidade</label>
                            <input type="text" name="necessity" class="form-control" id="s-necessity"
                                v-model="page.search.necessity" placeholder="Pesquise por partes da necessidade" />
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
                        Actions.Export('document-text-outline', export_etp),
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        necessity: [Mounts.StripHTML(), Mounts.Truncate(200)],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar ETP</h2>
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
                    <TabNav :tabs="tabs" :valids="page.valids" identify="tabbed" />
                    <form @submit.prevent="pageData.save({ process_id: page.data.process?.id })">
                        <!-- tab processo -->
                        <div class="tab-content">
                            <div id="process" class="tab-pane show active">
                                <div class="accordion mb-3" id="accordion-process">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="accordionSearchProcessHeadId">
                                            <button class="w-100 text-center px-2 py-3" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#accordionSearchColapseId"
                                                aria-expanded="true" aria-controls="accordionSearchColapseId">
                                                <h2 class="txt-color text-center m-0">
                                                    <i class="bi bi-journal-bookmark me-1"></i>
                                                    Localizar Processo
                                                </h2>
                                                <p class="validation txt-color-sec small text-center m-0" :class="{
                                                    'text-danger': page.valids.process
                                                }">
                                                    Aplique os filtros abaixo para localizar os Processos
                                                </p>
                                            </button>
                                        </h2>
                                        <div id="accordionSearchColapseId" class="accordion-collapse collapse show"
                                            aria-labelledby="accordion-processHeadId"
                                            data-bs-parent="#accordion-process">
                                            <div class="accordion-body p-0 m-0">
                                                <div class="p-4 pt-0 mx-2">
                                                    <div class="dashed-separator mb-3"></div>
                                                    <div class="row g-3">
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="date_s_ini" class="form-label">Data
                                                                Inicial</label>
                                                            <VueDatePicker auto-apply
                                                                v-model="page.process.search.date_i"
                                                                :enable-time-picker="false" format="dd/MM/yyyy"
                                                                :auto-position="false" model-type="yyyy-MM-dd"
                                                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                                calendar-class-name="dp-custom-calendar"
                                                                calendar-cell-class-name="dp-custom-cell"
                                                                menu-class-name="dp-custom-menu" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="date_s_fin" class="form-label">Data
                                                                Final</label>
                                                            <VueDatePicker auto-apply
                                                                v-model="page.process.search.date_f"
                                                                :enable-time-picker="false" format="dd/MM/yyyy"
                                                                :auto-position="false" model-type="yyyy-MM-dd"
                                                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                                                calendar-class-name="dp-custom-calendar"
                                                                calendar-cell-class-name="dp-custom-cell"
                                                                menu-class-name="dp-custom-menu" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="s-protocol" class="form-label">Protocolo</label>
                                                            <input type="text" name="protocol" class="form-control"
                                                                id="s-protocol" v-model="page.process.search.protocol"
                                                                placeholder="Número do Protocolo do Processo"
                                                                @keydown.enter.prevent="list_processes" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-4">
                                                            <label for="s-unit" class="form-label">Unidades</label>
                                                            <InputDropMultSelect v-model="page.process.search.units"
                                                                :options="page.selects.units" identify="units" />
                                                        </div>
                                                        <div class="col-sm-12 col-md-8">
                                                            <label for="s-description" class="form-label">Objeto</label>
                                                            <input type="text" name="description" class="form-control"
                                                                id="s-description"
                                                                v-model="page.process.search.description"
                                                                placeholder="Pesquise por partes do Objeto do Processo"
                                                                @keydown.enter.prevent="list_processes" />
                                                        </div>
                                                        <div class="d-flex flex-row-reverse mt-4">
                                                            <button type="button" @click="list_processes"
                                                                class="btn btn-action-primary">
                                                                <ion-icon name="search" class="fs-5"></ion-icon>
                                                                Localizar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-4 pt-0 mx-2">
                                                    <TableListRadio secondary identify="process"
                                                        v-model="page.data.process" :header="page.process.headers"
                                                        @onChange="fetchProcess" :body="page.process.data" :mounts="{
                                                            status: [Mounts.Cast(page.selects.process_status), Mounts.Status()],
                                                            description: [Mounts.Truncate(100)],
                                                        }" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="dfds" class="tab-pane">
                                <div v-if="page.data.process" class="m-0 p-0">
                                    <TableList :count="false" :header="page.dfd.headers" :body="page.data.process.dfds"
                                        :mounts="{
                                            status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
                                            description: [Mounts.Truncate(100)]
                                        }" :actions="[
                                            Actions.ModalDetails(dfd_details),
                                        ]" />
                                </div>
                                <div class="content py-3 m-0 w-100" v-else>
                                    <h2
                                        class="txt-color text-center m-0 d-flex justify-content-center align-items-center gap-1">
                                        <ion-icon name="warning" class="fs-5" />
                                        Atenção
                                    </h2>
                                    <p class="txt-color-sec small text-center m-0">
                                        É necessário selecionar um processo para visualizar as DFDs
                                    </p>
                                </div>
                            </div>

                            <div id="info" class="tab-pane">
                                <div class="content p-4 pt-0 g-3 m-0 row">
                                    <div class="col-sm-12 col-md-8">
                                        <label for="comission" class="form-label">Comissão</label>
                                        <select name="comission" class="form-control"
                                            :class="{ 'form-control-alert': page.valids.comission_id }" id="comission"
                                            v-model="page.data.comission_id">
                                            <option value=""></option>
                                            <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">
                                                {{ o.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="emission" class="form-label">Emissão</label>
                                        <VueDatePicker auto-apply v-model="page.data.emission"
                                            :input-class-name="page.valids.emission ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            :auto-position="false" locale="pt-br"
                                            calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />
                                    </div>
                                    <div class="col-sm-12 col-md-8">
                                        <label for="status" class="form-label">Situação Atual</label>
                                        <select name="status" class="form-control"
                                            :class="{ 'form-control-alert': page.valids.status }" id="status"
                                            v-model="page.data.status">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.status" :value="s.id" :key="s.id">{{
                                                s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label for="installment_type" class="form-label">Tipo de Parcelamento</label>
                                        <select name="installment_type" class="form-control"
                                            :class="{ 'form-control-alert': page.valids.installment_type }"
                                            id="installment_type" v-model="page.data.installment_type">
                                            <option value=""></option>
                                            <option v-for="s in page.selects.installment_types" :value="s.id"
                                                :key="s.id">{{ s.title }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <label for="installment_justification"
                                            class="form-label d-flex justify-content-between">
                                            Justificativa do parcelamento
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('installment_justification')">
                                                <ion-icon name="hardware-chip-outline" class="m-0"></ion-icon>
                                                Gerar com I.A
                                            </a>
                                        </label>
                                        <InputRichText :valid="page.valids.installment_justification"
                                            placeholder="Descrição do Objeto" identifier="installment_justification"
                                            v-model="page.data.installment_justification" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_description"
                                            class="form-label d-flex justify-content-between">
                                            Descrição sucinta do objeto
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('object_description')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.object_description"
                                            placeholder="Descrição do Objeto" identifier="object_description"
                                            v-model="page.data.object_description" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-flex justify-content-between">
                                            Classificação do objeto
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('object_classification')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.object_classification"
                                            placeholder="Classificação do Objeto" identifier="object_classification"
                                            v-model="page.data.object_classification" />
                                    </div>
                                </div>
                            </div>

                            <div id="necessidade" class="tab-pane">
                                <div class="content p-4 pt-0 g-3 m-0 row">
                                    <div class="col-12">
                                        <label for="necessity" class="form-label d-flex justify-content-between">
                                            Necessidade
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('necessity')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.necessity"
                                            placeholder="Descrição da Necessidade" identifier="necessity"
                                            v-model="page.data.necessity" />
                                    </div>
                                    <div class="col-12">
                                        <label for="contract_requirements"
                                            class="form-label d-flex justify-content-between">
                                            Descrição dos Requisitos da Contratação
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('contract_requirements')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_requirements"
                                            placeholder="Descrição dos Requisitos da Contratação"
                                            identifier="contract_requirements"
                                            v-model="page.data.contract_requirements" />
                                    </div>
                                    <div class="col-12">
                                        <label for="contract_forecast"
                                            class="form-label d-flex justify-content-between">
                                            Previsão de Realização da Contratação
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('contract_forecast')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_forecast"
                                            placeholder="Previsão de Realização da Contratação"
                                            identifier="contract_forecast" v-model="page.data.contract_forecast" />
                                    </div>
                                </div>
                            </div>

                            <div id="solucao" class="tab-pane">
                                <div class="content p-4 pt-0 g-3 m-0 row">
                                    <div class="col-12">
                                        <label for="market_survey"
                                            class="form-label d-flex justify-content-between">Levantamento de Mercado
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('market_survey')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.market_survey"
                                            placeholder="Levantamento de Mercado" identifier="market_survey"
                                            v-model="page.data.market_survey" />
                                    </div>
                                    <div class="col-12">
                                        <label for="solution_full_description"
                                            class="form-label d-flex justify-content-between">Descrição da Solução como
                                            um Todo
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('solution_full_description')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.solution_full_description"
                                            placeholder="Descrição da Solução como um Todo"
                                            identifier="solution_full_description"
                                            v-model="page.data.solution_full_description" />
                                    </div>
                                    <div class="col-12">
                                        <label for="contract_calculus_memories"
                                            class="form-label d-flex justify-content-between">Estimativa das Quantidades
                                            Contratadas
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('contract_calculus_memories')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_calculus_memories"
                                            placeholder="Estimativa das Quantidades Contratadas"
                                            identifier="contract_calculus_memories"
                                            v-model="page.data.contract_calculus_memories" />
                                    </div>
                                    <div class="col-12">
                                        <label for="contract_expected_price"
                                            class="form-label d-flex justify-content-between">Estimativa do Preço da
                                            Contratação
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('contract_expected_price')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_expected_price"
                                            placeholder="Estimativa do Preço da Contratação"
                                            identifier="contract_expected_price"
                                            v-model="page.data.contract_expected_price" />
                                    </div>
                                    <div class="col-12">
                                        <label for="solution_parcel_justification"
                                            class="form-label d-flex justify-content-between">Justificativa para o
                                            Parcelamento ou Não
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('solution_parcel_justification')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.solution_parcel_justification"
                                            placeholder="Justificativa para o Parcelamento ou Não"
                                            identifier="solution_parcel_justification"
                                            v-model="page.data.solution_parcel_justification" />
                                    </div>
                                    <div class="col-12">
                                        <label for="correlated_contracts"
                                            class="form-label d-flex justify-content-between">Contratações Correlatas
                                            e/ou Interdependentes
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('correlated_contracts')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.correlated_contracts"
                                            placeholder="Contratações Correlatas e/ou Interdependentes"
                                            identifier="correlated_contracts"
                                            v-model="page.data.correlated_contracts" />
                                    </div>
                                </div>
                            </div>

                            <div id="planejamento" class="tab-pane">
                                <div class="content p-4 pt-0 g-3 m-0 row">
                                    <div class="col-12">
                                        <label for="expected_results"
                                            class="form-label d-flex justify-content-between">Resultados Pretendidos
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('expected_results')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.expected_results"
                                            placeholder="Resultados Pretendidos" identifier="expected_results"
                                            v-model="page.data.expected_results" />
                                    </div>
                                    <div class="col-12">
                                        <label for="contract_previous_actions"
                                            class="form-label d-flex justify-content-between">Providências a Serem
                                            Tomadas
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('contract_previous_actions')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_previous_actions"
                                            placeholder="Providências a Serem Tomadas"
                                            identifier="contract_previous_actions"
                                            v-model="page.data.contract_previous_actions" />
                                    </div>
                                    <div class="col-12">
                                        <label for="contract_alignment"
                                            class="form-label d-flex justify-content-between">Alinhamento de Contrato
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('contract_alignment')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_alignment"
                                            placeholder="Alinhamento de Contrato" identifier="contract_alignment"
                                            v-model="page.data.contract_alignment" />
                                    </div>
                                    <div class="col-12">
                                        <label for="ambiental_impacts"
                                            class="form-label d-flex justify-content-between">Possíveis Impactos
                                            Ambientais
                                            <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="generate('ambiental_impacts')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                        </label>
                                        <InputRichText :valid="page.valids.ambiental_impacts"
                                            placeholder="Possíveis Impactos Ambientais" identifier="ambiental_impacts"
                                            v-model="page.data.ambiental_impacts" />
                                    </div>
                                </div>
                            </div>

                            <div id="viabilidade" class="tab-pane">
                                <div class="content p-4 pt-0 g-3 m-0 row">
                                    <div class="row mb-3 g-3">
                                        <div>
                                            <h2 class="txt-color text-center m-0">
                                                <i class="bi bi-check-all me-1"></i>
                                                Declaração de Viabilidade
                                            </h2>
                                            <p class="txt-color-sec small text-center m-0"
                                                :class="{ 'text-danger': page.valids.viability_declaration }">
                                                Selecione uma das opções abaixo
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input class="btn-check" id="viability-1" type="radio" name="viability"
                                                value="1" v-model="page.data.viability_declaration">
                                            <label class="btn btn-action-primary-tls w-100" for="viability-1">Esta
                                                equipe de
                                                planejamento
                                                declara viável esta contratação com base neste ETP, consoante o inciso
                                                XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.</label>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <input class="btn-check" id="viability-2" type="radio" name="viability"
                                                value="0" v-model="page.data.viability_declaration">
                                            <label class="btn btn-action-danger-tls w-100" for="viability-2">Esta equipe
                                                de
                                                planejamento
                                                declara inviável esta contratação com base neste ETP, consoante o inciso
                                                XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="anexos" class="tab-pane">
                                <AttachmentsCmp label="Listagem de arquivos anexados ao ETP"
                                    @callAlert="(data) => emit('callAlert', data)"
                                    @callRemove="(data) => emit('callRemove', data)"
                                    :origin="String(page.selects.vars?.ORIGIN_ETP)" :protocol="page.data.protocol" />
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button v-if="!page.data.id" @click="savePartially" type="button"
                                class="btn btn-action-secondary">
                                <ion-icon name="folder-open-outline" class="fs-5"></ion-icon>
                                Salvar parcialmente
                            </button>
                            <button @click="pageData.ui('register')" type="button" class="btn btn-action-secondary">
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
            <DfdDetails :dfd="page.dfd.data" :selects="page.selects" />
            <FooterMainUi />
        </main>
    </div>
</template>