<script setup>
    import { createApp, inject, onMounted, watch } from 'vue';
    import TableList from '@/components/table/TableList.vue';
    import NavMainUi from '@/components/NavMainUi.vue';
    import HeaderMainUi from '@/components/HeaderMainUi.vue';
    import FooterMainUi from '@/components/FooterMainUi.vue';
    import Layout from '@/services/layout';
    import Actions from '@/services/actions';
    import Mounts from '@/services/mounts';
    import http from '@/services/http';
    import gpt from '@/services/gpt';
    import utils from '@/utils/utils';
    import TabNav from '@/components/TabNav.vue';
    import Tabs from '@/utils/tabs';
    import InputRichText from '@/components/inputs/InputRichText.vue';
    import TableListRadio from '@/components/table/TableListRadio.vue';
    import DfdDetails from '@/components/DfdDetails.vue';
    import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';
    import exp from '@/services/export';
    import ReftermReport from './reports/ReftermReport.vue';
    import notifys from '@/utils/notifys';

    const sysapp = inject('sysapp')

    const emit = defineEmits(['callAlert', 'callUpdate'])

    const props = defineProps({
        datalist: { type: Array, default: () => [] }
    })

    const [page, pageData] = Layout.new(emit, {
        url: '/refterms',
        datalist: props.datalist,
        header: [
            { key: 'emission', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'comission.name', title: 'ORIGEM' },
            { title: 'NECESSIDADE', sub: [{ key: 'necessity' }] },
            { key: 'type', title: 'TIPO' },
        ],
        rules: {
            comission_id: 'required',
            necessity: 'required',
            contract_forecast: 'required',
            contract_requirements: 'required',
            contract_expected_price: 'required',
            market_survey: 'required',
            solution_full_description: 'required',
            ambiental_impacts: 'required',
            correlated_contracts: 'required',
            object_execution_model: 'required',
            contract_management_model: 'required',
            payment_measure_criteria: 'required',
            supplier_selection_criteria: 'required',
            funds_suitability: 'required',
            parts_obligation: 'required',
            emission: 'required',
            type: 'required',
        },
        process: {
            search: {},
            data: [],
            headers: [
                { key: 'date_hour_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
                { key: 'ordinators.name', title: 'ORDENADORES', err: 'Nenhum Ordenador' },
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
                { key: 'unit.name', title: 'ORIGEM', sub: [{ key: 'organ.name' }] },
                { title: 'OBJETO', sub: [{ key: 'description' }] },
                { key: 'status', title: 'SITUAÇÃO' }
            ],
        }
    })

    const tabs = new Tabs([
        { id: 'process', title: 'Processo' },
        { id: 'dfds', title: 'DFDs' },
        { id: 'info', title: 'Informações' },
        { id: 'solucao', title: 'Solução' },
        { id: 'exec', title: 'Execução' },
        { id: 'pagamentos', title: 'Pagamentos' },
        { id: 'gestao', title: 'Gestão' },
        { id: 'fiscalizacao', title: 'Fiscalização' },
    ])

    function list_processes() {
        http.post(`${page.url}/list_processes`, page.process.search, emit, (resp) => {
            page.process.data = resp.data ?? []
        })
    }

    function fetchEtp(e) {
        http.post(`${page.url}/fetch_etp/${e.target._value?.id}`, {}, emit, (res) => {
            page.data.supplier_selection_criteria = res.data.process.price_record?.suppliers_justification
            page.data.necessity = res.data.necessity
            page.data.contract_forecast = res.data.contract_forecast
            page.data.contract_requirements = res.data.contract_requirements
            page.data.contract_expected_price = res.data.contract_expected_price
            page.data.market_survey = res.data.market_survey
            page.data.solution_full_description = res.data.solution_full_description
            page.data.correlated_contracts = res.data.correlated_contracts
            page.data.etp_id = res.data.id
            page.data.estimated_budget = (res.data.process.dfds ?? [])
                .reduce((prev, curr) => prev + utils.currencyToFloat(curr.estimated_value), 0)
        }, () => {
            page.data.process = null
        })
    }

    function export_refterm(id) {
        http.get(`${page.url}/export/${id}`, emit, async (resp) => {
            const refterm = resp.data
            const containerReport = document.createElement('div')
            const instanceReport = createApp(ReftermReport, {
                refterm,
                qrdata: sysapp,
                organ: page.organ,
                selects: page.selects,
            })
            instanceReport.mount(containerReport)
            exp.exportPDF(containerReport, `TermoDeReferencia-${id}`)
        })
    }

    function generate(type) {
        const base = {
            organ: page.organ,
            comission: page.selects.comissions?.find(o => o.id === page.data.comission_id),
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
            case 'necessity':
                setValuesAndPayload(type, `
                Crie uma descrição concisa para a necessidade de um Termo de Referência de um processo usando a breve descrição
                para o órgão ${base.organ?.name} baseado na descrição do processo '${base.process?.description}' em plain text
            `);
                break;
            case 'contract_forecast':
                setValuesAndPayload(type, `
                Elabore detalhadamente uma previsão de contratação para o processo descrito no texto "${base.process?.description}"
                e no texto prévio "${page.data.contract_forecast ?? ''}" em plain text
            `);
                break;
            case 'contract_requirements':
                setValuesAndPayload(type, `
                Elabore detalhadamente os requerimentos de contrato para o processo descrito no texto "${base.process?.description}" em plain text
            `);
                break;
            case 'contract_expected_price':
                setValuesAndPayload(type, `
                Elabore detalhadamente um levantamento de preço para o processo descrito
                no texto "${base.process?.description}" em plain text
            `);
                break;
            case 'market_survey':
                setValuesAndPayload(type, `
                Elabore detalhadamente uma lista de itens e seus orçamentos aproximados para suprir as necessidades
                do processo descrito no texto "${base.process?.description}" em plain text
            `);
                break;
            case 'solution_full_description':
                setValuesAndPayload(type, `
                Elabore detalhadamente uma descrição completa para a solução do processo descrito em "${base.process?.description}" em plain text"
            `);
                break;
            case 'ambiental_impacts':
                setValuesAndPayload(type, `
                Elabore um levantamento de possíveis impactos ambientais
                na execução do processo descrito em "${base.process?.description}" em plain text
            `);
                break;
            case 'correlated_contracts':
                if (!page.data.correlated_contracts) {
                    emit('callAlert', notifys.info('Digite o contrato correlato'))
                    return
                }
                setValuesAndPayload(type, `
                Elabore detalhadamente uma descrição de correlação entre o processo descrito em "${base.process?.description}"
                e na breve descrição "${page.data.correlated_contracts}" em plain text
                `);
                break;
            case 'object_execution_model':
                if (!page.data.object_execution_model) {
                    emit('callAlert', notifys.info('Digite uma breve descrição'))
                    return
                }
                setValuesAndPayload(type, `
                Elabore detalhadamente um modelo de execução para o processo descrito em "${base.process?.description}"
                e na breve descrição "${page.data.object_execution_model ?? ''}" em plain text
            `);
                break;
            case 'contract_management_model':
                setValuesAndPayload(type, `
                Elabore uma descrição de um modelo de gerenciamento de contrato para o processo descrito em "${base.process?.description}"
                e na breve descrição "${page.data.contract_management_model ?? ''}" em plain text"
            `);
                break;
            case 'payment_measure_criteria':
                setValuesAndPayload(type, `
                Elabore uma critério de medida do pagamento do processo descrito no texto "${base.process?.description}"
                e na breve descrição "${page.data.payment_measure_criteria ?? ''}" em plain text"
            `);
                break;
            case 'supplier_selection_criteria':
                setValuesAndPayload(type, `
                Elabore uma critério de seleção para o fornecedor do contrato descrito no texto "${base.process?.description}"
                e na breve descrição "${page.data.supplier_selection_criteria ?? ''}" em plain text"
            `);
                break;
            case 'funds_suitability':
                setValuesAndPayload(type, `
                Elabore uma critério de seleção para o fornecedor do contrato descrito no texto "${base.process?.description}"
                e na breve descrição "${page.data.funds_suitability ?? ''}" em plain text"
            `);
                break;
            case 'parts_obligation':
                setValuesAndPayload(type, `
                Elabore uma levantamento das obrigações das partes para os fornecedores e os contratadores
                do processo descrito no texto "${base.process?.description}"
                e na breve descrição "${page.data.parts_obligation ?? ''}" em plain text"
            `);
                break;
            default:
                break;
        }

        gpt.generate(`${page.url}/generate`, payload, emit, callresp)
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
                        <h2>Termos de Referência</h2>
                        <p>
                            Termos de Referência para os processos do
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
                            <VueDatePicker auto-apply v-model="page.search.date_ini" :enable-time-picker="false"
                                :auto-position="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                                menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_fin" class="form-label">Data Final</label>
                            <VueDatePicker auto-apply v-model="page.search.date_fin" :enable-time-picker="false"
                                :auto-position="false" format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                                menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-8">
                            <label for="s-necessity" class="form-label">Necessidade</label>
                            <input type="text" name="necessity" class="form-control" id="s-necessity"
                                v-model="page.search.necessity" placeholder="Pesquise por partes da necessidade" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-type" class="form-label">Tipo</label>
                            <select name="type" class="form-control" id="s-status" v-model="page.search.type">
                                <option value=""></option>
                                <option v-for="o in page.selects.types" :key="o.id" :value="o.id">
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
                        Actions.Edit((id) => { pageData.update(id) }),
                        Actions.Delete(pageData.remove),
                        Actions.Export('document-text-outline', export_refterm),
                    ]" :mounts="{
                        type: [Mounts.Cast(page.selects.types)],
                        necessity: [Mounts.StripHTML(), Mounts.Truncate(200)],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Termo de Refência</h2>
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
                    <TabNav :tabs="tabs" identify="tabbed" />
                    <form @submit.prevent="pageData.save({ process_id: page.data.process?.id })">
                        <!-- tab processo -->
                        <div class="tab-pane fade row m-0 g-3" :class="{ 'show active': tabs.is('process') }">
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
                                        aria-labelledby="accordion-processHeadId" data-bs-parent="#accordion-process">
                                        <div class="accordion-body p-0 m-0">
                                            <div class="p-4 pt-0 mx-2">
                                                <div class="dashed-separator mb-3"></div>
                                                <div class="row g-3">
                                                    <div class="col-sm-12 col-md-4">
                                                        <label for="date_s_ini" class="form-label">Data
                                                            Inicial</label>
                                                        <VueDatePicker auto-apply v-model="page.process.search.date_i"
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
                                                        <VueDatePicker auto-apply v-model="page.process.search.date_f"
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
                                                            id="s-description" v-model="page.process.search.description"
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
                                                <TableListRadio secondary @onChange="fetchEtp" identify="process"
                                                    v-model="page.data.process" :header="page.process.headers"
                                                    :body="page.process.data" :mounts="{
                                                        status: [Mounts.Cast(page.selects.process_status), Mounts.Status()],
                                                        description: [Mounts.Truncate(200)],
                                                    }" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab dfds -->
                        <div class="tab-pane fade row m-0 g-3" :class="{ 'show active': tabs.is('dfds') }">
                            <div class="m-0 mb-4 text-center">
                                <p class="m-0">Valor Estimado Total</p>
                                <h1 class="m-0 txt-color">
                                    {{ utils.floatToCurrency(page.data.estimated_budget) }}
                                </h1>
                            </div>
                            <div v-if="page.data.process" class="m-0 p-0">
                                <TableList :count="false" :header="page.dfd.headers" :body="page.data.process.dfds"
                                    :mounts="{
                                        status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
                                        description: [Mounts.Truncate(128)]
                                    }" :actions="[
                                        Actions.ModalDetails(dfd_details),
                                    ]" />
                            </div>
                            <div class="content py-3 m-0" v-else>
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
                        <div class="tab-pane fade content row m-0 p-4 pt-1 g-3"
                            :class="{ 'show active': tabs.is('info') }">
                            <div class="col-sm-12 col-md-4">
                                <label for="emission" class="form-label">Emissão</label>
                                <VueDatePicker auto-apply v-model="page.data.emission"
                                    :input-class-name="page.valids.emission ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                    :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                    :auto-position="false" locale="pt-br" calendar-class-name="dp-custom-calendar"
                                    calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="type" class="form-label">Tipo</label>
                                <select name="type" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.type }" id="type"
                                    v-model="page.data.type">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.types" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="comission" class="form-label">Comissão</label>
                                <select name="comission" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.comission_id }" id="comission"
                                    v-model="page.data.comission_id">
                                    <option value=""></option>
                                    <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">
                                        {{ o.title }}
                                    </option>
                                </select>
                                <div id="comissionHelpBlock" class="form-text txt-color-sec">
                                    Ao selecionar a comissão/equipe de planejamento seus
                                    integrantes serão vinculados ao documento
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="necessity" class="form-label d-flex justify-content-between">
                                    Descrição Detalhada da Necessidade
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('necessity')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.necessity" placeholder="Descrição da Necessidade"
                                    identifier="necessity" v-model="page.data.necessity" />
                            </div>
                            <div class="col-12">
                                <label for="contract_requirements" class="form-label d-flex justify-content-between">
                                    Requisitos da Contratação
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('contract_requirements')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.contract_requirements"
                                    placeholder="Descrição dos Requisitos da Contratação"
                                    identifier="contract_requirements" v-model="page.data.contract_requirements" />
                            </div>
                        </div>
                        <div class="tab-pane fade content row m-0 p-4 pt-1 g-3"
                            :class="{ 'show active': tabs.is('solucao') }">
                            <div class="col-12">
                                <label for="market_survey"
                                    class="form-label d-flex justify-content-between">Levantamento de Mercado e
                                    Justificativa Técnica e Econômica
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('market_survey')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.market_survey" placeholder="Levantamento de Mercado"
                                    identifier="market_survey" v-model="page.data.market_survey" />
                            </div>
                            <div class="col-12">
                                <label for="solution_full_description"
                                    class="form-label d-flex justify-content-between">Descrição da Solução como um todo
                                    e Justificativa da Compra
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
                                <label for="contract_expected_price"
                                    class="form-label d-flex justify-content-between">Estimativa do Preço da
                                    Contratação
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('contract_expected_price')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.contract_expected_price"
                                    placeholder="Estimativa do Preço da Contratação"
                                    identifier="contract_expected_price" v-model="page.data.contract_expected_price" />
                            </div>
                        </div>
                        <div class="tab-pane fade content row m-0 p-4 pt-1 g-3"
                            :class="{ 'show active': tabs.is('exec') }">
                            <div class="col-12">
                                <label for="supplier_selection_criteria"
                                    class="form-label d-flex justify-content-between">Forma e critérios de seleção do
                                    fornecedor
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('supplier_selection_criteria')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.supplier_selection_criteria"
                                    placeholder="Forma e critérios de seleção do fornecedor"
                                    identifier="supplier_selection_criteria"
                                    v-model="page.data.supplier_selection_criteria" />
                            </div>
                            <div class="col-12">
                                <label for="contract_forecast" class="form-label d-flex justify-content-between">
                                    Alinhamento com o Planejamento Anual
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('contract_forecast')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.contract_forecast"
                                    placeholder="Previsão de Realização da Contratação" identifier="contract_forecast"
                                    v-model="page.data.contract_forecast" />
                            </div>
                            <div class="col-12">
                                <label for="ambiental_impacts"
                                    class="form-label d-flex justify-content-between">Previsão de Resultados e Impactos
                                    Ambientais
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('ambiental_impacts')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.ambiental_impacts"
                                    placeholder="Previsão de Resultados e Impactos Ambientais"
                                    identifier="ambiental_impacts" v-model="page.data.ambiental_impacts" />
                            </div>
                        </div>
                        <div class="tab-pane fade content row m-0 p-4 pt-1 g-3"
                            :class="{ 'show active': tabs.is('pagamentos') }">
                            <div class="col-12">
                                <label for="funds_suitability"
                                    class="form-label d-flex justify-content-between">Adequação orçamentária
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('funds_suitability')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.funds_suitability"
                                    placeholder="Adequação orçamentária" identifier="funds_suitability"
                                    v-model="page.data.funds_suitability" />
                            </div>
                            <div class="col-12">
                                <label for="payment_measure_criteria"
                                    class="form-label d-flex justify-content-between">Critérios de medição e de
                                    pagamento
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('payment_measure_criteria')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.payment_measure_criteria"
                                    placeholder="Critérios de medição e de pagamento"
                                    identifier="payment_measure_criteria"
                                    v-model="page.data.payment_measure_criteria" />
                            </div>
                        </div>
                        <div class="tab-pane fade content row m-0 p-4 pt-1 g-3"
                            :class="{ 'show active': tabs.is('gestao') }">
                            <div class="col-12">
                                <label for="contract_management_model"
                                    class="form-label d-flex justify-content-between">Modelo de gestão do contrato
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('contract_management_model')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.contract_management_model"
                                    placeholder="Modelo de gestão do contrato" identifier="contract_management_model"
                                    v-model="page.data.contract_management_model" />
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
                                    identifier="correlated_contracts" v-model="page.data.correlated_contracts" />
                            </div>
                        </div>
                        <div class="tab-pane fade content row m-0 p-4 pt-1 g-3"
                            :class="{ 'show active': tabs.is('fiscalizacao') }">
                            <div class="col-12">
                                <label for="parts_obligation"
                                    class="form-label d-flex justify-content-between">Obrigações das partes
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('parts_obligation')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.parts_obligation" placeholder="Ogrigações das partes"
                                    identifier="parts_obligation" v-model="page.data.parts_obligation" />
                            </div>
                            <div class="col-12">
                                <label for="object_execution_model"
                                    class="form-label d-flex justify-content-between">Modelo de execução do objeto
                                    <a href="#" class="a-ia d-flex align-items-center gap-1"
                                        @click="generate('object_execution_model')">
                                        <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                </label>
                                <InputRichText :valid="page.valids.object_execution_model"
                                    placeholder="Modelo de execução do objeto" identifier="object_execution_model"
                                    v-model="page.data.object_execution_model" />
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
            <DfdDetails :dfd="page.dfd.data" :selects="page.selects" />
            <FooterMainUi />
        </main>
    </div>
</template>