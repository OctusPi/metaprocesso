<script setup>
import utils from '@/utils/utils';
import { onMounted } from 'vue';

// Props
const props = defineProps({
	options: { type: Array, required: true },
	identify: { type: String, required: true },
	idkey: { type: [Object, String], default: () => null },
	castkey: { type: [Array], default: () => null },
});

const model = defineModel({ default: [] })

function mountCasts(option) {
	return props.castkey.map((item) => {
		let match = option[item.cast]

		if (item.property) {
			match = String(match[item.property])
		}

		if (item.replace) {
			const replacement = item.replace.dataset
				.find((obj) => obj[item.replace.from ?? 'id'] == match)
			if (replacement) {
				match = String(replacement[item.replace.to])
			}
		}

		return {
			id: option.id,
			title: utils.truncate(match, 120)
		}
	})
}

onMounted(() => {
	if (!model.value) {
		model.value = []
	}
})

</script>

<template>
	<div class="form-control select-multipe">
		<div class="form-check mb-1" v-for="option in props.options" :key="option.id + props.identify">
			<input class="form-check-input" type="checkbox" :name="option.id + props.identify" v-model="model"
				:value="!props.idkey ? option : option[props.idkey]" :id="option.id + props.identify">
			<label class="check-label form-check-label small d-flex gap-1" :for="option.id + props.identify">
				<span class="cast px-1" v-if="!props.castkey">
					{{ option.title }}
				</span>
				<template v-if="props.castkey">
					<span v-for="item in mountCasts(option)" :key="item.id" class="cast px-1">
						{{ item.title }}
					</span>
				</template>
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

.cast {
	display: flex;
	align-items: center;
	justify-content: center;
	border: 1px solid #ccc;
	font-size: 1.15em;
	border-radius: 4px;
}

.check-label {
	min-width: 768px;
}
</style>