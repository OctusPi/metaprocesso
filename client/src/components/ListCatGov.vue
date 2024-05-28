<script setup>

    import { ref, watch } from 'vue'
    import CatGov from '@/services/catgov'

    const catgov = new CatGov()
    // const emit   = defineEmits(['callAlert', 'respCatGov'])
    const props  = defineProps({
        search: {type:String}
    })

    const component = ref({})

    async function searchCat(search){
        if(search){
            component.value.items = await catgov.searchItem(search)
        }
    }

    async function getItem(i){
        component.value.list = await catgov.getItems(i)
        component.value.items = null
    }

    watch(() => props.search, (new_search) => {
        searchCat(new_search)
    })

</script>

<template>
    <div v-if="props.search" class="position-absolute my-2 top-100 start-0">
        <div class="form-control load-items-cat p-0 m-0">
            <ul>
                <!-- list categories -->
                <li v-for="i in component.items?.data" :key="i.codigo" 
                @click="getItem(i)"
                class="d-flex px-3 py-2">
                    <div class="me-3 item-type">{{ i.tipo }}</div>
                    <div class="flex-grow-1 item-desc">{{ i.nome }}</div>
                </li>

                <li v-for="l in component.list?.data" :key="l.items.codigoPdm">
                    {{ l.items.nomePmd }}
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
    ul{
        list-style: none;
        margin: 0;
        padding: 0;
    }

    ul li {
        cursor: pointer;
        border-bottom: var(--border-box);
    }

    ul li:hover{
        background-color: var(--color-base);
    }
    
    .item-type{
        width: 35px;
        border-right: var(--border-box);
    }
</style>