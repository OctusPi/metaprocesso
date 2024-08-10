<script setup>
import { ref, watch } from 'vue'
import utils from '@/utils/utils';
import TableListStatus from './TableListStatus.vue'

const props = defineProps({
    header: { type: Array },
    body: { type: Array, default: () => [] },
    casts: { type: Object, defaul: () => {} },
    smaller: { type: Boolean, default: () => false },
    count: { type: Boolean, default: () => true },
    identify: { type: String },
})

const model = defineModel({default: ''})
const body = ref(props.body)
const checkName = props.identify + '_radio'

function orderBy(key) {
    body.value.sort((a, b) => {
        if (typeof a[key] === 'string') {
            return a[key].localeCompare(b[key])
        }

        return a[key] - b[key]
    })
}

function extract_data(data, key){
    if(data.length){
        const extract = []
        data.forEach(a => {
            extract.push(a[key])
        })

        return extract.toString()
    }

    return data[key]
}

function getdata(data, obj, key, cast = null, subject = 'id') {
    const value = obj ? extract_data(data[obj],key) : data[key] ?? '';
    
    if (cast && props?.casts[key]) {

        const datacast = (props.casts[key]).find(ob => ob[subject] === value) ?? {}
        return datacast[cast] ?? ''
    }

    return value
}

function matchUtils(str, utilsList = []) {
    let aux = str
    utilsList.forEach((util) => {
        switch (util) {
            case 'html':
                aux = utils.stripHTML(str)
                break
            case 'truncate':
                aux = utils.truncate(str, 200)
                break
        }
    })

    return aux
}

function checkTheBox(e) {
    e.currentTarget.firstChild.firstChild.click()
}

function findModelById(item) {
    return model.value.id == item.id
}

watch(() => props.body, (newValue) => {
    body.value = newValue
});

</script>

<template>
    <p v-if="body.length && props.count" class="small txt-color-sec text-end pe-5">
        <i class="bi bi-grip-vertical"></i> {{ (body.length).toString().padStart(2, '0') }} Registros Localizados
    </p>
    <div v-if="body.length" class="table-responsive-sm">
        <table class="table-borderless table-hover" :class="props.smaller ? 'table tablesm' : 'table'">
            <thead v-if="props.header">
                <tr>
                    <th></th>
                    <th scope="col" v-for="h in props.header" :key="h.key" @click="orderBy(h.key)">
                        {{ h.title }} <i class="bi bi-arrow-down table-order-icon"></i>
                    </th>
                </tr>
            </thead>
            <tbody v-if="body">
                <tr @click="checkTheBox" v-for="b in body" :key="b.id" :class="{ 'active': findModelById(b) }">
                    <td class="align-middle">
                        <input class="form-check-input d-none" type="radio" :name="checkName" v-model="model"
                            :value="b" :id="b.id + checkName">
                        <i class="bi fs-5"
                            :class="[findModelById(b) ? 'txt-color-base bi-check-circle-fill' : 'bi-circle']"></i>
                    </td>
                    <td v-for="h in props.header" :key="`${b.id}-${h.key}`" class="align-middle">
                        <TableListStatus v-if="h.key === 'status'" :data="getdata(b, h?.obj, h.key, h?.cast)" />
                        <template v-else>{{ getdata(b, h?.obj, h.key, h?.cast) }}</template>
                        <p v-if="h.sub" class="small txt-color-sec p-0 m-0">
                            <span v-for="s in h.sub" :key="s.key" class="inline-block small">
                                {{ matchUtils(`${s.title ?? ''} ${getdata(b, s?.obj, s.key, s?.cast)}`,
                                    s?.utils) }}
                            </span>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style>
th {
    white-space: nowrap;
}

table td:nth-child(2) {
    white-space: nowrap;
  }


.table tr th:first-child {
    padding-left: 50px;
}

.table tr td:first-child {
    padding-left: 50px;
}

.table tr td:last-child {
    padding-right: 4cap;
}

.table,
.table th,
.table td {
    background-color: transparent !important;
    color: var(--color-text);
}

.table th {
    cursor: pointer;
    font-weight: 600;
    font-size: small;
}

.table-order-icon {
    font-size: 0.6rem;
}

.table th:hover i {
    color: var(--color-base);
}

.tablesm tr td {
    font-size: 0.85rem !important;
}

.tablesm tr th:first-child {
    padding-left: 5px !important;
}

.tablesm tr td:first-child {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
    padding-left: 5px !important;
}

.tablesm tr td:last-child {
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
    padding-right: 5px !important;
    text-align: end;
}

tr {
    cursor: pointer;
}

tr.active {
    background-color: var(--color-shadow);
}
</style>