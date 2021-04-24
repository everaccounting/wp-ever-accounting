/**
 * External dependencies
 */
const path = require('path');
const postcss = require('postcss');
const WebpackBar = require('webpackbar');
const sass = require('node-sass');
const UglifyJS = require('uglify-es');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const WebpackRTLPlugin = require('webpack-rtl-plugin');
const MiniCSSExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

/**
 * Internal dependencies
 */
const { localhost, files } = require('./package.json');
const DependencyExtractionWebpackPlugin = require('./packages/dependency-extraction-webpack-plugin/src/index');

const isProduction = process.env.NODE_ENV === 'production';
const NODE_ENV = process.env.NODE_ENV || 'development';

const styleTransform = (content, filePath) => {
	const compiled = sass.renderSync({
		file: filePath,
	});

	return postcss([require('cssnano')(require('./postcss.config.js'))])
		.process(compiled.css.toString())
		.then((result) => result.css);
};

const scriptTransform = (content) => {
	return Promise.resolve(
		Buffer.from(UglifyJS.minify(content.toString()).code)
	);
};

const config = {
	mode: NODE_ENV,
	entry: {
		app: './client/index.js',
	},
	output: {
		filename: '[name]/index.js',
		path: path.join(__dirname, 'dist'),
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
	plugins: [
		// MiniCSSExtractPlugin to extract the CSS that's gets imported into JavaScript.
		new MiniCSSExtractPlugin({
			// filename: ({ chunk }) =>
			// 	packages.includes(chunk.name)
			// 		? `./[name]/style.css`
			// 		: '../assets/css/[name].css',
			filename: `./[name]/style.css`,
			chunkFilename: './chunks/[id].style.css',
		}),

		new CopyWebpackPlugin([
			{
				from: './assets/css/public.scss',
				to: 'public.css',
				transform: styleTransform,
			},
		]),

		new WebpackRTLPlugin({
			filename: [/(\.css)/i, '-rtl$1'],
		}),

		// Browser sync.
		!isProduction &&
			new BrowserSyncPlugin(
				{
					host: 'localhost',
					port: 3000,
					proxy: localhost,
					open: false,
					files,
				},
				{
					injectCss: true,
					reload: false,
				}
			),

		new DependencyExtractionWebpackPlugin(),
		// Fancy WebpackBar.
		new WebpackBar(),
	].filter(Boolean),
	watchOptions: {
		ignored: ['**/node_modules', '**/packages/*/src'],
		aggregateTimeout: 500,
	},
};

module.exports = config;
