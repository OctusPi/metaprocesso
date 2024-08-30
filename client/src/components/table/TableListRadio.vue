<script setup>
import { onMounted, ref, watch } from "vue"
import TableList from "./TableList.vue";
import utils from "@/utils/utils";

const props = defineProps({
    body: { type: Array, default: () => [] },
    header: { type: Array, default: () => [] },
    mounts: { type: Object },
    smaller: { type: Boolean },
    sent: { type: Boolean, default: true },
    count: { type: Boolean, default: false },
    identify: { type: String },
    only: { type: String, default: () => null },
})

const model = defineModel({ default: {} })
const body = ref(props.body)

function getModel() {
    return Object.keys(model.value).length > 0 ? [model.value] : []
}

watch(() => props.body, (newValue) => {
    body.value = utils.reduceArrays(getModel(), newValue)
});

onMounted(() => {
    body.value = getModel()
})

</script>

<template>
    <TableList :header="props.header" :body="body" :order="false" :mounts="props.mounts" :smaller="props.smaller"
        :virtual="virtual" :sent="props.sent">
        <template #select="{ instance }">
            <input class="form-check-input" type="radio" :value="props.only ? instance[props.only] : instance"
                :name="props.identify + '_check'" v-model="model">
        </template>
    </TableList>
</template>

<style scoped>
input {
    background-color: transparent;
    width: 1.15rem;
    height: 1.15rem;
}
</style>