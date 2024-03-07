<script setup>
const alerts = {
    success: { style: 'alert-success', icon: 'bi-check-circle-fill', title: 'Sucesso!', msg: 'Operação realizada com sucesso!' },
    warning: { style: 'alert-warning', icon: 'bi-exclamation-triangle-fill', title: 'Alerta!', msg: 'Falha ao realizar solicitação!' },
    darger: { style: 'alert-darger', icon: 'bi-x-octagon-fill', title: 'Ocorreu um problema!', msg: 'Algo deu errado, verifique e tente novamente!' }
}

const props = defineProps({ alert:Object })

</script>

<template>
    <div v-if="props.alert.show" class="alert p-4 rounded-4" :class="alerts[props.alert.data.type].style" role="alert">

        <div class="alert-heading d-flex justify-content-between align-items-center mb-2">
            <h4 class="p-0 m-0"><i class="me-1 bi" :class="alerts[props.alert.data.type].icon"></i> {{ alerts[props.alert.data.type].title }}</h4>
            <button type="button" aria-label="Close" @click="props.alert.show = false"><i class="bi bi-x-circle"></i></button>
        </div>
        <p class="small p-0 m-0">{{ alerts[props.alert.data.type].msg }}</p>
        <p v-if="props.alert.data.msg" class="small p-0 m-0">{{ props.alert.data.msg }}</p>
    </div>
</template>