<script setup>
const props = defineProps({
    tabs: { type: Object },
    identify: { type: String }
})

</script>

<template>
    <ul class="nav nav-stretch" :id="props.identify" role="tablist">
        <li v-for="tab in tabs.tabs.value" :key="tab.id" class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-1" data-bs-toggle="tab" type="button" role="tab"
                @click="() => props.tabs.navigate(tab.id)" :class="{ 'active': props.tabs.is(tab.id) }"
                :id="`${tab.id}-tab`" :aria-controls="`${tab.id}-tab-pane`"
                :aria-selected="props.tabs.is(tab.id) ? 'true' : 'false'">
                <ion-icon v-if="tab.icon" :name="tab.icon" />
                {{ tab.title }}
            </button>
        </li>
    </ul>
    <div class="separator m-0 mb-4"></div>
</template>

<style scoped>
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

.tab-pane.show {
    display: flex
}
</style>