<script setup>

    import style from '@/stores/theme';

    function changeTheme(mode){
        const screen = document.getElementById('screen')
        if(screen){

            //remove theme
            screen.classList.forEach(cl => {
                screen.classList.remove(cl)
            })

            //set theme
            screen.classList.add(mode)
            style.setTheme(mode)

        }
    }
</script>

<template>
    <div class="dropdown"         data-bs-toggle="tooltip" data-bs-placement="top"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="This top tooltip is themed via CSS variables.">
        <button class="btn header-main-btn-icon me-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-palette2 fs-5"></i>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" @click="changeTheme('ligth')">
                <i class="bi bi-sun-fill me-1"></i>
                Tema Claro
            </a></li>
            <li><a class="dropdown-item" href="#" @click="changeTheme('dark')">
                <i class="bi bi-moon-stars-fill me-1"></i>
                Tema Escuro
            </a></li>
        </ul>
    </div>
</template>