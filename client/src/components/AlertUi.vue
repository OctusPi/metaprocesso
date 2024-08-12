<script setup>
import { ref, watch } from 'vue';

const props = defineProps({ alert:{ type: Object, required: true } })
const alert = ref(props.alert)
const efect = ref(false)

const alerts   = {
    success: { style: 'alert-success', icon: 'checkmark-circle-outline', title: 'Sucesso!', msg: 'Operação realizada com sucesso!', btn:'btn-alert-success' },
    warning: { style: 'alert-warning', icon: 'alert-circle-outline', title: 'Alerta!', msg: 'Falha ao realizar solicitação!', btn:'btn-alert-warning' },
    danger: { style: 'alert-danger', icon: 'bug-outline', title: 'Falha!', msg: 'Algo deu errado, verifique e tente novamente!', btn:'btn-alert-danger' },
    info: { style: 'alert-info', icon: 'information-circle-outline', title: 'Informe!', msg: '', btn:'btn-alert-info' }
}

function close_alert() {
    efect.value = true
    setTimeout(() => {
        alert.value.show = false;
    }, 200);
}

watch(() => props.alert, (newValue) => {
    efect.value = false
   alert.value = newValue
});
</script>

<template>
    <div class="wall w-100 vh-100 position-fixed top-0 start-0" :class="alert.show ? 'd-block' : 'd-none'">
        <div class="position-absolute top-50 start-50 translate-middle box-alert p-4 text-center shadow-sm" :class="{'efect-down' : efect} ">
            <ion-icon :name="alerts[alert.data.type]?.icon" class="fs-1 p-0 m-0" :class="alerts[alert.data.type]?.style"></ion-icon>
            <h2 class="p-0 m-0 mb-3 mt-1" :class="alerts[alert.data.type]?.style">{{ alerts[alert.data.type]?.title }}</h2>
            <p class="p-0 m-0 small">{{ alerts[alert.data.type]?.msg }}</p>
            <p class="p-0 m-0 small">{{ alert?.data.msg }}</p>

            <button @click="close_alert" type="button" class="btn btn-alert shadow-sm mx-auto mt-4" :class="alerts[alert.data.type]?.btn">
                <ion-icon name="chevron-down-outline" class="fs-2"></ion-icon>
            </button>
        </div>
    </div>
</template>

<style scoped>
    .box-alert{
        width: 300px;
        min-height: 120px;
        border-radius: 20px;
        background-color: var(--color-background-soft);
    }

    .efect-down{
        opacity: 0;
        transition: 0.5s;
    }

    .alert-success {
        color: var(--color-success);
    }

    .alert-warning {
        color: var(--color-warning);
    }

    .alert-danger {
        color: var(--color-danger-hover);
    }

    .alert-info {
        color: var(--color-base);
    }

    .btn-alert{
        border-radius: 50%;
        padding: 10px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-alert-success{
        background-color: var(--color-success);
        color: var(--color-success-txt);
    }

    .btn-alert-warning{
        background-color: var(--color-warning);
        color: var(--color-warning-txt);
    }

    .btn-alert-danger{
        background-color: var(--color-danger-hover);
        color: white;
    }

    .btn-alert-info{
        background-color: var(--color-base);
        color: white;
    }

</style>