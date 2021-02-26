/**
 * WordPress dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CustomTemplatedPathPlugin = require('@wordpress/custom-templated-path-webpack-plugin');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
/**
 * External dependencies
 */
const ESLintPlugin = require('eslint-webpack-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const path = require('path');
// eslint-disable-next-line import/no-extraneous-dependencies
const { get } = require('lodash');
// eslint-disable-next-line import/no-extraneous-dependencies
const webpack = require('webpack');
const WebpackBar = require('webpackbar');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const UglifyJS = require('uglify-es');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const WebpackRTLPlugin = require('webpack-rtl-plugin');
const MiniCSSExtractPlugin = require('mini-css-extract-plugin');

/**
 * Internal dependencies
 */
const pkg = require('./package.json');
/**
 * Settings
 */
const entries = {
	'ea-admin': './assets/js/ea-admin.js',
	// 'ea-select': './assets/js/ea-select.js',
	// 'ea-creatable': './assets/js/ea-creatable.js',
	// 'ea-exporter': './assets/js/ea-exporter.js',
	// 'ea-form': './assets/js/ea-form.js',
	// 'ea-helper': './assets/js/ea-helper.js',
	// 'ea-importer': './assets/js/ea-importer.js',
	// 'ea-modal': './assets/js/ea-modal.js',
	// 'ea-overview': './assets/js/ea-overview.js',
	// 'ea-settings': './assets/js/ea-settings.js',
	// 'ea-setup': './assets/js/ea-setup.js',
};

/**
 * Config
 */

const packages = ['utils', 'data'];
const host = 'http://accounting.test';
const isProduction = process.env.NODE_ENV === 'production';
const externals = [];
packages.forEach((name) => {
	externals[`@eaccounting/${name}`] = {
		this: [
			'eaccounting',
			name.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase()),
		],
	};
	entries[name] = `./packages/${name}`;
});

// eslint-disable-next-line no-unused-vars
const minifyJs = (content) => {
	return Promise.resolve(
		Buffer.from(UglifyJS.minify(content.toString()).code)
	);
};

/**
 * Config
 */
const config = {
	...defaultConfig,
	entry: entries,
	output: {
		...defaultConfig.output,
		filename: '[name].js',
		path: path.resolve(process.cwd(), 'dist'),
		library: ['eaccounting', '[modulename]'],
	},
	resolve: {
		...defaultConfig.resolve,
		extensions: ['.js', '.jsx', '.json', '.scss', '.css'],
		alias: { '@eaccounting': path.resolve(__dirname, 'packages') },
		modules: [path.resolve(__dirname, 'packages'), 'node_modules'],
	},
	externals: {
		...defaultConfig.externals,
		externals,
	},
	module: {
		rules: [...defaultConfig.module.rules].filter(Boolean),
	},
	plugins: [
		...defaultConfig.plugins
			.filter(
				(plugin) =>
					plugin.constructor.name !==
					'DependencyExtractionWebpackPlugin'
			)
			.filter((plugin) => plugin.constructor.name !== 'LiveReloadPlugin'),

		// new ESLintPlugin({
		// 	failOnError: false,
		// 	fix: false,
		// }),

		// Copy vendor files to ensure 3rd party plugins relying on a script
		// handle to exist continue to be enqueued.
		new CopyWebpackPlugin({
			patterns: [
				// Styles.
				// {
				// 	from: 'assets/css/',
				// 	to: 'css',
				// },

				// Scripts.
				{
					from: './node_modules/select2/dist/js/select2.js',
					to: 'jquery-select2.js',
					transform: (content) => minifyJs(content),
				},
				{
					from: './node_modules/chart.js/dist/chart.bundle.min.js',
					to: 'chartjs.js',
					transform: (content) => minifyJs(content),
				},
				{
					from:
						'./node_modules/chartjs-plugin-labels/build/chartjs-plugin-labels.min.js',
					to: 'chartjs-labels.js',
					transform: (content) => minifyJs(content),
				},
				{
					from: './node_modules/print-this/printThis.js',
					to: 'print-this.js',
					transform: (content) => minifyJs(content),
				},
				{
					from: './node_modules/inputmask/dist/inputmask.min.js',
					to: 'jquery-inputmask.js',
					transform: (content) => minifyJs(content),
				},
				{
					from: './node_modules/blockui/jquery.blockui.min.js',
					to: 'jquery-blockui.js',
					transform: (content) => minifyJs(content),
				},
			],
		}),

		// Remove the extra JS files Webpack creates for CSS entries.
		// This should be fixed in Webpack 5.
		new FixStyleOnlyEntriesPlugin(),

		// MiniCSSExtractPlugin to extract the CSS thats gets imported into JavaScript.
		new MiniCSSExtractPlugin({
			filename: 'css/[name].css',
			moduleFilename: ({ name }) =>
				name.match(/-block$/)
					? 'blocks/[name]/editor.css'
					: '[name].css',
			chunkFilename: '[id].css',
			rtlEnabled: true,
		}),

		// RTL style support.
		new WebpackRTLPlugin({
			filename: [/(\.css)/i, '-rtl$1'],
		}),

		// Compress images
		// Must happen after CopyWebpackPlugin
		new ImageminPlugin({
			disable: !isProduction,
			test: /\.(jpe?g|png|gif|svg)$/i,
		}),

		//Set plugin information run build
		new webpack.BannerPlugin(pkg.name + ' v' + pkg.version),
		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development'),
			},
		}),

		// Process custom modules.
		new CustomTemplatedPathPlugin({
			modulename(outputPath, data) {
				const entryName = get(data, ['chunk', 'name']);
				if (entryName) {
					return entryName.replace(/-([a-z])/g, (match, letter) =>
						letter.toUpperCase()
					);
				}
				return outputPath;
			},
		}),

		// Adjust for custom modules.
		new DependencyExtractionWebpackPlugin({
			injectPolyfill: true,
			requestToExternal: (request) => {
				if (externals[request]) {
					return externals[request].this;
				}
			},
			requestToHandle: (request) => {
				if (externals[request]) {
					return request.replace('@eaccounting/', 'ea-');
				}
			},
		}),

		!isProduction &&
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

		// Lint CSS.
		// new StyleLintPlugin({
		// 	context: path.resolve(process.cwd(), './assets/css'),
		// 	files: '**/*.scss',
		// 	allowEmptyInput: true,
		// }),

		// Fancy WebpackBar.
		new WebpackBar(),
	].filter(Boolean),
};
console.log(config)
module.exports = config;
