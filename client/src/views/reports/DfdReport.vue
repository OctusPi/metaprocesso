<script setup>
import { ref } from 'vue'
import dates from '@/utils/dates';
import TableList from '@/components/TableList.vue'
import TableListStatus from '@/components/TableListStatus.vue'

const props = defineProps({
    dfd: { type: Object, required: true }
})

const dfd = ref(props.dfd);
const items = ref({
   headers_list: [
        {
            obj: 'item',
            key: 'code',
            title: 'COD',
            sub: [{ obj: 'item', cast: 'title', key: 'type' }]
        },
        { obj: 'item', key: 'name', title: 'ITEM' },
        { obj: 'item', key: 'description', title: 'DESCRIÇÃO' },
        { obj: 'item', key: 'und', title: 'UDN', sub: [{ obj: 'item', key: 'volume' }] },
        {
            key: 'program',
            cast: 'title',
            title: 'VINC.',
            sub: [{ key: 'dotation', cast: 'title' }]
        },
        { key: 'quantity', title: 'QUANT.' }
    ]
})
</script>

<template>
    <header>
        <table>
            <tr>
                <td>
                    <img :src="dfd.organ.logomarca" class="h-logo">
                </td>
                <td>
                    <p>{{ dfd.organ.name }}</p>
                    <p>{{ dfd.unit.name }}</p>
                    <p>{{ dfd.unit.cnpj }}</p>
                    <p>{{ dfd.unit.address }}</p>
                    <p>{{ dfd.unit.phone }} {{ dfd.unit.email }}</p>
                </td>
            </tr>
        </table>
    </header>
    <main>
        <h1 class="text-center">DOCUMENTO DE FORMALIZAÇÃO DA DEMANDA</h1>
        <h2 class="text-center">{{ `${dfd.protocol ?? '*****'} - ${dfd.date_ini} - ${dfd.ip ?? '*****'}` }}</h2>
        <h2 class="text-center">{{ `PCA: ${dfd.year_pca} - Situação: ${dfd.status ?? 'Preview'}` }}</h2>

        <!-- origin -->
        <div class="box-revisor mb-4">
            <div class="box-revisor-title d-flex mb-4">
                <div class="bar-revisor-title me-2"></div>
                <div class="txt-revisor-title">
                    <h3>Origem da Demanda</h3>
                    <p>
                        Dados referentes a origem e responsabilidade pela
                        Demanda
                    </p>
                </div>
            </div>
            <div class="box-revisor-content">
                <div class="row">
                    <div class="col-md-4">
                        <h4>Orgão</h4>
                        <p>{{ dfd.organ.name }}</p>
                        <p>{{ dfd.organ.cnpj }}</p>
                    </div>
                    <div class="col-md-4">
                        <h4>Unidade</h4>
                        <p>{{ dfd.unit.name }}</p>
                        <p>{{ dfd.unit.cnpj }}</p>
                    </div>
                    <div class="col-md-4">
                        <h4>Ordenador de Despesas</h4>
                        <p>{{ dfd.ordinator.name }}</p>
                        <p>{{ dfd.ordinator.cpf }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h4>Demadantes</h4>
                        <p>{{ dfd.demandant.name }}</p>
                        <p>{{ dfd.demandant.cpf }}</p>
                    </div>
                    <div class="col-md-4">
                        <h4>Comissão / Equipe de Planejamento</h4>
                        <p>{{ dfd.comission.name }}</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h4>Integrantes da Comissão</h4>
                        ...
                    </div>
                </div>
            </div>
        </div>

        <!-- Infos -->
        <div class="box-revisor mb-4">
            <div class="box-revisor-title d-flex mb-4">
                <div class="bar-revisor-title me-2"></div>
                <div class="txt-revisor-title">
                    <h3>Informações Gerais</h3>
                    <p>
                        Dados de prioridade, previsão de contratação e
                        detalhamento de Objeto
                    </p>
                </div>
            </div>
            <div class="box-revisor-content">
                <div class="row">
                    <div class="col-md-3">
                        <h4>Data Envio</h4>
                        <p>{{ dfd.date_ini }}</p>
                    </div>
                    <div class="col-md-3">
                        <h4>Previsão Contratação</h4>
                        <p>
                            {{
                                dates.getMonthYear(dfd.estimated_date)
                            }}
                        </p>
                    </div>
                    <div class="col-md-2">
                        <h4>Ano PCA</h4>
                        <p>{{ dfd.year_pca ?? '*****' }}</p>
                    </div>
                    <div class="col-md-2">
                        <h4>Prioridade</h4>
                        <p>
                            <TableListStatus :data="dfd.priority" />
                        </p>
                    </div>
                    <div class="col-md-2">
                        <h4>Valor Estimado</h4>
                        <p>R${{ dfd.estimated_value ?? '*****' }}</p>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-3">
                        <h4>Tipo de Aquisição</h4>
                        <p>{{ dfd.acquisition_type }}</p>
                    </div>
                    <div class="col-md-3">
                        <h4>Forma Sugerida</h4>
                        <p>{{ dfd.suggested_hiring }}</p>
                    </div>
                    <div class="col-md-3">
                        <h4>Vinculo ou Dependência</h4>
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
                    </div>
                    <div class="col-md-3">
                        <h4>Registro de Preço</h4>
                        <p class="txt-very-small p-0 m-0">
                            Indique se a demanda se trata de registro de preços.
                        </p>
                        <p>
                            {{
                                dfd.price_taking ? 'Sim' : 'Não'
                            }}
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Descrição sucinta do Objeto</h4>
                        <p>{{ dfd.description ?? '*****' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="box-revisor mb-4">
            <div class="box-revisor-title d-flex mb-4">
                <div class="bar-revisor-title me-2"></div>
                <div class="txt-revisor-title">
                    <h3>Lista de Itens</h3>
                    <p>
                        Lista de materiais ou serviços vinculados a Demanda
                    </p>
                </div>
            </div>
            <div class="box-revisor-content">
                <!-- list items -->
                <div v-if="dfd?.items">
                    <TableList :smaller="true" :count="false" :header="items.headers_list" :body="dfd?.items"
                        :casts="{
                            type: [
                                { id: 1, title: 'Material' },
                                { id: 2, title: 'Serviço' }
                            ],
                            program: dfd.programs ?? [],
                            dotation: dfd.dotations ?? []
                        }" />
                </div>
            </div>
        </div>

        <!-- details -->
        <div class="box-revisor mb-4">
            <div class="box-revisor-title d-flex mb-4">
                <div class="bar-revisor-title me-2"></div>
                <div class="txt-revisor-title">
                    <h3>Detalhamento da Necessidade</h3>
                    <p>
                        Justificativas para necessidade e quantitativo de
                        itens demandados
                    </p>
                </div>
            </div>
            <div class="box-revisor-content">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Justificativa da necessidade da contratação</h4>
                        <p>{{ dfd.justification ?? '*****' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Justificativa dos quantitativos demandados</h4>
                        <p>
                            {{
                                dfd.justification_quantity ?? '*****'
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
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
        <p class="mt-4">{{ dfd.organ.postalcity }}</p>
    </main>
</template>

<style scoped>
.search-list-items {
    list-style: none;
    margin: 0;
    padding: 0;
}

.search-list-items li {
    cursor: pointer;
    border-bottom: var(--border-box);
}

.search-list-items li h3 {
    font-weight: 700;
    color: var(--color-base);
}

.search-list-items li:hover {
    background-color: var(--color-contrast-hover);
}

.item-type {
    width: 35px;
    border-right: var(--border-box);
}

.item-desc h3 {
    color: var(--color-base);
}

.item-desc p {
    font-size: 0.7rem;
    color: var(--color-text-sec);
}

.nav-step {
    margin: 0 !important;
    padding: 0 !important;
    position: relative;
    height: 60px;
}

.nav-line-step {
    height: 3px;
    width: 100%;
    background-color: var(--color-shadow);
    position: absolute;
    top: 50%;
    z-index: 0;
}

.nav-step-txt {
    background-color: var(--color-shadow);
    color: var(--color-shadow-2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: calc(50% - 30px);
}

.nav-step-txt i {
    font-size: 1.3rem;
    margin: 0;
    padding: 0;
}

.nav-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--color-shadow-2);
}

.nav-item .active .nav-step-txt {
    background-color: var(--color-base);
    color: white;
    transition: 400ms;
}

.nav-item .active .nav-line-step {
    background: rgb(202, 201, 201);
    background: linear-gradient(90deg,
            var(--color-shadow) 0%,
            var(--color-base) 50%,
            var(--color-shadow) 100%);
    transition: 400ms;
}

.active-label {
    color: var(--color-base);
}

.box-revisor {
    border-bottom: var(--border-box);
}

.bar-revisor-title {
    width: 5px;
    background-color: var(--color-base);
    border-radius: 2px;
}

.box-revisor-title h3 {
    color: var(--color-base);
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.box-revisor-title p {
    color: var(--color-text-secondary);
    font-size: small;
    margin: 0;
    padding: 0;
}

.box-revisor-content {
    padding: 0 10px 0 10px;
}

.box-revisor-content h4 {
    margin: 0;
    padding: 0;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--color-text);
}

.box-revisor-content p {
    color: var(--color-text-secondary);
    font-size: 0.9rem;
}

.btn-abs{
    top: -20px;
    right: 0;
}
</style>