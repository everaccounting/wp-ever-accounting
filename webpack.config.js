/**
 * External dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const MiniCSSExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const WebpackRTLPlugin = require('webpack-rtl-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const glob = require('glob');
const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	...defaultConfig,
	devtool: isProduction ? false : 'inline-source-map',
	entry: {
		app: './client/app/index.js',
		...glob
			.sync('./client/packages/*/index.js')
			.reduce((memo, filepath) => {
				const name = filepath
					.replace('./client/packages/', '')
					.replace('/index.js', '');
				return {
					...memo,
					[`${name}`]: {
						import: filepath,
						library: {
							name: [
								'eac',
								name.replace(/-([a-z])/g, (match, letter) =>
									letter.toUpperCase()
								),
							],
							type: 'window',
						},
					},
				};
			}, {}),
	},
	output: {
		clean: true,
		path: path.resolve(__dirname, 'assets/client'),
		// libraryTarget: 'window',
		chunkFilename: `chunks/[chunkhash].js`,
		uniqueName: '__everAccounting_webpackJsonp',
		libraryTarget: 'umd',
	},
	resolve: {
		...defaultConfig.resolve,
		alias: {
			'@eac/components': path.resolve(
				__dirname,
				'client/packages/components'
			),
			'@eac/navigation': path.resolve(
				__dirname,
				'client/packages/navigation'
			),
			'@eac/store': path.resolve(__dirname, 'client/packages/store'),
			'~': path.resolve(__dirname, 'client/app'),
			...defaultConfig.resolve.alias,
		},
		modules: [path.resolve(__dirname, 'client/packages'), 'node_modules'],
	},
	externals: {
		lodash: 'lodash',
		jquery: 'jQuery',
		$: 'jQuery',
		classNames: 'classNames',
	},
	performance: {
		maxAssetSize: (isProduction ? 100 : 10000) * 1024,
		maxEntrypointSize: (isProduction ? 400 : 40000) * 1024,
		hints: 'warning',
	},
	stats: {
		// Copied from `'minimal'`.
		all: false,
		errors: true,
		modules: true,
		warnings: true,
		children: false,
		// Our additional options.
		assets: true,
		errorDetails: true,
		excludeAssets: /\.(jpe?g|png|gif|svg|woff|woff2)$/i,
		moduleTrace: true,
		performance: true,
	},
	optimization: {
		...defaultConfig.optimization,
		// enable the splitChunks optimization when file size is more than 50kb
		splitChunks: {
			cacheGroups: {
				style: {
					type: 'css/mini-extract',
					test: /[\\/](\.module)?\.(sc|sa|c)ss$/,
					chunks: 'all',
					enforce: true,
					name(module, chunks, cacheGroupKey) {
						return `${cacheGroupKey}-${chunks[0].name}`;
					},
				},
				default: false,
			},
			maxAsyncRequests: Infinity,
		},
	},
	module: {
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.svg$/,
				issuer: /\.(j|t)sx?$/,
				use: ['@svgr/webpack', 'url-loader'],
				type: 'javascript/auto',
			},
			{
				test: /\.svg$/,
				issuer: /\.(sc|sa|c)ss$/,
				type: 'asset/inline',
			},
			{
				test: /\.(bmp|png|jpe?g|gif)$/i,
				type: 'asset/resource',
				generator: {
					filename: 'images/[name].[hash:8][ext]',
				},
			},
			{
				test: /\.(woff|woff2|eot|ttf|otf)$/i,
				type: 'asset/resource',
				generator: {
					filename: 'fonts/[name].[hash:8][ext]',
				},
			},
		].filter(Boolean),
	},
	plugins: [
		...defaultConfig.plugins.filter(
			(plugin) =>
				![
					'MiniCssExtractPlugin',
					'DependencyExtractionWebpackPlugin',
					'LiveReloadPlugin',
				].includes(plugin.constructor.name)
		),
		// During rebuilds, all webpack assets that are not used anymore will be
		// removed automatically. There is an exception added in watch mode for
		// fonts and images. It is a known limitations:
		// https://github.com/johnagan/clean-webpack-plugin/issues/159
		new CleanWebpackPlugin({
			cleanAfterEveryBuildPatterns: ['!fonts/**', '!images/**'],
			// Prevent it from deleting webpack assets during builds that have
			// multiple configurations returned to the webpack config.
			cleanStaleWebpackAssets: false,
		}),
		// MiniCSSExtractPlugin to extract the CSS that's gets imported into JavaScript.
		new MiniCSSExtractPlugin({
			filename: '[name].css',
			chunkFilename: '[id].css',
		}),
		new WebpackRTLPlugin({
			filename: [/(\.css)/i, '-rtl$1'],
		}),

		// Copy static assets to the `dist` folder.
		// new CopyWebpackPlugin({
		// 	patterns: [
		// 		{
		// 			from: '**/*.{jpg,jpeg,png,gif,svg}',
		// 			to: '[name][ext]',
		// 			noErrorOnMissing: true,
		// 			context: path.resolve(process.cwd(), './assets/images'),
		// 		},
		// 	],
		// }),

		// WP_NO_EXTERNALS global variable controls whether scripts' assets get
		// generated, and the default externals set.
		new RemoveEmptyScriptsPlugin(),

		new DependencyExtractionWebpackPlugin({
			injectPolyfill: true,
			// requestToExternal(request) {
			// 	if (request.startsWith('@eac/')) {
			// 		const handle = request.replace('@eac/', '');
			// 		return [
			// 			'eac',
			// 			handle.replace(/-([a-z])/g, (_, letter) =>
			// 				letter.toUpperCase()
			// 			),
			// 		];
			// 	}
			// },
			// requestToHandle(request) {
			// 	if (request.startsWith('@eac/')) {
			// 		const handle = request.replace('@eac/', '');
			// 		return 'eac-' + handle;
			// 	}
			// },
		}),
	].filter(Boolean),
};
