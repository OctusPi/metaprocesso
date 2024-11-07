<script setup>
import { inject } from 'vue';
const sysapp = inject('sysapp')
const props = defineProps({
    style: {type: String, default: 'main-footer'}
})
</script>

<template>
    <footer :class="props.style" class="py-3">
        {{ sysapp.copy }}&copy; - {{ sysapp.version }}
    </footer>
</template>

<style scoped>
.main-footer {
    font-size: 0.8rem;
    text-align: end;
}

@media(max-width:760px) {
    .main-footer {
        text-align: center;
    }
}
</style>