<script setup>
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';

const dfds = {
    chartOptions: {
        chart: {
            width: "100%",
            fontFamily: 'Inter, Helvetica, Arial',
            toolbar: {
                show: false
            }
        },
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
            categories: [],
            labels: {
                show: false
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        }
    },
    series: [
        {
            name: "Quantidade",
            data: []
        },
    ]
}

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
                <div class="container p-4">
                    <div class="row">
                        <div class="col-4 content">
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
                            <apexchart type="bar" :options="dfds.chartOptions" :series="dfds.series" />
                        </div>
                        <div class="col-4">
                            Teste
                        </div>
                        <div class="col-4">
                            Teste
                        </div>
                    </div>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>
