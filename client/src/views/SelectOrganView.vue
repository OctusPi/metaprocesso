<script setup>
import { inject, onMounted, ref } from 'vue';
import http from '@/services/http';
import auth from '@/stores/auth';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';
import organ from '@/stores/organ';

const emit = defineEmits(['callAlert'])

const sysapp = inject('sysapp')
const user   = auth.get_user()

const page = ref({
    data: {},
    selects: {
        organs:user?.organs ?? []
    },
    rules:{
        fields: {},
        valids:{}
    }
})

function apply_select(){
    const validation = forms.checkform(page.value.data, page.value.rules);
    if(!validation.isvalid){
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    http.post('/auth/auth_organ', page.value.data, emit, () => {
        organ.set_organ(page.value.selects.organs.find(o => o.id === page.value.data.organ))
        window.location = '/home'
    })
}

onMounted(() => {
    if (user === null) {
        window.location = '/'
    }

    if (user?.profile !== "Administrador") {
        page.value.rules.fields = {
            organ:'required'
        }
    }
})

</script>

<template>
    <main class="main-single-box d-flex justify-content-center align-items-center">
        <div class="single-box p-5">
            <header class="text-center mb-4">
                <img src="../assets/imgs/logo.svg" class="logomarca-box mb-3">
                <div>
                    <h1 class="m-0 p-0 ms-0 ms-lg-2 txt-color">{{ sysapp.name }}</h1>
                    <p class="p-0 m-0 text-color-sec small ms-0 ms-lg-2">{{ sysapp.desc }}</p>
                </div>
            </header>

            <h1 class="my-2 mb-4 text-center txt-color"> Selecione a Entidade </h1>

            <form class="row g-3" @submit.prevent="apply_select">
                <div class="mb-2">
                    <label for="organ" class="form-label">Orgão</label>
                    <select name="organ" class="form-control"
                    :class="{'form-control-alert' : page.rules.valids.organ}"
                    id="organ" v-model="page.data.organ">
                        <option></option>
                        <option v-for="o in page.selects.organs" :key="o.id" :value="o.id">
                            {{ o.name }}
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-action-primary w-100 d-flex align-items-center justify-content-center">
                        Proceguir
                        <ion-icon name="chevron-forward-outline" class="fs-6 ms-1"></ion-icon>
                    </button>
                </div>
                

                <div class="box-copyr">
                    <p class="txt-color-sec small p-0 m-0 text-center">Todos os direitos reservados.</p>
                    <p class="txt-color-sec small p-0 m-0 text-center">{{ sysapp.copy }}&copy;</p>
                </div>
                
            </form>
        </div>
    </main>
</template>
