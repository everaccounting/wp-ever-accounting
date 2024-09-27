/**
 * WordPress dependencies
 */
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const defaults = require( '@wordpress/scripts/config/webpack.config' );
/**
 * External dependencies
 */
const WebpackRemoveEmptyScript = require('webpack-remove-empty-scripts');
const MomentTimezoneDataPlugin = require( 'moment-timezone-data-webpack-plugin' );
const { resolve } = require( 'path' );
const path = require("path");

/**
 * Internal dependencies
 */
const PACKAGE_NAMESPACE = '@eac/';

module.exports = {
	...defaults,
	devtool: process.env.environment === 'development' ? 'cheap-module-eval-source-map' : false,
	output: {
		...defaults.output,
		filename: '[name].js',
		path: resolve( process.cwd(), './assets/' ),
		chunkFilename: 'chunks/[chunkhash].js',
		uniqueName: '__eac_webpackJsonp',
		libraryTarget: 'window',
		clean: true,
	},
	optimization: {
		...defaults.optimization,
		splitChunks: {
			// Not to generate chunk names because it caused a stressful workflow when deploying the plugin to WP.org
			// Styles in lazy does not work with default WordPress scripts config.
			name: false,
		},
	},
	module: {
		rules: [ ...defaults.module.rules ].filter( Boolean ),
	},
	plugins: [
		...defaults.plugins.filter( ( plugin ) => ! [ 'DependencyExtractionWebpackPlugin' ].includes( plugin.constructor.name ) ),

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

		new WebpackRemoveEmptyScript(
			{
				stage: WebpackRemoveEmptyScript.STAGE_AFTER_PROCESS_PLUGINS,
				remove: /\.(js)$/,
			}
		),
	],
};

