/** @type {import('tailwindcss').Config} */
const {join} = require('path');
module.exports = {
	prefix: 'tw-',
	corePlugins: {
		preflight: false,
	},
	content: [
		join(__dirname, 'includes/Admin/views/*.php'),
		join(__dirname, 'templates/**/*.php'),
	],
	media: false,
	theme: {
		extend: {
			fontFamily: {
				wp: [
					'-apple-system',
					'BlinkMacSystemFont',
					'Segoe UI',
					'Roboto',
					'Oxygen-Sans',
					'Ubuntu',
					'Cantarell',
					'Helvetica Neue',
					'sans-serif',
				],
			},
		},
		variants: {
			extend: {
				opacity: [ 'disabled' ],
			},
		},
	},
	plugins: [],
};
