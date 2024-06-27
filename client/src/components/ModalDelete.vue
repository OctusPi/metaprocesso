<script setup>
import http from '@/services/http';
import notifys from '@/utils/notifys';
import { ref } from 'vue';

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
            <div class="modal-content box">
                <div class="modal-header border border-0">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i> Confimação
                        de Exclusão</h5>
                    <button type="button" class="txt-color ms-auto" data-bs-dismiss="modal" aria-label="Close"><i
                            class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <h3 class="text-danger text-center"><i class="bi bi-exclamation-octagon fs-1"></i></h3>
                    <p class="text-center px-3">Os dados selecionados serão apagados, sem possibilidade de restauração.
                        Deseja continuar?</p>

                    <div class="px-3">
                        <label for="conf-password" class="form-label">Informe sua senha de acesso:</label>
                        <input type="password" name="password" class="form-control"
                            :class="{ 'form-control-alert': pass.is_out }" id="conf-password" v-model="pass.userpass">
                    </div>
                </div>
                <div class="modal-footer border border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i
                            class="bi bi-x-circle me-2"></i> Cancelar</button>
                    <button @click="remove" type="button" class="btn btn-outline-danger"
                        :data-bs-dismiss="pass.userpass ? 'modal' : null"><i class="bi bi-trash me-2"></i>
                        Excluir</button>
                </div>
            </div>
        </div>
    </div>
</template>