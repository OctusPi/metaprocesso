<script setup>
import { ref, onMounted } from 'vue'
import Quill from 'quill'

const model = defineModel()

const props = defineProps({
    identifier: { type: String },
    placeholder: { type: String },
    valid: { type: Boolean },
})

const richTextEl = ref(null)

const quill = ref(null)
const focus = ref(false)

onMounted(() => {
    quill.value = new Quill(richTextEl.value, {
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['link', 'formula'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                ['clean']
            ]
        },
        theme: 'snow'
    })
    quill.value.on('editor-change', () => {
        focus.value = quill.value.hasFocus()
    })
    quill.value.on('text-change', () => {
        if (quill.value.getLength() <= 1) {
            model.value = null
        } else {
            model.value = quill.value.getSemanticHTML()
        }
    })
    quill.value.root.innerHTML = model.value ?? ''
})

</script>

<template>
    <div :id="props.identifier" class="ocp-richtext" :class="{ 'form-control-alert': props.valid, 'is-focus': focus }">
        <div class="ocp-richtext-editor" ref="richTextEl"></div>
    </div>
</template>

<style>
.ocp-richtext {
    border: 2px solid var(--color-input-border) !important;
    background-color: var(--color-input);
    color: var(--color-input-txt);
    font-weight: 500;
    border-radius: 8px;
    transition: 200ms;
    padding: 8px !important;
}

.ocp-richtext.form-control-alert {
    background-color: var(--color-danger-tls);
}

.ocp-richtext.is-focus {
    background-color: var(--color-input);
}

.ocp-richtext.is-focus .ocp-richtext-editor {
    border-top: 1px solid var(--color-base) !important;
}

.ocp-richtext.form-control-alert .ocp-richtext-editor {
    border-top: 1px solid var(--color-danger-hover) !important;
}

.ocp-richtext-editor {
    height: 128px;
    border: 0;
    border-top: 1px solid var(--color-input-focus) !important;
    margin-top: 8px !important;
}
</style>