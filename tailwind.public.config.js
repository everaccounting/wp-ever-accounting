/**
 * External dependencies
 */
const { join } = require( 'path' );

module.exports = {
	prefix: 'tw-',
	content: [ join( __dirname, 'templates/**/*.php' ) ],
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
