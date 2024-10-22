<script setup>
import { ref } from "vue"
import Mounts from "@/services/mounts"

const props = defineProps({
    header: { type: Array, default: () => [] },
    body: { type: Array, default: () => [] },
    count: { type: Boolean, default: true },
    virtual: { type: Object, default: () => ({}) },
    mounts: { type: Object },
    errmsg: { type: String, default: 'Não foram localizados registros' }
})

const userBody = ref(props.body)
const userHeader = ref(props.header)

function multiplexer(instance, key) {
    if (key.includes('.')) {
        let value = instance
        key.split('.').forEach((subk) => {
            if (Array.isArray(value)) {
                value = value.map((item) => item[subk]).join(',')
            } else {
                value = value[subk] ?? ''
            }
        })
        return value
    }

    return instance[key]
}

function applyMounters(instance, header) {
    const mInstance = { ...instance, _virtual: {} }

    Object.keys(props.virtual).forEach((key) => {
        mInstance._virtual[key] = props.virtual[key](instance)
    })

    return header.map((attr) => {
        if (!attr.key) {
            return { value: null, classes: [] }
        }

        const initial = multiplexer(mInstance, attr.key)
        const title = Mounts.StripHTML()(initial)

        if (props.mounts && props.mounts[attr.key]) {
            return props.mounts[attr.key].reduce((prev, current) => {
                const { value, classes } = current(
                    prev.value,
                    mInstance
                )
                prev.classes.push(...classes)
                if (value != null) {
                    prev.value = value
                }
                return prev
            }, { title: title.value, value: initial, classes: [] })
        }
        return { title: title.value, value: initial, classes: [] }
    })
}

function getTitle(item) {
    return item.classes.includes('has-title') ? item.title : null
}

function getValue(value, header) {
    return value && value != ''
        ? value
        : header.err || null
}

</script>

<template>
    <p v-if="body.length && props.count" class="small txt-color-sec ps-5">
        <i class="bi bi-grip-vertical"></i> {{ body.length.toString().padStart(2, '0') }} Registros Localizados
    </p>
    <div v-if="body.length" class="table-responsive-md">
        <table class="w-100">
            <thead>
                <tr>
                    <th scope="col" v-for="(item, i) in props.header" :key="i">
                        {{ item.title }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(instance, i) in userBody" :key="i">
                    <td v-for="(mounted, j) in applyMounters(instance, userHeader)" :key="j" class="align-middle">
                        <div class="p-0 m-0">
                            <div v-if="userHeader[j].key" class="small txt-color-sec p-0 m-0" :class="mounted.classes" :title="getTitle(mounted)">
                                <template v-if="!userHeader[j].isBool">
                                    {{ getValue(mounted.value, userHeader[j]) }}
                                </template>
                            </div>
                        </div>
                        <div class="p-0 m-0">
                            <span v-for="(submounted, k) in applyMounters(instance, userHeader[j].sub ?? [])" :key="k" class="inline-block small me-1" :title="getTitle(submounted)" :class="submounted.classes">
                                <template v-if="!userHeader[j].sub[k].isBool">
                                    {{ userHeader[j].sub[k].title }}
                                    {{ getValue(submounted.value, userHeader[j].sub[k]) }}
                                </template>
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else class="text-center p-4 txt-color-sec">
        <ion-icon name="ellipsis-horizontal-outline" class="fs-4"></ion-icon>
        <p class="p-0 m-0 small">{{ errmsg }}</p>
    </div>
</template>

<style scoped>
@import url('../../assets/css/reports.css');
</style>
