<script setup>
import { ref } from 'vue'
import utils from '@/utils/utils'

const props = defineProps({
    riskmap: { type: Object, required: true },
    selects: { type: Object, required: true }
})

const riskmap = ref(props.riskmap)
const selects = ref(props.selects)

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
                MAPA DE RISCOS PARA O PROCESSO ADMINISTRATIVO <br> Nº
                {{ riskmap.process.protocol ?? '*****' }}
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
            <h3>Riscos</h3>
            <p>
                Listagem dos riscos identificados na proposição do dado processo
            </p>
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
        <p class="mt-4 text-center">{{ `${riskmap.comission.organ.postalcity ?? '*****'}, ${riskmap.date_version}` }}</p>
    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>