<script setup>
import { ref } from 'vue';
const props = defineProps({
    placeholder: { type: String, default: '' },
    identify: { type: String, required: true },
    label: { type: String },
    valid: { type: Boolean, default: false },
    type: { type: String, default: 'text' }
})

const data = ref('')
const model = defineModel({ default: [] })

function saveServiceIfUnique() {
    if (!Array.isArray(model.value)) {
        model.value = []
    }

    if (!model.value.includes(data.value) && data.value !== '') {
        model.value = model.value.concat([data.value])
        data.value = ''
        return
    }
}

function removeSevice(index) {
    if (Array.isArray(model.value)) {
        model.value = model.value.filter((o, i) => i !== index)
    }
}
</script>

<template>
    <label :for="identify" class="form-label">{{ label }}</label>
    <div class="input-group">
        <input @keydown.enter.prevent="saveServiceIfUnique" :type="$props.type" :name="identify" class="form-control"
            :id="identify" :placeholder="placeholder" v-model="data">
        <button @click="saveServiceIfUnique" type="button" class="btn btn-action-primary">
            <ion-icon name="add" class="fs-5"></ion-icon>
            Adicionar
        </button>
    </div>
    <div class="d-flex flex-wrap gap-2 form-control my-2" :class="{ 'form-control-alert': $props.valid }">
        <div class="tag d-flex gap-2" v-for="(item, i) in model" :key="i">
            <span>{{ item }}</span>
            <button @click="removeSevice(i)" type="button" class="btn m-0 p-0">
                <ion-icon name="close" class="txt-color"></ion-icon>
            </button>
        </div>
        <div v-if="model.length <= 0" class="text-center pb-1 txt-color-sec w-100">
            <ion-icon name="ellipsis-horizontal-outline" class="fs-4"></ion-icon>
            <p class="p-0 m-0 small">Ainda não foram adicionados itens...</p>
        </div>
    </div>
</template>

<style scoped>
.tag {
    background-color: var(--color-base-tls);
    color: var(--color-text);
    width: fit-content;
    padding: 2px 12px;
    border-radius: 24px;
}
</style>