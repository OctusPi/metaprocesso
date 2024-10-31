<script setup>

import { createApp, inject, onMounted, watch, toRaw } from 'vue';

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

const sysapp = inject('sysapp')

const emit = defineEmits(['callAlert', 'callUpdate', 'callRemove'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/etps',
    datalist: props.datalist,
    generate: {},
    header: [
        { key: 'emission', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { title:'PROCESSO', key:'process.protocol' },
        { title: 'NECESSIDADE', sub: [{ key: 'necessity' }] },
        { key: 'status', title: 'STATUS' },
    ],
    rules: {},
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
    },
    attachments: {}
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
    revisor: { title: 'Revisar' },
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
                url: `/attachments/${page.selects.vars?.ORIGIN_ETP}/${etp?.protocol}/download/${item.id}`,
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

function save_etp(mode = null) {

    const baseRules = {
        process: 'required',
        comission_id: 'required',
        emission: 'required'
    }

    const fullRules = {
        protocol: 'required',
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
        viability_declaration: 'required'
    }

    page.rules = mode === 'partial' ? baseRules : { ...baseRules, ...fullRules }

    Object.keys(page.valids).forEach(k => {
        page.valids[k] = false
    })

    pageData.save({ process_id: page.data.process?.id })
}

function generate(type) {
    if (!page.data.process) {
        return emit('callAlert', notifys.warning('Necessário selecionar o processo...'))
    }

    const base = {
        organ: page.organ,
        comission: page.selects.comissions?.find(o => o.id === page.data.comission_id),
        object_description: page.data?.object_description,
        process: page.data.process,
        dfds_quantity: (page.data?.process?.dfds ?? []).length,
        dfds_global: (page.data?.process?.dfds ?? []).reduce((a, i) =>
            a += utils.currencyToFloat(i.estimated_value)
            , 0)
    }

    let callresp, payload = null

    function setValuesAndPayload(key, mPayload) {
        payload = mPayload
        callresp = (resp) => {
            page.data[key] = `<p>${resp?.data?.choices[0]?.message.content ?? ''}</p>`
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
            Elabore a categorização e descrição detalhada do bem,
            serviço ou obra que será adquirido ou contratado com base nessa descrição:
            ${base.process?.description}. Leve em consideração as seguintes palavras chaves: ${page.data.object_classification}. 
            Siga esse padrão na elaboração da resposta: 
            materiais de limpeza, objeto da contratação, se enquadra como bem comum, 
            uma vez que os padrões de desempenho e qualidade estão objetivamente definidos 
            em conformidade com o com o artigo 20° da Lei 14.133/2021. Não se enquadra 
            como sendo de bem de natureza luxuosa, pois os padrões de desempenho e qualidade 
            podem ser objetivamente definidos, por meio de especificações usuais de mercado. 
            retorne a resposta em um único parágrafo em plain text.
        `);
            break;
        case 'necessity':
            setValuesAndPayload(type, `
            Descreva a necessidade da contratação ou aquisição baseado na descrição do objeto
            do processo: '${base.process?.description}'. Leve em consideração as palavras chave: '${page.data.necessity}'.
            Use como base esse texto para elaboração da resposta seguindo os padrões identificados: A aquisição futura de 
            materiais de limpeza é necessária, pois os itens que compõem essa contratação são indispensáveis à 
            operacionalização para a não interrupção das atividades nas unidades, durante o ano letivo e administrativo, 
            haja vista que são materiais de suma importância, utilizados para manutenção da limpeza e higienização 
            das dependências dos órgãos públicos do município XXXXXX. Os itens listados foram selecionados 
            por cada secretaria, garantindo a disponibilidade dos materiais comuns para o contínuo andamento das 
            rotinas administrativas nos setores vinculados. Vale ressaltar que esta aquisição é fundamental para 
            uma administração eficiente e deve atender a diversos usuários, tais como autoridades, funcionários, 
            visitantes e comunidade em geral. Tendo isso em vista, tais objetos comuns precisam estarem disponíveis, 
            conforme o aumento das demandas (previsíveis ou imprevisíveis) nesses setores. Retorne a reposta em um único parágrafo plain text.
        `);
            break;
        case 'contract_forecast':
            setValuesAndPayload(type, `
            Com base nesse objeto: '${base.object_description}'. E referenciado essa previsão data de contratação: 
            ${page.data.process?.date_hour_ini}. Elabore um texto descrevendo a previsão da data que será realizado o contrato.
            Retorne em plain text
        `);
            break;
        case 'contract_requirements':
            setValuesAndPayload(type, `
            De acordo com a lei de licitações do Brasil, faça a descrição dos requisitos de contração para o objeto:
            '${base.object_description}'. Elabore os requisitos de acordo com o tipo de item que está sendo contratado.
            Leve em consideração as seguintes palavras chaves: ${page.data.object_description}. Retorne a resposta em plain text.
        `);
            break;
        case 'market_survey':
            setValuesAndPayload(type, `
            Com base nesse objeto de contratação:'${base.process?.description}'. Elabore um texto com possiveis alternativas de mercado 
            para contratação leve em consideração formas de oportunidades de mercado das contratações públicas governamentais, 
            selecione e justifique a alternativa mais viável dentro do cenário. Leve em conta as seguintes palavras chave: ${page.data.market_survey}.
            Retorne em plain text.
        `);
            break;
        case 'solution_full_description':
            setValuesAndPayload(type, `
            Com base na solução de mercado escolhida nesse texto:'${page.data.market_survey}'. 
            E levando em consideração as palavras chave:'${page.data.solution_full_description}'.
            Elabore um texto que descreva e justifique a solução escolhida. Retorne em plain text.
        `);
            break;
        case 'contract_calculus_memories':
            setValuesAndPayload(type, `
            Crie texto fictício levando em consideração a lei de licitações do brasil 14.133/2.021,
            que justifique a Estimativa das Quantidades Contratadas nesse processo: ${base.process?.description}.
            Leve em consideração essas quantidades: ${JSON.stringify(page.gerate.dfds_items_data)}
            Retorne a respota em um único parágrafo em plain text.
        `);
            break;
        case 'contract_expected_price':
            setValuesAndPayload(type, `
            Elabore um texto descrito informando que os orçamentos para o processo: ${base.process?.description}.
            Foram realizados através do sistema de gerenciamento das cotações de preços e compras governamentais
            para Prefeituras e diversos órgãos Públicos chamado Metaprocesso. Nele foram realizadas consultas avançadas de itens em cestas 
            de preços, obtidas através de contratações semelhantes. Nele foram inseridos alguns filtros padrões, 
            que permite uma gestão eficaz e inteligente, dentre eles, os de maior destaque para este relatório foi 
            a utilização da média aritmética dos ${parseInt(page.generate?.proposals_data?.total_email ?? 0) + parseInt(page.generate?.proposals_data?.total_manual ?? 0)} orçamentos, a abrangência local, considerando a classificação do 
            objeto e o banco de preço do último ano, já que esses preços devem ser atuais, “preços de mercado”. 
            O valor estimado global é de ${parseFloat(page.generate?.proposals_data?.global_value ?? 0) / (parseInt(page.generate?.proposals_data?.total_email ?? 0) + parseInt(page.generate?.proposals_data?.total_manual ?? 0))}. 
            (escreva os valores citados na resposta em formato de moeda pt_br e por extenso). Retorne em apenas um parágrafo plain text.
        `);
            break;
        case 'correlated_contracts':
            setValuesAndPayload(type, `
            O processo: ${base.process?.description}. Possuí um total de ${base.dfds_quantity} documentos de
            formalização de demanda provenientes de ${base.dfds_quantity} entidades vinculadas ao orgão ${page.organ_name}.
            Se o número de DFDs informado no texto for maior que 01 afirme que existe Contratações Correlatas e/ou Interdependentes do 
            contrário negue a existencia de Contratações Correlatas e/ou Interdependentes.
            Retorne a resposta em um único parágrafo e não mecione o número de DFDs na sua afirmação.
        `);
            break;
        case 'contract_alignment':
            setValuesAndPayload(type, `
            Crie um texto afirmando que o processo de contratação: ${base.process?.description} está em conformidade 
            planejamento orçamentário do Plano Anual de Contratações (PPA) do orgão: ${page.organ_name}.
            Retorne apenas em um parágrafo em plain text.
        `);
            break;
        case 'expected_results':
            setValuesAndPayload(type, `
            Crie um texto descritivo com os resultados positivos esperados para um processo
            de contratação de: ${base.process?.description}. Leve em consideração essas palavras chave: ${page.data.expected_results}
            Retorne apenas em um parágrafo em plain text.
        `);
            break;
        case 'contract_previous_actions':
            setValuesAndPayload(type, `
            Crie um texto descritivo com as providências a serem tomadas para a contratação: ${base.process?.description}.
            Leve em consideração as palavras chave: ${page.data.contract_previous_actions}. Use esse texto como referências
            identificando seus padrões: 'Conforme especificações e quantitativos relacionados no presente estudo deverá ter 
            vigência pelo período de 12 meses, podendo ser prorrogado até 10 (dez) anos na forma dos artigos 
            106 e 107 da Lei n° 14.133, de 2021, e o objeto deve ser formalizado em contrato, considerando que a 
            demanda é recorrente, cuja interrupção pode provocar prejuízos às atividades das unidades demandantes. 
            Vale ressaltar que a demanda de materiais de limpeza e higiene foi incluída no Plano de Contratação Anual 
            e está alinhada com os objetivos das unidades administrativas. Por fim, deverá constar no contrato o ordenador 
            de despesas e o fiscal de contrato, conforme legislação pertinente.' Retorne apenas em um parágrafo em plain text.
        `);
            break;
        case 'ambiental_impacts':
            setValuesAndPayload(type, `
            Crie um texto descritivo com os possíveis impactos ambientais esperados para um processo
            de contratação de: ${base.process?.description}. Leve em consideração essas palavras chave: ${page.data.ambiental_impacts}
            Retorne apenas em um parágrafo em plain text.
        `);
            break;
        default:
            break;
    }

    gpt.generate(`${page.url}/generate`, payload, emit, callresp)
}

function improve_generate(key) {
    if (!page.data[key]) {
        return emit('callAlert', notifys.warning('Informe algum conteúdo para que a I.A revise e aprimore.'))
    }

    const payload = `Revise e aprimore esse texto: ${page.data[key]}`
    const callresp = (resp) => {
        page.data[key] = `<p>${resp?.data?.choices[0]?.message.content ?? ''}</p>`
    };

    gpt.generate(`${page.url}/generate`, payload, emit, callresp)
}

function fetch_process(e) {
    http.post(`${page.url}/${page.data.id ?? 0}/fetch_process/${e.target._value?.id}`, {}, emit, (res) => {
        page.data.solution_parcel_justification = res.data?.process?.installment_justification
        page.data.object_description = res.data?.process?.description
        page.generate.proposals_data = res.data?.proposals
        page.generate.dfds_items_data = res.data?.dfds_items

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

watch(() => page.ui.register, (newUi) => {
    if (newUi && page.data.id == null) {
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
                        necessity: [Mounts.StripHTML(), Mounts.Truncate()],
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
                    <form @submit.prevent="null">
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
                                                        @onChange="fetch_process" :body="page.process.data" :mounts="{
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
                                    <div class="col-sm-12 col-md-6">
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
                                    <div class="col-sm-12 col-md-3">
                                        <label for="emission" class="form-label">Emissão</label>
                                        <VueDatePicker auto-apply v-model="page.data.emission"
                                            :input-class-name="page.valids.emission ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                                            :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy"
                                            :auto-position="false" locale="pt-br"
                                            calendar-class-name="dp-custom-calendar"
                                            calendar-cell-class-name="dp-custom-cell"
                                            menu-class-name="dp-custom-menu" />
                                    </div>
                                    <div class="col-sm-12 col-md-3">
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
                                    <div class="col-12">
                                        <label for="object_description"
                                            class="form-label d-flex justify-content-between">
                                            Descrição sucinta do objeto

                                        </label>
                                        <InputRichText :valid="page.valids.object_description"
                                            placeholder="Descrição do Objeto" identifier="object_description"
                                            v-model="page.data.object_description" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Classificação do objeto
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('object_classification')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('object_classification')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
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
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Necessidade
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('necessity')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('necessity')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.necessity"
                                            placeholder="Descrição da Necessidade" identifier="necessity"
                                            v-model="page.data.necessity" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Descrição dos requisitos de Contratação
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('contract_requirements')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('contract_requirements')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_requirements"
                                            placeholder="Descrição dos Requisitos da Contratação"
                                            identifier="contract_requirements"
                                            v-model="page.data.contract_requirements" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Previsão de Realização da Contratação
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('contract_forecast')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('contract_forecast')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
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
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            levantamento de Mercado
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('market_survey')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('market_survey')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.market_survey"
                                            placeholder="Levantamento de Mercado" identifier="market_survey"
                                            v-model="page.data.market_survey" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Descrição da solução como um todo
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('solution_full_description')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('solution_full_description')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.solution_full_description"
                                            placeholder="Descrição da Solução como um Todo"
                                            identifier="solution_full_description"
                                            v-model="page.data.solution_full_description" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Estimativas das quantidades contratadas
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('contract_calculus_memories')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('contract_calculus_memories')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_calculus_memories"
                                            placeholder="Estimativa das Quantidades Contratadas"
                                            identifier="contract_calculus_memories"
                                            v-model="page.data.contract_calculus_memories" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Estimativas de preço da contratação
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('contract_expected_price')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('contract_expected_price')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_expected_price"
                                            placeholder="Estimativa do Preço da Contratação"
                                            identifier="contract_expected_price"
                                            v-model="page.data.contract_expected_price" />
                                    </div>
                                    <div class="col-12">
                                        <label for="solution_parcel_justification"
                                            class="form-label d-flex justify-content-between">Justificativa Parcelamento

                                        </label>
                                        <InputRichText :valid="page.valids.solution_parcel_justification"
                                            placeholder="Justificativa para o Parcelamento ou Não"
                                            identifier="solution_parcel_justification"
                                            v-model="page.data.solution_parcel_justification" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Contratações correlatas ou interdependentes
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('correlated_contracts')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('correlated_contracts')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
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
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Resultados pretendidos
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('expected_results')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('expected_results')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.expected_results"
                                            placeholder="Resultados Pretendidos" identifier="expected_results"
                                            v-model="page.data.expected_results" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Providências a serem tomadas
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('contract_previous_actions')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('contract_previous_actions')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_previous_actions"
                                            placeholder="Providências a Serem Tomadas"
                                            identifier="contract_previous_actions"
                                            v-model="page.data.contract_previous_actions" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Alinhamento de contrato
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('contract_alignment')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('contract_alignment')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
                                        </label>
                                        <InputRichText :valid="page.valids.contract_alignment"
                                            placeholder="Alinhamento de Contrato" identifier="contract_alignment"
                                            v-model="page.data.contract_alignment" />
                                    </div>
                                    <div class="col-12">
                                        <label for="object_classification"
                                            class="form-label d-md-flex justify-content-between">
                                            Possíveis impactos ambientais
                                            <div class="d-flex">
                                                <a href="#" class="a-ia d-flex align-items-center gap-1 me-3"
                                                @click="generate('ambiental_impacts')">
                                                <ion-icon name="hardware-chip-outline" /> Gerar com I.A</a>
                                                <a href="#" class="a-ia d-flex align-items-center gap-1"
                                                @click="improve_generate('ambiental_impacts')">
                                                <ion-icon name="sparkles-outline" /> Aprimorar com I.A</a>
                                            </div>
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
                                    @clone="(data) => { page.attachments = toRaw(data) }"
                                    :origin="String(page.selects.vars?.ORIGIN_ETP)" :protocol="page.data.protocol" />
                            </div>

                            <div id="revisor" class="tab-pane">
                                <div class="content p-4">
                                    <div class="box-revisor">
                                        <div class="box-revisor-title d-flex mb-4">
                                            <div class="txt-revisor-title">
                                                <h3>Informações</h3>
                                                <p>Informações gerais do Estudo Técnico Preliminar</p>
                                            </div>
                                        </div>
                                        <div class="box-revisor-content">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h4>Processo</h4>
                                                    <p>{{ page.data.process?.protocol ?? '*****' }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4>Comissão</h4>
                                                    <p>{{ utils.getTxt(
                                                        page.selects.comissions,
                                                        page.data.comission_id
                                                    ) }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4>Emissão</h4>
                                                    <p>{{ page.data.emission ?? '*****' }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4>Tipo de Parcelamento</h4>
                                                    <p>{{ utils.getTxt(
                                                        page.selects.installment_types,
                                                        page.data.installment_type
                                                    ) }}</p>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-12">
                                                    <h4>Descrição sucinta do objeto</h4>
                                                    <p v-html="page.data.object_description ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Classificação do objeto</h4>
                                                    <p v-html="page.data.object_classification ?? '*****'"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-revisor mt-4">
                                        <div class="box-revisor-title d-flex mb-4">
                                            <div class="txt-revisor-title">
                                                <h3>Necessidade</h3>
                                                <p>O que motivou a realização do ETP</p>
                                            </div>
                                        </div>
                                        <div class="box-revisor-content">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4>Necessidade</h4>
                                                    <p v-html="page.data.necessity ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Descrição dos requisitos da contratação</h4>
                                                    <p v-html="page.data.contract_requirements ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Previsão de Realização da Contratação</h4>
                                                    <p v-html="page.data.contract_forecast ?? '*****'"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-revisor mt-4">
                                        <div class="box-revisor-title d-flex mb-4">
                                            <div class="txt-revisor-title">
                                                <h3>Solução</h3>
                                                <p>Como serão resolvidos os problemas</p>
                                            </div>
                                        </div>
                                        <div class="box-revisor-content">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4>Levantamento de Mercado </h4>
                                                    <p v-html="page.data.market_survey ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Descrição da Solução como um Todo </h4>
                                                    <p v-html="page.data.solution_full_description ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Estimativa das Quantidades Contratadas</h4>
                                                    <p v-html="page.data.contract_calculus_memories ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Estimativa do Preço da Contratação</h4>
                                                    <p v-html="page.data.contract_expected_price ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Justificativa para o Parcelamento ou Não</h4>
                                                    <p v-html="page.data.solution_parcel_justification ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Contratações Correlatas e/ou Interdependentes</h4>
                                                    <p v-html="page.data.correlated_contracts ?? '*****'"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-revisor mt-4">
                                        <div class="box-revisor-title d-flex mb-4">
                                            <div class="txt-revisor-title">
                                                <h3>Planejamento</h3>
                                                <p>Passos a serem tomados pelo ETP</p>
                                            </div>
                                        </div>
                                        <div class="box-revisor-content">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4>Resultados Pretendidos</h4>
                                                    <p v-html="page.data.expected_results ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Providências a Serem Tomadas</h4>
                                                    <p v-html="page.data.contract_previous_actions ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Alinhamento de Contrato</h4>
                                                    <p v-html="page.data.contract_alignment ?? '*****'"></p>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Possíveis Impactos Ambientais</h4>
                                                    <p v-html="page.data.ambiental_impacts ?? '*****'"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-revisor mt-4">
                                        <div class="box-revisor-title d-flex mb-0">
                                            <div class="txt-revisor-title">
                                                <h3>Viabilidade</h3>
                                                <p>Declaração de viabilidade</p>
                                            </div>
                                        </div>
                                        <div class="box-revisor-content">
                                            <div class="row">
                                                <div class="col-12">
                                                    <p
                                                        :class="page.data.viability_declaration == 1 ? 'text-success' : 'text-danger'">
                                                        {{
                                                            page.data.viability_declaration != null &&
                                                                page.data.viability_declaration == 1
                                                                ? `Esta
                                                        equipe de
                                                        planejamento
                                                        declara viável esta contratação com base neste ETP, consoante o
                                                        inciso
                                                        XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.`
                                                                : `
                                                        Esta equipe
                                                        de
                                                        planejamento
                                                        declara inviável esta contratação com base neste ETP,
                                                        consoante o inciso
                                                        XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.
                                                        `
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-revisor mb-4">
                                        <div class="box-revisor-title mb-4">
                                            <h3>Anexos</h3>
                                            <p>
                                                Lista de arquivos anexados
                                            </p>
                                        </div>
                                        <div>
                                            <TableList secondary :header="page.attachments.header"
                                                :body="page.attachments.datalist" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button @click="save_etp" type="button" class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button @click="save_etp('partial')" type="button" class="btn btn-action-secondary">
                                <ion-icon name="folder-open-outline" class="fs-5"></ion-icon>
                                Salvar Parcialmente
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

            <FooterMainUi />
        </main>
    </div>
    <DfdDetails :dfd="page.dfd.data" :selects="page.selects" />
</template>