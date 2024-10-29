/**
 * External dependencies
 */
const glob = require( 'glob' );
/**
 * Internal dependencies
 */
const { config, NAMESPACE } = require( './.bin/webpack.config' );
const { dependencies } = require( './package.json' );
const path = require( 'path' );

module.exports = [
	{
		...config,
		entry: {
			...config.entry(),

			// 3rd party libraries.
			'js/chartjs': './node_modules/chart.js/dist/chart.js',
			'js/select2': './_assets/js/vendor/select2.js',
			'js/inputmask': './_assets/js/vendor/inputmask.js',
			'js/tiptip': './_assets/js/vendor/tiptip.js',
			'js/printthis': './_assets/js/vendor/printthis.js',
			'css/jquery-ui': './_assets/css/vendor/jquery-ui.scss',

			// Core plugins.
			'js/form': './_assets/js/admin/form.js',
			'js/modal': './_assets/js/admin/modal.js',
			'js/api': './_assets/js/admin/api.js',

			// Admin scripts.
			'js/admin': './_assets/js/admin/admin.js',
			'css/admin': './_assets/css/admin/admin.scss',

			// Frontend scripts.
			'css/frontend': './_assets/css/frontend/frontend.scss',

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
			path: path.resolve( __dirname, 'assets/client' ),
		},
	},
];
