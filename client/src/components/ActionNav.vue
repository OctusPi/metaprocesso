<script setup>

import { ref } from 'vue'

const emit = defineEmits(['action'])
const props = defineProps({
    id: [String, Number],
    calls: { type: Array, default: () => [] }
})

const calls = ref(props.calls)
const actions = {
    'update': {
        action: (id) => { emit('action', { e: 'action:update', i: id }) },
        icon: 'bi-pencil-square',
        title: 'Editar'
    },
    'delete': {
        action: (id) => { emit('action', { e: 'action:delete', i: id }) },
        icon: 'bi-trash',
        title: 'Excluir'
    },
    'fastdelete': {
        action: (id) => { emit('action', { e: 'action:fastdelete', i: id }) },
        icon: 'bi-trash',
        title: 'Excluir'
    },
    'download': {
        action: (id) => { emit('action', { e: 'action:download', i: id }) },
        icon: 'bi-file-earmark-text',
        title: 'Docs'
    },
    'members': {
        action: (id) => { emit('action', { e: 'action:members', i: id }) },
        icon: 'bi-people',
        title: 'Membros'
    },
    'extinction': {
        action: (id) => { emit('action', { e: 'action:extinction', i: id }) },
        icon: 'bi-calendar2-minus',
        title: 'Extinguir'
    },
    'items': {
        action: (id) => { emit('action', { e: 'action:items', i: id }) },
        icon: 'bi-boxes',
        title: 'Itens'
    }

}

</script>

<template>
    <div class="dropdown">
        <button class="btn btn-sm txt-color" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu px-2 py-3">
            <li>
                <a class="dropdown-item item-action-menu" href="#" v-for="c in calls" :key="actions[c].title"
                    :data-bs-toggle="c === 'delete' ? 'modal' : null"
                    :data-bs-target="c === 'delete' ? '#modalDelete' : null"
                    @click.prevent="actions[c].action(props.id)">
                    <i class="bi me-2" :class="actions[c].icon"></i>
                    {{ actions[c].title }}
                </a>
            </li>
        </ul>
    </div>
</template>

<style>
.item-action-menu {
    font-size: 0.9rem;
}
</style>