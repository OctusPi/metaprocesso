<script setup>
import { ref, onBeforeMount } from 'vue'
import QrcodeVue from 'qrcode.vue';
import dates from '@/utils/dates'
import utils from '@/utils/utils'
import Mounts from "@/services/mounts"

import TableListReport from '@/components/table/TableListReport.vue'

const props = defineProps({
    qrdata: { type: Object, default: () => { } },
    organ: { type: Object, required: true },
    process: { type: Object, required: true },
    pricerecord: { type: Object, required: true },
    proposals: { type: Array, required: true },
    items: { type: Array, default: () => [] },
    selects: { type: Array, default: () => [] }
})

const report = ref({
    items: props.items,
    proposals: props.proposals,
    finished_proposals: [...props.proposals.filter(o => o.status == 4)],
    winner: {}
})

const headers = {
    proposals: [
        { title: 'FORNECEDOR.', key: 'supplier.name', err: 'TCE/PNCP/VAREJO', sub: [{ key: 'supplier.cnpj', err: '***', }] },
        { title: 'MODALIDADE', key: 'modality' },
        { title: 'CONTATO.', key: 'supplier.email', sub: [{ key: 'supplier.phone' }, { key: 'supplier.address' }] },
        { title: 'VALOR', key: 'global' },
        { title: 'DATA/HORA', key: 'date_ini', sub: [{ key: 'hour_ini' }] },
        { title: 'STATUS', key: 'status' }
    ],
    items: []
}

function check_minor_price(proposals, index) {
    const proposal = proposals.reduce((p1, p2) => {
        return utils.currencyToFloat(p2.items[index].value)
            < utils.currencyToFloat(p1.items[index].value)
            ? p2 : p1
    })

    return proposal.id
}

function define_moda(proposals, index) {

    let frequency_now = 1;
    let frequency_major = 1;
    let frequency_value = utils.currencyToFloat(proposals[0].items[index].value)
    let value_now = utils.currencyToFloat(proposals[0].items[index].value)

    for (let i = 1; i < proposals.length; i++) {

        if (utils.currencyToFloat(proposals[i].items[index].value) === value_now) {
            frequency_now++
        } else {
            if (frequency_now > frequency_major) {
                frequency_major = frequency_now;
                frequency_value = value_now;
            }

            value_now = utils.currencyToFloat(proposals[i].items[index].value);
            frequency_now = 1;
        }
    }

    if (frequency_now > frequency_major) {
        frequency_value = value_now;
    }

    return frequency_now > 1 || frequency_major > 1 ? frequency_value : 0;
}

function calcs() {

    const total_proposals = report.value.finished_proposals.length

    report.value.items.forEach((i, k) => {

        const sorted_proposals = [...report.value.finished_proposals].slice().sort((a, b) => {
            return utils.currencyToFloat(b.items[k].value) < utils.currencyToFloat(a.items[k].value)
        })

        const base_media = report.value.finished_proposals.reduce((acc, p) => {
            if (p.id === sorted_proposals[0].id) {
                p.winner = (p.winner ?? 0) + 1
            }
            return acc + utils.currencyToFloat(p.items[k].value)
        }, 0)

        const base_mediana = (total_proposals % 2) != 0
            ? utils.currencyToFloat(sorted_proposals[Math.floor(total_proposals / 2)].items[k].value)
            : ((utils.currencyToFloat(sorted_proposals[(total_proposals / 2) - 1].items[k].value) + utils.currencyToFloat(sorted_proposals[(total_proposals / 2)].items[k].value)) / 2).toFixed(2)

        const base_moda = define_moda(sorted_proposals, k)

        i.media = parseFloat(((base_media * i.quantity) / total_proposals).toFixed(2))
        i.mediana = parseFloat((base_mediana * i.quantity).toFixed(2))
        i.moda = parseFloat((base_moda * i.quantity).toFixed(2))
    });
}

function check_winner() {
    return report.value.finished_proposals.reduce((p1, p2) => {
        return p2.winner > p1.winner ? p2 : p1
    })
}

onBeforeMount(() => {
    calcs()
    report.value.winner = check_winner()
})

</script>

<template>
    <header class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="ct-logo me-2">
                <img :src="organ.logomarca" class="h-logo">
            </div>
            <div class="h-info">
                <h1>{{ organ.name }}</h1>
                <p>{{ organ.cnpj }}</p>
                <p>{{ organ.address }}</p>
                <p>{{ organ.phone }} {{ organ.email }}</p>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-2 text-end">
                <p class="p-0 m-0 x-small">{{ qrdata.url }}</p>
                <p class="p-0 m-0 x-small">{{ `${qrdata.name} - ${qrdata.version}` }}</p>
                <p class="p-0 m-0 x-small">{{ qrdata.copy }}</p>
                <p class="p-0 m-0 x-small">{{ dates.dateTxtNow() }}</p>
            </div>
            <qrcode-vue :value="qrdata.url" :size="parseInt('65')" level="H" />
        </div>
    </header>

    <main>
        <div class="my-4">
            <h1 class="text-center">MAPA DE PREÇOS</h1>
            <h2 class="text-center">
                {{ `${pricerecord.protocol ?? '*****'} - ${pricerecord.date_ini} - ${pricerecord.ip ?? '*****'}` }}
            </h2>
            <h2 class="text-center">
                {{ `Método de Seleção: ${utils.getTxt(selects.process_types,
                    process.type)} - PCA: ${process.year_pca} - Situação: ${utils.getTxt(selects.status,
                pricerecord.status)}` }}
            </h2>
        </div>

        <!-- process object -->
        <div class="my-4">
            <h2>Processo: {{ process.protocol }}</h2>
            <p class="text-justify enfase">{{ process.description }}</p>
        </div>

        <!-- list index proposals -->
        <div class="table-title">
            <h3>Propostas</h3>
            <p>
                Lista de propostas recebidas
            </p>
        </div>

        <p class="mb-2 text-justify"> {{ pricerecord?.suppliers_justification }}</p>

        <div v-if="proposals.length > 0">
            <TableListReport :count="false" :header="headers.proposals" :body="report.proposals" :selects="selects"
                :mounts="{
                    modality: [Mounts.Cast(selects.proposal_modalities)],
                    status: [Mounts.Cast(selects.proposal_status)]
                }" />
        </div>
        <div v-else class="small mb-4">
            <p>Ainda não existem propostas associadas a coleta</p>
        </div>

        <!-- mapa itens cada fornecedor -->
        <div class="table-title">
            <h3>Mapa de Preços por Item</h3>
            <p>
                Lista de Itens para cada coleta com cálculo de média/mediana/moda
            </p>
        </div>
        <table class="w-100">
            <thead>
                <tr>
                    <th>COD.</th>
                    <th>ITEM</th>
                    <th>UND.</th>
                    <th>QUANT.</th>
                    <th>MÉDIA</th>
                    <th>MEDIANA</th>
                    <th>MODA</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="(i, k) in report.items" :key="i.id">
                    <tr>
                        <td class="align-middle">
                            <div class="p-0 m-0 small enfase">{{ i.item.code }}</div>
                            <div class="p-0 m-0 small">{{ i.item.category == 1 ? 'Material' : 'Serviço' }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small enfase">{{ i.item.name }}</div>
                            <div class="p-0 m-0 small">{{ i.item.description }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small enfase">{{ i.item.und }}</div>
                            <div class="p-0 m-0 small">{{ i.item.volume }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small enfase text-center">{{ i.quantity }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small text-center">{{ utils.floatToCurrency(i.media) }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small text-center">{{ utils.floatToCurrency(i.mediana) }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small text-center">{{ utils.floatToCurrency(i.moda) }}</div>
                        </td>
                    </tr>
                    <tr v-for="p in report.finished_proposals" :key="p.id"
                        :class="{ 'price_winner': check_minor_price(report.finished_proposals, k) == p.id }">
                        <td class="align-middle" colspan="4">
                            <div class="p-0 m-0 small enfase">{{ p.supplier?.name ?? 'Coleta Banco de Preços' }}</div>
                            <div class="p-0 m-0 small">{{ p.supplier?.cnpj ?? '' }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small">{{ p.items[k]?.origin ?? 'E-mail' }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small">Valor Unit.</div>
                            <div class="p-0 m-0 small enfase">{{ utils.floatToCurrency(p.items[k].value ?? 0) }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="p-0 m-0 small">Total</div>
                            <div class="p-0 m-0 small enfase">{{ utils.floatToCurrency(parseFloat((p.items[k].quantity *
                                utils.currencyToFloat(p.items[k].value)).toFixed(2))) }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" class="py-2"></td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- proposata vencedora com base no calculo definido -->
        <div class="table-title">
            <h3>Proposta Vencedora</h3>
            <p>
                Proposta selecionada com base no tipo de cálculo definido
            </p>
        </div>
        <TableListReport :count="false" :header="headers.proposals" :body="[report.winner]" :selects="selects" :mounts="{
            modality: [Mounts.Cast(selects.proposal_modalities)],
            status: [Mounts.Cast(selects.proposal_status)]
        }" />

        <table class="w-100 my-4">
            <thead>
                <tr>
                    <th>COD.</th>
                    <th>ITEM</th>
                    <th>UND.</th>
                    <th>QUANT.</th>
                    <th>VALOR</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(i) in report.winner.items" :key="i.id">
                    <td class="align-middle">
                        <div class="p-0 m-0 small enfase">{{ i.item.code }}</div>
                        <div class="p-0 m-0 small">{{ i.item.category == 1 ? 'Material' : 'Serviço' }}</div>
                    </td>
                    <td class="align-middle">
                        <div class="p-0 m-0 small enfase">{{ i.item.name }}</div>
                        <div class="p-0 m-0 small">{{ i.item.description }}</div>
                    </td>
                    <td class="align-middle">
                        <div class="p-0 m-0 small enfase">{{ i.item.und }}</div>
                        <div class="p-0 m-0 small">{{ i.item.volume }}</div>
                    </td>
                    <td class="align-middle">
                        <div class="p-0 m-0 small enfase text-center">{{ i.quantity }}</div>
                    </td>
                    <td class="align-middle">
                        <div class="p-0 m-0 small text-center">{{ utils.floatToCurrency(i.value) }}</div>
                    </td>
                    <td class="align-middle">
                        <div class="p-0 m-0 small text-center">{{ utils.floatToCurrency(parseFloat((i.quantity *
                                utils.currencyToFloat(i.value)).toFixed(2))) }}</div>
                    </td>

                </tr>
            </tbody>
        </table>

        <!-- assinatura dos responsáveis -->


    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');

.price_winner {
    background-color: var(--color-highline);
}
</style>