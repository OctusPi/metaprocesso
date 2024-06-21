<script setup>

const props = defineProps({
    data:{type:String , required: true}
})

const badges = {
    'Rascunho'  : 'text-bg-secondary',
    'Enviado'   : 'text-bg-primary',
    'Pendente'  : 'text-bg-warning',
    'Bloqueado' : 'text-bg-danger',
    'Processado': 'text-bg-success'
}

</script>

<template>
    <span class="badge rounded-pill" :class="badges[props.data] ?? 'text-bg-secondary'">{{ props.data }}</span>
</template>