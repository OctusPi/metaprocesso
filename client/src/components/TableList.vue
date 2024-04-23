<script setup>
    import { ref, watch } from 'vue'
    import ActionNav from './ActionNav.vue'

    const props = defineProps({
        header  :{type: Array},
        body    :{type: Array, default: () => []},
        actions :{type: Array},
        casts   :{type: Object}
    })
    
    const emit  = defineEmits(['action:update', 'action:delete', 'action:download'])
    const body  = ref(props.body)
    
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

    function casting(key, search, subject, extract = null){
        const findObj = subject.find(obj => obj[key] === search)
        return extract && findObj && findObj[extract] ? findObj[extract] ?? '' : findObj
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
                    <td v-for="h in props.header" :key="`${b.id}-${h.key}`" class="align-middle">
                        {{ props.casts[h.key] ? casting('id', b[h.key], props.casts[h.key], 'title') :  b[h.key] }}
                        <p v-if="h.sub" class="small txt-color-sec p-0 m-0">
                            <span 
                                v-for="s in h.sub" :key="s.key" 
                                class="inline-block small">
                                {{ `${s.title ?? ''} ${props.casts[s.key] ? casting('id', b[s.key], props.casts[s.key], 'title') :  b[s.key] ?? '' }` }} 
                            </span>
                        </p>
                    </td>
                    <td v-if="props.actions" class="align-middle"><ActionNav :id="b.id" :calls="props.actions" @action="propagateEmit" /></td>
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