<script setup>
</script>

<template>
    <header class="header-main d-md-flex flex-reverse justify-content-between align-items-center">
        <div class="header-main-action md-my-2">
            <button type="button">1</button>
            <button type="button">2</button>

            <div class="btn-group" role="group">
                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                    <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                </ul>
            </div>
        </div>
        <div class="header-main-title d-flex align-items-center">
            <i class="bi nav-link-icon bi-grid me-2"></i>
            <div>
                <h1 class="p-0 m-0">Title Page</h1>
                <span class="m-0 p-0 small">Description about functionality page</span>
            </div>
        </div>

        <div class="header-main-searchbar px-4">
            <form class="row g-3">
                <div class="input-group">
                    <div class="input-group-text" id="btnGroupAddon">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" class="form-control" placeholder="Localizar"
                        aria-label="Input group example" aria-describedby="btnGroupAddon">
                </div>
            </form>
        </div>
    </header>
</template>