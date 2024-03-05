<script setup>

</script>

<template>
    <div class="dropdown"         data-bs-toggle="tooltip" data-bs-placement="top"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="This top tooltip is themed via CSS variables.">
        <button class="btn header-main-btn-icon me-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-palette2"></i>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
    </div>
</template>