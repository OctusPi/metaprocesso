<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import { onMounted, watch } from 'vue';
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';

const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
    datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: '/users',
    datalist: props.datalist,
    header: [
        { key: 'name', title: 'NOME' },
        { key: 'email', title: 'E-MAIL' },
        { key: 'profile', title: 'PERFIL', sub: [{ key: 'passchange', title: 'Senha' }] },
        { key: 'status', title: 'STATUS', sub: [{ key: 'lastlogin', title: 'Ultimo Acesso:' }] }
    ],
    rules: {
        name: 'required',
        email: 'required|email',
        status: 'required',
        profile: 'required',
        modules: 'required',
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
                        <h2>Usuários</h2>
                        <p>Listagem dos usuários do sistema</p>
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
                            <input type="text" name="name" class="form-control" id="s-name"
                                v-model="page.search.name" placeholder="Pesquise por partes do nome do usuário">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-email" class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" id="s-email"
                                v-model="page.search.email" placeholder="user@mail.com">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="s-profile" class="form-label">Perfil</label>
                            <select name="profile" class="form-control" id="s-profile"
                                v-model="page.search.profile">
                                <option></option>
                                <option v-for="s in page.selects.profiles" :value="s.id" :key="s.id">
                                    {{ s.title }}
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
                        Actions.Delete(pageData.remove),
                    ]" :mounts="{
                        status: [Mounts.Cast(page.selects.status), Mounts.Status()],
                        profile: [Mounts.Cast(page.selects.profiles)],
                        passchange: [Mounts.Cast([{ id: 0, title: 'Ativa' }, { id: 1, title: 'Mudança de Senha' }])],
                    }" />
                </div>
            </section>

            <!-- Register -->
            <section v-if="page.ui.register" class="main-section container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Registrar Usuário</h2>
                        <p>Registro dos usuários do sistema</p>
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
                            <div class="col-sm-12">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.name }" id="name"
                                    placeholder="Sr. Snake" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.email }" id="email"
                                    placeholder="user@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="profile" class="form-label">Perfil</label>
                                <select name="profile" class="form-control"
                                    :class="{ 'form-control-alert': page.valids.profile }" id="profile"
                                    v-model="page.data.profile">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.profiles" :value="s.id" :key="s.id">
                                        {{ s.title }}
                                    </option>
                                </select>
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
                            <div class="col-sm-12">
                                <label for="modules" class="form-label">Modulos</label>
                                <InputDropMultSelect v-model="page.data.modules" :options="page.selects.modules"
                                    identify="modules" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="organs" class="form-label">Orgãos</label>
                                <InputDropMultSelect v-model="page.data.organs" :options="page.selects.organs"
                                    identify="organs" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="units" class="form-label">Unidades</label>
                                <InputDropMultSelect v-model="page.data.units" :options="page.selects.units"
                                    identify="units" />
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="sectors" class="form-label">Setores</label>
                                <InputDropMultSelect v-model="page.data.sectors" :options="page.selects.sectors"
                                    identify="sectors" />
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


<style scoped></style>