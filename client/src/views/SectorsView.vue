<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import organ from '@/stores/organ';
import { onMounted } from 'vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/sectors',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'IDENTIFICAÇÃO' },
        { key: 'unit.name', title: 'VÍNCULO' },
        { key: 'description', title: 'DESCRIÇÃO' },
    ],
    rules: {
        name: 'required',
        unit: 'required'
    }
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
                        <h2>Setores</h2>
                        <p>Listagem das Setores Atreladas às unidades do órgão <span class="txt-color">{{ organ.organ }}</span></p>
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
                                placeholder="Pesquise por partes do nome do setor">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-unit" class="form-label">Unidade</label>
                            <select name="unit" class="form-control" id="s-unit" v-model="page.search.unit">
                                <option value=""></option>
                                <option v-for="o in page.selects.units" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-description" class="form-label">Nome</label>
                            <input type="text" name="description" class="form-control" id="s-description"
                                v-model="page.search.description" placeholder="Pesquise por partes da descrição">
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
                        <h2>Registrar Setor</h2>
                        <p>Registrar um setor atrelado a uma unidade</p>
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
                                    placeholder="Nome de identificação do Setor" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="unit" class="form-label">Unidade</label>
                                <select name="unit" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.unit }" id="unit"
                                    v-model="page.data.unit">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.units" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" id="description"
                                    placeholder="Tipo de Setor: Escola Municipal, UBS, Setor Administrativo"
                                    v-model="page.data.description">
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