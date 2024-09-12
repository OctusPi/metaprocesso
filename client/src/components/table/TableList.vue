<script setup>
import { ref, watch, toRaw } from "vue"

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
    const mInstance = {...instance, _virtual: {}}

    Object.keys(props.virtual).forEach((key) => {
        mInstance._virtual[key] = props.virtual[key](instance)
    })

    return header.map((attr) => {
        if (!attr.key) {
            return { value: null, classes: [] }
        }

        if (props.mounts && props.mounts[attr.key]) {
            return props.mounts[attr.key].reduce((initial, current) => {
                const { value, classes } = current(initial.value, mInstance)

                initial.classes.push(...classes)

                if (value != null) {
                    initial.value = value
                }

                return initial
            }, { value: multiplexer(mInstance, attr.key), classes: [] })
        }

        return { value: multiplexer(mInstance, attr.key), classes: [] }
    })
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
                                <div v-if="userHeader[j].key" class="small txt-color-sec" :class="mounted.classes">
                                    {{ getValue(mounted.value, userHeader[j]) }}
                                </div>
                            </div>
                            <div>
                                <span v-for="(submounted, k) in applyMounters(instance, userHeader[j].sub ?? [])"
                                    :key="k" class="inline-block small me-1" :class="submounted.classes">
                                    {{ userHeader[j].sub[k].title }}
                                    {{ getValue(submounted.value, userHeader[j].sub[k]) }}
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

<style scoped>
.tablelist {
    border-radius: 15px;
}

.tablelist-prim {
    background-color: var(--color-background-soft);
}

.tablelist-sec {
    background-color: var(--color-input);
}

thead th {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

th {
    white-space: nowrap;
}

.table-order-icon {
    pointer-events: none;
}

th:hover .table-order-icon {
    color: var(--color-text-secondary);
}

[data-asc] .table-order-icon {
    color: var(--color-base);
}

[data-asc]:hover .table-order-icon {
    color: var(--color-base);
}

.cursor-pointer {
    cursor: pointer;
}

table td:nth-child(1) {
    white-space: nowrap;
}

tbody tr:last-child {
    border-radius: 15px;
}

tbody tr:last-child td:last-child {
    border-radius: 0 0 15px;
}

tbody tr:last-child td:first-child {
    border-radius: 0 0 0 15px;
}

.table tr th:first-child {
    padding-left: 2vw;
}

.table tr td:first-child {
    padding-left: 2vw;
}

.table tr td:last-child {
    padding-right: 2vw;
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
    font-size: 0.8rem;
}

.table th:hover i .tablesm tr td {
    font-size: 0.85rem !important;
}

.tablesm tr td:first-child {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
    padding-left: 10px !important;
}

.tablesm tr td:last-child {
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
    padding-right: 10px !important;
    text-align: end;
}

.selectable {
    width: 1.3rem;
    height: 1.3rem;
}
</style>