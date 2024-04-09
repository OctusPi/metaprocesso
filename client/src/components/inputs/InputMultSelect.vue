<script setup>
import { ref, watch } from 'vue';

// Props
const props = defineProps({
	value: { type: Array, default: () => [] },
	options: { type: Array, required: true },
	identifier: { type: String, required: true }
});

// Emits
const emit = defineEmits(['update:value']);

// Data
const selectedValues = ref([...props.value]);

// Watcher para detectar alterações em selectedValues
watch(() => selectedValues.value, (newValue) => {
	emit('update:value', { identifier: props.identifier, value: newValue }); // Emitir evento para notificar o componente pai sobre a mudança
}, { deep: true });

// Método para alternar a seleção de uma opção
function toggleSelection(optionId) {
	const index = selectedValues.value.indexOf(optionId);
	if (index !== -1) {
		selectedValues.value.splice(index, 1); // Remove o valor se já estiver selecionado
	} else {
		selectedValues.value.push(optionId); // Adiciona o valor se não estiver selecionado
	}
}
</script>

<template>
	<select class="form-control select-multipe" multiple>
		<option v-for="option in options" :key="option.id" :value="option.id"
			:selected="selectedValues.includes(option.id)" @mousedown.prevent.stop="toggleSelection(option.id)">
			{{ option.title }}
		</option>
	</select>
</template>

<style>
.select-multipe {
	padding: 0;
}

.select-multipe option {
	padding: 8px;
	cursor: pointer;
}
</style>