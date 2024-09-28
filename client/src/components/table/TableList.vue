<script setup>
import { ref, watch, toRaw } from "vue"
import Mounts from "@/services/mounts"

const props = defineProps({
    body: { type: Array, default: () => [] },
    header: { type: Array, default: () => [] },
    actions: { type: Array },
    virtual: { type: Object, default: () => ({}) },
    mounts: { type: Object },
    smaller: { type: Boolean },
    sent: { type: Boolean, default: true },
    order: { type: Boolean, default: true },
    count: { type: Boolean, default: true },
    secondary: { type: Boolean, default: false },
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

function orderBy(e, key) {
    if (e.target.dataset.asc) {
        e.target.removeAttribute('data-asc')
        userBody.value.sort((a, b) => {
            return a[key] < b[key] ? -1 : a[key] > b[key] ? 1 : 0
        })
    } else {
        e.target.dataset.asc = true
        userBody.value.sort((a, b) => {
            return a[key] < b[key] ? 1 : a[key] > b[key] ? -1 : 0
        })
    }
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
        : header.err || '-'
}

function checkInput(e) {
    if (e.target.tagName != 'INPUT') {
        e.currentTarget.querySelector('input').click()
    }
}

watch(() => props.body, (newval) => {
    userBody.value = newval
})

</script>

<template>
    <div v-if="sent" class="tablelist" :class="[props.secondary ? 'tablelist-sec' : 'tablelist-prim']">
        <div v-if="body.length" class="table-responsive-md">
            <table class="m-0 table-borderless table-striped table-hover"
                :class="[props.smaller ? 'table tablesm' : 'table']">
                <thead>
                    <tr>
                        <th v-if="$slots.select"></th>
                        <th v-for="(item, i) in props.header" :key="i"
                            @click="(e) => props.order && orderBy(e, item.key)">
                            <div class="d-flex align-items-center gap-1">
                                {{ item.title }}
                                <ion-icon v-if="props.order" name="swap-vertical-outline"
                                    class="table-order-icon fs-6"></ion-icon>
                            </div>
                        </th>
                        <th v-if="props.actions && !$slots.select"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(instance, i) in userBody" :key="i" @click="(e) => $slots.select && checkInput(e)"
                        :class="[$slots.select && 'cursor-pointer']">
                        <td v-if="$slots.select" class="align-middle">
                            <slot :instance="toRaw(instance)" name="select"></slot>
                        </td>
                        <td v-for="(mounted, j) in applyMounters(instance, userHeader)" :key="j" class="align-middle">
                            <div>
                                <div v-if="userHeader[j].key" class="small txt-color-sec" :class="mounted.classes"
                                    :title="getTitle(mounted)">
                                    <template v-if="!userHeader[j].isBool">
                                        {{ getValue(mounted.value, userHeader[j]) }}
                                    </template>
                                    <template v-else>
                                        <ion-icon v-if="mounted.value && mounted.value != ''" name="checkmark-outline"
                                            class="fs-5 text-success" />
                                        <ion-icon v-else name="close-outline" class="fs-5 text-danger" />
                                    </template>
                                </div>
                            </div>
                            <div>
                                <span v-for="(submounted, k) in applyMounters(instance, userHeader[j].sub ?? [])"
                                    :key="k" class="inline-block small me-1" :title="getTitle(submounted)"
                                    :class="submounted.classes">
                                    <template v-if="!userHeader[j].sub[k].isBool">
                                        {{ userHeader[j].sub[k].title }}
                                        {{ getValue(submounted.value, userHeader[j].sub[k]) }}
                                    </template>
                                    <template v-else>
                                        <ion-icon v-if="submounted.value && submounted.value != ''"
                                            name="checkmark-outline" class="fs-5 text-success" />
                                        <ion-icon v-else name="close-outline" class="fs-5 text-danger" />
                                    </template>
                                </span>
                            </div>
                        </td>
                        <td v-if="props.actions && !props.selectable" class="align-middle">
                            <div class="dropdown d-flex">
                                <button class="btn btn-sm txt-color ms-auto" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <ion-icon name="ellipsis-vertical-outline" class="fs-6" />
                                </button>
                                <ul class="dropdown-menu px-2 py-3">
                                    <li>
                                        <a v-for="(item, j) in props.actions" :key="j"
                                            class="dropdown-item item-action-menu d-flex align-items-center" href="#"
                                            @click.prevent="item.action && item.action(instance.id)"
                                            :data-bs-target="item.modal" :data-bs-toggle="item.modal ? 'modal' : null">
                                            <ion-icon :name="item.icon" class="me-2" />
                                            {{ item.title }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="text-center p-4 txt-color-sec">
            <ion-icon name="ellipsis-horizontal-outline" class="fs-4"></ion-icon>
            <p class="p-0 m-0 small">Não foram localizados registros...</p>
        </div>
    </div>
    <div v-else class="text-center txt-color-sec p-4">
        <ion-icon name="chatbox-ellipses-outline" class="fs-3"></ion-icon>
        <p class="p-0 m-0 small">Aplique o filtro na opção localizar, para visualizar os dados...</p>
    </div>
</template>