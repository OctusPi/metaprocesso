<script setup>
    import { ref, watch } from 'vue'
    import ActionNav from './ActionNav.vue'

    const props = defineProps({
        header:Array,
        body:{type: Array, default: () => []},
        actions:Array
    })
    
    const emit = defineEmits(['action:update', 'action:delete'])
    const body = ref(props.body)
    
    watch(() => props.body, (newValue) => {
        body.value = newValue
    });

    function orderBy(key){
        body.value.sort((a,b) => {
            if(typeof a[key] === 'string'){
                return a[key].localeCompare(b[key])
            }
            
            return a[key] - b[key]
        })
    }

    function propagateEmit(emt){
        emit(emt.e, emt.i)
    }
</script>

<template>
    <div v-if="body.length" class="table-responsive-sm">
        <table class="table table-borderless table-hover">
            <thead v-if="props.header">
                <tr>
                    <th scope="col" v-for="h in props.header" :key="h.key" @click="orderBy(h.key)">{{ h.title }}<i class="bi bi-arrow-down-up ms-2 table-order-icon"></i></th>
                    <th v-if="props.actions" scope="col"></th>
                </tr>
            </thead>
            <tbody v-if="body">
                <tr v-for="b in body" :key="b.id">
                    <td v-for="h in props.header" :key="`${b.id}-${h.key}`">{{ b[h.key] }}</td>
                    <td v-if="props.actions"><ActionNav :id="b.id" :calls="props.actions" @action="propagateEmit" /></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else class="text-center txt-color-sec">
        <i class="bi bi-boxes fs-4"></i>
        <p class="small">Não foram localizados registros...</p>
    </div>
</template>

<style>

    .table tr th:first-child{
        padding-left: 50px;
    }

    .table tr td:first-child{
        padding-left: 50px;
    }

    .table tr td:last-child{
        padding-right: 4cap;
        text-align: end;
    }

    .table, .table th, .table td{
        background-color: transparent !important;
        color: var(--color-text);
    }

    .table th{
        cursor: pointer;
        font-weight: 600;
        font-size: small;
    }
    .table-order-icon{
        font-size: small;
    }

    .table th:hover i{
        color: var(--color-base);
    }
    
   
</style>