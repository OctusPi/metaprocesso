<script setup>
const props = defineProps({
    data: { type: String, required: true }
})

function get_badge() {
    const badges = {
        'text-bg-secondary': 'Rascunho|Inativo|Baixa',
        'text-bg-primary': 'Enviado',
        'text-bg-warning': 'Pendente|Média',
        'text-bg-danger': 'Bloqueado|Alta',
        'text-bg-success': 'Processado|Ativo|Ativa'
    }
    for(let i in badges){
        const a = (badges[i]).split('|')
        if(a.includes(props.data)){
            return i
        }
    }

    return 'text-bg-secondary'
}
</script>

<template>
    <span class="badge rounded-pill" :class="get_badge()">{{ props.data }}</span>
</template>
