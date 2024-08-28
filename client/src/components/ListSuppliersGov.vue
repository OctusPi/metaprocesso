<script setup>
import SuppliersGov from '@/services/suppliersgov';
import { ref } from 'vue';

const emit = defineEmits(['resp-select']);
const suppliersgov = new SuppliersGov();

const suppliers = ref([]);
const supplier = ref('');
const search = ref('');

async function searchSuppliers() {
  const { data } = await suppliersgov.list(search.value);
  suppliers.value = data;
}

function clear() {
  supplier.value = '';
  suppliers.value = [];
}

function makeAddress(item) {
  const municipio = item._links.municipio?.title?.split(':')[1] || 'Desconhecido';
  return {
    ...item,
    address: `${municipio}, ${item.uf}`,
  };
}

function selectSupplier(item) {
  emit('resp-select', makeAddress(item));
  clear();
}
</script>

<template>
  <div class="row position-relative">
    <div class="col-sm-12">
      <label for="search-suppliersgov" class="form-label">Localizar Fornecedor SICAF</label>
      <div class="input-group">
        <input name="search-suppliersgov" type="text" class="form-control"
          placeholder="Localizar Fornecedor de Compras Gov" aria-label="Localizar Fornecedor de Compras Gov"
          aria-describedby="button-search-suppliersgov" v-model="search" @keyup.enter="searchSuppliers" @input="clear" />

        <button class="btn btn-action-primary" type="button" @click="searchSuppliers">
          <ion-icon name="search" class="fs-5"></ion-icon>
        </button>
      </div>
    </div>

    <div v-if="suppliers.length" class="position-absolute my-2 top-100 start-0">
      <div class="form-control load-items-cat p-0 m-0">
        <ul>
          <li v-for="item in suppliers" :key="item.codigo" @click="selectSupplier(item)" class="d-flex px-3 py-2">
            <div class="me-3 item-type">{{ item.uf }}</div>
            <div class="item-desc">{{ item.nome }}</div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.search {
  width: 100%;
  max-height: 400px;
  overflow: scroll;
  border-radius: 12px;
  z-index: 999;
}

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