<script setup>
  import TableList from '@/components/table/TableList.vue';
  import NavMainUi from '@/components/NavMainUi.vue';
  import HeaderMainUi from '@/components/HeaderMainUi.vue';
  import FooterMainUi from '@/components/FooterMainUi.vue';
  import Layout from '@/services/layout';
  import Actions from '@/services/actions';
  import Mounts from '@/services/mounts';
  import masks from '@/utils/masks';
  import { onMounted, watch } from 'vue';

  const emit = defineEmits(['callAlert', 'callUpdate'])

  const props = defineProps({
    datalist: { type: Array, default: () => [] }
  })

  const [page, pageData] = Layout.new(emit, {
    url: '/pca',
    datalist: props.datalist,
    header: [
      { sub: [{ key: 'emission' }], title: 'EMISSÃO' },
      { title: 'ANO REFERÊNCIA', key: 'reference_year' },
      { key: 'comission.name', title: 'COMISSÃO' },
      { key: 'observations', title: 'OBSERVAÇÕES' },
      { key: 'status', title: 'STATUS' }
    ],
    rules: {
      comission_id: 'required',
      reference_year: 'required',
      emission: 'required',
      price: 'required',
      status: 'required',
    }
  })

  watch(() => props.datalist, (newdata) => {
    page.datalist = newdata
  })

  onMounted(() => {
    pageData.selects()
    pageData.list()
  })

</script>

<template>
  <div class="page">
    <NavMainUi />
    <main class="main">
      <HeaderMainUi />

      <!-- List -->
      <section v-if="!page.ui.register" class="main-section container-fluid p-4">
        <div role="heading" class="inside-title mb-4">
          <div>
            <h2>PCAs</h2>
            <p>Listagem dos Planos de Contratações Anuais</p>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <button @click="pageData.ui('register')" class="btn btn-action-primary">
              <ion-icon name="add" class="fs-5"></ion-icon>
              Adicionar
            </button>
            <button @click="pageData.ui('search')" class="btn btn-action-secondary">
              <ion-icon name="search" class="fs-5"></ion-icon>
              Pesquisar
            </button>
          </div>
        </div>
        <!-- Search -->
        <div v-if="page.ui.search" role="search" class="content container p-4 mb-4">
          <form @submit.prevent="pageData.list" class="row g-3">
            <div class="col-sm-12 col-md-4">
              <label for="s-reference_year" class="form-label">Ano de referência</label>
              <input maxlength="4" type="text" name="s-reference_year" class="form-control"
                :class="{ 'form-control-alert': page.valids.reference_year }" id="s-reference_year" placeholder="AAAA"
                v-maska:[masks.masknumbs] v-model="page.search.reference_year" />
            </div>
            <div class="col-sm-12 col-md-4">
              <label for="s-status" class="form-label">Status</label>
              <select name="status" class="form-control" id="s-status" v-model="page.search.status">
                <option value=""></option>
                <option v-for="o in page.selects.status" :key="o.id" :value="o.id">
                  {{ o.title }}
                </option>
              </select>
            </div>
            <div class="col-sm-12 col-md-4">
              <label for="s-comission" class="form-label">Comissão</label>
              <select name="comission" class="form-control" id="s-comission" v-model="page.search.comission_id">
                <option value=""></option>
                <option v-for="o in page.selects.comissions" :key="o.id" :value="o.id">
                  {{ o.title }}
                </option>
              </select>
            </div>
            <div class="d-flex flex-row-reverse mt-4">
              <button type="submit" class="btn btn-action-primary">
                <ion-icon name="filter"></ion-icon>
                Aplicar Filtro
              </button>
            </div>
          </form>
        </div>
        <div role="list" class="container p-0">
          <TableList :header="page.header" :body="page.datalist" :actions="[
            Actions.Edit(pageData.update),
            Actions.Delete(pageData.remove),
          ]" :mounts="{
            status: [Mounts.Cast(page.selects.status), Mounts.Status()],
          }" />
        </div>
      </section>

      <!-- Register -->
      <section v-if="page.ui.register" class="main-section container-fluid p-4">
        <div role="heading" class="inside-title mb-4">
          <div>
            <h2>Registrar PCA</h2>
            <p>Preencha os dados abaixo para realizar o registro</p>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <button @click="pageData.ui('register')" class="btn btn-action-secondary">
              <ion-icon name="arrow-back" class="fs-5"></ion-icon>
              Voltar
            </button>
          </div>
        </div>
        <div role="form" class="container p-0">
          <form class="form-row" @submit.prevent="pageData.save()">
            <div class="row m-0 mb-3 g-3 content p-4 pt-1">
              <div class="col-sm-12 col-md-4">
                <label for="reference_year" class="form-label">Ano do PCA</label>
                <input maxlength="4" type="text" name="reference_year" class="form-control"
                  :class="{ 'form-control-alert': page.valids.reference_year }" id="reference_year" placeholder="AAAA"
                  v-maska:[masks.masknumbs] v-model="page.data.reference_year" />
              </div>
              <div class="col-sm-12 col-md-4">
                <label for="emission" class="form-label">Criação do PCA</label>
                <VueDatePicker auto-apply v-model="page.data.emission"
                  :input-class-name="page.valids.emission ? 'dp-custom-input-dtpk-alert' : 'dp-custom-input-dtpk'"
                  :enable-time-picker="false" format="dd/MM/yyyy" model-type="dd/MM/yyyy" :auto-position="false"
                  locale="pt-br" calendar-class-name="dp-custom-calendar" calendar-cell-class-name="dp-custom-cell"
                  menu-class-name="dp-custom-menu" />
              </div>
              <div class="col-sm-12 col-md-4">
                <label for="price" class="form-label">Preço</label>
                <input v-maska:[masks.maskmoney] @maska="(v) => page.data.price = v.detail.unmasked" type="text"
                  name="price" class="form-control" :class="{ 'form-control-alert': page.valids.price }" id="price"
                  placeholder="R$ 0.00" v-bind:value="page.data.price" />
              </div>
              <div class="col-sm-12 col-md-8">
                <label for="comission" class="form-label">Comissão/Equipe de
                  Planejamento</label>
                <select name="comission" class="form-control"
                  :class="{ 'form-control-alert': page.valids.comission_id }" id="comission"
                  v-model="page.data.comission_id">
                  <option value=""></option>
                  <option v-for="s in page.selects.comissions" :value="s.id" :key="s.id">
                    {{ s.title }}
                  </option>
                </select>
                <div id="comissionHelpBlock" class="form-text txt-color-sec">
                  Ao selecionar a comissão/equipe de planejamento seus
                  integrantes serão vinculados ao documento
                </div>
              </div>
              <div class="col-sm-12 col-md-4">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-control" :class="{ 'form-control-alert': page.valids.status }"
                  id="status" v-model="page.data.status">
                  <option value=""></option>
                  <option v-for="s in page.selects.status" :value="s.id" :key="s.id">
                    {{ s.title }}
                  </option>
                </select>
              </div>
              <div class="col-sm-12">
                <label for="observations" class="form-label">
                  Observações
                </label>
                <textarea name="observations" class="form-control" rows="4" :class="{
                  'form-control-alert': page.valids.observations
                }" id="observations" v-model="page.data.observations"></textarea>
              </div>
            </div>
            <div class="d-flex flex-row-reverse gap-2 mt-4">
              <button class="btn btn-action-primary">
                <ion-icon name="checkmark-circle-outline" class="fs-5"></ion-icon>
                Registrar
              </button>
              <button @click="pageData.ui('register')" class="btn btn-action-secondary">
                <ion-icon name="close-outline" class="fs-5"></ion-icon>
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </section>

      <FooterMainUi />
    </main>
  </div>
</template>
