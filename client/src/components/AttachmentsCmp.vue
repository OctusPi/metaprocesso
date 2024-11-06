<script setup>
import { onMounted, watch } from 'vue'
import TableList from '@/components/table/TableList.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import FileInput from './inputs/FileInput.vue';

const emit = defineEmits(['callAlert', 'callRemove', 'clone'])

const props = defineProps({
    origin: { type: String },
    protocol: { type: String },
    label: { type: String },
    types: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
    url: `/attachments/${props.origin}/${props.protocol}`,
    data: {
        origin: props.origin,
        protocol: props.protocol,
    },
    header: [
        { key: 'type', title: 'TIPO' },
        { key: 'updated_at', title: 'ENVIADO EM' },
    ],
    rules: {
        origin: 'required',
        protocol: 'required',
        type: 'required',
        document: 'required',
    }
})

watch(() => props.protocol, (newdata) => {
    page.data.url = `/attachments/${props.origin}/${newdata}`
    page.data.protocol = newdata
    pageData.listWithCb(() => {
        emit('clone', page)
    })
})

watch(() => page.ui.register, (newdata) => {
    if (newdata && !page.data.id) {
        page.data.origin = props.origin
        page.data.protocol = props.protocol
    }
})

onMounted(() => {
    pageData.listWithCb(() => {
        emit('clone', page)
    })
})
</script>

<template>
    <div class="m-0 p-0" v-if="props.origin && props.protocol">
        <div v-if="!page.ui.register" class="container-fluid m-0 p-4 content">
            <div role="heading" class="inside-title mb-4">
                <div>
                    <h2>Anexos</h2>
                    <p>
                        {{ props.label }}
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" @click="pageData.ui('register')" class="btn btn-action-primary">
                        <ion-icon name="add" class="fs-5"></ion-icon>
                        Adicionar
                    </button>
                </div>
            </div>
            <div class="container p-0">
                <TableList secondary :header="page.header" :body="page.datalist" :actions="[
                    Actions.Edit(pageData.update),
                    Actions.Delete((id) => pageData.remove(id, pageData.list)),
                    Actions.Dowload((id) => pageData.download(id, `Anexo`)),
                ]" />
            </div>
        </div>
        <div v-if="page.ui.register" class="container-fluid m-0 p-4 content">
            <div role="heading" class="inside-title mb-3">
                <div>
                    <h2>Novo Anexo</h2>
                    <p>Preencha os dados abaixo para realizar o registro</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button @click="pageData.save" type="button" class="btn btn-action-primary">
                        <ion-icon name="cloud-upload-outline" class="fs-5"></ion-icon>
                        Upload
                    </button>
                    <button type="button" @click="pageData.ui('register')" class="btn btn-action-tertiary">
                        <ion-icon name="close-outline" class="fs-5"></ion-icon>
                        Cancelar
                    </button>
                </div>
            </div>
            <div role="form" class="p-0">
                <form class="form-row" @submit.prevent="pageData.save">
                    <div class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <label for="type" class="form-label">Modalide</label>
                            <select name="type" class="form-control"
                                :class="{ 'form-control-alert': page.valids.type }" id="type"
                                v-model="page.data.type">
                                <option v-for="o in props.types" :key="o.id" :value="o.id">
                                    {{ o.title }}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label for="name" class="form-label">Nome Descritivo</label>
                            <input class="form-control" placeholder="Identificação do Documento" type="text" id="name"
                                v-model="page.data.name">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <FileInput label="Arquivo" identify="attachments-upload" v-model="page.data.document"
                                :valid="page.valids.document" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div v-else class="content p-4 py-3">
        <h2 class="txt-color text-center m-0 d-flex justify-content-center align-items-center gap-1">
            <ion-icon name="warning" class="fs-5" />
            Atenção
        </h2>
        <p class="txt-color-sec small text-center m-0">
            É necessário haver um protocolo e uma origem do arquivo para continuar
        </p>
    </div>
</template>