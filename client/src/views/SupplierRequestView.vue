<script setup>
import { onMounted } from 'vue';

import Layout from '@/services/layout';
import masks from '@/utils/masks';

import FooterMainUi from '@/components/FooterMainUi.vue';
import InputInsertTag from '@/components/inputs/InputInsertTag.vue';
import HeaderColapseUi from '@/components/HeaderColapseUi.vue';
import { useRoute } from 'vue-router';


const emit = defineEmits(['callAlert', 'callUpdate'])
defineProps({
    datalist: { type: Array, default: () => [] }
})

const route = useRoute()

const [page, pageData] = Layout.new(emit, {
    url: `/external-suppliers/${route.params.organId}`,
    search: {
        sent: false
    },
    rules: {
        name: 'required',
        cnpj: 'required|cnpj',
        address: 'required',
        email: 'required',
        phone: 'required',
        modality: 'required'
    },
    registered: false
})

onMounted(() => {
    pageData.selects()
})

</script>

<template>
    <div class="page page-colapsed">
        <main class="main">
            <HeaderColapseUi />
            <section v-if="page.registered" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div class="text-center content p-5 mx-auto">
                        <span class="title-icon success mb-3">
                            <ion-icon name="storefront" />
                        </span>
                        <h2>Fornecedor Registrado</h2>
                        <p>Obrigado por colaborar conosco!</p>
                    </div>
                </div>
            </section>

            <section v-if="!page.registered" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div class="w-100 text-center">
                        <span class="title-icon mb-3">
                            <ion-icon name="storefront" />
                        </span>
                        <h2>Registrar Fornecedor</h2>
                        <p>Preencha o formulário abaixo realizar o cadastro</p>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row"
                        @submit.prevent="() => pageData.save({}, () => page.registered = true, false)">
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
                        </div>
                    </form>
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

.title-icon {
    display: flex;
    justify-content: center;
}

.title-icon ion-icon {
    display: flex;
    padding: .8rem;
    font-size: 1.35em;
    border-radius: 12px;
    background-color: var(--color-base-tls);
    color: var(--color-base);
}


.title-icon.success ion-icon {
    background-color: rgba(40, 255, 76, 0.2);
    color: rgb(56, 231, 85);
}

.main-footer {
    text-align: center;
    position: initial
}
</style>