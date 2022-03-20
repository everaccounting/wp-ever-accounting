<template>
	<div id="app">
		<h1>Backend App</h1>
		<p>Lorem ipsum dolor sit amet, consectetur</p>
			<Link>Click me</Link>
	</div>
</template>

<script>
const {Link} = eaccounting.components;
console.log(Link);
export default {
	name: 'App',
	components: {
	  Link
	}

}
</script>
