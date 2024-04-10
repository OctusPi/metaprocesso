<script setup>
    import { ref } from 'vue';
    
    const props = defineProps({
        params:{type: Object, required:true}
    })

    const pass = ref("")

    function remove(){
        console.log(props.route)
    }
</script>

<template>
    <div class="modal fade" id="modalDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content box">
                <div class="modal-header border border-0">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i> Confimação de Exclusão</h5>
                    <button type="button" class="txt-color ms-auto" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <h3 class="text-danger text-center"><i class="bi bi-exclamation-octagon fs-1"></i></h3>
                    <p class="text-center p-3">Os dados selecionados serão apagados, sem possibilidade de restauração. Deseja continuar?</p>

                    <div class="my-2">
                    <label for="conf-password" class="form-label">Informe sua senha de acesso:</label>
                    <input type="password" name="password" class="form-control"
                    id="conf-password" v-model="pass">
                </div>
                </div>
                <div class="modal-footer border border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i> Cancelar</button>
                    <button @click="remove" type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="bi bi-trash me-2"></i> Excluir</button>
                </div>
            </div>
        </div>
    </div>
</template>