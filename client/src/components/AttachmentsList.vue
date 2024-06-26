<script setup>
import { onMounted, ref, watch } from 'vue'
import TableList from '@/components/TableList.vue';
import InputUpload from '@/components/inputs/InputUpload.vue';
import Ui from '@/utils/ui';
import Data from '@/services/data';

const emit = defineEmits(['callAlert', 'callRemove'])

const props = defineProps({
    origin: { type: String },
    protocol: { type: String },
    types: { type: Object }
})

const page = ref({
    baseURL: `/attachments/${props.origin}/${props.protocol}`,
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    search: {},
    data: {
        origin: props.origin,
        protocol: props.protocol,
    },
    datalist: [],
    dataheader: [
        { key: 'type', title: 'TIPO', cast: 'title' },
        { key: 'updated_at', title: 'ENVIADO EM' },
    ],
    selects: {
        types: props.types
    },
    rules: {
        fields: {
            origin: 'required',
            protocol: 'required',
            type: 'required',
            file: 'required',
        },
        valids: {}
    }
})
const ui = new Ui(page, 'Anexos')
const data = new Data(page, emit, ui)

watch(() => props.protocol, (newdata) => {
    page.value.baseURL = `/attachments/${props.origin}/${newdata}`
    page.value.data.protocol = newdata
    data.list()
})

watch(() => page.value.data, (newdata) => {
    page.value.data = newdata
    page.value.data.origin = props.origin
    page.value.data.protocol = props.protocol
})

onMounted(() => {
    data.list()
})
</script>

<template>
    <div>
        <template v-if="props.origin && props.protocol">
            <div class="mb-4">
                <h2 class="txt-color text-center m-0">
                    <i class="bi bi-file-earmark-pdf"></i>
                    {{ page.title.primary }}
                </h2>
                <p class="txt-color-sec small text-center m-0">
                    {{ page.title.secondary }}
                </p>
            </div>
            <!--BOX REGISTER-->
            <div v-if="page.uiview.register" id="register-box" class="mb-4 p-0">
                <input type="hidden" name="id" v-model="page.data.id">
                <div class="row mb-3 g-3">
                    <div class="col-sm-12 col-md-12">
                        <label for="type" class="form-label">Tipos</label>
                        <select name="type" class="form-control"
                            :class="{ 'form-control-alert': page.rules.valids.type }" id="type"
                            v-model="page.data.type">
                            <option value=""></option>
                            <option v-for="s in page.selects.types" :value="s.id" :key="s.id">{{ s.title }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <InputUpload v-model="page.data.file" :valid="page.rules.valids.file" />
                    </div>
                </div>

                <div class="d-flex flex-row-reverse mt-4">
                    <button @click="ui.toggle('list')" type="button" class="btn btn-outline-warning">Cancelar <i
                            class="bi bi-x-circle"></i></button>
                    <button @click="data.save()" type="button" class="btn btn-outline-primary mx-2">Salvar <i
                            class="bi bi-check2-circle"></i></button>
                </div>
            </div>
            <div v-if="!page.uiview.register">
                <div class="inside-box mb-4 form-neg-box">
                    <TableList @action:update="data.update" @action:fastdelete="data.fastremove" :header="page.dataheader"
                        :body="page.datalist" :actions="['update', 'fastdelete']"
                        :casts="{ 'type': page.selects.types }" />
                </div>
                <div class="action-buttons d-flex ms-auto">
                    <button @click="ui.toggle('register')" type="button"
                        class="btn btn-action btn-action-primary ms-auto">
                        <i class="bi bi-plus-circle"></i>
                        <span class="title-btn-action ms-2 d-none d-md-block d-lg-inline">Novo Anexo</span>
                    </button>
                </div>
            </div>

        </template>
        <template v-if="!props.origin || !props.protocol">
            <h2 class="txt-color text-center m-0">
                <i class="bi bi-search me-1"></i>
                Erro ao iniciar anexos
            </h2>
            <p class="txt-color-sec small text-center m-0">
                É necessário haver uma origem e um protocolo para continuar
            </p>
        </template>
    </div>
</template>