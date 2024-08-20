<script setup>


</script>

<template>
    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
        data-bs-title="This top tooltip is themed via CSS variables.">
        <button class="btn header-main-btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <ion-icon name="calendar-outline" class="fs-5"></ion-icon>
        </button>
        <ul class="dropdown-menu shadow-lg">
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    Sem dados a exibir...
                </a>
            </li>
        </ul>
    </div>
</template>