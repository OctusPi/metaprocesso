<script setup>
    import { onMounted, ref } from 'vue';
    import auth from '@/stores/auth';

    const user = auth.getUser()
    const timesession = ref('60:00')

    function isLoggedIn(){
        if(!auth.token || !user){
            logout()
        }
    }

    function logout(){
        
        auth.clear()
        window.location = "/"
    }

    function countTimeSession(){
        let minutes = 59
        let seconds = 59
        
        function regressivecount(){
            if(seconds > 0){
                seconds--
            }else{
                seconds = 59
                minutes--
            }

            timesession.value = minutes >= 0 ? minutes.toString().padStart(2, '0')+':'+seconds.toString().padStart(2, '0') : '00:00'

            if(minutes < 0){
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
    <div class="dropdown">
        <button class="btn header-main-btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-person-workspace me-3 fs-5"></i>
                    <span>
                        <span class="d-block p-0 m-0 small">Usuário</span>
                        <span class="d-block p-0 m-0 small fw-bolder">{{ user.name }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-gear me-3 fs-5"></i>
                    <span>
                        <span class="d-block p-0 m-0 small">Perfil</span>
                        <span class="d-block p-0 m-0 small fw-bolder">{{ user.profile }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-calendar-week me-3 fs-5"></i>
                    <span>
                        <span class="d-block p-0 m-0 small">Ultimo Acesso</span>
                        <span class="d-block p-0 m-0 small fw-bolder">{{ user.last_login }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-alarm me-3 fs-5"></i>
                    <span>
                        <span class="d-block p-0 m-0 small">Tempo Restante</span>
                        <span class="d-block p-0 m-0 small fw-bolder">{{ timesession }}</span>
                    </span>
                </a>
            </li>
            <li>
                <a @click="logout" class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-door-open me-3 fs-5"></i>
                    <span>
                        <span class="d-block p-0 m-0 small">Fazer Logout</span>
                        <span class="d-block p-0 m-0 small fw-bolder">Sair com Segurança</span>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</template>