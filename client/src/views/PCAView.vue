<script setup>
import TableList from '@/components/table/TableList.vue';
import NavMainUi from '@/components/NavMainUi.vue';
import HeaderMainUi from '@/components/HeaderMainUi.vue';
import FooterMainUi from '@/components/FooterMainUi.vue';
import Layout from '@/services/layout';
import Actions from '@/services/actions';
import Mounts from '@/services/mounts';
import masks from '@/utils/masks';
import { onMounted, ref, watch } from 'vue';
import DfdDetails from '@/components/DfdDetails.vue';
import http from '@/services/http';
import { Mask } from 'maska';
import utils from '@/utils/utils';
const emit = defineEmits(['callAlert', 'callUpdate'])

const props = defineProps({
  datalist: { type: Array, default: () => [] }
})

const [page, pageData] = Layout.new(emit, {
  url: '/pca',
  datalist: props.datalist,
  header: [
    { title: 'ANO E EMISSÃO', key: 'reference_year', sub: [{ key: 'emission' }] },
    { key: 'comission.name', title: 'COMISSÃO' },
    { key: 'observations', title: 'OBSERVAÇÕES' },
    { key: 'status', title: 'STATUS' },
  ],
  rules: {
    comission_id: 'required',
    reference_year: 'required',
    emission: 'required',
    price: 'required',
    status: 'required',
  },
  dfd: {
    year: '',
    datalist: [],
    pca: {},
    headers: [
      { key: 'date_ini', title: 'IDENTIFICAÇÃO', sub: [{ key: 'protocol' }] },
      { key: 'demandant.name', title: 'DEMANDANTE' },
      { key: 'ordinator.name', title: 'ORDENADOR' },
      { key: 'unit.name', title: 'ORIGEM' },
      { title: 'OBJETO', sub: [{ key: 'description' }] },
      { key: 'status', title: 'SITUAÇÃO' },
    ],
  },
})

const dfdsChart = ref({})

function populateDfds(dataset) {
  const keys = Object.keys(dataset)
  const values = Object.values(dataset)
  dfdsChart.value = {
    total: values.length,
    series: [{
      name: 'Quantidade',
      data: values
    }],
    chartOptions: {
      chart: {
        id: 'dfdsChart',
        offsetX: -4,
        width: "100%",
        fontFamily: 'Inter, Helvetica, Arial',
        toolbar: {
          show: false
        }
      },
      colors: ['var(--color-base)'],
      grid: {
        borderColor: 'var(--color-input-focus)',
        position: 'front',
        strokeDashArray: 7,
        sort: true
      },
      plotOptions: {
        bar: {
          borderRadius: 8,
          barHeight: "80%",
          borderRadiusApplication: 'end',
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false,
        textAnchor: 'start',
        formatter: function (_, opt) {
          return opt.w.globals.labels[opt.dataPointIndex]
        },
      },
      yaxis: {
        labels: {
          color: 'var(--color-input-focus)',
          show: true
        },
      },
      xaxis: {
        categories: keys,
        stepSize: 1,
        axisBorder: {
          show: true,
          color: 'var(--color-input-focus)',
        }
      }
    }
  }
}

const listDfdsForPCA = (id) => {
  const pca = page.datalist.find(o => o.id === id)
  http.post(`${page.url}/list_dfds/${pca?.reference_year}`, {}, emit, (res) => {
    page.dfd.pca = pca
    page.dfd.year = pca?.reference_year
    page.dfd.datalist = res.data?.datalist
    page.dfd.estimated = res.data?.estimated
    populateDfds(res.data?.dfds_chart ?? [])
    pageData.ui('prepare')
  })
}

const dfd_details = (id) => {
  if (page.dfd.datalist) {
    page.dfd.data = page.dfd.datalist.find(obj => obj.id === id)
    http.get(`${page.url}/list_dfd_items/${id}`, emit, (resp) => {
      page.dfd.data.items = resp.data
    })
  }
}

watch(() => page.ui.register, (newdata) => {
    if (newdata && page.data.id == null) {
        page.data.protocol = utils.dateProtocol(page.organ?.id)
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
      <section v-if="!page.ui.register && !page.ui.prepare" class="main-section container-fluid p-4">
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
            Actions.Create('document-attach', 'DFDs', listDfdsForPCA),
          ]" :mounts="{
            status: [Mounts.Cast(page.selects.status), Mounts.Status()],
            observations: [Mounts.Truncate()],
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
                <label for="protocol" class="form-label">Protocolo</label>
                <input type="text" name="protocol" class="form-control"
                  :class="{ 'form-control-alert': page.valids.protocol }" id="protocol" v-model="page.data.protocol"
                  placeholder="Protocolo">
              </div>
              <div class="col-sm-12 col-md-4">
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
                <label for="price" class="form-label">Preço</label>
                <input type="text" name="price" class="form-control"
                  :class="{ 'form-control-alert': page.valids.price }" id="price" placeholder="R$ 0.00"
                  v-model="page.data.price" v-maska:[masks.maskmoney] />
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

      <!-- List -->
      <section v-if="page.ui.prepare" class="main-section container-fluid p-4">
        <div role="heading" class="inside-title mb-4">
          <div>
            <h2>PCA {{ page.dfd.year }}</h2>
            <p>Listagem das DFDs declaradas no ano de <span class="text-white">{{ page.dfd.year }}</span></p>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <button @click="pageData.ui('prepare')" class="btn btn-action-secondary">
              <ion-icon name="arrow-back" class="fs-5"></ion-icon>
              Voltar
            </button>
          </div>
        </div>
        <div role="list" class="container p-0">
          <div class="row">
            <div class="col-12 col-lg-6 mb-4">
              <div class="content h-100 d-flex flex-column">
                <div class="p-4 h-100 mb-5">
                  <div class="chart-heading">
                    <span>
                      <ion-icon name="information-circle" />
                    </span>
                    <div>
                      <h1>Informações Gerais</h1>
                      <p>Dados do PCA de {{ page.dfd.year }}</p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center justify-content-center w-100 mt-auto h-100">
                    <div class="row w-100">
                      <div class="col-md-4 text-center">
                        <h1 class="m-0 text-white fs-5">{{ page.dfd.datalist.length }}</h1>
                        <p class="m-0">Quantidade de Itens</p>
                      </div>
                      <div class="col-md-4 text-center">
                        <h1 class="m-0 text-white fs-5">
                          R$ {{ (new Mask(masks.maskmoney)).masked(page.dfd.estimated) }}
                        </h1>
                        <p class="m-0">Valor Estimado Total</p>
                      </div>
                      <div class="col-md-4 text-center">
                        <h1 class="m-0 text-white fs-5">
                          {{ page.dfd.pca.emission }}
                        </h1>
                        <p class="m-0">Criação do PCA</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-6 mb-4 graph">
              <div class="content h-100 d-flex flex-column block">
                <div class="p-4 pb-0">
                  <div class="chart-heading">
                    <span>
                      <ion-icon name="document" />
                    </span>
                    <div>
                      <h1>Itens por Tipo</h1>
                      <p>Valores estimados por tipo</p>
                    </div>
                  </div>
                </div>
                <div class="px-2 my-auto" v-if="dfdsChart.total > 0">
                  <apexchart height="260" type="bar" :options="dfdsChart.chartOptions" :series="dfdsChart.series" />
                </div>
                <div v-else class="d-flex justify-content-center align-items-center h-100">
                  <div class="text-center p-4">
                    <ion-icon name="document" class="fs-4" />
                    <p>Sem DFDs recentes</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <TableList :count="false" :header="page.dfd.headers" :body="page.dfd.datalist" :mounts="{
            status: [Mounts.Cast(page.selects.dfds_status), Mounts.Status()],
            description: [Mounts.Truncate(100)]
          }" :actions="[
            Actions.ModalDetails(dfd_details),
          ]" />
        </div>
      </section>

      <DfdDetails :dfd="page.dfd.data" :selects="page.selects" />

      <FooterMainUi />
    </main>
  </div>
</template>

<style scoped>
.graph {
  min-height: 300px;
}
</style>