/**
 * External dependencies
 */
const glob = require( 'glob' );
/**
 * Internal dependencies
 */
const { config, NAMESPACE } = require( '@eac/scripts/config/webpack.config' );
const { dependencies } = require( './package.json' );
const path = require( 'path' );

module.exports = [
	{
		...config,
		entry: {
			...config.entry(),

			// 3rd party libraries.
			'js/chartjs': './node_modules/chart.js/dist/Chart.js',
			'js/select2': './assets/js/vendor/select2.js',
			'js/inputmask': './assets/js/vendor/inputmask.js',
			'js/tiptip': './assets/js/vendor/tiptip.js',
			'js/printthis': './assets/js/vendor/printthis.js',
			'css/jquery-ui': './assets/css/vendor/jquery-ui.scss',

			// Core plugins.
			'js/form': './assets/js/admin/form.js',
			'js/modal': './assets/js/admin/modal.js',
			'js/api': './assets/js/admin/api.js',

			// Admin scripts.
			'js/admin': './assets/js/admin/admin.js',
			'css/admin': './assets/css/admin/admin.scss',
			'css/setup': './assets/css/admin/setup.scss',
			'js/setup': './assets/js/admin/setup.js',

			// Frontend scripts.
			'css/frontend': './assets/css/frontend/frontend.scss',

			// Client scripts.
			// ...glob.sync( './client/*/*/index.js' ).reduce( ( memo, file ) => {
			// 	const [ type, name ] = new RegExp( 'client/(.*)/(.*)/index.js' )
			// 		.exec( file )
			// 		.slice( 1 );
			// 	return {
			// 		...memo,
			// 		[ `client/${ type }-${ name }` ]: path.resolve( __dirname, file ),
			// 	};
			// }, {} ),
		},
	},
	//Package scripts.
	{
		...config,
		entry: Object.keys( dependencies )
			.filter( ( dependency ) => dependency.startsWith( NAMESPACE ) )
			.map( ( packageName ) => packageName.replace( NAMESPACE, '' ) )
			.reduce( ( memo, packageName ) => {
				const name = packageName.replace( /-([a-z])/g, ( _, letter ) =>
					letter.toUpperCase()
				);
				return {
					...memo,
					[ packageName ]: {
						import: `./packages/${ packageName }/src/index.js`,
						library: {
							name: [ 'eac', name ],
							type: 'window',
						},
					},
				};
			}, {} ),
		output: {
			...config.output,
			path: path.resolve( __dirname, 'build/client' ),
		},
	},
];
