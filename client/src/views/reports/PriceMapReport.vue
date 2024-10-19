<script setup>
import QrcodeVue from 'qrcode.vue';
import dates from '@/utils/dates'
// import { ref } from 'vue'
// import utils from '@/utils/utils'
// import TableListReport from '@/views/reports/TableListReport.vue'

defineProps({
    qrdata: { type: Object, default: () => { } },
    organ: { type: Object, required: true },
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
                <p>{{ dfd.unit.name }}</p>
                <p>{{ dfd.unit.address }}</p>
                <p>{{ dfd.unit.phone }} {{ dfd.unit.email }}</p>
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
        <!-- object -->

        <!-- resumo propostas -->

        <!-- mapa itens cada fornecedor -->

        <!-- proposata vencedora com base no calculo definido -->

        <!-- assinatura dos responsáveis -->


    </main>
</template>