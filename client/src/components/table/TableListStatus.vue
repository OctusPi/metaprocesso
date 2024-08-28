<script setup>
import Mounts from '@/services/mounts';
import { ref, watch } from 'vue';

const props = defineProps({
    data: { type: String, required: true }
})

const el = ref(Mounts.Status()(props.data))

watch(() => props.data, (newval) => {
    el.value = Mounts.Status()(newval)
})

</script>

<template>
    <span :class="el.classes">{{ el.value }}</span>
</template>