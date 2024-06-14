<script setup>
const props = defineProps({
    tabInstance: { type: Object },
    identify: { type: String }
})
</script>

<template>
    <ul class="nav nav-fill mb-5" :id="props.identify" role="tablist">
        <li v-for="tab in props.tabInstance.tabs.value" :key="tab.id" class="nav-item" role="presentation">
            <button class="nav-link nav-step" data-bs-toggle="tab" type="button" role="tab"
                @click="props.tabInstance.navigate_tab(null, tab.id)" :class="{ 'active': tab.status }"
                :id="`${tab.id}-tab`" :aria-controls="`${tab.id}-tab-pane`"
                :aria-selected="tab.status ? 'true' : 'false'">
                <div class="nav-line-step"></div>
                <div class="nav-step-txt mx-auto">
                    <i class="bi" :class="tab.icon"></i>
                </div>
            </button>
            <span class="nav-label" :class="{ 'active-label': tab.status }">{{ tab.title }}</span>
        </li>
    </ul>
</template>

<style scoped>
.nav-step {
    margin: 0 !important;
    padding: 0 !important;
    position: relative;
    height: 60px;
}

.nav-line-step {
    height: 3px;
    width: 100%;
    background-color: var(--color-shadow);
    position: absolute;
    top: 50%;
    z-index: 0;
}

.nav-step-txt {
    background-color: var(--color-shadow);
    color: var(--color-shadow-2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: calc(50% - 30px);
}

.nav-step-txt i {
    font-size: 1.3rem;
    margin: 0;
    padding: 0;
}

.nav-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--color-shadow-2);
}

.nav-item .active .nav-step-txt {
    background-color: var(--color-base);
    color: white;
    transition: 400ms;
}

.nav-item .active .nav-line-step {
    background: rgb(202, 201, 201);
    background: linear-gradient(90deg,
            var(--color-shadow) 0%,
            var(--color-base) 50%,
            var(--color-shadow) 100%);
    transition: 400ms;
}

.active-label {
    color: var(--color-base);
}

.box-revisor {
    border-bottom: var(--border-box);
}

.bar-revisor-title {
    width: 5px;
    background-color: var(--color-base);
    border-radius: 2px;
}

.box-revisor-title h3 {
    color: var(--color-base);
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.box-revisor-title p {
    color: var(--color-text-secondary);
    font-size: small;
    margin: 0;
    padding: 0;
}

.box-revisor-content {
    padding: 0 10px 0 10px;
}

.box-revisor-content h4 {
    margin: 0;
    padding: 0;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--color-text);
}

.box-revisor-content p {
    color: var(--color-text-secondary);
    font-size: 0.9rem;
}

@media (max-width: 755px) {
    .nav-step {
        height: 50px;
    }

    .nav-step-txt {
        width: 50px;
        height: 50px;
        left: calc(50% - 25px);
    }

    .nav-step-txt i {
        font-size: 1.2rem;
    }

    .nav-label {
        display: none;
    }
}
</style>
