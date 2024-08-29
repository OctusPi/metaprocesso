<script setup>
import { ref, onMounted } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.core.css'

const model = defineModel()
const initialValue = ref(model.value ?? '')

const props = defineProps({
    identifier: { type: String },
    placeholder: { type: String },
    valid: { type: Boolean },
})

const richTextEl = ref(null)
const richTextToolbarEl = ref(null)

const quill = ref(null)
const focus = ref(false)

onMounted(() => {
    quill.value = new Quill(richTextEl.value, {
        modules: {
            toolbar: richTextToolbarEl.value
        },
    })
    quill.value.on('editor-change', () => {
        focus.value = quill.value.hasFocus()
    })
    quill.value.on('text-change', () => {
        if (quill.value.getLength() < 2) {
            model.value = null
        } else {
            model.value = quill.value.getSemanticHTML()
        }
    })
})

</script>

<template>
    <div :id="props.identifier" class="ocp-richtext" :class="{ 'form-control-alert': props.valid, 'is-focus': focus }">
        <div class="ocp-richtext-toolbar" ref="richTextToolbarEl" :class="{ 'is-focus': focus }">
            <button class="ql-bold"><ion-icon name="bold" /></button>
            <button class="ql-italic"><ion-icon name="bold" /></button>
            <button class="ql-underline"><ion-icon name="bold" /></button>
            <button class="ql-strike"><ion-icon name="bold" /></button>
            <button class="ql-list" value="bullet"><ion-icon name="bold" /></button>
            <button class="ql-list" value="ordered"><ion-icon name="bold" /></button>
        </div>
        <div class="ocp-richtext-editor" ref="richTextEl" v-html="initialValue"></div>
    </div>
</template>

<style>
.ocp-richtext {
    border: 2px solid var(--color-input-border) !important;
    background-color: var(--color-input);
    color: var(--color-text);
    font-weight: 500;
    border-radius: 8px;
    transition: 200ms;
    padding: 8px;
}

.ocp-richtext.form-control-alert {
    border: 2px solid var(--color-danger);
}

.ocp-richtext-toolbar {
    border: 2px solid transparent;
    display: flex;
    gap: 4px;
    border: 1px dashed var(--color-input-focus);
    padding: 4px;
    border-radius: 8px;
    transition: inherit;
}

.ocp-richtext.form-control-alert .ocp-richtext {
    border: 2px solid var(--color-danger) !important;
}

.ocp-richtext-toolbar.is-focus {
    background-color: var(--color-input);
}

.ocp-richtext-toolbar *.ql-active {
    color: var(--color-base);
}

.ocp-richtext-toolbar>button {
    padding: 0 6px;
    background-color: transparent;
    border: 0;
    border-radius: 4px;
    transition: 200ms;
}

.ocp-richtext-toolbar>button:hover {
    background-color: var(--color-base-tls);
    color: var(--color-base);
}

.ocp-richtext-toolbar>button:hover {
    color: var(--color-base);
}

.ocp-richtext-toolbar i {
    font-size: 1.25em;
}

.ocp-richtext-editor {
    height: 120px;
}
</style>