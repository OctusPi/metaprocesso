<script setup>
import { inject, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import http from '@/services/http';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';


const sysapp = inject('sysapp')
const emit = defineEmits(['callAlert'])

const route = useRoute()
const token = route.params.token
const user = ref(undefined)
const success = ref(false)
const page = ref({
    data: {
        token: token
    },
    rules:{
        fields: {
            newpass:'required',
            confpass:'required',
        },
        valids:{}
    }
})

function checktoken(){
    function respCheckToken(response){
        if(response.status === 200){
            user.value = response.data?.user
        }
    }

    http.post('/auth/verify', {token:token}, emit, respCheckToken)
}

function renew(){
    const validation = forms.checkform(page.value.data, page.value.rules);
    if(!validation.isvalid){
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    function respRenewPass(response, emit){
        if(response.status === 200){
            success.value = true
        }

        http.response(response, emit)
    }

    http.post('/auth/renew', page.value.data, emit, respRenewPass)
}

onMounted(() => {
    checktoken()
})
</script>

<template>
    <main class="container-back-box d-flex justify-content-center align-items-center">
        <div class="box p-5 rounded-4 shadow-sm container-box">
            <header class="d-lg-flex align-items-center text-center text-lg-start mb-4">
                <img src="../assets/imgs/logo.svg" class="logomarca-box mb-3 mb-lg-0">
                <div>
                    <h1 class="m-0 p-0 ms-0 ms-lg-2 sistem-title-box">{{ sysapp.name }}</h1>
                    <p class="p-0 m-0 text-color-sec small ms-0 ms-lg-2">{{ sysapp.desc }}</p>
                </div>
            </header>
            
            <h1 class="my-2 mb-4 text-center txt-color"> Cadastrar Nova Senha </h1>

            <div v-if="user && token && !success">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle icon-user"></i>
                    <h2 class="mt-4">{{ user.name ?? 'Username' }}</h2>
                    <p class="small txt-color-sec p-0 m-0">Perfil: {{ user.profile }}</p>
                    <p class="small txt-color-sec p-0 m-0">Ultimo Acesso: {{ user.last_login }}</p>
                </div>

                <form class="row g-3" @submit.prevent="renew">
                    
                    <div>
                        <label for="newpass" class="form-label d-flex justify-content-between">
                            Nova Senha
                        </label>
                        <input type="password" name="newpass" v-model="page.data.newpass"
                        :class="{'form-control-alert' : page.rules.valids.newpass}"
                        class="form-control" id="newpass" placeholder="*********">
                    </div>

                    <div class="mb-3">
                        <label for="newpass" class="form-label d-flex justify-content-between">
                            Confirmar Senha
                        </label>
                        <input type="password" name="confpass" v-model="page.data.confpass"
                        :class="{'form-control-alert' : page.rules.valids.confpass}"
                        class="form-control" id="confpass" placeholder="*********">
                    </div>

                    <p class="small text-danger text-center">Sua deve ter pelo menos 08 caracteres, com letras, maiusculas, minusculas, números e simbolos!</p>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-outline-warning w-100">Cadastrar <i class="bi bi-check-circle"></i></button>
                    </div>

                    <div class="box-copyr">
                        <p class="txt-color-sec small p-0 m-0 text-center">Todos os direitos reservados.</p>
                        <p class="txt-color-sec small p-0 m-0 text-center">{{ sysapp.copy }}&copy;</p>
                    </div>
                    
                </form>
            </div>

            <div v-else-if="success" class="text-center">
                <i class="bi bi-check2-square text-success fs-1"></i>
                <p class="mb-4">Sua senha foi alterada, uma notificação por e-mail foi enviada, por favor realize seu acesso normalmente com sua nova senha.</p>

                <RouterLink to="/">Realizar Login</RouterLink>
            </div>

            <div v-else class="text-center">
                <i class="bi bi-exclamation-diamond text-warning fs-1"></i>
                <p class="mb-4">Token expirado ou inválido, solicite uma nova recuperação de senha.</p>

                <RouterLink to="/recover">Recuperar Senha</RouterLink>
            </div>

        </div>
    </main>
</template>

<style>
.icon-user{
    font-size: 3rem;
}
</style>