<script setup>
import { computed, ref } from 'vue'
import QrcodeVue from 'qrcode.vue'
import dates from '@/utils/dates'
import utils from '@/utils/utils'

const props = defineProps({
  qrdata: { type: Object, default: () => { } },
  organ: { type: Object, required: true },
  etp: { type: Object, required: true },
  selects: { type: Object, required: true },
})

const etp = ref(props.etp)
const selects = ref(props.selects)
const organ = ref(props.organ)

const approvedBy = computed(() => {
  return etp.value.process?.dfds?.map(o => o.ordinator)
    .filter(o => o)
})

const team = computed(() => {
  return etp.value.process?.comission_members
})

const responsibles = computed(() => {
  return etp.value.process?.dfds?.map(o => o.demandant)
    .filter(o => o)
})

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
      <p>PROCESSO {{ etp.process.protocol }}</p>
    </div>
    <div class="etp-content">
      <div class="mt-4">
        <h1 class="etp-title">DESCRIÇÃO DO OBJETO</h1>
        <div v-html="etp.object_description"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">CLASSIFICAÇÃO DO OBJETO</h1>
        <div v-html="etp.object_classification"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">DESCRIÇÃO DA NECESSIDADE</h1>
        <div v-html="etp.necessity"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">TIPO DE PARCELAMENTO</h1>
        <div v-html="utils.getTxt(props.selects.installment_types, etp.installment_type)"></div>
      </div>


      <div class="mt-4">
        <h1 class="etp-title">JUSTIFICATIVA DO TIPO DE PARCELAMENTO</h1>
        <div v-html="etp.installment_justification"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">DEMONSTRAÇÃO DA PREVISÃO DA CONTRATAÇÃO NO PLANO DE CONTRATAÇÕES ANUAL</h1>
        <div v-html="etp.contract_forecast"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">DESCRIÇÃO DOS REQUISITOS DA CONTRATAÇÃO</h1>
        <div v-html="etp.contract_requirements"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">LEVANTAMENTO DE MERCADO</h1>
        <div v-html="etp.market_survey"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">ESTIMATIVA DAS QUANTIDADES A SEREM CONTRATADAS (MEMÓRIAS DE CÁLCULO)</h1>
        <div v-html="etp.contract_calculus_memories"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">ESTIMATIVA DO VALOR DA CONTRATAÇÃO</h1>
        <div v-html="etp.contract_expected_price"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">DESCRIÇÃO DA SOLUÇÃO COMO UM TODO E ESPECIFICAÇÃO DO SERVIÇO</h1>
        <div v-html="etp.solution_full_description"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">JUSTIFICATIVA PARA O PARCELAMENTO OU NÃO DA SOLUÇÃO</h1>
        <div v-html="etp.solution_parcel_justification"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">CONTRATAÇÕES CORRELATAS E/OU INTERDEPENDENTE</h1>
        <div v-html="etp.correlated_contracts"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">ALINHAMENTO ENTRE A CONTRATAÇÃO E O PLANEJAMENTO</h1>
        <div v-html="etp.contract_alignment"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">RESULTADOS PRETENDIDOS</h1>
        <div v-html="etp.expected_results"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">PROVIDÊNCIAS A SEREM ADOTADAS PREVIAMENTE À CELEBRAÇÃO DO CONTRATO</h1>
        <div v-html="etp.contract_previous_actions"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">POSSÍVEIS IMPACTOS AMBIENTAIS E TRATAMENTOS</h1>
        <div v-html="etp.ambiental_impacts"></div>
      </div>

      <div class="mt-4">
        <h1 class="etp-title">DECLARAÇÃO DE VIABILIDADE</h1>
        <p v-if="etp.viability_declaration">Esta equipe de
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
          <template v-if="responsibles.length > 0">
            <div v-for="person, i in responsibles" :key="i" class="col-12 mx-5">
              <strong class="signature">{{ person.name }}</strong>
              <p class="small">Demandante</p>
            </div>
          </template>
          <p v-else>Não informado</p>
        </div>
      </div>
      <div class="comission-signatures text-center mt-4">
        <h1>AUXILIADO PELA EQUIPE TÉCNICA DE PLANEJAMENTO</h1>
        <div class="row gap-3 justify-content-center mt-3">
          <template v-if="team.length > 0">
            <div v-for="person, i in team" :key="i" class="col-12 mx-5">
              <strong class="signature">{{ person.name }}</strong>
              <p class="small">{{ utils.getTxt(selects.responsibilities, person.responsibility) }}</p>
            </div>
          </template>
          <p v-else>Não informado</p>
        </div>
      </div>
      <div class="user-signatures text-center mt-4">
        <h1>APROVADO POR</h1>
        <div class="row gap-3 justify-content-center mt-3">
          <template v-if="approvedBy.length > 0">
            <div  v-for="person, i in approvedBy" :key="i" class="col-12 mx-5">
              <strong class="signature">{{ person.name }}</strong>
              <p class="small">Ordenador de Demanda</p>
            </div>
          </template>
          <p v-else>Não informado</p>
        </div>
      </div>
    </div>
    <p class="mt-4 text-center">{{ `${organ.postalcity ?? '*****'}, ${etp.process.date_hour_ini}` }}</p>
  </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');

.etp-content {
  counter-reset: section;
}

.etp-title::before {
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