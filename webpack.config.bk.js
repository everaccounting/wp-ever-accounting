/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const CustomTemplatedPathPlugin = require( '@wordpress/custom-templated-path-webpack-plugin' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const ImageminPlugin = require( 'imagemin-webpack-plugin' ).default;
/**
 * External dependencies
 */
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );
const path = require( 'path' );
const { get } = require( 'lodash' );
const webpack = require( 'webpack' );
const { merge } = require( 'webpack-merge' );
const WebpackBar = require( 'webpackbar' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const UglifyJS = require( 'uglify-es' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
const WebpackRTLPlugin = require( 'webpack-rtl-plugin' );

/**
 * Internal dependencies
 */
const pkg = require( './package.json' );

/**
 * Settings
 */
const entries = {
	'admin': './assets/js/eaccounting/ea-admin.js',
	'createable': './assets/js/eaccounting/ea-creatable.js',
	'exporter': './assets/js/eaccounting/ea-exporter.js',
	'form': './assets/js/eaccounting/ea-form.js',
	'helper': './assets/js/eaccounting/ea-helper.js',
	'importer': './assets/js/eaccounting/ea-importer.js',
	'modal': './assets/js/eaccounting/ea-modal.js',
	'overview': './assets/js/eaccounting/ea-overview.js',
	'settings': './assets/js/eaccounting/ea-settings.js',
	'setup': './assets/js/eaccounting/ea-setup.js',
	// 'admin-style': './assets/css/admin.scss',
	// 'public-style': './assets/css/public.scss',
	// 'release-style': './assets/css/release.scss',
	// 'setup-style': './assets/css/setup.scss',
};

const packages = [];
const host = 'http://accounting.test';
const isProduction = process.env.NODE_ENV === 'production';

/**
 * Config
 */
const externals = [];
packages.forEach( ( name ) => {
	externals[ `@eaccounting/${ name }` ] = {
		this: [
			'eaccounting',
			name.replace( /-([a-z])/g, ( match, letter ) =>
				letter.toUpperCase()
			),
		],
	};
	entries[ name ] = `./packages/${ name }`;
} );

// eslint-disable-next-line no-unused-vars
const minifyJs = ( content ) => {
	return Promise.resolve(
		Buffer.from( UglifyJS.minify( content.toString() ).code )
	);
};

const config = merge(
	{
		...defaultConfig,
		entry: entries,
		plugins: defaultConfig.plugins.filter(
			( plugin ) => plugin.constructor.name !== 'LiveReloadPlugin'
		),
	},
	{
		output: {
			path: path.resolve( '.', './dist' ),
			library: [ 'eaccounting', '[modulename]' ],
			libraryTarget: 'this',
		},
		resolve: {
			extensions: [ '.js', '.jsx', '.json', '.scss', '.css' ],
			alias: { '@eaccounting': path.resolve( __dirname, 'packages' ) },
			modules: [ path.resolve( __dirname, 'packages' ), 'node_modules' ],
		},
		externals,
		module: {
			rules: [
				isProduction && {
					test: /\.js$/,
					loader: 'webpack-remove-debug',
					exclude: /node_modules/,
				},
			].filter( Boolean ),
		},
		plugins: [
			new ESLintPlugin( {
				fix: true,
			} ),

			new WebpackRTLPlugin( {
				filename: [ /(\.css)/i, '-rtl$1' ],
			} ),

			// Copy vendor files to ensure 3rd party plugins relying on a script
			// handle to exist continue to be enqueued.
			new CopyWebpackPlugin( {
				patterns: [
					// Styles.
					// {
					// 	from: 'assets/css/',
					// 	to: 'css',
					// },

					// Scripts.
					{
						from: './node_modules/select2/dist/js/select2.js',
						to: 'select2.min.js',
						transform: ( content ) => minifyJs( content ),
					}
				]
			} ),

			new FixStyleOnlyEntriesPlugin(),
			// Compress images
			// Must happen after CopyWebpackPlugin
			new ImageminPlugin( {
				disable: ! isProduction,
				test: /\.(jpe?g|png|gif|svg)$/i,
			} ),

			//Set plugin information run build
			new webpack.BannerPlugin( pkg.name + ' v' + pkg.version ),
			new webpack.DefinePlugin( {
				'process.env': {
					NODE_ENV: JSON.stringify(
						process.env.NODE_ENV || 'development'
					),
				},
			} ),

			// Process custom modules.
			new CustomTemplatedPathPlugin( {
				modulename( outputPath, data ) {
					const entryName = get( data, [ 'chunk', 'name' ] );
					if ( entryName ) {
						return entryName.replace(
							/-([a-z])/g,
							( match, letter ) => letter.toUpperCase()
						);
					}
					return outputPath;
				},
			} ),

			// Adjust for custom modules.
			new DependencyExtractionWebpackPlugin( {
				injectPolyfill: true,
				requestToExternal: ( request ) => {
					if ( externals[ request ] ) {
						return externals[ request ].this;
					}
				},
				requestToHandle: ( request ) => {
					if ( externals[ request ] ) {
						return request.replace( '@eaccounting/', 'ea-' );
					}
				},
			} ),

			isProduction &&
				new BrowserSyncPlugin(
					{
						host: 'localhost',
						port: 3000,
						proxy: host,
						open: false,
						files: [
							'**/*.php',
							'dist/js/**/*.js',
							'dist/css/**/*.css',
							'dist/svg/**/*.svg',
							'dist/images/**/*.{jpg,jpeg,png,gif}',
							'dist/fonts/**/*.{eot,ttf,woff,woff2,svg}',
						],
					},
					{
						injectCss: true,
						reload: false,
					}
				),

			// Fancy WebpackBar.
			new WebpackBar(),
		].filter( Boolean ),
		// Performance settings.
		performance: {
			maxAssetSize: 100000,
		},
		stats: {
			all: false,
			assets: true,
			builtAt: true,
			colors: true,
			errors: true,
			hash: true,
			timings: true,
		},
		watchOptions: {
			ignored: [ /node_modules/ ],
		},
	}
);

module.exports = config;
