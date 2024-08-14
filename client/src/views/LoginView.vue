<script setup>
import { inject, onMounted, ref } from 'vue';
import auth from '@/stores/auth';
import http from '@/services/http';
import forms from '@/services/forms';
import notifys from '@/utils/notifys';

const sysapp = inject('sysapp')
const user = ref(auth.get_user())


const emit = defineEmits(['callAlert'])
const page = ref({
    data: {
        username:'octus@mail.com',
        password:'senha123'
    },
    rules:{
        fields: {
            username:'required|email',
            password:'required'
        },
        valids:{}
    }
})

function login(){
    
    const validation = forms.checkform(page.value.data, page.value.rules);
    if(!validation.isvalid){
        emit('callAlert', notifys.warning(validation.message))
        return
    }

    http.post('/auth', page.value.data, emit, (response) => {
        auth.set_user(response.data.user)
        if(response.status === 200){
            window.location = "/selectorgan"
        }
    })
}

function islogged(){
    
    if(user.value){
        http.get('/auth/check', emit, null, (response) => {
            if(response.status === 200){ return }
            logout()
        });
    }
}

function logout(){
    auth.clear()
    user.value = null
}

onMounted(() => {
    islogged()
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

            <div v-if="user" class="text-center">
                <ion-icon name="person-circle-outline" class="fs-2"></ion-icon>
                <h2 class="mt-4">{{ user.name }}</h2>
                <p class="small txt-color-sec p-0 m-0">Perfil: {{ user.profile }}</p>
                <p class="small txt-color-sec p-0 m-0">Ultimo Acesso: {{ user.last_login }}</p>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-sm btn-outline-warning" @click="logout"><i class="bi bi-door-open me-2"></i> Sair do Sistema</button>
                    <RouterLink to="/dashboard" class="btn btn-sm btn-outline-primary"> <i class="bi bi-house-gear me-2"></i>Partiu!</RouterLink>
                </div>
            </div>

            <form v-else class="row g-3" @submit.prevent="login">
                <div class="mb-2">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="email" name="username" class="form-control" :class="{'form-control-alert' : page.rules.valids.username}"
                    id="username" placeholder="nome@example.com" v-model="page.data.username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label d-flex justify-content-between">
                        Senha
                        <RouterLink to="/recover" class="box-link">Esqueceu sua senha?</RouterLink>
                    </label>
                    <input type="password" name="password" class="form-control" id="password"
                    :class="{'form-control-alert' : page.rules.valids.password}"
                        placeholder="***********" v-model="page.data.password">
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-action-primary w-100 d-flex align-items-center justify-content-center">
                        Entrar
                        <ion-icon name="enter-outline" class="fs-6 ms-1"></ion-icon>
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