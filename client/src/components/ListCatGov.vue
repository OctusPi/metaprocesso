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
            component.value.find = await catgov.searchItem(search)
        }
    }

    watch(() => props.search, (new_search) => {
        searchCat(new_search)
    })

</script>

<template>
    <div v-if="props.search" class="load-items-cat position-absolute my-2 top-100 start-0">
        <div class="form-control">
            <ul>
                <li class="d-flex">
                    <div class="me-2">M</div>
                    <div class="flex-grow-1">Description Item </div>
                </li>
                <li class="d-flex">
                    <div class="me-2">S</div>
                    <div class="flex-grow-1">Description Item </div>
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
</style>