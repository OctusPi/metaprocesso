<script setup>

import { onMounted, ref, watch } from 'vue';

const show_items = ref(false)

const props = defineProps({
	options: { type: Array, required: true },
	identify: { type: String, required: true },
	idkey: { type: [Object, String], default: () => null },
	valid: { type: Boolean, default: false }
});

const model = defineModel({ default: [] })

const valid = ref(props.valid)

watch(() => props.valid, (newVal) => {
	valid.value = newVal
})

onMounted(() => {
	if (!model.value) {
		model.value = []
	}
})

</script>

<template>
	<div class="position-relative">
		<div :class="{ 'form-control-alert': valid }" @click="show_items = !show_items" class="form-control d-flex justify-content-between btn-drop">
			<span>Selecionar Itens</span>
			<i class="bi" :class="show_items ? 'bi-caret-up-fill' : 'bi-caret-down-fill'"></i>
		</div>
		<div v-show="show_items" class="w-100 position-absolute div-drop">
			<div class="form-check mb-1" v-for="option in props.options" :key="option.id + props.identify">
				<input class="form-check-input" type="checkbox"
					:name="option.id + props.identify" v-model="model"
					:value="!props.idkey ? option : option[props.idkey]" :id="option.id + props.identify">
				<label class="check-label form-check-label" :for="option.id + props.identify">
					{{ option.title }}
				</label>
			</div>
		</div>
	</div>


</template>

<style>
.btn-drop {
	cursor: pointer;
}

.div-drop {
	border: 2px solid var(--color-input-border);
	border-radius: 8px;
	background-color: var(--color-input);
	padding: 10px 14px;
	color: var(--color-text);
	font-weight: 500;
}

.check-label {
	font-size: 0.9rem !important;
}
</style>