const path = require('path');
const chalk = require('chalk');
const webpack = require('webpack');
const pkg = require('./package.json');
const {get} = require('lodash');
const TerserPlugin = require('terser-webpack-plugin');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const ProgressBarPlugin = require('progress-bar-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const CustomTemplatedPathPlugin = require('@wordpress/custom-templated-path-webpack-plugin');
const DuplicatePackageCheckerPlugin = require('duplicate-package-checker-webpack-plugin');

const postcssPresetEnv = require('postcss-preset-env');
const postcssFocus = require('postcss-focus');
const postcssReporter = require('postcss-reporter');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const CopyWebpackPlugin = require('copy-webpack-plugin');

const NODE_ENV = process.env.NODE_ENV || 'development';
const externals = [];

const packages = [
	'components',
	'data',
	'hoc',
	'store',
];
const entryPoints = {};
packages.forEach(name => {
	externals[`@eaccounting/${name}`] = {
		this: ['eaccounting', name.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase())],
	};
	entryPoints[name] = `./packages/${name}`;
});

module.exports = {
	mode: NODE_ENV,
	devtool: NODE_ENV === 'development' ? 'inline-source-map' : false,
	entry: {
		client: './client',
		...entryPoints
	},
	output: {
		filename: './assets/dist/[name].js',
		path: __dirname,
		library: ['eaccounting', '[modulename]'],
		libraryTarget: 'this',
	},
	externals,
	resolve: {
		extensions: ['.js', '.jsx', '.json', '.scss', '.css'],
		modules: [path.resolve(__dirname, 'client'), 'node_modules'],
	},
	optimization: {
		minimize: 'production' === NODE_ENV,
		minimizer: [
			new TerserPlugin({
				cache: true,
				parallel: true,
				terserOptions: {
					ecma: 5,
					mangle: {
						reserved: 'production' === NODE_ENV ? [] : ['translate'],
						safari10: true,
					},
				},
			}),
		],
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
			{
				test: /\.s?css$/,
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					{
						// postcss loader so we can use autoprefixer and theme Gutenberg components
						loader: 'postcss-loader',
						options: {
							config: {
								path: 'postcss.config.js',
							},
						},
					},
					{
						loader: 'sass-loader',
						// query: {
						// 	includePaths: ['src/stylesheets/abstracts'],
						// 	data:
						// 		'@import "_colors"; ' + '@import "_variables"; ' + '@import "_breakpoints"; ' + '@import "_mixins"; ',
						// },
					},
				],
			},
			{
				test: /\.(?:gif|jpg|jpeg|png|svg)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							emitFile: true, // On the server side, don't actually copy files
							name: '[name].[ext]',
							outputPath: '/assets/dist',
						},
					},
				],
			},
			{
				test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: '/assets/dist/',
						},
					},
				],
			},
			{
				test: /\.svg$/,
				use: ['@svgr/webpack', 'url-loader'],
			},
		],
	},
	plugins: [
		new ProgressBarPlugin({
			format: chalk.blue('Build core script') +
				' [:bar] ' + chalk.green(':percent') +
				' :msg (:elapsed seconds)',
		}),
		new DependencyExtractionWebpackPlugin({injectPolyfill: true}),
		new webpack.BannerPlugin('WP Ever Accounting v' + pkg.version),
		new webpack.DefinePlugin({
			'process.env': {NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development')},
			EACCOUNTING_VERSION: "'" + pkg.version + "'",
		}),
		new FixStyleOnlyEntriesPlugin(),
		new CustomTemplatedPathPlugin({
			modulename(outputPath, data) {
				const entryName = get(data, ['chunk', 'name']);
				if (entryName) {
					return entryName.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
				}
				return outputPath;
			},
		}),
		new DuplicatePackageCheckerPlugin(),
		new webpack.LoaderOptionsPlugin({
			options: {
				postcss: [
					postcssFocus(),
					postcssPresetEnv({
						browsers: ['last 2 versions', 'IE > 10'],
					}),
					postcssReporter({
						clearMessages: true,
					}),
				],
			},
			output: {
				path: path.join(__dirname, 'assets/dist'),
			},
		}),
		new MiniCssExtractPlugin({
			filename: './assets/dist/[name].css',
		}),
		new CopyWebpackPlugin(
			packages.map(packageName => ({
				from: `./packages/${packageName}/build-style/*.css`,
				to: `./assets/dist/${packageName}.css`,
				flatten: true,
				transform: content => content,
			}))
		)
	],
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
		ignored: [/node_modules/],
	},
	performance: {
		hints: false,
	},
	watch: true
};
