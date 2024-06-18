<script setup>
import InputMultSelect from '@/components/inputs/InputMultSelect.vue';
import masks from '@/utils/masks';
import { ref } from 'vue';

const search = ref({})

const props = defineProps({
    data: { type: Object },
    organs: { type: Array }
})

const selects = ref({
    organs: [],
    units: [],
    dfds: [],
})

</script>

<template>
    <div class="row g-3">
        <div class="col-sm-12 col-md-8">
            <label for="object" class="form-label">Objeto</label>
            <input type="text" name="object" class="form-control" id="object" v-model="search.object"
                placeholder="Pesquise por partes do objecto">
        </div>
        <div class="col-sm-12 col-md-4">
            <label for="s-organ" class="form-label">Orgão</label>
            <select name="organ" class="form-control" id="s-organ" @change="props.data.selects('organ', search.organ)">
                <option value=""></option>
                <option v-for="o in selects.organs" :key="o.id" :value="o.id">
                    {{ o.title }}
                </option>
            </select>
        </div>
        <div class="col-sm-12 col-md-8">
            <label for="s-unit" class="form-label">Unidade</label>
            <select v-model="search.unit" name="unit" class="form-control" id="s-unit">
                <option value=""></option>
                <option v-for="o in selects.units" :key="o.id" :value="o.id">
                    {{ o.title }}
                </option>
            </select>
        </div>
        <div class="col-sm-12 col-md-4">
            <label for="emission" class="form-label">Data</label>
            <input v-model="search.emission" type="text" name="emission" class="form-control" id="emission" placeholder="dd/mm/aaaa"
                v-maska:[masks.maskdate]>
        </div>
        <div class="d-flex flex-row-reverse mt-4">
            <button type="button" class="btn btn-action btn-action-primary">
                Pesquisar <i class="bi bi-search"></i>
            </button>
        </div>
    </div>
    <div v-if="false" class="col-sm-12 col-md-12">
        <label for="dfds" class="form-label">Selecione as DFDs</label>
        <InputMultSelect :options="selects.dfds" identify="dfds" />
    </div>
</template>