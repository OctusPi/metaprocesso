<script setup>
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import { onMounted, ref } from 'vue';
import http from '@/services/http';
import TableList from '@/components/table/TableList.vue';
import Mounts from '@/services/mounts';

const emit = defineEmits('callAlert')

const dfdsChart = ref({})
const processesChart = ref({})
const pricesChart = ref({})

function populateDfds(dataset) {
    const keys = Object.keys(dataset)
    const values = Object.values(dataset)
    dfdsChart.value = {
        total: values.length,
        series: [{
            name: 'Quantidade',
            data: values
        }],
        chartOptions: {
            chart: {
                id: 'dfdsChart',
                offsetX: -4,
                width: "100%",
                fontFamily: 'Inter, Helvetica, Arial',
                toolbar: {
                    show: false
                }
            },
            colors: ['var(--color-base)'],
            grid: {
                borderColor: 'var(--color-input-focus)',
                position: 'front',
                strokeDashArray: 7,
                sort: true
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    barHeight: "80%",
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false,
                textAnchor: 'start',
                formatter: function (_, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex]
                },
            },
            yaxis: {
                labels: {
                    color: 'var(--color-input-focus)',
                    show: true
                },
            },
            xaxis: {
                categories: keys,
                stepSize: 1,
                axisBorder: {
                    show: true,
                    color: 'var(--color-input-focus)',
                }
            }
        }
    }
}

function populateProcesses(dataset) {
    const keys = Object.keys(dataset)
    const values = Object.values(dataset)
    processesChart.value = {
        total: values.length,
        series: values,
        chartOptions: {
            chart: {
                id: 'processesChart',
                width: "100%",
                type: 'polarArea',
                fontFamily: 'Inter, Helvetica, Arial',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: false
                },
                sparkline: {
                    enabled: true
                }
            },
            theme: {
                monochrome: {
                    enabled: true,
                    shadeTo: 'light',
                    shadeIntensity: 0.6
                }
            },
            legend: {
                show: true,
                fontSize: '11px',
            },
            labels: keys
        },
    }
}

function populatePrices(dataset) {
    const keys = Object.keys(dataset)
    const values = Object.values(dataset)
    pricesChart.value = {
        total: values.length,
        series: [{
            name: 'Quantidade',
            data: values,
        }],
        chartOptions: {
            chart: {
                id: 'pricesChart',
                width: "100%",
                sparkline: {
                    enabled: true
                },
                fontFamily: 'Inter, Helvetica, Arial',
                toolbar: {
                    show: false
                }
            },
            colors: ['var(--color-base)'],
            fill: {
                colors: ['var(--color-base-tls)']
            },
            xaxis: {
                categories: keys,
                labels: {
                    show: true,
                    offsetY: 4
                }
            }
        },
    }
}

const processes = ref({
    datalist: [],
    selects: {},
    header: [
        { key: 'date_hour_ini', title: 'PROTOCOLO', sub: [{ key: 'protocol' }] },
        { key: 'modality', title: 'CLASSIFICAÇÃO', sub: [{ key: 'type' }] },
        { title: 'OBJETO', sub: [{ key: 'description' }] },
    ],
    complete_header: [
        { key: 'date_hour_ini', title: 'PROTOCOLO', sub: [{ key: 'protocol' }] },
        { key: 'dfds.protocol', title: 'DFDS' },
        { key: 'pricerecords', title: 'COLETAS', isBool: true },
        { key: 'etp', title: 'ETP', isBool: true },
        { key: 'riskmaps', title: 'MAPAS', isBool: true },
        { key: 'refterm', title: 'T. REFERÊNCIA', isBool: true },
        { key: 'proposal', title: 'PUBLICAÇÃO ', isBool: true },
    ],
})

const users = ref({
    datalist: [],
    selects: {},
    header: [
        { key: 'name', title: 'NOME', sub: [{ key: 'email' }] },
        { key: 'lastlogin', title: 'ÚLTIMO ACESSO', err: 'Não Acessado' },
    ],
})

onMounted(() => {
    http.post('/home/list', {}, emit, (res) => {
        populateDfds(res.data.dfds_chart)
        populateProcesses(res.data?.processes_chart)
        populatePrices(res.data?.prices_chart)
        processes.value.datalist = res.data?.processes?.datalist
        processes.value.selects = res.data?.processes?.selects
        users.value.datalist = res.data?.users?.datalist
    })
})

</script>

<template>
    <div class="page">
        <NavMainUi />
        <main class="main">
            <HeaderMainUi />
            <section class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Dashboard</h2>
                        <p>Panorama resultados ano corrente</p>
                    </div>
                </div>
                <div class="container p-0 p-md-4">
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-4">
                            <div class="content h-100 d-flex flex-column block">
                                <div class="p-4 pb-0">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="document" />
                                        </span>
                                        <div>
                                            <h1>DFDs</h1>
                                            <p>DFDs por situação</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-2 my-auto" v-if="dfdsChart.total > 0">
                                    <apexchart height="260" type="bar" :options="dfdsChart.chartOptions"
                                        :series="dfdsChart.series" />
                                </div>
                                <div v-else class="d-flex justify-content-center align-items-center h-100">
                                    <div class="text-center p-4">
                                        <ion-icon name="document" class="fs-4" />
                                        <p>Sem DFDs recentes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-4">
                            <div class="content h-100 d-flex flex-column block">
                                <div class="p-4 pb-0">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="document-text" />
                                        </span>
                                        <div>
                                            <h1>Processos</h1>
                                            <p>Processos por situação</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2 my-auto" v-if="dfdsChart.total > 0">
                                    <apexchart height="230" type="polarArea" :options="processesChart.chartOptions"
                                        :series="processesChart.series" />
                                </div>
                                <div v-else class="d-flex justify-content-center align-items-center h-100">
                                    <div class="text-center p-4">
                                        <ion-icon name="document-text" class="fs-4" />
                                        <p>Sem processos recentes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-4">
                            <div class="content h-100 d-flex flex-column block">
                                <div class="p-4 pb-0">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="pricetags" />
                                        </span>
                                        <div>
                                            <h1>Coletas de Preços</h1>
                                            <p>Coletas por situação</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-auto" v-if="pricesChart.total > 0">
                                    <apexchart height="270" type="radar" :options="pricesChart.chartOptions"
                                        :series="pricesChart.series" />
                                </div>
                                <div v-else class="d-flex justify-content-center align-items-center h-100">
                                    <div class="text-center p-4">
                                        <ion-icon name="pricetags" class="fs-4" />
                                        <p class="">Sem coletas recentes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-8 mb-4">
                            <div class="content">
                                <div class="p-4">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="document-text" />
                                        </span>
                                        <div>
                                            <h1>Processos Recentes</h1>
                                            <p>Processos registrados recentemente</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-listage listage h-100"
                                    :class="[processes.datalist.length < 1 && 'd-flex align-items-center justify-content-center']">
                                    <TableList :header="processes.header" :body="processes.datalist" :mounts="{
                                        type: [Mounts.Cast(processes.selects.types)],
                                        modality: [Mounts.Cast(processes.selects.modalities)],
                                        description: [Mounts.Truncate()],
                                    }" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-4">
                            <div class="content">
                                <div class="p-4">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="people" />
                                        </span>
                                        <div>
                                            <h1>Usuários</h1>
                                            <p>Usuários recentes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-listage listage h-100"
                                    :class="[users.datalist.length < 1 && 'd-flex align-items-center justify-content-center']">
                                    <TableList :header="users.header" :body="users.datalist" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="content">
                                <div class="p-4">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="document-text" />
                                        </span>
                                        <div>
                                            <h1>Processos em Andamento</h1>
                                            <p>Listagem dos Processos em Andamento</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-listage listage h-100"
                                    :class="[processes.datalist.length < 1 && 'd-flex align-items-center justify-content-center']">
                                    <TableList :header="processes.complete_header" :body="processes.datalist" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped>
.block {
    min-height: 320px;
}

.listage {
    height: 280px !important;
    max-height: 280px !important;
}
</style>