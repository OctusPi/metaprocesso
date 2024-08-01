<script setup>
import { ref } from 'vue'
import TableListStatus from '@/components/TableListStatus.vue'
import utils from '@/utils/utils';

const props = defineProps({
    header: { type: Array },
    body: { type: Array, default: () => [] },
    casts: { type: Object },
    smaller: {type: Boolean, default:() => false},
    count:{type: Boolean, default: () => true},
    detachStatus: {type: Boolean, default: true},
})

const body = ref(props.body)

function extract_data(data, key) {
    if (data.length) {
        const extract = []
        data.forEach(a => {
            extract.push(a[key])
        })

        return extract.toString()
    }

    return data[key]
}

function getdata(data, obj, key, cast = null, subject = 'id') {
    const value = obj ? extract_data(data[obj], key) : data[key] ?? '';

    if (cast && props.casts[key]) {
        let castArr = typeof props.casts[key] === "function"
            ? props.casts[key](data)
            : props.casts[key]
        const datacast = castArr.find(obj => obj[subject] === value) ?? {}
        return datacast[cast] ?? ''
    }

    return value
}


</script>

<template>
    <p v-if="body.length && props.count" class="small txt-color-sec ps-5">
        <i class="bi bi-grip-vertical"></i> {{ (body.length).toString().padStart(2, '0') }} Registros Localizados
    </p>
    <div v-if="body.length" class="table-responsive-sm">
        <table class="w-100" :class="props.smaller ? 'tablesm' : ''">
            <thead v-if="props.header">
                <tr>
                    <th scope="col" v-for="h in props.header" :key="h.key">
                        {{ h.title }}
                    </th>
                </tr>
            </thead>
            <tbody v-if="body">
                <tr v-for="b in body" :key="b.id">
                    <td v-for="h in props.header" :key="`${b.id}-${h.key}`" class="align-middle">
                       <TableListStatus v-if="h.key === 'status' && props.detachStatus" :data="getdata(b, h?.obj, h.key, h?.cast)"  />
                       <template v-else>{{ getdata(b, h?.obj, h.key, h?.cast) }}</template>
                        <p v-if="h.sub" class="small txt-color-sec p-0 m-0">
                            <span v-for="s in h.sub" :key="s.key" class="inline-block small">
                                {{ utils.truncate(`${s.title ?? ''} ${getdata(b, s?.obj, s.key, s?.cast)}`, 250) }}
                            </span>
                        </p>
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

<style scoped>
*{
    font-size: 0.7rem;
    color: black !important;
}
table,
th,
td {
    border: 1px solid black;
    border-collapse: collapse;
    border-radius: 0;
}

td {
    padding: 2px 5px;
    text-align: left !important;
}

</style>