/**
 * External dependencies
 */
const path = require('path');
const glob = require('glob');
const UglifyJS = require('uglify-es');
const webpack = require('webpack');
const WebpackBar = require('webpackbar');
const MiniCSSExtractPlugin = require('mini-css-extract-plugin');
const WebpackRTLPlugin = require('webpack-rtl-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer')
	.BundleAnalyzerPlugin;
const { get } = require('lodash');
/**
 * WordPress dependencies
 */
const CustomTemplatedPathPlugin = require('@wordpress/custom-templated-path-webpack-plugin');

/**
 * Internal dependencies
 */
const pkg = require('./package.json');
const DependencyExtractionWebpackPlugin = require('./bin/dependency-extraction-webpack-plugin/src/index');

const isProduction = process.env.NODE_ENV === 'production';
const NODE_ENV = process.env.NODE_ENV || 'development';

const packages = [];
const minifyJs = (content) => {
	return Promise.resolve(
		Buffer.from(UglifyJS.minify(content.toString()).code)
	);
};

const webpackConfig = {
	mode: NODE_ENV,
	entry: {
		app: './client/index.js',
		admin: './assets/css/admin.scss',
		public: './assets/css/public.scss',
		release: './assets/css/release.scss',
		setup: './assets/css/setup.scss',
		'jquery-ui': './assets/css/jquery-ui.scss',
		...glob.sync('./packages/*/index.js').reduce((memo, filepath) => {
			const name = path.basename(path.dirname(filepath));
			memo[name] = filepath;
			packages.push(name);
			return memo;
		}, {}),
	},
	output: {
		filename: '[name]/index.js',
		chunkFilename: `chunks/[name].js`,
		path: path.join(__dirname, 'dist'),
		library: ['eaccounting', '[modulename]'],
		libraryTarget: 'this',
		jsonpFunction: '__eaccounting_webpackJsonp',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				enforce: 'pre',
				loader: 'eslint-loader',
				options: {
					fix: true,
				},
			},
			{
				test: /\.jsx?$/,
				exclude: /node_modules/,
				use: [
					require.resolve('thread-loader'),
					{
						loader: require.resolve('babel-loader'),
						options: {
							// Babel uses a directory within local node_modules
							// by default. Use the environment variable option
							// to enable more persistent caching.
							cacheDirectory:
								process.env.BABEL_CACHE_DIRECTORY || true,
						},
					},
				],
			},
			{
				test: /\.(png|jpe?g|gif|svg|eot|ttf|woff|woff2)$/,
				loader: 'url-loader',
			},
			{
				test: /\.s?css$/,
				use: [
					{
						loader: MiniCSSExtractPlugin.loader,
					},
					{
						loader: require.resolve('css-loader'),
						options: {
							sourceMap: !isProduction,
							url: false,
						},
					},
					{
						loader: require.resolve('postcss-loader'),
					},
					{
						loader: require.resolve('sass-loader'),
						options: {
							sourceMap: !isProduction,
						},
					},
				],
			},
		].filter(Boolean),
	},
	resolve: {
		extensions: ['.js', '.jsx', '.json', '.scss', '.css'],
		modules: [path.resolve(__dirname, 'packages'), 'node_modules'],
		alias: {
			'@eaccounting': path.resolve(__dirname, 'packages'),
			'lodash-es': 'lodash',
		},
	},
	plugins: [
		// During rebuilds, all webpack assets that are not used anymore will be
		// removed automatically. There is an exception added in watch mode for
		// fonts and images. It is a known limitations:
		// https://github.com/johnagan/clean-webpack-plugin/issues/159
		new CleanWebpackPlugin({
			cleanAfterEveryBuildPatterns: ['!fonts/**', '!images/**'],
		}),

		// MiniCSSExtractPlugin creates JavaScript assets for CSS that are
		// obsolete and should be removed. Related webpack issue:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/85
		new FixStyleOnlyEntriesPlugin({
			silent: true,
		}),
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

		// MiniCSSExtractPlugin to extract the CSS thats gets imported into JavaScript.
		new MiniCSSExtractPlugin({
			filename: ({ chunk }) =>
				packages.includes(chunk.name)
					? `./[name]/style.css`
					: '../assets/css/[name].css',
			chunkFilename: './chunks/[id].style.css',
		}),

		new WebpackRTLPlugin({
			filename: [/(\.css)/i, '-rtl$1'],
		}),

		new CopyWebpackPlugin(
			glob.sync('./assets/js/admin/!(*.min.js|*.map)').map((file) => ({
				from: file,
				to: path.resolve(
					__dirname,
					'./assets/js/admin/[name].min.[ext]'
				),
				transform: (content) => minifyJs(content),
			}))
		),
		process.env.ANALYZE && new BundleAnalyzerPlugin(),

		// WP_NO_EXTERNALS global variable controls whether scripts' assets get
		// generated, and the default externals set.
		!process.env.WP_NO_EXTERNALS && new DependencyExtractionWebpackPlugin(),

		//Set plugin information run build
		new webpack.BannerPlugin(pkg.name + ' v' + pkg.version + '\n'),
		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development'),
			},
		}),
		// Browser sync.
		!isProduction &&
			new BrowserSyncPlugin(
				{
					host: 'localhost',
					port: 3000,
					proxy: pkg.localhost,
					open: false,
					files: [
						// '**/*.php',
						// 'dist/js/**/*.js',
						// 'dist/css/**/*.css',
						// 'dist/svg/**/*.svg',
						// 'dist/images/**/*.{jpg,jpeg,png,gif}',
						// 'dist/fonts/**/*.{eot,ttf,woff,woff2,svg}',
					],
				},
				{
					injectCss: true,
					reload: false,
				}
			),
		// Fancy WebpackBar.
		new WebpackBar(),
	].filter(Boolean),
	optimization: {
		minimize: NODE_ENV !== 'development',
		// Only concatenate modules in production, when not analyzing bundles.
		concatenateModules: isProduction && !process.env.WP_BUNDLE_ANALYZER,
		splitChunks: {
			cacheGroups: {
				default: false,
			},
		},
		minimizer: [
			new TerserPlugin({
				cache: true,
				parallel: true,
				sourceMap: !isProduction,
				terserOptions: {
					ecma: 5,
					output: {
						comments: /translators:/i,
					},
					compress: {
						passes: 2,
					},
					mangle: {
						reserved: isProduction ? [] : ['__', '_n', '_nx', '_x'],
						safari10: true,
					},
				},
				extractComments: false,
			}),
		],
	},
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
		maxAssetSize: 500000,
	},
};
if (!isProduction) {
	// WP_DEVTOOL global variable controls how source maps are generated.
	// See: https://webpack.js.org/configuration/devtool/#devtool.
	webpackConfig.devtool = process.env.WP_DEVTOOL || 'source-map';
	webpackConfig.module.rules.unshift({
		test: /\.js$/,
		exclude: [/node_modules/],
		use: require.resolve('source-map-loader'),
		enforce: 'pre',
	});
}
module.exports = webpackConfig;
