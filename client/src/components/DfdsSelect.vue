<script setup>
import Data from '@/services/data';
import Ui from '@/utils/ui';
import { onMounted, ref, watch } from 'vue';
import TableListSelect from './TableListSelect.vue';

const props = defineProps({
    valid: { type: Boolean },
    identifier: { type: String },
    organ: { type: [Number, String], defaut: null }
})
const emit = defineEmits(['callAlert', 'callRemove', 'getMeta'])
const model = defineModel()

const page = ref({
    baseURL: '/dfds',
    dataheader: [
        { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
        { obj: 'demandant', key: 'name', title: 'DEMANDANTE' },
        { obj: 'ordinator', key: 'name', title: 'ORDENADOR' },
        { obj: 'unit', key: 'name', title: 'ORIGEM', sub: [{ obj: 'organ', key: 'name' }] },
        { title: 'OBJETO', sub: [{ key: 'description', utils: ['truncate'] }] },
        { key: 'status', cast: 'title', title: 'SITUAÇÃO' }
    ],
    title: { primary: '', secondary: '' },
    uiview: { register: false, search: false },
    data: { items: [] },
    datalist: [],
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
    },

    items: {
        dataheader: []
    }
})

const data = new Data(page, emit, new Ui(page, '-'))

const accordionId = props.identifier + '-accordion'
const accordionCollapseId = accordionId + '-collapse'
const accordionCollapseHeaderId = accordionCollapseId + '-header'

const organ = ref(props.organ)

function loadMeta(organ) {
    data.selects('organ', organ)
    emit('getMeta', page)
}

watch(() => props.organ, (newVal) => {
    organ.value = newVal
    page.value.search.organ = newVal
    loadMeta(newVal)
})

onMounted(() => {
    loadMeta(props.organ)
})

</script>

<template>
    <div>
        <div class="accordion" :id="accordionId">
            <div class="accordion-item">
                <h2 class="accordion-header" :id="accordionCollapseHeaderId">
                    <button class="w-100 text-center px-2 py-3" type="button" :data-bs-toggle="[organ && 'collapse']"
                        :data-bs-target="'#' + accordionCollapseId" aria-expanded="false"
                        :aria-controls="accordionCollapseId">
                        <h2 class="txt-color text-center m-0">
                            <i class="bi bi-journal-album me-1"></i>
                            Localizar DFDs
                        </h2>
                        <p class="validation txt-color-sec small text-center m-0"
                            :class="{ 'text-danger': props.valid || !props.organ }">
                            {{
                                organ
                                    ? "Preencha os campos abaixo para localizar as DFDs"
                                    : "É necessário selecionar um órgão para continuar"
                            }}
                        </p>
                    </button>
                </h2>
                <div :id="accordionCollapseId" class="accordion-collapse collapse"
                    :aria-labelledby="accordionCollapseHeaderId" :data-bs-parent="'#' + accordionId">
                    <div class="accordion-body p-0">
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-sm-12 col-md-4">
                                    <label for="date_s_ini" class="form-label">Data Inicial</label>
                                    <VueDatePicker auto-apply v-model="page.search.date_i" :enable-time-picker="false"
                                        format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                        input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                        calendar-class-name="dp-custom-calendar"
                                        calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <label for="date_s_fin" class="form-label">Data Final</label>
                                    <VueDatePicker auto-apply v-model="page.search.date_f" :enable-time-picker="false"
                                        format="dd/MM/yyyy" model-type="yyyy-MM-dd"
                                        input-class-name="dp-custom-input-dtpk" locale="pt-br"
                                        calendar-class-name="dp-custom-calendar"
                                        calendar-cell-class-name="dp-custom-cell" menu-class-name="dp-custom-menu" />
                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <label for="s-protocol" class="form-label">Protocolo</label>
                                    <input type="text" name="protocol" class="form-control" id="s-protocol"
                                        v-model="page.search.protocol" placeholder="Número do Protocolo" />
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
                                <div class="col-sm-12 col-md-8">
                                    <label for="s-description" class="form-label">Objeto</label>
                                    <input type="text" name="description" class="form-control" id="s-description"
                                        v-model="page.search.description"
                                        placeholder="Pesquise por partes do Objeto do DFD" />
                                </div>

                                <div class="d-flex flex-row mt-4">
                                    <button @click="data.listForSearch('organ')" type="button"
                                        class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                        Localizar DFDs
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="model?.length > 0" class="mt-4">
                            <TableListSelect :count="false" :identify="props.identifier"
                                :casts="{ 'status': page.selects.status }" :header="page.dataheader" :body="model"
                                v-model="model" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="organ && page.datalist.length > 0" class="mt-4 form-neg-box">
            <TableListSelect :identify="props.identifier" :casts="{ 'status': page.selects.status }"
                :header="page.dataheader" :body="page.datalist" v-model="model" />
        </div>

        <div class="mt-4" v-if="organ && page.datalist.length < 1">
            <div class="text-center txt-color-sec">
                <i class="bi bi-boxes fs-4"></i>
                <p class="small">Não foram localizados registros...</p>
            </div>
        </div>
    </div>
</template>
