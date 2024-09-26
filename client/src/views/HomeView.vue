<script setup>
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import { onMounted, reactive } from 'vue';
import http from '@/services/http';

const emit = defineEmits('callAlert')

const statusMethods = {
    chartOptions: (categories) => ({
        chart: {
            id: 'dfds',
            width: "100%",
            height: 220,
            offsetX: -8,
            fontFamily: 'Inter, Helvetica, Arial',
            toolbar: {
                show: false
            }
        },
        colors: ['var(--color-base)'],
        grid: {
            show: false
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
            enabled: true,
            textAnchor: 'middle',
            formatter: function (_, opt) {
                return opt.w.globals.labels[opt.dataPointIndex]
            },
        },
        yaxis: {
            labels: {
                show: false
            }
        },
        xaxis: {
            categories: categories,
            stepSize: 1
        }
    }),
    series: (series) => [
        {
            name: "Quantidade",
            data: series
        },
    ]
}

const dfds = reactive({
    chartOptions: statusMethods.chartOptions([]),
    series: statusMethods.series([]),
})

const processes = reactive({
    chartOptions: statusMethods.chartOptions([]),
    series: statusMethods.series([]),
})

const prices = reactive({
    chartOptions: statusMethods.chartOptions([]),
    series: statusMethods.series([]),
})

onMounted(() => {
    http.post('/home/list', {}, emit, (res) => {
        dfds.series = statusMethods.series(Object.values(res.data.dfds))
        dfds.chartOptions = statusMethods.chartOptions(Object.keys(res.data.dfds))

        processes.series = statusMethods.series(Object.values(res.data.processes))
        processes.chartOptions = statusMethods.chartOptions(Object.keys(res.data.processes))

        prices.series = statusMethods.series(Object.values(res.data.prices))
        prices.chartOptions = statusMethods.chartOptions(Object.keys(res.data.prices))
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
                        <p> Panorama resultados ano corrente</p>
                    </div>
                </div>
                <div class="container p-0 p-md-4">
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="content">
                                <div class="p-4 pb-0">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="document" />
                                        </span>
                                        <div>
                                            <h1 class="m-0">DFDS</h1>
                                            <p class="m-0">DFDS por status</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <apexchart type="bar" :options="dfds.chartOptions" :series="dfds.series" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="content">
                                <div class="p-4">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="document-text" />
                                        </span>
                                        <div>
                                            <h1 class="m-0">Processos</h1>
                                            <p class="m-0">Processos por status</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-4">
                                    <apexchart type="bar" :options="processes.chartOptions"
                                        :series="processes.series" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="content">
                                <div class="p-4">
                                    <div class="chart-heading">
                                        <span>
                                            <ion-icon name="pricetags" />
                                        </span>
                                        <div>
                                            <h1 class="m-0">Coletas de Preços</h1>
                                            <p class="m-0">Coletas por Status</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-4">
                                    <apexchart type="bar" :options="prices.chartOptions" :series="prices.series" />
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
