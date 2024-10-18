<script setup>
import { onBeforeMount, ref } from 'vue';
import QrcodeVue from 'qrcode.vue';
import utils from '@/utils/utils'
import dates from '@/utils/dates'

const props = defineProps({
    qrdata: { type: Object, default: () => { } },
    supplier: {type: Object},
    logomarca: { type: String },
    representation:{ type: Object, required: true },
    process: { type: Object, required: true },
    modality:{type: Number, default: 1},
    items: { type: Object, required: true },
    author:{type: Object, default: () => {}}
})

let globalValue = ref(0)

onBeforeMount(() => {
    props.items.forEach(element => {
        globalValue.value += parseFloat((parseInt(element.quantity) * utils.currencyToFloat(element.value)).toFixed(2))
    });
})

</script>

<template>
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="ct-logo me-2">
                <img :src="logomarca" class="h-logo">
            </div>
            <div class="h-info" v-if="props.supplier">
                <h1>{{ supplier.name }}</h1>
                <p>{{ supplier.cnpj }}</p>
                <p>{{ supplier.address }}</p>
                <p>{{ supplier.phone }} {{ supplier.email }}</p>
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

    <main>
        <div class="my-4">
            <h1 class="text-center">COTAÇÃO DE PREÇOS</h1>
        </div>

        <div class="my-4">
            <h1 class="mb-3">Solicitante: {{ process.organ.name }}</h1>
            <div v-if="props.supplier">
                <p>Fornecedor: {{ supplier?.name }}</p>
                <p>CNPJ: {{ supplier?.cnpj }}</p>
                <p>Endereço: {{ supplier?.address }}</p>
                <p>Contato: {{ supplier?.phone }} {{ supplier?.email }}</p>
            </div>
        </div>

        <div class="my-4">
            <h1>Processo: {{ process.protocol }}</h1>
            <p>Data Solicitação: {{ process.date_ini }}</p>
            <p>Prazo Resposta: {{ process.date_fin }} - 00:00:00</p>
            <p class="text-justify">Objeto: {{ process.description }}</p>
        </div>


        <!-- Items -->
        <div class="table-title">
            <h3>Lista de Itens</h3>
            <p>
                Lista de materiais ou serviços requeridos na coleta
            </p>
        </div>
        <div class="mb-3">
            <table class="w-100">
                <thead>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>ITEM</th>
                        <th>UNIT.</th>
                        <th class="text-center">QUANT.</th>
                        <th>VALOR UNIT.</th>
                        <th>TOTAL</th>
                        <th v-if="props.modality !== 1">ORIGEM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="i in items" :key="i.id">
                        <td class="align-middle">
                            <div class="small">{{ i.item.code }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="small txt-color-sec">{{ i.item.name }}</div>
                            <div class="small">{{ i.item.description }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="small txt-color-sec">{{ i.item.und }}</div>
                            <div class="small">{{ i.item.volume }}</div>
                        </td>
                        <td class="align-middle text-center">
                            <div class="small">{{ i.quantity }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="small">{{ utils.floatToCurrency(i.value) }}</div>
                        </td>
                        <td class="align-middle">
                            <div class="small">{{ utils.floatToCurrency((i.quantity * utils.currencyToFloat(i.value)).toFixed(2)) }}</div>
                        </td>
                        <td v-if="props.modality !== 1" class="align-middle">
                            <div class="small">{{ i.origin ?? '*****' }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- details -->
        <div class="table-title">
            <h3>Detalhamento</h3>
            <p>
                Valor global e detalhamento de validade da proposta
            </p>
        </div>
        <table>
            <tbody>
                <tr>
                    <td colspan="3">
                        <h3>Valor Global</h3>
                        <p>{{ utils.floatToCurrency(globalValue) }}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="wrapper">
                        <h3>Validade da Proposta</h3>
                        <p class="small text-justify">
                            De acordo com o princípio da vinculação ao instrumento convocatório, estabelecido no art. 3º
                            da Lei nº 8.666/1993, que rege as licitações e contratos administrativos, as condições
                            previamente fixadas no edital ou no documento de convocação da coleta de preços devem ser
                            observadas pelas partes envolvidas, garantindo transparência e segurança jurídica ao
                            processo. No caso específico de propostas comerciais, a validade do prazo estipulado deve
                            ser expressamente informada para que as partes tenham clareza sobre o período de vigência da
                            oferta apresentada. Neste sentido, fixamos o prazo de 60 (sessenta) dias para a validade da
                            presente proposta de coleta de preços, contados a partir da data de sua emissão, nos termos
                            da legislação supracitada, em especial o art. 64 da Lei nº 8.666/1993, que dispõe sobre os
                            prazos de validade das propostas em licitações públicas e processos de contratação, podendo
                            ser prorrogado de comum acordo entre as partes
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>


        <!-- assings -->
        <div class="row mt-5">
            <div class="col text-center" v-if="props.modality === 1">
                <p>___________________________________</p>
                <p>{{ representation.name }}</p>
                <p>CPF: {{ representation.cpf }}</p>
            </div>
            <div v-else class="col text-center">
                <p>___________________________________</p>
                <p>{{ author.name }}</p>
                <p>Responsável pela Coleta</p>
            </div>
        </div>

        <!-- City and Date -->
        <p class="mt-4 text-center">{{ `${process.organ.postalcity ?? '*****'}, ${dates.dateTxtNow()}` }}</p>
    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>