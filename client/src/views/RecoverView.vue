<script setup>
import http from '@/services/http'

</script>

<template>
    <main class="container-back-box d-flex justify-content-center align-items-center">
        <div class="box p-5 rounded-4 shadow-sm container-box">
            <header class="d-lg-flex align-items-center text-center text-lg-start mb-4">
                <img src="../assets/imgs/logo.svg" class="logomarca-box mb-3 mb-lg-0">
                <div>
                    <h1 class="m-0 p-0 ms-0 ms-lg-2 sistem-title-box">Contratos Plus</h1>
                    <p class="p-0 m-0 text-color-sec small ms-0 ms-lg-2">Gestão e Fiscalização de Contratos</p>
                </div>
            </header>
            
            <h1 class="my-2 text-center txt-color"> Solicitar Recuperação de Senha </h1>

            <form class="row g-3" @submit.prevent="login">
                <div class="mb-2">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="email" name="username" class="form-control" id="username"
                        placeholder="nome@example.com">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label d-flex justify-content-between">
                        Senha
                        <RouterLink to="/" class="box-link">Esqueceu sua senha?</RouterLink>
                    </label>
                    <input type="password" name="password" class="form-control" id="password"
                        placeholder="***********">
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-outline-primary w-100">Entrar <i class="bi bi-check2-circle"></i></button>
                </div>
                

                <div class="box-copyr">
                    <p class="txt-color-sec small p-0 m-0 text-center">Todos os direitos reservados.</p>
                    <p class="txt-color-sec small p-0 m-0 text-center">Octuspi 2024&copy;</p>
                </div>
                
            </form>
        </div>
    </main>
</template>