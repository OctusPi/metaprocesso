<script setup>
import QrcodeVue from 'qrcode.vue';
import dates from '@/utils/dates'
// import { ref } from 'vue'
// import utils from '@/utils/utils'
// import TableListReport from '@/views/reports/TableListReport.vue'

defineProps({
    qrdata: { type: Object, default: () => { } },
    organ: { type: Object, required: true },
    process: { type: Object, required: true },
    pricerecord: { type: Object, required: true },
    proposals: { type: Array, required: true }
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
        <div class="my-2">
            <h1 class="text-center">MAPA DE PREÇOS</h1>
            <h2 class="text-center">{{ `${pricerecord.protocol ?? '*****'} - ${pricerecord.date_ini} - ${pricerecord.ip ?? '*****'}` }}</h2>
            <h2 class="text-center">{{ `PCA: ${process.year_pca} - Situação: ${utils.getTxt(
                selects.status,
                process.status
            )}` }}</h2>
        </div>
        
        <!-- process object -->
        <div class="my-2">
            <h2>{{ process.description }}</h2>
        </div>

        <!-- list index proposals -->
        <div class="table-title">
            <h3>Propostas</h3>
            <p>
                Lista de propostas associadas a coleta de preços
            </p>
        </div>

        <div v-if="proposals.length > 0">
            <!-- <TableListReport :detach-status="false" :smaller="true" :count="false" :header="items.headers_list"
                :body="catalog?.items" :casts="{
                    status: selects.items_status ?? [],
                    origin: selects.items_origins ?? [],
                    category: selects.items_categories ?? [],
                }" /> -->
        </div>
        <div v-else class="small mb-4">
            <p>Ainda não existem propostas associadas a coleta</p>
        </div>

        <!-- mapa itens cada fornecedor -->

        <!-- proposata vencedora com base no calculo definido -->

        <!-- assinatura dos responsáveis -->


    </main>
</template>