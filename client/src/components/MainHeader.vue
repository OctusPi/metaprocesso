<script setup>
</script>

<template>
    <header class="header-main d-md-flex justify-content-between align-items-center">
        <div class="header-main-title d-flex align-items-center flex-fill">
            <i class="bi nav-link-icon bi-grid me-2 rounded-circle txt-color-base"></i>
            <div>
                <h1 class="p-0 m-0 txt-color">Title Page</h1>
                <span class="m-0 p-0 small txt-color-sec">Description about functionality page</span>
            </div>
        </div>

        <div class="header-main-searchbar flex-fill">
            <div class="dropdown w-100">
                <button class="header-main-btn-searchbar rounded-4" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-search"></i> Localizar
                </button>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </div>
        </div>

        <div class="header-main-action my-md-2 d-flex flex-fill justify-content-end">
            <div class="dropdown">
  <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-bell"></i>
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><a class="dropdown-item" href="#">Something else here</a></li>
  </ul>
</div>
<div class="dropdown">
  <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-moon-stars"></i>
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><a class="dropdown-item" href="#">Something else here</a></li>
  </ul>
</div>
<div class="dropdown">
  <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-person-circle"></i>
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><a class="dropdown-item" href="#">Something else here</a></li>
  </ul>
</div>
        </div>
    </header>
</template>