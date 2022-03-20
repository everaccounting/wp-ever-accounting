<template>
		Hello John
	<button class="link" @click="onClick">
		<slot></slot>
	</button>
</template>
<script>
export default {
	name: "Link",
	methods: {
		onClick($event) {
			this.$emit('click', $event);
		},
	},
};
</script>

