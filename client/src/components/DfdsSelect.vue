<script setup>
import InputMultSelect from '@/components/inputs/InputMultSelect.vue';
import Data from '@/services/data';
import masks from '@/utils/masks';
import Ui from '@/utils/ui';
import { onMounted, ref } from 'vue';

const props = defineProps({
    valid: { type: Boolean },
    identifier: { type: String },
    organ: { type: Number }
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
    search: {
        organ: props.organ
    },
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
    data.selects('organ', props.organ)
})

const accordionId = props.identifier + '-accordion'
const accordionCollapseId = accordionId + '-collapse'
const accordionCollapseHeaderId = accordionCollapseId + '-header'

</script>

<template>
    <div>
        <div class="accordion" :id="accordionId">
            <div class="accordion-item">
                <h2 class="accordion-header" :id="accordionCollapseHeaderId">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        :data-bs-target="'#' + accordionCollapseId" aria-expanded="false"
                        :aria-controls="accordionCollapseId">
                        <i class="bi bi-search me-2"></i>
                        Pesquisar por DFDs
                    </button>
                </h2>
                <div :id="accordionCollapseId" class="accordion-collapse collapse"
                    :aria-labelledby="accordionCollapseHeaderId" :data-bs-parent="'#' + accordionId">
                    <div class="accordion-body">
                        <div class="my-2">
                            <h2 class="txt-color text-center m-0">
                                <i class="bi bi-journal-album me-1"></i>
                                Selecione as DFDs
                            </h2>
                            <p class="validation txt-color-sec small text-center m-0"
                                :class="{ 'text-danger': props.valid }">
                                Preencha os campos abaixo para escolher as DFDs
                            </p>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-12 col-md-12">
                                <label for="protocol" class="form-label">Protocolo</label>
                                <input type="text" name="protocol" class="form-control" id="protocol"
                                    v-model="page.search.protocol" placeholder="Pesquise por partes do protocolo">
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
                                <label for="date_ini" class="form-label">Data</label>
                                <input v-model="page.search.date_ini" type="text" name="date_ini" class="form-control"
                                    id="date_ini" placeholder="dd/mm/aaaa" v-maska:[masks.maskdate]>
                            </div>
                            <div class="d-flex flex-row-reverse mt-4">
                                <button data-bs-toggle="collapse" :data-bs-target="'#' + accordionCollapseId"
                                    @click="data.listForSearch('organ')" type="button" class="btn btn-action btn-action-primary">
                                    Pesquisar <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="page.datalist.length > 0" class="mt-2 col-sm-12 col-md-12">
            <label for="dfds" class="form-label">Selecione as DFDs</label>
            <InputMultSelect :castkey="[
                { cast: 'protocol' },
                { cast: 'unit', property: 'name' },
                { cast: 'date_ini' },
                { cast: 'status', replace: { to: 'title', dataset: page.selects.status } },
                { cast: 'description' },
            ]" v-model="model" :options="page.datalist" identify="dfds" />
        </div>
        <div class="mt-4" v-if="page.datalist.length < 1">
            <div class="text-center txt-color-sec">
                <i class="bi bi-boxes fs-4"></i>
                <p class="small">Não foram localizados registros...</p>
            </div>
        </div>
    </div>
</template>