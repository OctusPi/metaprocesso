<script setup>
import { inject, ref } from 'vue';
import http from '@/services/http';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';

const sysapp = inject('sysapp')
const emit = defineEmits(['callAlert'])
const page = ref({
    data: {
        username:'',
    },
    rules:{
        fields: {
            username:'required|email',
        },
        valids:{}
    }
})

function recover(){
    const validation = forms.checkform(page.value.data, page.value.rules);
    if(!validation.isvalid){
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    http.post('/auth/recover', page.value.data, emit)
}
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

            <h1 class="my-2 mb-4 text-center txt-color"> Recuperação de Senha </h1>

            <form class="row g-3" @submit.prevent="recover">
                <div class="mb-2">
                    <label for="username" class="form-label d-flex justify-content-between">
                        E-mail
                        <RouterLink to="/" class="box-link">Ir para login</RouterLink>
                    </label>
                    <input type="email" name="username" class="form-control" :class="{'form-control-alert' : page.rules.valids.username}"
                    id="username" placeholder="usuário@example.com" v-model="page.data.username">
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-action-primary w-100 d-flex align-items-center justify-content-center">
                        Recuperar
                        <ion-icon name="mail-unread-outline" class="fs-6 ms-1"></ion-icon>
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
