<script setup>
// import { ref } from 'vue'
import QrcodeVue from 'qrcode.vue';
import dates from '@/utils/dates'
import utils from '@/utils/utils'
import Mounts from "@/services/mounts"

import TableListReport from '@/components/table/TableListReport.vue'

defineProps({
    qrdata: { type: Object, default: () => { } },
    organ: { type: Object, required: true },
    process: { type: Object, required: true },
    pricerecord: { type: Object, required: true },
    proposals: { type: Array, required: true },
    selects: { type: Array, default: () => [] }
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
                process.type)} - PCA: ${process.year_pca} - Situação: ${utils.getTxt(selects.status, pricerecord.status)}` }}
            </h2>
        </div>

        <!-- process object -->
        <div class="my-4">
            <h2>{{ process.description }}</h2>
        </div>

        <!-- list index proposals -->
        <div class="table-title">
            <h3>Propostas</h3>
            <p>
                Lista de propostas recebidas
            </p>
        </div>

        <div v-if="proposals.length > 0">
            <TableListReport :count="false" :header="headers.proposals" :body="proposals" :selects="selects" :mounts="{
                modality: [Mounts.Cast(selects.proposal_modalities)],
                status: [Mounts.Cast(selects.proposal_status)]
            }" />
        </div>
        <div v-else class="small mb-4">
            <p>Ainda não existem propostas associadas a coleta</p>
        </div>

        <!-- mapa itens cada fornecedor -->

        <!-- proposata vencedora com base no calculo definido -->

        <!-- assinatura dos responsáveis -->


    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>