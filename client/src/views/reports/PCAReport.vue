<script setup>
    import { ref } from 'vue'
    import QrcodeVue from 'qrcode.vue';
    import utils from '@/utils/utils'
    import dates from '@/utils/dates';
    import Mounts from "@/services/mounts"
    import TableListReport from '@/components/table/TableListReport.vue'

    const props = defineProps({
        qrdata: { type: Object, default: () => { } },
        organ: { type: Object, required: true },
        pca: { type: Object, required: true },
        selects: { type: Object, required: true },
    });

    const pca = ref(props.pca)
    const selects = ref(props.selects)
    const organ = ref(props.organ)

    const dfds = ref({
        datalist: pca.value.dfds,
        datalist_items: pca.value.items,
        headers: [
            { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
            { key: 'demandant.name', title: 'DEMANDANTE' },
            { key: 'ordinator.name', title: 'ORDENADOR' },
            { key: 'unit.name', title: 'ORIGEM' },
            { title: 'OBJETO', sub: [{ key: 'description' }] },
            { key: 'status', title: 'SITUAÇÃO' }
        ],
        headers_items: [
        {
            key: 'item.code',
            title: 'COD'
        },
        { key: 'item.name', title: 'ITEM' },
        { key: 'item.description', title: 'DESCRIÇÃO' },
        { key: 'item.und', title: 'UDN', sub: [{ key: 'item.volume' }] },
        {
            key: 'program.name',
            title: 'VINCULO',
            sub: [{ key: 'dotation.name' }]
        },
        { key: 'quantity', title: 'QUANT.' }
    ]
    })
</script>

<template>
    <header>
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="ct-logo me-2">
                    <img :src="organ.logomarca" class="h-logo">
                </div>
                <div class="h-info">
                    <h1>{{ organ.name }}</h1>
                    <p>{{ organ.name }}</p>
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
        </div>
    </header>

    <main>
        <div class="my-4">
            <h1 class="text-center">
                PLANO DE CONTRATAÇÕES ANUAL (PCA) <br>
            </h1>
            <h2 class="text-center">{{ `ANO: ${pca.reference_year} - Situação: ${utils.getTxt(
                selects.status,
                pca.status
            )}` }}</h2>
        </div>

        <div class="table-title">
            <h3>Detalhes</h3>
            <p>
                Informações detalhadas do PCA de {{ pca.reference_year }}
            </p>
        </div>
        <table>
            <tbody>
                <tr>
                    <td colspan="3" class="wrapper">
                        <h3>Orgão</h3>
                        <p>{{ pca.organ.name ?? '*****' }}</p>
                        <p>{{ pca.organ.cnpj ?? '*****' }}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="wrapper">
                        <h3>Comissão/Equipe de Planejamento</h3>
                        <p>{{ pca.comission.name ?? '*****' }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3>Protocolo</h3>
                        <p>{{ pca.protocol ?? '*****' }}</p>
                    </td>
                    <td>
                        <h3>Orçamento Previsto</h3>
                        <p>{{ pca.price ?? '*****' }}</p>
                    </td>
                    <td>
                        <h3>Emissão</h3>
                        <p>{{ pca.emission ?? '*****' }}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="wrapper">
                        <h3>Integrantes da Comissão</h3>
                        <p class="p-0 m-0 small" v-for="m in JSON.parse(pca.comission_members)" :key="m.id">
                            {{ `${utils.getTxt(selects.responsibilitys, m.responsibility)}
                            : ${m.name} ` }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="wrapper">
                        <h3>Observações</h3>
                        <p>{{ pca.observations ?? '*****' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="table-title">
            <h3>DFDs</h3>
            <p>
                Listagem das DFDs do PCA de {{ pca.reference_year ?? '*****' }}
            </p>
        </div>
        <div class="mb-3">
            <TableListReport :smaller="true" :count="false" :header="dfds.headers" :body="dfds.datalist" :mounts="{
            status: [Mounts.Cast(selects.dfds_status)],
        }" />
        </div>

        <div class="table-title">
            <h3>Lista de Itens</h3>
            <p>
                Lista de materiais ou serviços do PCA
            </p>
        </div>
        <div class="mb-3">
            <TableListReport :smaller="true" :count="false" :header="dfds.headers_items" :body="dfds.datalist_items" errmsg="PCA não possuí materiais ou serviços" />
        </div>

        <p class="mt-4 text-center">{{ `${pca.organ.postalcity ?? '*****'}, ${pca.emission}` }}</p>
    </main>
</template>

<style scoped>
    @import url('../../assets/css/reports.css');

    .colapse {
        padding: 0;
        border: none;
    }

    .nomargin {
        margin: 0;
    }

    .noborder {
        padding: 0;
        margin: 0;
        border-collapse: collapse;
    }
</style>