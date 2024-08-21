<script setup>
import { onMounted, ref } from 'vue';
import auth from '@/stores/auth';
import storgan from '@/stores/organ';

const user  = auth.get_user()
const organ = (user?.organs ?? []).find(obj => obj.id === storgan.organ)
const timesession = ref('60:00')

function isLoggedIn() {
    if (!user || !user?.token) {
        logout()
    }
}

function logout() {

    auth.clear()
    window.location = "/"
}

function countTimeSession() {
    let minutes = 59
    let seconds = 59

    function regressivecount() {
        if (seconds > 0) {
            seconds--
        } else {
            seconds = 59
            minutes--
        }

        timesession.value = minutes >= 0 ? minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0') : '00:00'

        if (minutes < 0) {
            logout()
        }
    }

    setInterval(regressivecount, 1000);
}

onMounted(() => {
    isLoggedIn()
    countTimeSession()
})

</script>

<template>
    <div class="dropdown" v-if="user">
        <button class="btn header-main-btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <ion-icon name="person-outline" class="fs-5"></ion-icon>
        </button>
        <ul class="dropdown-menu shadow-lg">
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <ion-icon name="person-outline" class="me-3 fs-5"></ion-icon>
                    <span>
                        <span class="d-block p-0 m-0">Usuário</span>
                        <span class="d-block p-0 m-0 fw-bolder">{{ user.name }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <ion-icon name="id-card-outline" class="me-3 fs-5"></ion-icon>
                    <span>
                        <span class="d-block p-0 m-0">Perfil</span>
                        <span class="d-block p-0 m-0 fw-bolder">{{ user.profile }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <ion-icon name="business-outline" class="me-3 fs-5"></ion-icon>
                    <span>
                        <span class="d-block p-0 m-0">Orgão</span>
                        <span class="d-block p-0 m-0 fw-bolder">{{ organ?.name ?? 'Não Informado' }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <ion-icon name="calendar-outline" class="me-3 fs-5"></ion-icon>
                    <span>
                        <span class="d-block p-0 m-0">Ultimo Acesso</span>
                        <span class="d-block p-0 m-0 fw-bolder">{{ user.last_login }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <ion-icon name="timer-outline" class="me-3 fs-5"></ion-icon>
                    <span>
                        <span class="d-block p-0 m-0">Tempo Restante</span>
                        <span class="d-block p-0 m-0 fw-bolder">{{ timesession }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a @click="logout" class="dropdown-item d-flex align-items-center" href="#">
                    <ion-icon name="log-out-outline" class="me-3 fs-5"></ion-icon>
                    <span>
                        <span class="d-block p-0 m-0">Fazer Logout</span>
                        <span class="d-block p-0 m-0 fw-bolder">Sair com Segurança</span>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</template>

<style scoped>
span {
    font-size: 0.8rem;
    color: var(--color-text-sec);
    margin: 0 !important;
    padding: 0 !important;
}

span.fw-bolder {
    color: var(--color-text);
}
</style>