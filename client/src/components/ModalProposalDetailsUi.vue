<script setup>
import { watch } from 'vue';
import layout from '@/services/layout';

const props = defineProps({
    collect: { type: Object, default: () => {} },
    selects: { type: Object, default: () => {} },
    modal: { type: String, default: 'modalProposalDetails' }
})

const [page, ] = layout.new(null, {
    data: props.collect ?? {},
    selects: props.selects,
    items_headers: [
        { key: 'item.code', title: 'COD', sub: [{ key: 'item.type' }] },
        { key: 'item.name', title: 'ITEM' },
        { key: 'item.description', title: 'DESCRIÇÃO' },
        { key: 'item.und', title: 'UDN', sub: [{ key: 'item.volume' }] },
        { key: 'program.name', title: 'VINC.', sub: [{ key: 'dotation.name' }] },
        { key: 'quantity', title: 'QUANT.' }
    ]
})

watch(() => props.collect, (newval) => {
    page.data = newval
})

watch(() => props.selects, (newval) => {
    page.selects = newval
})

</script>

<template>
    <div class="modal fade" :id="props.modal" tabindex="-1" :aria-labelledby="'#' + props.modal + 'Label'"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content p-4" v-if="!Object.is(page.data, {})">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-6 p-0 m-0 d-flex align-items-center gap-2"
                        :id="'#' + props.modal + 'Label'">
                        <ion-icon name="document-attach" class="icon-dfd" />
                        Coleta {{ page.data.protocol }}
                    </h1>
                    <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-action-close ms-auto">
                        <ion-icon name="close" class="fs-4"></ion-icon>
                    </button>
                </div>
                <div class="modal-body border-0">

                    <!-- organ -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Vinculo</h3>
                                <p>
                                    Dados do orgão e unidades vinculadas ao processo
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Orgão</h4>
                                    <p>
                                        {{ page.data?.organ.name }}
                                    </p>
                                    <p class="p-0 m-0 form-text">{{ page.data?.organ.cnpj }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Unidades</h4>
                                    <p>
                                        Nome unidades
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Demandantes</h4>
                                    <p>
                                        nome dos demandantes
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- origin -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Processo</h3>
                                <p>
                                    Dados do processo que originou a coleta de preço
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Protocolo</h4>
                                    <p>
                                        num protocol
                                    </p>
                                    <p class="p-0 m-0 form-text">data abertura</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Modalidade</h4>
                                    <p>
                                        tipo modalidade
                                    </p>
                                    <p class="p-0 m-0 form-text">tipo de aquisicao</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Coleta de Preço / Parcelamento</h4>
                                    <p>
                                        Se é coleta de preço
                                    </p>
                                    <p class="p-0 m-0 form-text">se tem parcelamento</p>
                                </div>
                                <div class="col-md-12">
                                    <h4>Descrição do Objeto</h4>
                                    <p>
                                        obj name
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- dfds -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Lista de DFDs</h3>
                                <p>
                                    Listagem dos DFDs atrelados ao processo
                                </p>
                            </div>
                        </div>
                        <div>
                            <!-- list dfds -->
                            <div>
                                <!-- <TableList secondary :count="false" :order="false" :header="page.items_headers"
                                    :body="page.data.items" :mounts="{
                                        'item.type': [Mounts.Cast(page.selects.items_types)],
                                    }" /> -->
                            </div>
                        </div>
                    </div>

                    <!-- Supplier -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Fornecedor</h3>
                                <p>
                                    Dados do fornecedor responsável por responder a solicitação
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Fornecedor</h4>
                                    <p>
                                        nome fornec
                                    </p>
                                    <p class="p-0 m-0 form-text">cnpj fornec</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Representante</h4>
                                    <p>
                                        nome represe
                                    </p>
                                    <p class="p-0 m-0 form-text">cpf repres</p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Contato</h4>
                                    <p>
                                        telefone email fornec
                                    </p>
                                    <p class="p-0 m-0 form-text">endereco</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="box-revisor mb-4">
                        <div class="box-revisor-title d-flex mb-4">
                            <div class="txt-revisor-title">
                                <h3>Coleta</h3>
                                <p>
                                    Informações da coleta lisgatem de itens e valores
                                </p>
                            </div>
                        </div>
                        <div class="box-revisor-content">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <h4>Modalidade Coleta</h4>
                                    <p>
                                        email/inserçao manual
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Data Inicio Coletas</h4>
                                    <p>
                                        data inicial
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4>Prazo final</h4>
                                    <p>
                                        data final
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <!-- list items -->
                            <div>
                                <!-- <TableList secondary :count="false" :order="false" :header="page.items_headers"
                                    :body="page.data.items" :mounts="{
                                        'item.type': [Mounts.Cast(page.selects.items_types)],
                                    }" /> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.icon-dfd {
    background-color: var(--color-base-tls);
    color: var(--color-base);
    padding: 8px;
    border-radius: 8px;
    font-size: 1.2em;
}

p{
    margin: 0;
    padding: 0;
}
</style>