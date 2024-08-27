<script setup>
import { ref } from 'vue';
import http from '@/services/http';
import notifys from '@/utils/notifys';

const emit = defineEmits(['callAlert', 'callUpdate'])
const props = defineProps({
    params: { type: Object, required: true }
})

const pass = ref({
    userpass: null,
    is_out: false
})

function remove() {
    if (!pass.value.userpass) {
        pass.value.is_out = true
        emit('callAlert', notifys.warning('Informe sua senha de acesso!'))
        return
    }

    const data = {
        id: props.params.id,
        password: pass.value.userpass
    }

    http.destroy(`${props.params.url}/destroy`, data, emit, (resp) => {
        if (http.success(resp)) {
            http.post(`${props.params.url}/list`, props.params.search, emit, (resp) => {
                console.log(resp.data)
                emit('callUpdate', resp.data)
            })
        }
    })
}
</script>

<template>
    <div class="modal fade" id="modalDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content box content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn txt-color ms-auto" data-bs-dismiss="modal" aria-label="Close">
                        <ion-icon name="close" class="fs-5" />
                    </button>
                </div>
                <div class="modal-body py-0">
                    <div class="text-center text-danger">
                        <ion-icon name="warning-outline" class="fs-1" />
                    </div>
                    <p class="text-center px-3">Os dados selecionados serão apagados, sem possibilidade de restauração.
                        Deseja continuar?</p>
                    <div>
                        <input placeholder="Senha de acesso" type="password" name="password" class="form-control"
                            :class="{ 'form-control-alert': pass.is_out }" id="conf-password" v-model="pass.userpass">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-action-tertiary" data-bs-dismiss="modal">
                        <ion-icon name="close" />
                        Cancelar
                    </button>
                    <button @click="remove" type="button" class="btn btn-danger"
                        :data-bs-dismiss="pass.userpass ? 'modal' : null">
                        <ion-icon name="trash" />
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>