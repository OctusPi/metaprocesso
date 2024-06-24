<script setup>

const props = defineProps({
    data:{type:String , required: true}
})


let badges = 'text-bg-secondary'

switch (props.data) {
    case 'Rascunho':
    case 'Inativo':
        badges = 'text-bg-secondary'
        break
    case 'Enviado':
        badges = 'text-bg-primary'
        break
    case 'Pendente':
        badges = 'text-bg-warning'
        break
    case 'Bloqueado':
        badges = 'text-bg-danger'
        break
    case 'Processado':
    case 'Ativo':
        badges = 'text-bg-success'
        break
    default:
        break
}

</script>

<template>
    <span class="badge rounded-pill" :class="badges">{{ props.data }}</span>
</template>