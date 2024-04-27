/** @type {import('tailwindcss').Config} */
const {join} = require('path');
module.exports = {
	prefix: 'tw-',
	corePlugins: {
		preflight: false,
	},
	content: [
		join(__dirname, 'includes/Admin/views/**/*.php')
	],
	media: false,
	theme: {},
	plugins: [],
};
