<script setup>
import { ref } from 'vue'
import QrcodeVue from 'qrcode.vue'
import dates from '@/utils/dates'
import utils from '@/utils/utils'

const props = defineProps({
    qrdata: { type: Object, default: () => { } },
    organ: { type: Object, required: true },
    refterm: { type: Object, required: true },
    selects: { type: Object, required: true },
})

const refterm = ref(props.refterm)
const selects = ref(props.selects)
const organ = ref(props.organ)

</script>

<template>
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="ct-logo me-2">
                <img :src="organ.logomarca" class="h-logo">
            </div>
            <div class="h-info">
                <h1>{{ organ.name }}</h1>
                <p>{{ organ.address }}</p>
                <p>{{ organ.phone }} - {{ organ.email }}</p>
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
        <div class="text-center mt-4">
            <h1>ESTUDO TÉCNICO PRELIMINAR (ETP)</h1>
            <p>PROCESSO {{ refterm.process.protocol }}</p>
        </div>
        <div class="term-content">
            <div class="mt-4">
                <h1 class="term-title">DESCRIÇÃO DO OBJETO</h1>
                <div v-html="refterm.object_description"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">CLASSIFICAÇÃO DO OBJETO</h1>
                <div v-html="refterm.object_classification"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">DESCRIÇÃO DA NECESSIDADE</h1>
                <div v-html="refterm.necessity"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">DEMONSTRAÇÃO DA PREVISÃO DA CONTRATAÇÃO NO PLANO DE CONTRATAÇÕES ANUAL</h1>
                <div v-html="refterm.contract_forecast"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">DESCRIÇÃO DOS REQUISITOS DA CONTRATAÇÃO</h1>
                <div v-html="refterm.contract_requirements"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">LEVANTAMENTO DE MERCADO</h1>
                <div v-html="refterm.market_survey"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">ESTIMATIVA DAS QUANTIDADES A SEREM CONTRATADAS (MEMÓRIAS DE CÁLCULO)</h1>
                <div v-html="refterm.contract_calculus_memories"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">ESTIMATIVA DO VALOR DA CONTRATAÇÃO</h1>
                <div v-html="refterm.contract_expected_price"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">DESCRIÇÃO DA SOLUÇÃO COMO UM TODO E ESPECIFICAÇÃO DO SERVIÇO</h1>
                <div v-html="refterm.solution_full_description"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">JUSTIFICATIVA PARA O PARCELAMENTO OU NÃO DA SOLUÇÃO</h1>
                <div v-html="refterm.solution_parcel_justification"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">CONTRATAÇÕES CORRELATAS E/OU INTERDEPENDENTE</h1>
                <div v-html="refterm.correlated_contracts"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">ALINHAMENTO ENTRE A CONTRATAÇÃO E O PLANEJAMENTO</h1>
                <div v-html="refterm.contract_alignment"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">RESULTADOS PRETENDIDOS</h1>
                <div v-html="refterm.expected_results"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">PROVIDÊNCIAS A SEREM ADOTADAS PREVIAMENTE À CELEBRAÇÃO DO CONTRATO</h1>
                <div v-html="refterm.contract_previous_actions"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">POSSÍVEIS IMPACTOS AMBIENTAIS E TRATAMENTOS</h1>
                <div v-html="refterm.ambiental_impacts"></div>
            </div>

            <div class="mt-4">
                <h1 class="term-title">DECLARAÇÃO DE VIABILIDADE</h1>
                <p v-if="refterm.viability_declaration">Esta equipe de
                    planejamento declara viável esta contratação com base neste ETP, consoante o inciso
                    XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.</p>
                <p v-else>
                    Esta equipe de planejamento declara inviável esta contratação com base neste ETP, consoante o inciso
                    XIII. art 7º da IN 40 de maio de 2022 da SEGES/ME.</p>
            </div>
        </div>

        <div class="signatures mt-4">
            <div class="user-signatures text-center">
                <h1>RESPONSÁVEIS</h1>
                <div class="row gap-3 justify-content-center mt-3">
                    <div v-for="person in refterm.process.dfds.map(item => item.demandant)" :key="person.id"
                        class="col-12 mx-5">
                        <strong class="signature">{{ person.name }}</strong>
                        <p class="small">Demandante</p>
                    </div>
                </div>
            </div>
            <div class="comission-signatures text-center mt-4">
                <h1>AUXILIADO PELA EQUIPE TÉCNICA DE PLANEJAMENTO</h1>
                <div class="row gap-3 justify-content-center mt-3">
                    <div v-for="person in refterm.process.comission_members" :key="person.id" class="col-12 mx-5">
                        <strong class="signature">{{ person.name }}</strong>
                        <p class="small">{{ utils.getTxt(selects.responsibilities, person.responsibility) }}</p>
                    </div>
                </div>
            </div>
            <div class="user-signatures text-center mt-4">
                <h1>APROVADO POR</h1>
                <div class="row gap-3 justify-content-center mt-3">
                    <div v-for="person in refterm.process.dfds.map(item => item.ordinator)" :key="person.id"
                        class="col-12 mx-5">
                        <strong class="signature">{{ person.name }}</strong>
                        <p class="small">Ordenador de Demanda</p>
                    </div>
                </div>
            </div>
        </div>
        <p class="mt-4 text-center">{{ `${organ.postalcity ?? '*****'}, ${refterm.process.date_hour_ini}` }}</p>
    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');

.term-content {
    counter-reset: section;
}

.term-title::before {
    counter-increment: section;
    content: counter(section) '. ';
}

.signature {
    display: flex;
    justify-content: center;
    border-top: 1px solid #000;
    width: 50%;
    margin: 8px auto 0;
}
</style>