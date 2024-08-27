<script setup>
import { computed } from 'vue'
import TableListStatus from '@/components/table/TableListStatus.vue'
import utils from '@/utils/utils';

const props = defineProps({
    header: { type: Array },
    body: { type: Array, default: () => [] },
    casts: { type: Object },
    smaller: { type: Boolean, default: false },
    count: { type: Boolean, default: true },
    detachStatus: { type: Boolean, default: true },
    errmsg: { type: String, default: 'Não foram localizados registros' },
})

const body = computed(() => props.body)

const extractData = (data, key) => {
    return Array.isArray(data) && data.length
        ? data.map(item => item[key]).toString()
        : data[key];
}

const getData = (data, obj, key, cast = null, subject = 'id') => {
    const value = obj ? extractData(data[obj], key) : (data[key] ?? '');

    if (cast && props.casts[key]) {
        const castArr = typeof props.casts[key] === "function"
            ? props.casts[key](data)
            : props.casts[key];

        const datacast = castArr.find(item => item[subject] === value) ?? {};
        return datacast[cast] ?? '';
    }

    return value;
}
</script>

<template>
    <p v-if="body.length && props.count" class="small txt-color-sec ps-5">
        <i class="bi bi-grip-vertical"></i> {{ body.length.toString().padStart(2, '0') }} Registros Localizados
    </p>
    <div v-if="body.length" class="table-responsive-sm">
        <table class="w-100">
            <thead v-if="props.header">
                <tr>
                    <th scope="col" v-for="h in props.header" :key="h.key">
                        {{ h.title }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="b in body" :key="b.id">
                    <td v-for="h in props.header" :key="`${b.id}-${h.key}`" class="align-middle"
                        :class="[h.fclass && h.fclass(b)]">
                        <TableListStatus v-if="h.key === 'status' && props.detachStatus"
                            :data="getData(b, h.obj, h.key, h.cast)" />
                        <span v-else>{{ getData(b, h.obj, h.key, h.cast) }}</span>
                        <p v-if="h.sub" class="small txt-color-sec p-0 m-0">
                            <span v-for="s in h.sub" :key="s.key" class="inline-block small">
                                {{ utils.truncate(`${s.title ?? ''} ${getData(b, s.obj, s.key, s.cast)}`, 250) }}
                            </span>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else>
        <p class="d-flex align-items-center missing">
            <i class="bi bi-info-circle me-1"></i>
            {{ props.errmsg }}
        </p>
    </div>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>
