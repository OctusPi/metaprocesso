<script setup>

import SuppliersGov from '@/services/suppliersgov';
import { ref } from 'vue'

const emit = defineEmits(['resp-select'])

// const mock = {
//     _embedded: {
//         fornecedores: {
//             0: {
//                 id: 1,
//                 cnpj: "000000000000",
//                 cpf: null,
//                 nome: "IRMAOS RODOPOULOS",
//                 ativo: true,
//                 recadastrado: null,
//                 id_municipio: 1234,
//                 uf: "DF",
//                 id_natureza_juridica: 2,
//                 id_porte_empresa: 2,
//                 id_ramo_negocio: 123,
//                 id_unidade_cadastradora: null,
//                 id_cnae: 4112,
//                 habilitado_licitar: true,
//                 _links: {
//                     municipio: "Municipio 1: Brasilia"
//                 }
//             },
//             1: {
//                 id: 1,
//                 cnpj: "000000000000",
//                 cpf: null,
//                 nome: "IRMAOS RODOPOULOS",
//                 ativo: true,
//                 recadastrado: null,
//                 id_municipio: 1234,
//                 uf: "DF",
//                 id_natureza_juridica: 2,
//                 id_porte_empresa: 2,
//                 id_ramo_negocio: 123,
//                 id_unidade_cadastradora: null,
//                 id_cnae: 4112,
//                 habilitado_licitar: true,
//                 _links: {
//                     municipio: "Municipio 1: Brasilia"
//                 }
//             },
//         }
//     }
// }

const suppliersgov = new SuppliersGov()

const suppliers = ref(null)
const supplier = ref(null)
const search = ref(null)

async function searchSuppliers() {
    const { data } = await suppliersgov.list(search)
    suppliers.value = data
}

function clear() {
    supplier.value = null
    suppliers.value = null
}

function makeAddress(item) {
    return {
        ...item,
        address: item._links.municipio?.split(':')[1] + ', ' + item.uf,
    }
}

function selectSupplier(item) {
    emit('resp-select', makeAddress(item))
    clear()
}

</script>

<template>
    <div class="row position-relative">
        <div class="col-sm-12">
            <label for="search-suppliersgov" class="form-label">Localizar Fornecedor SICAF</label>
            <div class="input-group">
                <input name="search-suppliersgov" type="text" class="form-control"
                    placeholder="Localizar Fornecedor de Compras Gov" aria-label="Localizar Fornecedor de Compras Gov"
                    aria-describedby="button-search-suppliersgov" v-model="search" @keyup.enter="searchSuppliers"
                    @keyup="clear">
                <button class="btn btn-group-input" type="button" id="button-search-suppliersgov"
                    @click="searchSuppliers">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <div v-if="suppliers" class="position-absolute my-2 top-100 start-0">
            <div class="form-control load-items-cat p-0 m-0">
                <ul>
                    <li v-for="item in suppliers" :key="item.codigo" @click="selectSupplier(item)"
                        class="d-flex px-3 py-2">
                        <div class="me-3 item-type">{{ item.uf }}</div>
                        <div class="item-desc">{{ item.nome }}</div>
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

ul li h3 {
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