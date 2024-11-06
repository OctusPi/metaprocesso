<script setup>
import { ref } from 'vue'
import utils from '@/utils/utils'
import TableListReport from './TableListReport.vue';
import PseudoData from '@/services/pseudodata';

const props = defineProps({
    pca: { type: Object, required: true },
    selects: { type: Object, required: true },
})

const pca = ref(props.pca)
const selects = ref(props.selects)

const dfds = ref({
    datalist: pca.value.process.dfds,
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    dataheader: [
        { key: 'verb_id', title: 'ID' },
        { key: 'risk_name', title: 'Risco' },
        { key: 'risk_related', title: 'Relacionado ao(à)' },
        { key: 'risk_probability', cast: 'value', title: 'P' },
        { key: 'risk_impact', cast: 'value', title: 'I' },
        {
            key: 'risk_level', title: 'Nível de Risco (P X I)', fclass: (body) => {
                if (body.risk_level <= 50) {
                    return 'green'
                }
                if (body.risk_level <= 100) {
                    return 'yellow'
                }
                if (body.risk_level <= 225) {
                    return 'red'
                }
            }
        }
    ],
    selects: {},
    data: {},
    search: {},
    rules: {
        fields: {},
        valids: {}
    },
})


</script>

<template>
    <header>
        <div class="d-flex align-items-center">
            <div class="ct-logo me-2">
                <img :src="pca.organ.logomarca" class="h-logo">
            </div>
            <div class="h-info">
                <h1>{{ pca.organ.name }}</h1>
                <p>{{ pca.comission.name }}</p>
                <p>{{ pca.comission.address }}</p>
                <p>{{ pca.organ.address }}</p>
                <p>{{ pca.organ.phone }} {{ pca.organ.email }}</p>
            </div>
        </div>
    </header>

    <main>
        <div class="my-4">
            <h1 class="text-center">
                MAPA DE RISCOS PARA O PROCESSO ADMINISTRATIVO <br>
                Nº {{ pca.process.protocol ?? '*****' }}
                {{ pca.process.date_hour_ini }} - {{ pca.process.ip }}
            </h1>
            <h2 class="text-center">{{ `PCA: ${pca.process.year_pca} - Situação: ${utils.getTxt(
                selects.status_process,
                pca.process.status
            )}` }}</h2>
        </div>

        <div class="table-title">
            <h3>Informações Gerais</h3>
            <p>
                Dados referentes a origem e a classificação do mapa
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
                        <h3>Unidade</h3>
                        <p>{{ pca.unit.name ?? '*****' }}</p>
                        <p>{{ pca.unit.cnpj ?? '*****' }}</p>
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
                        <h3>Versão</h3>
                        <p>{{ pca.version ?? '*****' }}</p>
                    </td>
                    <td>
                        <h3>Fase</h3>
                        <p>{{ utils.getTxt(selects.phases, pca.phase) ?? '*****' }}</p>
                    </td>
                    <td>
                        <h3>Emissão</h3>
                        <p>{{ pca.date_version ?? '*****' }}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="wrapper">
                        <h3>Integrantes da Comissão</h3>
                        <p class="p-0 m-0 small" v-for="m in pca.comission.comission_members" :key="m.id">
                            {{ `${utils.getTxt(selects.responsibilitys, m.responsibility)}
                            : ${m.name} ` }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="table-title">
            <h3>Identificação de Riscos</h3>
            <p>
                Listagem não exaustiva dos riscos identificados na proposição do processo
            </p>
        </div>
        <div class="mb-3">
            <TableListReport :count="false" :header="riskiness.dataheader" :body="riskiness.datalist" :casts="{
                risk_impact: selects.risk_impacts,
                risk_probability: selects.risk_probabilities,
            }" />
        </div>

        <!-- City and Date -->
        <p class="mt-4 text-center">{{ `${pca.organ.postalcity ?? '*****'}, ${pca.date_version}` }}
        </p>
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

<style>
.green {
    background-color: rgb(98, 255, 98);
}

.yellow {
    background-color: rgb(255, 255, 125);
}

.red {
    background-color: rgb(255, 126, 126);
}
</style>