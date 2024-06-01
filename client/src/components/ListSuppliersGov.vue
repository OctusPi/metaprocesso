<script setup>

import { ref } from 'vue'

const emit   = defineEmits(['select'])

</script>

<template>
    <div class="row position-relative">
        <div class="col-sm-12">
            <label for="organ" class="form-label">Localizar Fornecedor SICAF</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Localizar Material/Serviço Compras Gov"
                    aria-label="Localizar Material/Serviço Compras Gov" aria-describedby="button-search-catgov"
                    v-model="component.search" @keyup.enter="searchItems" @keyup="clear">
                <button class="btn btn-group-input" type="button" id="button-search-catgov" @click="searchItems">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <div v-if="component.search && (component.items || component.list)" class="position-absolute my-2 top-100 start-0">
            <div class="form-control load-items-cat p-0 m-0">
                <ul>
                    <li v-for="i in component.list" :key="i.codigo" @click="getItems(i)" class="d-flex px-3 py-2">
                        <div class="me-3 item-type">{{ i.tipo }}</div>
                        <div class="item-desc">{{ i.nome }}</div>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<style scoped>
ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

ul li {
    cursor: pointer;
    border-bottom: var(--border-box);
}

ul li h3{
    font-weight: 700;
    color: var(--color-base);
}

ul li:hover {
    background-color: var(--color-contrast-hover);
}

.item-type {
    width: 35px;
    border-right: var(--border-box);
}
</style>