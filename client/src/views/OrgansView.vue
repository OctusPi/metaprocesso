<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import masks from '@/utils/masks';
import { onMounted, watch } from 'vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/organs',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cnpj' }] },
        { key: 'phone', title: 'CONTATO', sub: [{ key: 'email' }] },
        { key: 'postalcity', title: 'LOCALIZAÇÃO', sub: [{ key: 'address' }] },
        { key: 'status', cast: 'title', title: 'STATUS' }
    ],
    rules: {
        name: 'required',
        cnpj: 'required',
        phone: 'required',
        email: 'required|email',
        address: 'required',
        postalcity: 'required',
        postalcode: 'required',
        status: 'required'
    }
})

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
            <section v-if="!page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Órgãos</h2>
                        <p>Listagem dos órgãos disponíveis</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
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
                                placeholder="Pesquise por partes do nome do orgão">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-cnpj" class="form-label">CNPJ</label>
                            <input type="cnpj" name="cnpj" class="form-control" id="s-cnpj" v-model="page.search.cnpj"
                                placeholder="000.000.00/0000-00" v-maska:[masks.maskcnpj]>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-postalcity" class="form-label">Cidade</label>
                            <input type="postalcity" name="postalcity" class="form-control" id="s-postalcity"
                                v-model="page.search.postalcity" placeholder="Nome da Cidade">
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
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Órgão</h2>
                        <p>Preencha os dados abaixo para realizar o registro</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
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
                            <div class="mt-3 text-center position-relative c-logo">
                                <div class="v-logo position-absolute">
                                    <img v-if="page.data.logomarca" :src="page.data.logomarca" class="img-logo">
                                    <div v-else class="icon-logo mt-4">
                                        <ion-icon name="image"></ion-icon>
                                        <p>Adicionar Logo</p>
                                    </div>
                                </div>
                                <input type="file" name="logo" class="i-logo position-absolute"
                                    @change="(e) => pageData.upload(e, 'logomarca')">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Entidade Central" v-model="page.data.name">
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
                                    placeholder="orgao@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="postalcity" class="form-label">Cidade</label>
                                <input type="text" name="postalcity" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.postalcity }" id="postalcity"
                                    placeholder="Cidade - UF" v-model="page.data.postalcity">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="postalcode" class="form-label">CEP</label>
                                <input type="text" name="postalcode" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.postalcode }" id="postalcode"
                                    placeholder="00000-000" v-model="page.data.postalcode" v-maska:[masks.maskcep]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="tcecode" class="form-label">Código TCE</label>
                                <input type="text" name="tcecode" class="form-control" id="tcecode"
                                    placeholder="00000000" v-model="page.data.tcecode" maxlength="10">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="ibgecode" class="form-label">Código IBGE</label>
                                <input type="text" name="ibgecode" class="form-control" id="ibgecode"
                                    placeholder="00000000" v-model="page.data.ibgecode" maxlength="10">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.status }" id="status"
                                    v-model="page.data.status">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.status" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
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