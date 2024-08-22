<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    identify: { type: String, required: true },
    valid: { type: Boolean, default: false },
    label: { type: String, default: () => 'Arquivo' },
});

const model = defineModel({ default: null })
const valid = ref(props.valid)

const name = ref(model.value)

function handleFile(event) {
    const file = event.target.files[0]
    if (file) {
        model.value = file
        name.value = file.name
    }
}

watch(() => props.valid, (newVal) => {
    valid.value = newVal
})


</script>

<template>
    <div>
        <label role="text" class="form-label">{{ props.label }}</label>
        <div class="d-flex gap-1">
            <label :for="props.identify" class="form-control upload-label m-0">
                <div class="d-flex no-wrap align-items-center gap-2 overflow-hidden">
                    <ion-icon v-if="model" name="cloud-done-outline" class="upload-icon fs-5" />
                    <ion-icon v-else name="cloud-upload-outline" class="upload-icon fs-5" />
                    {{ (name && name != "null") ? name : `Selecionar Arquivo` }}
                </div>
                <input :id="props.identify" type="file" name="document" class="d-none" @change="handleFile"
                    accept="application/pdf">
            </label>
        </div>
    </div>
</template>

<style scoped>
.upload-label {
    cursor: pointer;
    border: 1px dashed var(--color-base);
}

.upload-label:hover {
    background-color: var(--color-input-focus);
}

.upload-icon {
    color: var(--color-base);
}
</style>