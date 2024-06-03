<script setup>

import { ref } from 'vue'
import CatGov from '@/services/catgov'

const emit   = defineEmits(['resp-catgov'])
const catgov = new CatGov()
const component = ref({
    search: '',
    selected: null,
    list: null,
    items: null
})

async function searchItems() {
    
    if(component.value.search){
        const list = await catgov.searchItem(component.value.search)
        component.value.list = list?.data
        component.value.selected = null
        component.value.items = null
    }
}

async function getItems(i) {
    const list = await catgov.getItems(i)
    component.value.selected = i
    component.value.items = list
    component.value.list  = null
    component.value.items.items = i.tipo === 'S' ? [list?.items] : list?.items
}

function selectItem(item){
    emit('resp-catgov', {
        category: component.value.selected,
        item: item,
        units: component.value.items?.units,
        accounting: component.value.items?.accounting
    });

    component.value.search = null
    clear()
}

function clear(){
    if(!component.value.search){
        component.value.selected = null
        component.value.items = null
        component.value.list  = null
    }
}

</script>

<template>
    <div class="row position-relative">
        <div class="col-sm-12">
            <label for="organ" class="form-label">Localizar CATMAT/CATSER</label>
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
                    <!-- list categories -->
                    <li v-for="i in component.list" :key="i.codigo" @click="getItems(i)" class="d-flex px-3 py-2">
                        <div class="me-3 item-type">{{ i.tipo }}</div>
                        <div class="item-desc">{{ i.nome }}</div>
                    </li>

                    <!-- list items -->
                    <template v-if="component.selected?.tipo === 'M'">
                        <li v-for="l in component.items?.items" :key="l.codigoItem" @click="selectItem(l)" class="d-flex align-items-center px-3 py-2">
                            <div class="me-3 item-type">{{ component.selected?.tipo }}</div>
                            <div class="item-desc">
                                <h3 class="m-0 p-0 small">{{ `${l.codigoItem} - ${l.nomePdm}` }}</h3>
                                <span class="m-0 p-0 me-2 small" v-for="c in l.buscaItemCaracteristica" :key="c.codigoCaracteristica">
                                    {{ `${c.nomeCaracteristica}: ${c.nomeValorCaracteristica};` }}
                                </span>
                            </div>
                        </li>
                    </template>

                    <template v-if="component.selected?.tipo === 'S'">
                        <li v-for="l in component.items?.items" :key="l.codigoServico" @click="selectItem(l)" class="d-flex align-items-center px-3 py-2">
                            <div class="me-3 item-type">{{ component.selected?.tipo }}</div>
                            <div class="item-desc">
                                <h3 class="m-0 p-0 small">{{ `${l.codigoServico} - ${component.selected.nome}` }}</h3>
                                <p class="m-0 p-0 me-2 small"> Nome do Grupo: {{ l.nomeGrupo }}</p>
                                <p class="m-0 p-0 me-2 small"> Descrição: {{ l.descricaoServicoAcentuado }}</p>
                            </div>
                        </li>
                    </template>
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