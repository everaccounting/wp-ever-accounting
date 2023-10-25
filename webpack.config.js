/**
 * External dependencies
 */
const glob = require( 'glob' );
const path = require( 'path' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );

/**
 * Internal dependencies
 */
const baseConfig = require( './bin/webpack/base.config' );
const { dependencies } = require( './package.json' );
const PACKAGE_NAMESPACE = '@eac/';

module.exports = [
	// App config.
	{
		...baseConfig,
		entry: glob.sync( './client/*/index.js' ).reduce( ( memo, filepath ) => {
			const name = filepath.replace( 'client/', '' ).replace( '/index.js', '' );
			return {
				...memo,
				[ name ]: path.resolve( __dirname, filepath ),
			};
		}, {} ),
		output: {
			...baseConfig.output,
			path: path.resolve( __dirname, 'assets/client' ),
		},
		plugins: [
			...baseConfig.plugins,
			// Browser sync.
			process.env.NODE_ENV !== 'production' &&
				new BrowserSyncPlugin(
					{
						host: 'localhost',
						port: 3000,
						proxy: 'http://accounting.test',
						open: false,
						files: [
							'**/**/*.php',
							'dist/**/*.js',
							'dist/**/*.css',
							'dist/**/*.svg',
							'dist/**/*.{jpg,jpeg,png,gif}',
							'dist/**/*.{eot,ttf,woff,woff2,svg}',
						],
					},
					{
						injectCss: true,
						reload: false,
					}
				),
		],
	},
	// Packages config.
	{
		...baseConfig,
		entry: Object.keys( dependencies )
			.filter( ( dependency ) => dependency.startsWith( PACKAGE_NAMESPACE ) )
			.map( ( packageName ) => packageName.replace( PACKAGE_NAMESPACE, '' ) )
			.reduce( ( memo, packageName ) => {
				return {
					...memo,
					[ packageName ]: {
						import: `./packages/${ packageName }/src/index.js`,
						library: {
							name: [ 'eac', packageName.replace( /-([a-z])/g, ( _, letter ) => letter.toUpperCase() ) ],
							type: 'window',
						},
					},
				};
			}, {} ),
		output: {
			...baseConfig.output,
			path: path.resolve( __dirname, 'assets/packages' ),
		},
	},
];
