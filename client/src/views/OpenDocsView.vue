<script setup>
import { inject, reactive } from 'vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import TableList from '@/components/table/TableList.vue';
import InputDropMultSelect from '@/components/inputs/InputDropMultSelect.vue';

const sysapp = inject('sysapp')

const page = reactive( {
    url: '/opendocs',
    datalist: [],
    header: [
        { key: 'name', title: 'CATÁLOGO' },
        { key: 'comission.name', title: 'ORIGEM', sub: [{ key: 'organ.name' }] },
        { title: 'DESCRIÇÃO', sub: [{ key: 'description' }] }
    ],
    search: {
        sent: false
    },
    selects: {
        organs: [],
        doctypes: [
            {id:1, title:'Editais de credenciamento e/ou de pré-qualificação e respectivos anexos'},
            {id:2, title:'Avisos de contratação direta e respectivos anexos'},
            {id:3, title:'Editais de licitação (compras e/ou alienações) e respectivos anexos'},
            {id:4, title:'Atas de registro de preço'},
            {id:5, title:'Contratos e Termos aditivos'}
        ]
    },
    rules: {
        name: 'required',
        comission_id: 'required'
    }
})
</script>

<template>
    <main class="container">

            <header class="py-4 d-flex align-items-center">
                <img src="@/assets/imgs/logo.svg" class="logosystem">
                <div class="ms-2">
                    <h1 class="p-0 m-0">{{ sysapp.name }}</h1>
                    <p class="p-0 m-0 small">{{ sysapp.desc }}</p>
                </div>
            </header>

            <!-- List -->
            <section  class="main-section-opendocs container-fluid p-4">
                <div role="heading" class="inside-title mb-4">
                    <div>
                        <h2>Docs. Contratações</h2>
                        <p>
                            Documentos referentes a processos de aquisição de bens ou serviço em qualquer etapa, conforme os incisos III, IV e V. do §2º do art. 174 da Lei 14.133/2021. 
                        </p>
                    </div>
                </div>

                <!-- Search -->
                <div role="search" class="content container p-4 mb-4">
                    <form @submit.prevent="pageData.list" class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_ini" class="form-label">Data Inicial</label>
                            <VueDatePicker auto-apply v-model="page.search.date_i" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                :auto-position="false" locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="date_s_fin" class="form-label">Data Final</label>
                            <VueDatePicker auto-apply v-model="page.search.date_f" :enable-time-picker="false"
                                format="dd/MM/yyyy" model-type="yyyy-MM-dd" input-class-name="dp-custom-input-dtpk"
                                :auto-position="false" locale="pt-br" calendar-class-name="dp-custom-calendar"
                                calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                        </div>
                        <div class="col-sm-12 col-md-4">
                                <label for="organs" class="form-label">Orgãos</label>
                                <InputDropMultSelect v-model="page.search.organs" :options="page.selects.organs"
                                    identify="organs" label="name" />
                            </div>
                            <div class="col-sm-12">
                                <label for="doctypes" class="form-label">Tipos de Documentos</label>
                                <InputDropMultSelect v-model="page.search.doctypes" :options="page.selects.doctypes"
                                    identify="doctypes" label="title" />
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
                    
                    <TableList :sent="page.search.sent" :header="page.header" :body="page.datalist" />
                </div>
            </section>
            <FooterMainUi />
        </main>
</template>

<style scoped>
.logosystem{
    width: 50px;
    height: auto;
}

.footer-opendocs{
    font-size: 0.7rem;
}
</style>