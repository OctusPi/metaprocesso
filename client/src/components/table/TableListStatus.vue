<script setup>
import Mounts from '@/services/mounts';
import { ref } from 'vue';

const props = defineProps({
    data: { type: String, required: true }
})

const el = ref(Mounts.Status()(props.data))

</script>

<template>
    <span :class="el.classes">{{ el.value }}</span>
</template>