<script setup>
import InputMultSelect from '@/components/inputs/InputMultSelect.vue';
import Data from '@/services/data';
import masks from '@/utils/masks';
import Ui from '@/utils/ui';
import { onMounted, ref } from 'vue';

const props = defineProps({
    valid: { type: Boolean }
})
const emit = defineEmits(['callAlert', 'callRemove'])
const model = defineModel()

const page = ref({
    baseURL: '/dfds',
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: { items: [] },
    datalist: [],
    dataheader: [],
    search: {},
    selects: {
        organs: [],
        units: [],
        status: [],
    },
    rules: {
        fields: {},
        valids: {}
    }
})

const data = new Data(page, emit, new Ui(page, '-'))

onMounted(() => {
    data.selects()
})

</script>

<template>
    <div>
        <div class="mb-4">
            <h2 class="txt-color text-center m-0">
                <i class="bi bi-search me-1"></i>
                Selecione as DFDs
            </h2>
            <p class="validation txt-color-sec small text-center m-0" :class="{ 'text-danger': props.valid }">
                Preencha os campos abaixo para escolher as DFDs
            </p>
        </div>
        <div class="row g-3">
            <div class="col-sm-12 col-md-8">
                <label for="object" class="form-label">Objeto</label>
                <input type="text" name="object" class="form-control" id="object" v-model="page.search.object"
                    placeholder="Pesquise por partes do objeto">
            </div>
            <div class="col-sm-12 col-md-4">
                <label for="s-organ" class="form-label">Orgão</label>
                <select name="organ" class="form-control" id="s-organ" v-model="page.search.organ"
                    @change="data.selects('organ', page.search.organ)">
                    <option value=""></option>
                    <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">
                        {{ o.title }}
                    </option>
                </select>
            </div>
            <div class="col-sm-12 col-md-8">
                <label for="s-unit" class="form-label">Unidade</label>
                <select v-model="page.search.unit" name="unit" class="form-control" id="s-unit">
                    <option value=""></option>
                    <option v-for="o in page.selects.units" :key="o.id" :value="o.id">
                        {{ o.title }}
                    </option>
                </select>
            </div>
            <div class="col-sm-12 col-md-4">
                <label for="emission" class="form-label">Data</label>
                <input v-model="page.search.emission" type="text" name="emission" class="form-control" id="emission"
                    placeholder="dd/mm/aaaa" v-maska:[masks.maskdate]>
            </div>
            <div class="d-flex flex-row-reverse mt-4">
                <button @click="data.listForSearch" type="button" class="btn btn-action btn-action-primary">
                    Pesquisar <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div v-if="page.datalist.length > 0" class="col-sm-12 col-md-12">
            <label for="dfds" class="form-label">Selecione as DFDs</label>
            <InputMultSelect :castkey="[
                { cast: 'protocol' },
                { cast: 'date_ini' },
                { cast: 'status', replace: { to: 'title', dataset: page.selects.status } },
                { cast: 'unit', replace: { to: 'title', dataset: page.selects.units } },
                { cast: 'description' },
            ]" v-model="model" :options="page.datalist" identify="dfds" />
        </div>
        <div v-if="page.datalist.length < 1">
            <div class="text-center txt-color-sec">
                <i class="bi bi-boxes fs-4"></i>
                <p class="small">Não foram localizados registros...</p>
            </div>
        </div>
    </div>
</template>