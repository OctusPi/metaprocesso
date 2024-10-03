<script setup>
import { watch } from 'vue';

const props = defineProps({
    tabs: { type: Object },
    identify: { type: String },
    valids: { type: Object },
    errs: {
        type: Array, default: () => [
            'form-control-alert',
            'text-danger',
            'dp-custom-input-dtpk-alert'
        ]
    },
})

const tabs = props.tabs.elements.value

const mapErrors = () => {
    return props.errs.map(o => '.' + o).join(',')
}

const selectToggler = (itemId) => {
    return document.querySelector(`[data-bs-target="#${itemId}"]`)
}

const getPanes = () => {
    return document.querySelectorAll('.tab-pane')
}

watch(() => props.valids, () => {
    getPanes()?.forEach((item) => {
        const toggler = selectToggler(item.id)
        const selection = item.querySelectorAll(mapErrors())

        if (!toggler) return

        if (selection.length > 0) {
            toggler.firstElementChild.classList.remove('d-none')
            toggler.firstElementChild.textContent = selection.length
        } else {
            toggler.firstElementChild.textContent = null
            toggler.firstElementChild.classList.add('d-none')
        }
    })
}, { deep: true })

</script>

<template>
    <ul class="nav nav-stretch" :id="props.identify" role="tablist">
        <li v-for="tabId, i in Object.keys(tabs)" :key="tabId" class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-1 position-relative" data-bs-toggle="tab"
                :data-bs-target="'#' + tabId" :class="{ 'active': i == 0 }">
                {{ tabs[tabId]?.title }}
                <span class="badge position-absolute translate-middle badge rounded-pill bg-danger">
                </span>
            </button>
        </li>
    </ul>
    <div class="separator m-0 mb-4"></div>
</template>

<style scoped>
.badge {
    right: -10px;
    top: 6px;
    font-size: .6em;
}

.separator {
    background-color: var(--color-input);
    width: 100%;
    height: 3px;
}

.dark .separator {
    background-color: var(--color-background-soft);
}

.nav-link {
    color: var(--color);
    border-bottom: 3px solid transparent;
    margin-bottom: -3px;
    font-weight: 600;
}

.active {
    color: var(--color-base);
    border-bottom: 3px solid var(--color-base) !important;
}
</style>

<style>
.tab-pane {
    display: none;
}
</style>