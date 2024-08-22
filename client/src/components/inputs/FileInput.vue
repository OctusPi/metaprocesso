<script setup>

import utils from '@/utils/utils';
import { ref, watch } from 'vue';

const props = defineProps({
    identify: { type: String, required: true },
    downloadName: { type: String, default: () => 'Arquivo' },
    valid: { type: Boolean, default: false },
    label: { type: String, default: () => 'Arquivo' },
    mimes: { type: String, default: () => 'application/pdf' }
});

const model = defineModel({ default: null })
const valid = ref(props.valid)

function handleFile(event) {
    const file = event.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onloadend = () => {
            model.value = reader.result
        }
    }
}

function countDataUrl(dataUrl) {
    const data = dataUrl.split(',')
    
    if (data.length < 2) {
        model.value = null
        return
    }

    return `${parseInt(data[1].length * (3 / 4 / 1000))} KB`
}

function downloadModel() {
    var link = document.createElement("a");
    link.download = `${props.label}-${props.downloadName}-${utils.dateNow()}`;
    link.href = model.value;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="cloud-upload-outline" class="upload-icon fs-5" />
                    {{ model ? `Enviado ${countDataUrl(model)}` : `Selecionar Arquivo` }}
                </div>
                <input :id="props.identify" type="file" name="document" class="d-none" @change="handleFile"
                    :accept="props.mimes">
            </label>
            <button v-if="model" type="button" class="btn form-control-btn btn-action-tertiary" @click="downloadModel">
                <ion-icon name="download-outline" class="fs-5" />
            </button>
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