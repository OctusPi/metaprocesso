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
        <div class="ocp-richtext-toolbar" ref="richTextToolbarEl">
            <span class="ql-formats">
                <button type="button" class="ql-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 3a1 1 0 0 1 1-1h6a4.5 4.5 0 0 1 3.274 7.587A4.75 4.75 0 0 1 11.25 18H5a1 1 0 0 1-1-1V3Zm2.5 5.5v-4H11a2 2 0 1 1 0 4H6.5Zm0 2.5v4.5h4.75a2.25 2.25 0 0 0 0-4.5H6.5Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="ql-italic">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd"
                            d="M8 2.75A.75.75 0 0 1 8.75 2h7.5a.75.75 0 0 1 0 1.5h-3.215l-4.483 13h2.698a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3.215l4.483-13H8.75A.75.75 0 0 1 8 2.75Z"
                            clipRule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="ql-underline">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.75 2a.75.75 0 0 1 .75.75V9a4.5 4.5 0 1 0 9 0V2.75a.75.75 0 0 1 1.5 0V9A6 6 0 0 1 4 9V2.75A.75.75 0 0 1 4.75 2ZM2 17.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="ql-strike">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M11.617 3.963c-1.186-.318-2.418-.323-3.416.015-.992.336-1.49.91-1.642 1.476-.152.566-.007 1.313.684 2.1.528.6 1.273 1.1 2.128 1.446h7.879a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1 0-1.5h3.813a5.976 5.976 0 0 1-.447-.456C5.18 7.479 4.798 6.231 5.11 5.066c.312-1.164 1.268-2.055 2.61-2.509 1.336-.451 2.877-.42 4.286-.043.856.23 1.684.592 2.409 1.074a.75.75 0 1 1-.83 1.25 6.723 6.723 0 0 0-1.968-.875Zm1.909 8.123a.75.75 0 0 1 1.015.309c.53.99.607 2.062.18 3.01-.421.94-1.289 1.648-2.441 2.038-1.336.452-2.877.42-4.286.043-1.409-.377-2.759-1.121-3.69-2.18a.75.75 0 1 1 1.127-.99c.696.791 1.765 1.403 2.952 1.721 1.186.318 2.418.323 3.416-.015.853-.288 1.34-.756 1.555-1.232.21-.467.205-1.049-.136-1.69a.75.75 0 0 1 .308-1.014Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="ql-list" value="bullet">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 4.75A.75.75 0 0 1 6.75 4h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 4.75ZM6 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 10Zm0 5.25a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75a.75.75 0 0 1-.75-.75ZM1.99 4.75a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1v-.01ZM1.99 15.25a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1v-.01ZM1.99 10a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1V10Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="ql-list" value="ordered">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M3 1.25a.75.75 0 0 0 0 1.5h.25v2.5a.75.75 0 0 0 1.5 0V2A.75.75 0 0 0 4 1.25H3ZM2.97 8.654a3.5 3.5 0 0 1 1.524-.12.034.034 0 0 1-.012.012L2.415 9.579A.75.75 0 0 0 2 10.25v1c0 .414.336.75.75.75h2.5a.75.75 0 0 0 0-1.5H3.927l1.225-.613c.52-.26.848-.79.848-1.371 0-.647-.429-1.327-1.193-1.451a5.03 5.03 0 0 0-2.277.155.75.75 0 0 0 .44 1.434ZM7.75 3a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM7.75 9.25a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM7.75 15.5a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM2.625 13.875a.75.75 0 0 0 0 1.5h1.5a.125.125 0 0 1 0 .25H3.5a.75.75 0 0 0 0 1.5h.625a.125.125 0 0 1 0 .25h-1.5a.75.75 0 0 0 0 1.5h1.5a1.625 1.625 0 0 0 1.37-2.5 1.625 1.625 0 0 0-1.37-2.5h-1.5Z" />
                    </svg>
                </button>
            </span>
        </div>
        <div class="ocp-richtext-editor" ref="richTextEl" v-html="initialValue"></div>
    </div>
</template>

<style scoped>
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

.ocp-richtext.form-control-alert .ocp-richtext-editor {
    border-top: 1px solid var(--color-danger-hover);
}

.ocp-richtext.is-focus {
    background-color: var(--color-input);
}

.ocp-richtext.is-focus .ocp-richtext-editor {
    border-top: 1px solid var(--color-base);
}

.ocp-richtext-toolbar {
    display: flex;
    gap: 6px
}

.ql-formats {
    display: flex;
    gap: 6px;
}

button {
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 0;
    color: var(--color-text);
    padding: 6px;
    border-radius: 4px;
}

select {
    background: transparent;
    border: 0;
    color: var(--color-text);
    padding: 6px;
    border-radius: 4px;
}

button:hover {
    background-color: var(--color-base-tls);
    color: var(--color-base);
}

svg {
    width: 15px;
    height: 15px;
}

.ql-active {
    color: var(--color-base);
}

.ocp-richtext-editor {
    height: 128px;
    border-top: 1px solid var(--color-input-focus);
    margin-top: 8px !important;
}
</style>