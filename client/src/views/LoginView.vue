<script setup>
import { inject, ref } from 'vue';
import http from '@/services/http';
import { useJwt } from '@/stores/auth';


const sysapp = inject('sysapp')

const emit = defineEmits(['callAlert'])
const page = ref({
    data: {
        username:'',
        password:''
    },
    rules:{
        username:'required|email',
        password:'required'
    },
    valids:{}
})


function login(){
    function resplogin(response, emit){

        if(response.status === 200){
            const auth = useJwt()
            auth.setToken(response.data.token)
            auth.setUser(response.data.user)
        }
        
        http.response(response, emit)
    }

    http.post('/auth', page.value.data, emit, resplogin)
}

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
            <form class="row g-3" @submit.prevent="login">
                <div class="mb-2">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="email" name="username" class="form-control" id="username"
                        placeholder="nome@example.com" v-model="page.data.username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label d-flex justify-content-between">
                        Senha
                        <RouterLink to="/recover" class="box-link">Esqueceu sua senha?</RouterLink>
                    </label>
                    <input type="password" name="password" class="form-control" id="password"
                        placeholder="***********" v-model="page.data.password">
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-outline-primary w-100">Entrar <i class="bi bi-check2-circle"></i></button>
                </div>
                

                <div class="box-copyr">
                    <p class="txt-color-sec small p-0 m-0 text-center">Todos os direitos reservados.</p>
                    <p class="txt-color-sec small p-0 m-0 text-center">{{ sysapp.copy }}&copy;</p>
                </div>
                
            </form>
        </div>
    </main>
</template>