<script setup>
import { onMounted } from 'vue';

// Props
const props = defineProps({ 
	options: { type: Array, required: true },
	identify: {type: String, required: true},
	idkey:{type: [Object, String], default: () => null }
});
const model = defineModel({default: []})

onMounted(() => {
	if(!model.value){
		model.value = []
	}
})

</script>

<template>
	<div class="form-control select-multipe">
		<div class="form-check mb-1" v-for="option in props.options" :key="option.id+props.identify">
			<input class="form-check-input" type="checkbox" :name="option.id+props.identify"
			v-model="model"
			:value="!props.idkey ? option : option[props.idkey]" 
			:id="option.id+props.identify">
			<label class="form-check-label small" :for="option.id+props.identify">
				{{ option.title }}
			</label>
		</div>
	</div>
</template>

<style>

.select-multipe {
	height: 200px;
	overflow: auto;
	padding: 16px;
}
</style>