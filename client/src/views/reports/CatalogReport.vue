<script setup>
import { ref } from 'vue'
import QrcodeVue from 'qrcode.vue';
import dates from '@/utils/dates';
import TableListReport from '@/views/reports/TableListReport.vue'

const props = defineProps({
    qrdata: {type: Object, default:() => { }},
    catalog: { type: Object, required: true },
    selects: { type: Object, required: true }
})

const catalog = ref(props.catalog)
const selects = ref(props.selects)

const items = ref({
    headers_list: [
        { key: 'code', title: 'COD.', sub: [{ key: 'origin', cast: 'title' }] },
        { key: 'name', title: 'ITEM', sub: [{ key: 'category', cast: 'title' }] },
        { key: 'und', title: 'UND.' },
        { key: 'volume', title: 'VOLUME' },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] },
        { key: 'status', cast: 'title', title: 'STATUS' }
    ]
})

</script>

<template>
    <main>
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="ct-logo me-2">
                    <img :src="catalog.organ.logomarca" class="h-logo">
                </div>
                <div class="h-info">
                    <h1>{{ catalog.organ.name }}</h1>
                    <p>{{ catalog.organ.address }}</p>
                    <p>{{ catalog.organ.phone }} {{ catalog.organ.email }}</p>
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
        </div>
        <div class="my-4">
            <h1 class="text-center">CATÁLOGO DE ITENS GOVERNAMENTAIS</h1>
            <h2 class="text-center">{{ `${catalog.name ?? '*****'}` }}</h2>
        </div>

        <div class="table-title">
            <h3>Informações Gerais</h3>
            <p>
                Dados referentes à origem e a finalidade do catálogo
            </p>
        </div>
        <table>
            <tbody>
            <tr>
                <td colspan="3">
                    <h3>Orgão</h3>
                    <p>{{ catalog.organ.name ?? '*****' }}</p>
                    <p>{{ catalog.organ.cnpj ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h3>Comissão/Equipe de Planejamento</h3>
                    <p>{{ catalog.comission.name ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h3>Descrição do Catálogo</h3>
                    <p>{{ catalog.name ?? '*****' }}</p>
                </td>
            </tr>
        </tbody>
        </table>

        <div class="table-title">
            <h3>Itens do Catálogo</h3>
            <p>
                Lista de materiais ou serviços vinculados ao Catálogo
            </p>
        </div>

        <div v-if="catalog?.items.length > 0">
            <TableListReport :detach-status="false" :smaller="true" :count="false" :header="items.headers_list"
                :body="catalog?.items" :casts="{
                    status: selects.items_status ?? [],
                    origin: selects.items_origins ?? [],
                    category: selects.items_categories ?? [],
                }" />
        </div>
        <div v-else class="small mb-4">
            <p>Catálogo não possuí materiais ou serviços</p>
        </div>

        <!-- City and Date -->
        <p class="mt-4 text-center">{{ `${catalog.organ.postalcity ?? '*****'}, ${dates.nowPtbr().date}` }}</p>
    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>