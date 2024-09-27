<script setup>
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import { onMounted, ref } from 'vue';
import http from '@/services/http';

const emit = defineEmits('callAlert')

const dfds = ref({})
const processes = ref({})
const prices = ref({})

function populateDfds(dataset) {
    const keys = Object.keys(dataset)
    const values = Object.values(dataset)
    dfds.value = {
        total: values.length,
        series: [{
            name: 'Quantidade',
            data: values
        }],
        chartOptions: {
            chart: {
                id: 'dfds',
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
    processes.value = {
        total: values.length,
        series: values,
        chartOptions: {
            chart: {
                id: 'processes',
                width: "100%",
                type: 'polarArea',
                fontFamily: 'Inter, Helvetica, Arial',
                toolbar: {
                    show: false
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
    prices.value = {
        total: values.length,
        series: [{
            name: 'Quantidade',
            data: values,
        }],
        chartOptions: {
            chart: {
                id: 'prices',
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

onMounted(() => {
    http.post('/home/list', {}, emit, (res) => {
        populateDfds(res.data.dfds)
        populateProcesses(res.data.processes)
        populatePrices(res.data.prices)
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
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="content h-100 d-flex flex-column">
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
                                <div class="px-2" v-if="dfds.total > 0">
                                    <apexchart height="260" type="bar" :options="dfds.chartOptions" :series="dfds.series" />
                                </div>
                                <div v-else class="d-flex justify-content-center align-items-center h-100">
                                    <div class="text-center p-4">
                                        <ion-icon name="pricetags" class="fs-4" />
                                        <p>Sem coletas recentes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="content h-100 d-flex flex-column">
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
                                <div class="p-2" v-if="dfds.total > 0">
                                    <apexchart height="230" type="polarArea" :options="processes.chartOptions"
                                        :series="processes.series" />
                                </div>
                                <div v-else class="d-flex justify-content-center align-items-center h-100">
                                    <div class="text-center p-4">
                                        <ion-icon name="pricetags" class="fs-4" />
                                        <p>Sem coletas recentes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="content h-100 d-flex flex-column">
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
                                <div v-if="prices.total > 0">
                                    <apexchart height="270" type="radar" :options="prices.chartOptions" :series="prices.series" />
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
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>
