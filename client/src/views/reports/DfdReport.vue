<script setup>
import { ref } from 'vue'
import QrcodeVue from 'qrcode.vue';
import dates from '@/utils/dates'
import utils from '@/utils/utils'
import TableListReport from '@/views/reports/TableListReport.vue'

const props = defineProps({
    qrdata: {type: Object, default:() => { }},
    organ:{ type: Object, required: true },
    dfd: { type: Object, required: true },
    selects: { type: Object, required: true }
})

const dfd = ref(props.dfd)
const selects = ref(props.selects)
const items = ref({
    headers_list: [
        {
            obj: 'item',
            key: 'code',
            title: 'COD'
        },
        { obj: 'item', key: 'name', title: 'ITEM' },
        { obj: 'item', key: 'description', title: 'DESCRIÇÃO' },
        { obj: 'item', key: 'und', title: 'UDN', sub: [{ obj: 'item', key: 'volume' }] },
        {
            key: 'program',
            cast: 'name',
            title: 'VINCULO',
            sub: [{ key: 'dotation', cast: 'name' }]
        },
        { key: 'quantity', title: 'QUANT.' }
    ]
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
        </div>

    <main>
        <div class="my-4">
            <h1 class="text-center">DOCUMENTO DE FORMALIZAÇÃO DA DEMANDA</h1>
            <h2 class="text-center">{{ `${dfd.protocol ?? '*****'} - ${dfd.date_ini} - ${dfd.ip ?? '*****'}` }}</h2>
            <h2 class="text-center">{{ `PCA: ${dfd.year_pca} - Situação: ${utils.getTxt(
                selects.status,
                dfd.status
            )}` }}</h2>
        </div>

        <!-- origin -->
        <div class="table-title">
            <h3>Origem da Demanda</h3>
            <p>
                Dados referentes a origem e responsabilidade pela
                Demanda
            </p>
        </div>
        <table>
        <tbody>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Orgão</h3>
                    <p>{{ organ.name ?? '*****' }}</p>
                    <p>{{ organ.cnpj ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Unidade</h3>
                    <p>{{ dfd.unit.name ?? '*****' }}</p>
                    <p>{{ dfd.unit.cnpj ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Ordenador de Despensas</h3>
                    <p>{{ dfd.ordinator.name ?? '*****' }}</p>
                    <p>{{ dfd.ordinator.cpf ?? '*****' }}</p>
                </td>
                <td>
                    <h3>Demandante Responsável</h3>
                    <p>{{ dfd.demandant.name ?? '*****' }}</p>
                    <p>{{ dfd.demandant.cpf ?? '*****' }}</p>
                </td>
                <td>
                    <h3>Comissão/Equipe de Planejamento</h3>
                    <p>{{ dfd.comission.name ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Integrantes da Comissão</h3>
                    <p class="p-0 m-0 small" v-for="m in dfd.comission_members" :key="m.id">
                        {{ `${utils.getTxt(selects.responsibilitys, m.responsibility)}
                        : ${m.name} ` }}
                    </p>
                </td>
            </tr>
        </tbody>
        </table>


        <!-- Infos -->
        <div class="table-title">
            <h3>Informações Gerais</h3>
            <p>
                Dados de prioridade, previsão de contratação e
                detalhamento de Objeto
            </p>
        </div>
        <table>
            <tbody>
            <tr>
                <td>
                    <h3>Previsão de Contratação</h3>
                    <p>
                        {{
                            dates.getMonthYear(dfd.estimated_date)
                        }}
                    </p>
                </td>
                <td>
                    <h3>Nivel de Prioridade</h3>
                    <p>
                        {{
                            utils.getTxt(
                                selects.prioritys,
                                dfd.priority
                            )
                        }}
                    </p>
                </td>
                <td>
                    <h3>Registro de Preço</h3>
                    <p class="txt-very-small p-0 m-0"> Indique se a demanda se trata de registro de preços.</p>
                    <p>{{ dfd.price_taking ? 'Sim' : 'Não' }} </p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Tipo de Aquisição</h3>
                    <p>
                        {{
                            utils.getTxt(
                                selects.acquisitions,
                                dfd.acquisition_type
                            )
                        }}
                    </p>
                </td>
                <td>
                    <h3>Forma Sugerida</h3>
                    <p>{{
                            utils.getTxt(
                                selects.hirings,
                                dfd.suggested_hiring
                            )
                        }}</p>
                </td>
                <td>
                    <h3>Valor Estimado</h3>
                    <p class="txt-very-small p-0 m-0">O valor estimado preliminar para esta contratação é de:</p>
                    <p>R${{ dfd.estimated_value ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Vinculo ou Dependência</h3>
                    <p class="txt-very-small p-0 m-0">
                        Dependência com o
                        objeto de outro documento de formalização de
                        demanda
                    </p>
                    <p>
                        {{
                            dfd.bonds ? 'Sim Possui' : 'Não Possui'
                        }}
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Descrição sucinta do Objeto</h3>
                    <p>{{ dfd.description ?? '*****' }}</p>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Items -->
        <div class="table-title">
            <h3>Lista de Itens</h3>
            <p>
                Lista de materiais ou serviços vinculados a Demanda
            </p>
        </div>
        <div class="mb-3">
            <TableListReport :smaller="true" :count="false" :header="items.headers_list" :body="dfd?.items" :casts="{
                program: selects.programs ?? [],
                dotation: selects.dotations ?? []
            }" errmsg="Demanda não possuí materiais ou serviços" />
        </div>

        <!-- details -->
        <div class="table-title">
            <h3>Detalhamento da Necessidade</h3>
            <p>
                Justificativas para necessidade e quantitativo de itens demandados
            </p>
        </div>
        <table>
        <tbody>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Justificativa da necessidade da contratação</h3>
                    <p>{{ dfd.justification ?? '*****' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="wrapper">
                    <h3>Justificativa dos quantitativos demandados</h3>
                    <p>
                        {{
                            dfd.justification_quantity ?? '*****'
                        }}
                    </p>
                </td>
            </tr>
        </tbody>
        </table>


        <!-- assings -->
        <div class="row mt-5">
            <div class="col-6 text-center">
                <p>___________________________________</p>
                <p>{{ dfd.ordinator.name }}</p>
                <p>Ordenador de Despesas</p>
            </div>
            <div class="col-6 text-center">
                <p>___________________________________</p>
                <p>{{ dfd.demandant.name }}</p>
                <p>Demandante Responsável</p>
            </div>
        </div>

        <!-- City and Date -->
        <p class="mt-4 text-center">{{ `${organ.postalcity ?? '*****'}, ${dfd.date_ini}` }}</p>
    </main>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>