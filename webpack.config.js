/**
 * External dependencies
 */
const path = require( 'path' );
const WebpackBar = require( 'webpackbar' );
// eslint-disable-next-line import/no-extraneous-dependencies
const TerserPlugin = require( 'terser-webpack-plugin' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
// eslint-disable-next-line import/no-extraneous-dependencies
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const ImageminPlugin = require( 'imagemin-webpack-plugin' ).default;
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );
const StyleLintPlugin = require( 'stylelint-webpack-plugin' );

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
/**
 * Internal dependencies
 */
const pkg = require( './package.json' );

const isProduction = process.env.NODE_ENV === 'production';

const config = {
	...defaultConfig,
	entry: {
		'ea-admin': './assets/js/admin/ea-admin.js',
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( process.cwd(), 'dist' ),
	},
	// Build rules to handle asset files.
	optimization: {
		splitChunks: {
			cacheGroups: {
				default: false,
			},
		},
		minimizer: [
			isProduction &&
				new TerserPlugin( {
					cache: true,
					parallel: true,
					sourceMap: false,
					terserOptions: {
						parse: {
							// We want terser to parse ecma 8 code. However, we don't want it
							// to apply any minfication steps that turns valid ecma 5 code
							// into invalid ecma 5 code. This is why the 'compress' and 'output'
							// sections only apply transformations that are ecma 5 safe
							// https://github.com/facebook/create-react-app/pull/4234
							ecma: 8,
						},
						compress: {
							ecma: 5,
							warnings: false,
							// Disabled because of an issue with Uglify breaking seemingly valid code:
							// https://github.com/facebook/create-react-app/issues/2376
							// Pending further investigation:
							// https://github.com/mishoo/UglifyJS2/issues/2011
							comparisons: false,
							// Disabled because of an issue with Terser breaking valid code:
							// https://github.com/facebook/create-react-app/issues/5250
							// Pending futher investigation:
							// https://github.com/terser-js/terser/issues/120
							inline: 2,
						},
						output: {
							ecma: 5,
							comments: false,
						},
						ie8: false,
					},
				} ),
		].filter( Boolean ),
	},
	module: {
		rules: [
			// Lint JS.
			{
				test: /\.js$/,
				enforce: 'pre',
				loader: 'eslint-loader',
				options: {
					fix: true,
				},
			},
			...defaultConfig.module.rules,
		].filter( Boolean ),
	},
	plugins: [
		...defaultConfig.plugins
			.filter(
				( plugin ) => plugin.constructor.name !== 'LiveReloadPlugin'
			)
			.filter(
				( plugin ) =>
					plugin.constructor.name !== 'FixStyleOnlyEntriesPlugin'
			),
		// MiniCSSExtractPlugin to extract the CSS thats gets imported into JavaScript.
		new MiniCssExtractPlugin( {
			esModule: false,
			filename: '[name].css',
			chunkFilename: '[id].css',
		} ),

		// MiniCSSExtractPlugin creates JavaScript assets for CSS that are
		// obsolete and should be removed. Related webpack issue:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/85
		new FixStyleOnlyEntriesPlugin( {
			silent: true,
		} ),

		// Compress images
		// Must happen after CopyWebpackPlugin
		new ImageminPlugin( {
			disable: ! isProduction,
			test: /\.(jpe?g|png|gif|svg)$/i,
		} ),

		! isProduction &&
			new BrowserSyncPlugin(
				{
					host: 'localhost',
					port: 3000,
					proxy: pkg.localhost,
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

		// Lint CSS.
		// ! isProduction &&
		// 	new StyleLintPlugin( {
		// 		context: path.resolve( process.cwd(), './assets/css/' ),
		// 		fix: true,
		// 		files: '**/**/*.scss',
		// 		quiet: true,
		// 	} ),

		// Fancy WebpackBar.
		new WebpackBar(),
	].filter( Boolean ),
	stats: {
		// Copied from `'minimal'`.
		all: false,
		errors: true,
		maxModules: 0,
		modules: true,
		warnings: true,
		// Our additional options.
		assets: true,
		errorDetails: true,
		excludeAssets: /\.(jpe?g|png|gif|svg|woff|woff2)$/i,
		moduleTrace: true,
		performance: true,
	},
	// Performance settings.
	performance: {
		maxAssetSize: 100000,
	},
};

module.exports = config;
