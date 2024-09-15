/** @type {import('tailwindcss').Config} */
const {join} = require('path');
module.exports = {
	prefix: 'tw-',
	corePlugins: {
		preflight: false,
	},
	content: [
		join(__dirname, 'includes/Admin/**/views/**/**/*.php'),
		join(__dirname, 'templates/**/*.php'),
		join(__dirname, '.assets/packages/**/*.js'),
		join(__dirname, '.assets/client/**/*.js'),
	],
	media: false,
	theme: {},
	plugins: [],
};
