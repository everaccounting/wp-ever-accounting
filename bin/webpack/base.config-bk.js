/**
 * WordPress dependencies
 */
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const defaults = require( '@wordpress/scripts/config/webpack.config' );
/**
 * External dependencies
 */
const MomentTimezoneDataPlugin = require( 'moment-timezone-data-webpack-plugin' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const WebpackRTLPlugin = require( '@automattic/webpack-rtl-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const path = require( 'path' );
const { resolve } = require( 'path' );

/**
 * Internal dependencies
 */
const PACKAGE_NAMESPACE = '@eac/';

module.exports = function ( { entry = {}, output = {}, modify = ( x ) => x } = {} ) {
	return modify( {
		...defaults,
		devtool: process.env.environment === 'development' ? 'cheap-module-eval-source-map' : false,
		entry,
		output: {
			...defaults.output,
			filename: '[name]/index.js',
			path: resolve( process.cwd(), 'dist' ),
			chunkFilename: `chunks/[id].js`,
			uniqueName: '__everAccounting_webpackJsonp',
			libraryTarget: 'window',
			...output,
		},
		optimization: {
			...defaults.optimization,
			splitChunks: {
				// Not to generate chunk names because it caused a stressful workflow when deploying the plugin to WP.org
				// Styles in lazy does not work with default wordpress scripts config.
				name: false,
			},
		},
		module: {
			rules: [ ...defaults.module.rules ].filter( Boolean ),
		},
		plugins: [
			...defaults.plugins.filter(
				( plugin ) =>
					! [ 'DependencyExtractionWebpackPlugin', 'MiniCssExtractPlugin' ].includes(
						plugin.constructor.name
					)
			),

			// Extracts CSS into separate files.
			new MiniCssExtractPlugin( {
				filename: '[name]/style.css',
				chunkFilename: 'chunks/[id].style.css',
			} ),
			// Generates RTL CSS files.
			new WebpackRTLPlugin( {
				filename: '[name]/style-rtl.css',
				minify: process.env.NODE_ENV === 'development' ? false : { safe: true },
			} ),
			// Reduces data for moment-timezone.
			new MomentTimezoneDataPlugin( {
				// This strips out timezone data before the year 2000 to make a smaller file.
				startYear: 2000,
			} ),

			// Extracts dependencies from the source code.
			new DependencyExtractionWebpackPlugin( {
				injectPolyfill: true,
				requestToExternal( request ) {
					if ( request.startsWith( PACKAGE_NAMESPACE ) ) {
						return [
							'eac',
							request
								.substring( PACKAGE_NAMESPACE.length )
								.replace( /-([a-z])/g, ( _, letter ) => letter.toUpperCase() ),
						];
					}
				},
				requestToHandle( request ) {
					if ( request.startsWith( PACKAGE_NAMESPACE ) ) {
						return `eac-${ request
							.substring( PACKAGE_NAMESPACE.length )
							.replace( /-([a-z])/g, ( _, letter ) => letter.toUpperCase() ) }`;
					}
				},
			} ),

			// Browser sync.
			process.env.NODE_ENV !== 'production' &&
				new BrowserSyncPlugin(
					{
						host: 'localhost',
						// port: 3000,
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
	} );
};
