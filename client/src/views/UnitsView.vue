<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import masks from '@/utils/masks';
import organ from '@/stores/organ';
import { onMounted } from 'vue';
import { watch } from 'vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/units',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cnpj' }] },
        { key: 'phone', title: 'CONTATO', sub: [{ key: 'email' }] },
        { title: 'VINCULO', sub: [{ key: 'address' }] }
    ],
    rules: {
        name: 'required',
        cnpj: 'required',
        phone: 'required',
        email: 'required|email',
        address: 'required',
    }
})

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
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
            <section v-if="!page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Unidades</h2>
                        <p>
                            Listagem das Unidades Atreladas ao
                            <span class="txt-color">{{ organ.get_organ()?.name }}</span>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
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
                        <div class="col-sm-12 col-md-4">
                            <label for="s-name" class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" id="s-name" v-model="page.search.name"
                                placeholder="Pesquise por partes do nome da unidade">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-cnpj" class="form-label">CNPJ</label>
                            <input type="cnpj" name="cnpj" class="form-control" id="s-cnpj" v-model="page.search.cnpj"
                                placeholder="000.000.00/0000-00" v-maska:[masks.maskcnpj]>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-address" class="form-label">Endereço</label>
                            <input type="address" name="address" class="form-control" id="s-address"
                                v-model="page.search.address" placeholder="Cidade, Rua, No.">
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
                        Actions.Delete(pageData.remove),
                    ]" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registro de Unidades</h2>
                        <p>Registrar uma unidade para o Órgão selecionado</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                            <ion-icon name="arrow-back" class="fs-5"></ion-icon>
                            Voltar
                        </button>
                    </div>
                </div>
                <div role="form" class="container p-0">
                    <form class="form-row" @submit.prevent="pageData.save()">
                        <div class="row m-0 mb-3 g-3 content p-4 pt-1">
                            <input type="hidden" name="id" v-model="page.id">
                            <div class="col-sm-12 col-md-8">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Nome Secretaria, Departamento, Unidade" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.cnpj }" id="cnpj"
                                    placeholder="00.000.000/0000-00" v-model="page.data.cnpj" v-maska:[masks.maskcnpj]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" name="phone" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.phone }" id="phone"
                                    placeholder="(00)9.0000-0000" v-model="page.data.phone" v-maska:[masks.maskphone]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.email }" id="email"
                                    placeholder="unidade@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
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

            <FooterMainUi />
        </main>
    </div>
</template>


<style scoped>
.c-logo {
    height: 120px;
}

.v-logo {
    width: 120px;
    height: 120px;
    left: calc(50% - 60px);
    border-radius: 50%;
    border: 1px dashed var(--color-base);
    background-color: var(--tls-blue);
}

.i-logo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    cursor: pointer;
    left: calc(50% - 60px);
    opacity: 0;
}

.img-logo {
    width: 118px;
    height: 118px;
    border-radius: 50%;
    object-fit: cover;
}

.icon-logo ion-icon {
    font-size: 2rem;
    opacity: 0.6;
}

.icon-logo p {
    font-size: 0.7rem;
    color: var(--color-text-secondary);
}
</style>