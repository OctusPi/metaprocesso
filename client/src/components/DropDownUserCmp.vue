<script setup>
import { onMounted, ref } from 'vue';
import auth from '@/stores/auth';
import storgan from '@/stores/organ';

const user = auth.get_user()
const organ = storgan.get_organ()
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
    <div class="d-flex align-items-center">
        <div class="dropdown" v-if="user">
            <button class="btn header-main-btn-icon d-flex align-items-center" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <ion-icon name="person-outline" class="me-0 me-md-2 fs-5"></ion-icon>
                <div class="text-start label-btn-header">
                    <span class="d-block p-0 m-0 fw-bolder">{{ user.name.split(' ')[0] }}</span>
                    <span class="d-block p-0 m-0">{{ user.profile }}</span>
                </div>
            </button>
            <ul class="dropdown-menu shadow-lg">
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="/selectorgan">
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
            </ul>
        </div>

        <button @click="logout" class="btn header-main-btn-icon d-flex align-items-center" type="button">
            <ion-icon name="log-out-outline" class="me-0 me-md-2 fs-5"></ion-icon>
            <div class="text-start label-btn-header">
                <span class="d-block p-0 m-0 fw-bolder">Fazer Logout</span>
                <span class="d-block p-0 m-0">Sair com Segurança</span>
            </div>
        </button>
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