<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
	options: { type: Array, required: true, default: () => [] },
	identify: { type: String, required: true },
	idkey: { type: [Object, String], default: 'id' },
	valid: { type: Boolean, default: false },
	label: { type: String, default: 'title' }
});

const show_items = ref(false);
const model = defineModel({ default: [] });
const t_items = ref(0); 
const s_items = ref([]);
const valid = ref(props.valid);
const checkall = ref(false);

let isUpdatingFromModel = false; 
let isUpdatingFromSItems = false; 

const show_selected = () => `${t_items.value.toString().padStart(2, '0')} Itens Selecionados`;

const check_all = () => {
	if (!checkall.value) {
		s_items.value = [];
	} else {
		s_items.value = props.options.map(p => p[props.idkey]);
	}
};

watch(() => props.valid, (newVal) => {
	valid.value = newVal;
});

watch(s_items, (newItems) => {
	if (!isUpdatingFromModel) {
		isUpdatingFromSItems = true;
		const newModel = props.options.filter(option => newItems.includes(option[props.idkey]));
		if (JSON.stringify(newModel) !== JSON.stringify(model.value)) {
			model.value = newModel;
		}
		t_items.value = newItems.length;
		isUpdatingFromSItems = false;
	}
}, { immediate: true });

watch(model, (newModel) => {
	if (!isUpdatingFromSItems) {
		isUpdatingFromModel = true;
		const newSItems = newModel.map(item => item[props.idkey]);
		if (JSON.stringify(newSItems) !== JSON.stringify(s_items.value)) {
			s_items.value = newSItems;
		}
		t_items.value = newSItems.length;
		isUpdatingFromModel = false;
	}
}, { immediate: true });

</script>

<template>
	<div class="position-relative" @mouseover="show_items = true" @mouseleave="show_items = false">
		<div :class="{ 'form-control-alert': valid, 'style-open': show_items }" class="form-control d-flex align-items-center justify-content-between btn-drop">
			<span>{{ show_selected() }}</span>
			<ion-icon @click="show_items = !show_items" :name="show_items ? 'chevron-up-outline' : 'chevron-down-outline'"></ion-icon>
		</div>
		<div v-show="show_items" class="w-100 position-absolute div-drop" :class="{'style-open-box': show_items}">
			<div class="form-check form-switch d-flex flex-row-reverse align-items-center" v-if="props.options.length">
				<input class="form-check-input m-0 ms-2" type="checkbox" role="switch" id="flexSwitchCheckChecked" v-model="checkall" @change="check_all">
				<label class="form-check-label p-0 m-0 ms-3" for="flexSwitchCheckChecked">Selecionar Todos</label>
			</div>

			<div class="form-check mb-1" v-for="option in props.options" :key="option.id + props.identify">
				<input class="form-check-input" type="checkbox"
					:name="option.id + props.identify" v-model="s_items"
					:value="option[props.idkey]" :id="option.id + props.identify">
				<label class="check-label form-check-label" :for="option.id + props.identify">
					{{ option[props.label] }}
				</label>
			</div>
		</div>
	</div>
</template>

<style>
.btn-drop {
	user-select: none;
	cursor: pointer;
	overflow: hidden;
}

.div-drop {
	border: 2px solid var(--cl-border);
	border-radius: 8px;
	background-color: var(--color-input);
	padding: 10px 14px;
	color: var(--cl-txt);
	font-weight: 500;
	max-height: 220px;
	overflow: auto;
	z-index: 2000;
}

.check-label {
	font-size: 0.9rem !important;
}

.style-open {
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;
}

.style-open-box {
	border-top-left-radius: 0;
	border-top-right-radius: 0;
}
</style>
