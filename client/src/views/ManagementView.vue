<script setup>
import { onMounted, ref } from 'vue'

import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';

const emit = defineEmits(['callAlert'])

const showSearch = ref(false)
const showRegister = ref(false)
const titlePrimaty = ref('')
const titleSecondary = ref('')
const frmSelects = ref({
    status: [],
    profiles:[],
    organs: [],
    units: [],
    sectors: [],
    modules: []
})

const page = ref({
    data: {id:0},
    rules:{
        fields: {
            name:'required',
            email:'required|email',
            status:'required',
            profile:'required',
            modules:'required'
        },
        valids:{}
    }
})

function toggleRegister(){
    showSearch.value = false
    showRegister.value = !showRegister.value

    if(showRegister.value === true){
        titlePrimaty.value = 'Registro de Usuários'
        titleSecondary.value = 'Insira os dados para registro e permissionamento de usuários'
    }
}

function toggleSearch(){
    showSearch.value = !showSearch.value
    showRegister.value = false
}

function save(){
    const validation = forms.checkform(page.value.data, page.value.rules);
    if(!validation.isvalid){
        emit('callAlert', notifys.warning(validation.message))
        return
    }
}

onMounted(() => {

})

</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-house-gear',
                title: 'Gestão e Controle Organizacional',
                description: 'Estrutura e Perminissionamento de Usuários'
            }" />

            <div class="box p-4 mb-4 rounded-4">
                <div class="d-md-flex justify-content-between align-items-center mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ titlePrimaty }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ titleSecondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <div class="dropdown">
                            <button type="button" class="btn btn-action btn-action-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                                <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Gestão</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <RouterLink to="/organs" class="dropdown-item">
                                        <i class="bi bi-building-fill-gear me-1"></i> Orgãos
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/units" class="dropdown-item">
                                        <i class="bi bi-house-gear-fill me-1"></i> Unidades
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/sectors" class="dropdown-item">
                                        <i class="bi bi-grid-fill me-1"></i> Setores
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/programs" class="dropdown-item">
                                        <i class="bi bi-circle-square me-1"></i> Programas
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/dotations" class="dropdown-item">
                                        <i class="bi bi-piggy-bank-fill me-1"></i> Dotações
                                    </RouterLink>
                                </li>
                                <li>
                                    <RouterLink to="/management" class="dropdown-item">
                                        <i class="bi bi-person-bounding-box me-1"></i> Usuários
                                    </RouterLink>
                                </li>
                            </ul>
                        </div>
                        <button @click="toggleRegister" type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                        <button @click="toggleSearch" type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-search"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Pesquisar</span>
                        </button>
                        <button type="button" class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-file-richtext"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Exportar</span>
                        </button>
                    </div>
                </div>

                <div v-if="showSearch" id="search-box" class="mb-4">
                    Pesquisar
                </div>

                <div v-if="!showRegister" id="list-box" class="mb-4">
                    Lista
                </div>

                <div v-if="showRegister" id="register-box">
                    <form class="form-row" @submit.prevent="save">
                        <input type="hidden" name="id" v-model="page.data.id">
                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" :class="{'form-control-alert' : page.rules.valids.name}"
                                id="name" placeholder="Sr Tromba" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" :class="{'form-control-alert' : page.rules.valids.email}"
                                id="email" placeholder="user@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control" :class="{'form-control-alert' : page.rules.valids.status}"
                                id="status"  v-model="page.data.status">
                                    <option value=""></option>
                                    <option v-for="s in frmSelects.status" :value="s.id" :key="s.id">{{ s.title }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="organs" class="form-label">Orgãos</label>
                                <select name="organs" class="form-control" :class="{'form-control-alert' : page.rules.valids.organs}"
                                id="organs"  v-model="page.data.organs">
                                    <option value=""></option>
                                    <option v-for="s in frmSelects.organs" :value="s.id" :key="s.id">{{ s.title }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="units" class="form-label">Unidades</label>
                                <select name="units" class="form-control" :class="{'form-control-alert' : page.rules.valids.units}"
                                id="units"  v-model="page.data.units">
                                    <option value=""></option>
                                    <option v-for="s in frmSelects.units" :value="s.id" :key="s.id">{{ s.title }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="sectors" class="form-label">Setores</label>
                                <select name="sectors" class="form-control" :class="{'form-control-alert' : page.rules.valids.sectors}"
                                id="sectors"  v-model="page.data.sectors">
                                    <option value=""></option>
                                    <option v-for="s in frmSelects.sectors" :value="s.id" :key="s.id">{{ s.title }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="profile" class="form-label">Perfil</label>
                                <select name="profile" class="form-control" :class="{'form-control-alert' : page.rules.valids.profile}"
                                id="profile"  v-model="page.data.profile">
                                    <option value=""></option>
                                    <option v-for="s in frmSelects.profiles" :value="s.id" :key="s.id">{{ s.title }}</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="modules" class="form-label">Modulos</label>
                                <select name="modules" class="form-control" :class="{'form-control-alert' : page.rules.valids.modules}"
                                id="modules"  v-model="page.data.modules">
                                    <option value=""></option>
                                    <option v-for="s in frmSelects.modules" :value="s.id" :key="s.id">{{ s.title }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="toggleRegister" type="button" class="btn btn-outline-warning">Cancelar <i class="bi bi-x-circle"></i></button>
                            <button type="submit" class="btn btn-outline-primary mx-2">Salvar <i class="bi bi-check2-circle"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</template>