<script setup>
import { ref, onMounted } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.core.css'

const richTextEl = ref(null)
const richTextToolbarEl = ref(null)

const quill = ref(null)
const focus = ref(false)

onMounted(() => {
    quill.value = new Quill(richTextEl.value, {
        modules: {
            toolbar: richTextToolbarEl.value
        }
    })
    quill.value.on('editor-change', () => {
        focus.value = quill.value.hasFocus()
    })
})

</script>

<template>
    <div class="ocp-richtext" :class="{ 'is-focus': focus }">
        <div class="ocp-richtext-toolbar" ref="richTextToolbarEl" :class="{ 'is-focus': focus }">
            <button class="ql-bold"><i class="bi bi-type-bold"></i></button>
            <button class="ql-italic"><i class="bi bi-type-italic"></i></button>
            <button class="ql-underline"><i class="bi bi-type-underline"></i></button>
            <button class="ql-strike"><i class="bi bi-type-strikethrough"></i></button>
            <button class="ql-list" value="bullet"><i class="bi bi-list-ul"></i></button>
            <button class="ql-list" value="ordered"><i class="bi bi-list-ol"></i></button>
        </div>
        <div class="ocp-richtext-editor" ref="richTextEl"></div>
    </div>
</template>

<style>
.ocp-richtext {
    border: 2px solid var(--color-input-border);
    background-color: var(--color-imput);
    color: var(--color-text);
    font-weight: 500;
    border-radius: 8px;
    transition: 200ms;
    padding: 8px;
}

.ocp-richtext.is-focus {
    border: 2px solid var(--color-base);
    background-color: var(--color-input-focus);
    color: var(--color-text);
    outline: none;
    box-shadow: none;
    -webkit-box-shadow: none;
}

.ocp-richtext-toolbar {
    border: 2px solid transparent;
    display: flex;
    gap: 4px;
    background-color: var(--color-shadow);
    padding: 4px 8px;
    border-radius: 46px;
    transition: inherit;
}

.ocp-richtext-toolbar.is-focus {
    border: 2px solid var(--color-base);
}

.ocp-richtext-toolbar *.ql-active {
    color: var(--color-base);
}

.ocp-richtext-toolbar>button {
    padding: 0 6px;
    transition: 200ms;
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