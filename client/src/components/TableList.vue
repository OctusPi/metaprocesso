<script setup>
import { ref, watch } from 'vue'
import ActionNav from './ActionNav.vue'
import TableListStatus from './TableListStatus.vue'

const props = defineProps({
    header: { type: Array },
    body: { type: Array, default: () => [] },
    actions: { type: Array },
    casts: { type: Object },
    smaller: {type: Boolean, default:() => false},
    count:{type: Boolean, default: () => true}
})

const emit = defineEmits([
    'action:update',
    'action:delete',
    'action:fastdelete',
    'action:download',
    'action:members',
    'action:extinction',
    'action:items'
])

const body = ref(props.body)

function orderBy(key) {
    body.value.sort((a, b) => {
        if (typeof a[key] === 'string') {
            return a[key].localeCompare(b[key])
        }

        return a[key] - b[key]
    })
}

function getdata(data, obj, key, cast = null, subject = 'id') {
    const value = obj ? data[obj][key] : data[key] ?? '';

    if (cast && props?.casts[key]) {
        const datacast = (props.casts[key]).find(obj => obj[subject] === value) ?? {}
        return datacast[cast] ?? ''
    }

    return value
}

function propagateEmit(emt) {
    emit(emt.e, emt.i)
}

watch(() => props.body, (newValue) => {
    body.value = newValue
});
</script>

<template>
    <p v-if="body.length && props.count" class="small txt-color-sec ps-5">
        <i class="bi bi-grip-vertical"></i> {{ (body.length).toString().padStart(2, '0') }} Registros Localizados
    </p>
    <div v-if="body.length" class="table-responsive-sm">
        <table class="table-borderless table-striped table-hover" :class="props.smaller ? 'table tablesm' : 'table'">
            <thead v-if="props.header">
                <tr>
                    <th scope="col" v-for="h in props.header" :key="h.key" @click="orderBy(h.key)">
                        {{ h.title }} <i class="bi bi-arrow-down table-order-icon"></i>
                    </th>
                    <th v-if="props.actions" scope="col"></th>
                </tr>
            </thead>
            <tbody v-if="body">
                <tr v-for="b in body" :key="b.id">
                    <td v-for="h in props.header" :key="`${b.id}-${h.key}`" class="align-middle">
                       <TableListStatus v-if="h.key === 'status'" :data="getdata(b, h?.obj, h.key, h?.cast)"  />
                       <template v-else>{{ getdata(b, h?.obj, h.key, h?.cast) }}</template>
                        <p v-if="h.sub" class="small txt-color-sec p-0 m-0">
                            <span v-for="s in h.sub" :key="s.key" class="inline-block small">
                                {{ `${s.title ?? ''} ${getdata(b, s?.obj, s.key, s?.cast)}` }}
                            </span>
                        </p>
                    </td>
                    <td v-if="props.actions" class="align-middle">
                        <ActionNav :id="b.id" :calls="props.actions" @action="propagateEmit" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else class="text-center txt-color-sec">
        <i class="bi bi-boxes fs-4"></i>
        <p class="small">Não foram localizados registros...</p>
    </div>
</template>

<style>
th{
    white-space:nowrap;
}

.table tr th:first-child {
    padding-left: 50px;
}

.table tr td:first-child {
    padding-left: 50px;
}

.table tr td:last-child {
    padding-right: 4cap;
    text-align: end;
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

.tablesm tr td{
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
</style>