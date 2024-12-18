/**
 * External dependencies
 */
const { join } = require( 'path' );

module.exports = {
	prefix: 'tw-',
	content: [
		join( __dirname, 'includes/Admin/**/*.php' ),
		join( __dirname, 'includes/Admin/views/*.php' ),
		join( __dirname, 'client/**/*.js' ),
		join( __dirname, 'packages/*/*.js' ),
	],
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
