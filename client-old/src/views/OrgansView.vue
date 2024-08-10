<script setup>
import { onMounted, ref, watch } from 'vue'
import masks from '@/utils/masks'
import MainNav from '@/components/MainNav.vue';
import MainHeader from '@/components/MainHeader.vue';
import TableList from '@/components/TableList.vue';
import ManagementNav from '@/components/ManagementNav.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';

const emit = defineEmits(['callAlert', 'callRemove'])
const props = defineProps({ datalist: { type: Array, default: () => [] } })
const page = ref({
    baseURL: '/organs',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    search: {},
    data: {},
    datalist: props.datalist,
    dataheader: [
        { key: 'name', title: 'IDENTIFICAÇÃO', sub: [{ key: 'cnpj' }] },
        { key: 'phone', title: 'CONTATO', sub: [{ key: 'email' }] },
        { key: 'postalcity', title: 'LOCALIZAÇÃO', sub: [{ key: 'address' }] },
        { key: 'status', cast: 'title', title: 'STATUS' }
    ],
    selects: {
        status: []
    },
    rules: {
        fields: {
            name: 'required',
            cnpj: 'required',
            phone: 'required',
            email: 'required|email',
            address: 'required',
            postalcity: 'required',
            postalcode: 'required',
            status: 'required'
        },
        valids: {}
    }
})

const ui = new Ui(page, 'Órgãos')
const data = new Data(page, emit, ui)

function handleFile(event) {
	const file = event.target.files[0]
	if (file) {

		const reader  = new FileReader()
		reader.readAsDataURL(file)
		reader.onloadend = () =>{
			page.value.data.logomarca = reader.result
		}
	}
}

watch(() => props.datalist, (newdata) => {
    page.value.datalist = newdata
})

onMounted(() => {
    data.selects()
    data.list()
})

</script>

<template>
    <main class="container-primary">
        <MainNav />
        <section class="container-main">
            <MainHeader :header="{
                icon: 'bi-building-gear',
                title: 'Gerenciamento de Orgão',
                description: 'Registro de Unidades Executoras Centralizadoras'
            }" />

            <div class="box box-main p-0 rounded-4">
                <!--HEDER PAGE-->
                <div class="d-md-flex justify-content-between align-items-center w-100 px-4 px-md-5 pt-5 mb-4">
                    <div class="info-list">
                        <h2 class="txt-color p-0 m-0">{{ page.title.primary }}</h2>
                        <p class="small txt-color-sec p-0 m-0">{{ page.title.secondary }}</p>
                    </div>
                    <div class="action-buttons d-flex my-2">
                        <ManagementNav />
                        <button @click="ui.toggle('register')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-plus-circle"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Adicionar</span>
                        </button>
                        <button @click="ui.toggle('search')" type="button"
                            class="btn btn-action btn-action-primary ms-2">
                            <i class="bi bi-search"></i>
                            <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Localizar</span>
                        </button>
                    </div>
                </div>

                <!--BOX LIST-->
                <div v-if="!page.uiview.register" id="list-box" class="inside-box mb-4">

                    <!--SEARCH BAR-->
                    <div v-if="page.uiview.search" id="search-box" class="px-4 px-md-5 mb-5">
                        <form @submit.prevent="data.list" class="row g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="s-name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" id="s-name"
                                    v-model="page.search.name" placeholder="Pesquise por partes do nome do orgão">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-cnpj" class="form-label">CNPJ</label>
                                <input type="cnpj" name="cnpj" class="form-control" id="s-cnpj"
                                    v-model="page.search.cnpj" placeholder="000.000.00/0000-00"
                                    v-maska:[masks.maskcnpj]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="s-postalcity" class="form-label">Cidade</label>
                                <input type="postalcity" name="postalcity" class="form-control" id="s-postalcity"
                                    v-model="page.search.postalcity" placeholder="Nome da Cidade">
                            </div>

                            <div class="d-flex flex-row-reverse mt-4">
                                <button type="submit" class="btn btn-outline-primary mx-2">Aplicar Filtro <i
                                        class="bi bi-check2-circle"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- DATA LIST -->
                    <TableList @action:update="data.update" @action:delete="data.remove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'delete']"
                        :casts="{ 'status': page.selects.status }" />
                </div>

                <!--BOX REGISTER-->
                <div v-if="page.uiview.register" id="register-box" class="inside-box px-4 px-md-5 mb-4">
                    <form class="form-row" @submit.prevent="data.save()">
                        <input type="hidden" name="id" v-model="page.data.id">
                        
                        <div class="my-4 text-center position-relative c-logo">
                            <div class="v-logo position-absolute">
                                <img v-if="page.data.logomarca" :src="page.data.logomarca"  class="img-logo">
                                <div v-else class="icon-logo mt-4">
                                    <i class="bi bi-image"></i>
                                    <p>Adicionar Logo</p>
                                </div>
                            </div>
                            <input type="file" name="logo" class="i-logo position-absolute" @change="handleFile">
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.name }" id="name"
                                    placeholder="Entidade Central" v-model="page.data.name">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.cnpj }" id="cnpj"
                                    placeholder="00.000.000/0000-00" v-model="page.data.cnpj" v-maska:[masks.maskcnpj]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" name="phone" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.phone }" id="phone"
                                    placeholder="(00)9.0000-0000" v-model="page.data.phone" v-maska:[masks.maskphone]>
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.email }" id="email"
                                    placeholder="orgao@example.com" v-model="page.data.email">
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" name="address" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.address }" id="address"
                                    placeholder="Logradouro, número - bairro (Rua do Amor, 110 - Centro)"
                                    v-model="page.data.address">
                            </div>
                        </div>

                        <div class="row mb-3 g-3">
                            <div class="col-sm-12 col-md-4">
                                <label for="postalcity" class="form-label">Cidade</label>
                                <input type="text" name="postalcity" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.postalcity }" id="postalcity"
                                    placeholder="Cidade - UF" v-model="page.data.postalcity">
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="postalcode" class="form-label">CEP</label>
                                <input type="text" name="postalcode" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.postalcode }" id="postalcode"
                                    placeholder="00000-000" v-model="page.data.postalcode" v-maska:[masks.maskcep]>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control"
                                    :class="{ 'form-control-alert': page.rules.valids.status }" id="status"
                                    v-model="page.data.status">
                                    <option value=""></option>
                                    <option v-for="s in page.selects.status" :value="s.id" :key="s.id">{{ s.title }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mt-4">
                            <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">Cancelar <i
                                    class="bi bi-x-circle"></i></button>
                            <button type="submit" class="btn btn-outline-primary mx-2">Salvar <i
                                    class="bi bi-check2-circle"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
    .c-logo{
        height: 120px;
    }
    .v-logo{
        width: 120px;
        height: 120px;
        left: calc(50% - 60px);
        border-radius: 50%;
        border: 1px dashed var(--color-base);
    }

    .i-logo{
        width: 120px;
        height: 120px;
        border-radius: 50%;
        cursor: pointer;
        left: calc(50% - 60px);
        opacity: 0;
    }

    .img-logo{
        width: 118px;
        height: 118px;
        border-radius: 50%;
    }

    .icon-logo i{
        font-size: 2rem;
        opacity: 0.6;
    }

    .icon-logo p{
        font-size: 0.7rem;
        color: var(--color-text-secondary);
    }
</style>