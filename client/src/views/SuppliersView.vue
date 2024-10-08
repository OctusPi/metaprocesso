<script setup>
import { onMounted, ref, watch } from 'vue';

import Layout from '@/services/layout';
import Actions from '@/services/actions';
import masks from '@/utils/masks';

import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TableList from '@/components/table/TableList.vue';
import Mounts from '@/services/mounts';
import InputInsertTag from '@/components/inputs/InputInsertTag.vue';
import http from '@/services/http';
import notifys from '@/utils/notifys';

const emit = defineEmits(['callAlert', 'callUpdate'])
const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/suppliers',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'FORNECEDOR', sub: [{ key: 'cnpj', title: 'CNPJ: ' }] },
        { key: 'modality', title: 'DEFINIÇÃO', sub: [{ key: 'size' }] },
        { key: 'address', title: 'ENDEREÇO' },
    ],
    search: {
        sent: false
    },
    rules: {
        name: 'required',
        cnpj: 'required',
        address: 'required',
        email: 'required',
        phone: 'required',
        modality: 'required'
    }
})

const remoteModalId = 'remoteModal'
const remoteToggler = ref()
const remoteEmails = ref([])

function sendRemoteEmails() {
    if (remoteEmails.value.length < 1) {
        emit('callAlert', notifys.info('Adicione ao menos um email'))
        return
    }
    http.post(`${page.url}/send_form`, { emails: remoteEmails.value }, emit, () => {
        remoteToggler.value.click()
        remoteEmails.value = []
    })
}

watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
})

onMounted(() => {
    pageData.selects()
    pageData.list()
})

</script>

<template>
    <div class="page">

        <NavMainUi />

        <main class="main">
            <HeaderMainUi />

            <!-- List -->
            <section v-if="!page.ui.register && !page.ui.prepare" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Fornecedores</h2>
                        <p>
                            Listagem dos fornecedoes vinculados ao
                            <span class="txt-color">{{ page.organ_name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button data-bs-toggle="modal" ref="remoteToggler" :data-bs-target="'#' + remoteModalId"
                            class="btn btn-action-secondary">
                            <ion-icon name="git-compare-outline" class="fs-5"></ion-icon>
                            Cadastro Remoto
                        </button>
                        <button @click="pageData.ui('register')" class="btn btn-action-primary">
                            <ion-icon name="add" class="fs-5"></ion-icon>
                            Adicionar
                        </button>
                        <button @click="pageData.ui('search')" class="btn btn-action-secondary">
                            <ion-icon name="search" class="fs-5"></ion-icon>
                            Pesquisar
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-8">
                            <label for="s-name" class="form-label">Fornecedor</label>
                            <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                placeholder="Pesquise por partes do nome do Fornecedor">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-cnpj" class="form-label">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control" id="s-cnpj" v-model="page.search.cnpj"
                                placeholder="Pesquise por partes do CNPJ do Fornecedor">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-address" class="form-label">Endereço</label>
                            <input type="text" name="address" class="form-control" id="s-address"
                                v-model="page.search.address"
                                placeholder="Pesquise por partes do endereço do Fornecedor">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="modality" class="form-label">Modalidade</label>
                            <select name="modality" class="form-control" id="modality" v-model="page.search.modality">
                                <option value=""></option>
                                <option v-for="s in page.selects.modalities" :value="s.id" :key="s.id">{{ s.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="size" class="form-label">Porte</label>
                            <select name="size" class="form-control" id="size" v-model="page.search.size">
                                <option value=""></option>
                                <option v-for="s in page.selects.sizes" :value="s.id" :key="s.id">{{ s.title }}
                                </option>
                            </select>
                        </div>
                        <div class="d-flex flex-row-reverse mt-4">
                            <button type="submit" class="btn btn-action-primary">
                                <ion-icon name="filter"></ion-icon>
                                Aplicar Filtro
                            </button>
                        </div>
                    </form>
                </div>

                <div role="list" class="container p-0">
                    <TableList :header="page.header" :body="page.datalist" :actions="[
                        Actions.Edit(pageData.update),
                        Actions.Delete(pageData.remove)
                    ]" :mounts="{
                        modality: [Mounts.Cast(page.selects.modalities)],
                        size: [Mounts.Cast(page.selects.sizes)]
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Fornecedor</h2>
                        <p>Registro de catalógo de fornecedores do Órgão</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row" @submit.prevent="() => pageData.save()">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Fornecedor</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Nome do Fornecedor" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="modality" class="form-label">Modalidade</label>
                                <select name="modality" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.modality }" id="modality"
                                    v-model="page.data.modality">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.modalities" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" class="form-control" v-maska:[masks.maskcnpj]
                                    :class="{ 'form-control-alert': page.valids.cnpj }" id="cnpj"
                                    placeholder="XX.XXX.XXX/XXXX-XX" v-model="page.data.cnpj">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="agent" class="form-label">Agente</label>
                                <input type="text" name="agent" class="form-control" id="agent"
                                    placeholder="Nome do Agente" v-model="page.data.agent">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" name="cpf" class="form-control" id="cpf" v-maska:[masks.maskcpf]
                                    placeholder="XXX.XXX.XXX-XX" v-model="page.data.cpf">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" name="phone" class="form-control" v-maska:[masks.maskphone]
                                    :class="{ 'form-control-alert': page.valids.phone }" id="phone"
                                    placeholder="(00) 90000-0000" v-model="page.data.phone">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="size" class="form-label">Porte</label>
                                <select name="size" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.size }" id="size"
                                    v-model="page.data.size">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.sizes" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.email }" id="email"
                                    placeholder="Email de contato do Fornecedor" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12">
                                <InputInsertTag placeholder="Adicionar serviços do fornecedor" identify="service"
                                    label="Serviços" v-model="page.data.services" />
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse gap-2 mt-4">
                            <button class="btn btn-action-primary">
                                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                                Registrar
                            </button>
                            <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                                <ion-icon name="close-outline" class="fs-5"></ion-icon>
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!--Remote Modal-->
            <section class="modal fade" :id="remoteModalId" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered mx-auto">
                    <div class="modal-content p-4 content">
                        <div class="modal-body p-0 my-1">
                            <div role="heading" class="inside-title w-100 mb-3">
                                <div>
                                    <h2>Solicitar Cadastro de Fornecedor</h2>
                                    <p>
                                        Encaminhe a solicitação de cadastro por email para os fornecedores
                                    </p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button data-bs-dismiss="modal" aria-label="Close" class="btn btn-action-close">
                                        <ion-icon name="close" class="fs-5"></ion-icon>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <InputInsertTag placeholder="Email do Fornecedor" identify="email"
                                    v-model="remoteEmails" />
                                <div class="mt-4">
                                    <button @click="sendRemoteEmails" class="btn btn-action-primary ms-auto">
                                        <ion-icon name="mail-outline" class="fs-5"></ion-icon>
                                        Enviar Emails
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <FooterMainUi />
        </main>
    </div>
</template>

<style scoped>
.tag {
    background-color: var(--color-base-tls);
    color: var(--color-text);
    width: fit-content;
    padding: 2px 12px;
    border-radius: 24px;
}
</style>