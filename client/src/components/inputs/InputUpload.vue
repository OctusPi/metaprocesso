<script setup>
import { ref, watch } from 'vue';

const emit = defineEmits(['callAlert', 'update'])
const model = defineModel()
const props = defineProps({
    identify: { type: String },
    valid: { type: Boolean },
})

const valid = ref(false)

watch(() => props.valid, (newVal) => {
    valid.value = newVal
})

const fileName = ref(null)

function changeFile(e) {
    const f = e.target.files[0]
    model.value = f
    fileName.value = f.name
}

</script>

<template>
    <label class="form-label txt-color fileupload" :for="props.identify" :class="{ 'form-control-alert' : valid  }">
        <i class="fileupload-icon bi mb-1" :class="{ 'bi-upload': !fileName, 'bi-file-earmark-pdf': fileName }"></i>
        <span v-if="!fileName">
            Solte ou selecione o arquivo aqui
        </span>
        <span v-if="fileName">
            {{ fileName }}
        </span>
        <input accept=".pdf,application/pdf" @change="changeFile" type="file" name="uploaded"
            class="form-control fileupload-input" :id="props.identify">
    </label>
</template>

<style>
.fileupload {
    border: 2px dashed var(--color-base);
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-radius: 12px;
}

label.form-control-alert {
    border: 2px dashed var(--color-danger);
}

.fileupload-icon {
    font-size: 1.75em;
}

.fileupload-input {
    display: none;
}
</style>