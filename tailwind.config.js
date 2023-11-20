module.exports = {
	prefix: 'eac-',
	content: [ './client/**/*.{js,jsx,ts,tsx}', './packages/**/*.{js,jsx,ts,tsx}', './src/**/*.php' ],
	corePlugins: {
		preflight: false,
	},
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
};
