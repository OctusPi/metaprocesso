<script setup>

</script>

<template>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-sm btn-vertical btn-primary d-flex align-items-center py-1 px-2" data-bs-toggle="modal"
        data-bs-target="#modalManual">
        <ion-icon name="information-circle-outline" class="fs-6"></ion-icon>
        Manual
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modalManual" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content box p-3">
                <div class="modal-header d-flex justify-content-between border-0">
                    <h5 class="modal-title d-flex align-items-center">
                        <ion-icon name="information-circle-outline" class="fs-5 me-1"></ion-icon>
                        Manual de Uso Metaprocesso
                    </h5>
                    <div class="d-flex ms-2">
                        <button type="button" class="btn icon-close" data-bs-dismiss="modal" aria-label="Close">
                            <ion-icon name="close-circle-outline" class="fs-5"></ion-icon>
                        </button>
                    </div>
                </div>
                <div class="modal-body w-100" data-bs-spy="scroll" data-bs-offset="0">

                    
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

.btn-vertical {
    transform: rotate(270deg);
    position: fixed;
    right: -28px;
    top: 49%;
    cursor: pointer;
    border-radius: 6px 6px 0 0;
    background-color: var(--color-base);
    color: white;
    z-index: 900;
    font-size: 0.8rem;
}

.btn-vertical:hover{
    background-color: var(--color-base-hover);
}

.icon-close{
    color: var(--color-text);
}

.icon-close:hover {
    cursor: pointer;
    color: var(--color-danger);
}
</style>
