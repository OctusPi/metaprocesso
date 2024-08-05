<script setup>
import { ref } from 'vue'
import utils from '@/utils/utils'
import TableListReport from './TableListReport.vue';
import PseudoData from '@/services/pseudodata';

const props = defineProps({
    riskmap: { type: Object, required: true },
    selects: { type: Object, required: true }
})

const riskmap = ref(props.riskmap)
const selects = ref(props.selects)

const riskiness = ref({
    datalist: riskmap.value.riskiness,
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    dataheader: [
        { key: 'verb_id', title: 'ID' },
        { key: 'risk_name', title: 'Risco' },
        { key: 'risk_related', title: 'Relacionado ao(à)' },
        { key: 'risk_probability', cast: 'value', title: 'P' },
        { key: 'risk_impact', cast: 'value', title: 'I' },
        { key: 'risk_level', title: 'Nível de Risco (P X I)', fclass: (body) => {
            if (body.risk_level <= 50) {
                return 'green'
            }
            if (body.risk_level <= 100) {
                return 'yellow'
            }
            if (body.risk_level <= 225) {
                return 'red'
            }
        } }
    ],
    selects: {},
    data: {},
    search: {},
    rules: {
        fields: {},
        valids: {}
    },
})

const accomp = ref({
    datalist: riskmap.value.accompaniments,
    selects: {},
    risk: {},
    uiview: {},
    title: { primary: '', secondary: '' },
    data: {},
    search: {},
    dataheader: [
        { key: 'accomp_date', title: 'Data' },
        { key: 'accomp_risk', cast: 'verb_id', title: 'Risco' },
        { key: 'accomp_action', cast: 'verb_id', title: 'Ação' },
        { key: 'accomp_treatment', title: 'Acompanhamento das Ações de Tratamento' },
    ],
    rules: {
        fields: {},
        valids: {}
    },
})

const PREVENTIVES = 1;
const CONTINGENCE = 2;

function actions(risk, act) {
    return risk.risk_actions.filter((item) => {
        return item.risk_action_type == act
    })
}

</script>

<template>
    <header>
        <div class="d-flex align-items-center">
            <div class="ct-logo me-2">
                <img :src="riskmap.comission.organ.logomarca" class="h-logo">
            </div>
            <div class="h-info">
                <h1>{{ riskmap.comission.organ.name }}</h1>
                <p>{{ riskmap.comission.unit.name }}</p>
                <p>{{ riskmap.comission.unit.address }}</p>
                <p>{{ riskmap.comission.unit.phone }} {{ riskmap.comission.unit.email }}</p>
            </div>
        </div>
    </header>

    <main>
        <div class="my-4">
            <h1 class="text-center">
                MAPA DE RISCOS PARA O PROCESSO ADMINISTRATIVO <br>
                Nº {{ riskmap.process.protocol ?? '*****' }}
                {{ riskmap.process.date_hour_ini }} - {{ riskmap.process.ip }}
            </h1>
            <h2 class="text-center">{{ `PCA: ${riskmap.process.year_pca} - Situação: ${utils.getTxt(
                selects.status_process,
                riskmap.process.status
            )}` }}</h2>
        </div>

        <div class="table-title">
            <h3>Informações Gerais</h3>
            <p>
                Dados referentes a origem e a classificação do mapa
            </p>
        </div>
        <table>
            <tr>
                <td colspan="3">
                    <h3>Orgão</h3>
                    <p>{{ riskmap.comission.organ.name ?? '*****' }}</p>
                    <p>{{ riskmap.comission.organ.cnpj ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h3>Unidade</h3>
                    <p>{{ riskmap.comission.unit.name ?? '*****' }}</p>
                    <p>{{ riskmap.comission.unit.cnpj ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h3>Comissão Responsável</h3>
                    <p>{{ riskmap.comission.name ?? '*****' }}</p>
                    <p>{{ riskmap.comission.cnpj ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Versão</h3>
                    <p>{{ riskmap.version ?? '*****' }}</p>
                </td>
                <td>
                    <h3>Fase</h3>
                    <p>{{ utils.getTxt(selects.phases, riskmap.phase) ?? '*****' }}</p>
                </td>
                <td>
                    <h3>Emissão</h3>
                    <p>{{ riskmap.date_version ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <h3>Integrantes da Comissão</h3>
                    <p class="p-0 m-0 small" v-for="m in riskmap.comission.comission_members" :key="m.id">
                        {{ `${utils.getTxt(selects.responsibilitys, m.responsibility)}
                        : ${m.name} ` }}
                    </p>
                </td>
            </tr>
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

        <div class="table-title">
            <h3>Riscos e Ações</h3>
            <p>
                Listagem dos riscos e de susas ações cabíveis no âmbito do processo
            </p>
        </div>
        <table>
            <tr v-for="risk in riskiness.datalist" :key="risk.id">
                <th class="text-center">Risco {{ risk.verb_id }}</th>
                <td class="colapse">
                    <table class="noborder">
                        <tr>
                            <th style="width: 20%;">Risco</th>
                            <td>{{ risk.risk_name }}</td>
                        </tr>
                        <tr>
                            <th style="width: 20%;">Probabilidade</th>
                            <td>{{ utils.getTxt(selects.risk_probabilities, risk.risk_probability) }}</td>
                        </tr>
                        <tr>
                            <th style="width: 20%;">Impacto</th>
                            <td>{{ utils.getTxt(selects.risk_impacts, risk.risk_impact) }}</td>
                        </tr>

                        <!--Danos-->
                        <tr v-for="damage in risk.risk_damage" :key="damage.id">
                            <th>Dano {{ damage.verb_id }}</th>
                            <td>{{ damage.risk_damage }}</td>
                        </tr>
                        <tr>
                            <th>Tratamento</th>
                            <td>{{ risk.risk_treatment }}</td>
                        </tr>
                        <tr v-if="actions(risk, PREVENTIVES).length > 0">
                            <td colspan="2" class="colapse">
                                <table class="nomargin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th style="width: 80%;">Ação Preventiva</th>
                                            <th style="width: 20%;">Responsável</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="action in actions(risk, PREVENTIVES)" :key="action.id">
                                            <td>{{ action.verb_id }}</td>
                                            <td>{{ action.risk_action_name }}</td>
                                            <td>{{ action.risk_action_responsability }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr v-if="actions(risk, CONTINGENCE).length > 0">
                            <td colspan="2" class="colapse">
                                <table class="nomargin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th style="width: 80%;">Ação de Contingência</th>
                                            <th style="width: 20%;">Responsável</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="action in actions(risk, CONTINGENCE)" :key="action.id">
                                            <td>{{ action.verb_id }}</td>
                                            <td>{{ action.risk_action_name }}</td>
                                            <td>{{ action.risk_action_responsability }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="table-title">
            <h3>Acompanhamentos</h3>
            <p>
                Listagem dos acompanhamentos para os riscos e suas ações
            </p>
        </div>
        <div class="mb-3">
            <TableListReport :count="false" :header="accomp.dataheader" :body="accomp.datalist" :casts="{
                accomp_risk: riskiness.datalist,
                accomp_action: (data) => PseudoData.findInRef(riskiness.datalist, 'risk_actions', 'id', data.accomp_risk)
            }" />
        </div>

        <div class="row mt-5">
            <div class="col-4 text-center">
                <p>___________________________________</p>
                <p>Integrante Requisitante</p>
            </div>
            <div class="col-4 text-center">
                <p>___________________________________</p>
                <p>Integrante Técnico</p>
            </div>
            <div class="col-4 text-center">
                <p>___________________________________</p>
                <p>Integrante Administrativo</p>
            </div>
        </div>

        <!-- City and Date -->
        <p class="mt-4 text-center">{{ `${riskmap.comission.organ.postalcity ?? '*****'}, ${riskmap.date_version}` }}
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