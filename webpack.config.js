const path = require('path');
const webpack = require('webpack');
const {get} = require('lodash');
const TerserPlugin = require('terser-webpack-plugin');
const WordPressExternalDependenciesPlugin = require('@automattic/wordpress-external-dependencies-plugin');
// PostCSS plugins
const postcssPresetEnv = require('postcss-preset-env');
const postcssFocus = require('postcss-focus');
const postcssReporter = require('postcss-reporter');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const DuplicatePackageCheckerPlugin = require('duplicate-package-checker-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const CustomTemplatedPathPlugin = require('@wordpress/custom-templated-path-webpack-plugin');

const pkg = require('./package.json');
const NODE_ENV = process.env.NODE_ENV || 'development';

const externals = {
	'@wordpress/blocks': {this: ['wp', 'blocks']},
	'@wordpress/element': {this: ['wp', 'element']},
	'@wordpress/hooks': {this: ['wp', 'hooks']},
	'@wordpress/url': {this: ['wp', 'url']},
	'@wordpress/html-entities': {this: ['wp', 'htmlEntities']},
	'@wordpress/i18n': {this: ['wp', 'i18n']},
	'@wordpress/keycodes': {this: ['wp', 'keycodes']},
	tinymce: 'tinymce',
	moment: 'moment',
	react: 'React',
	lodash: 'lodash',
	'react-dom': 'ReactDOM',
};

const eAccountingPackages = [
	'components',
	// 'csv-export',
	// 'currency',
	// 'date',
	'navigation',
	// 'number',
];

const entryPoints = {};
eAccountingPackages.forEach(name => {
	externals[`@eaccounting/${name}`] = {
		this: ['eaccounting', name.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase())],
	};
	entryPoints[name] = `./packages/${name}`;
});

const webpackConfig = {
	mode: NODE_ENV,
	entry: {
		app: './src',
		...entryPoints,
	},
	output: {
		filename: './dist/[name]/index.js',
		path: __dirname,
		library: ['eaccounting', '[modulename]'],
		libraryTarget: 'this',
	},
	externals,
	optimization: {
		minimize: 'production' === process.env.NODE_ENV,
		minimizer: [
			new TerserPlugin({
				cache: true,
				parallel: true,
				terserOptions: {
					ecma: 5,
					mangle: {
						reserved: 'production' === process.env.NODE_ENV ? [] : ['translate'],
						safari10: true,
					},
				},
			}),
		],
	},
	module: {
		rules: [
			{
				parser: {
					amd: false,
				},
			},
			{
				test: /\.(js|jsx)$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
			{
				test: /\.s?css$/,
				// exclude: /node_modules/,
				use: [MiniCssExtractPlugin.loader, 'css-loader',
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
						query: {
							includePaths: ['src/stylesheets/abstracts'],
							data:
								'@import "_colors"; ' +
								'@import "_variables"; ' +
								'@import "_breakpoints"; ' +
								'@import "_mixins"; ',
						},
					}]
			},
			{
				test: /\.(?:gif|jpg|jpeg|png|svg)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							emitFile: true, // On the server side, don't actually copy files
							name: '[name].[ext]',
							outputPath: '/dist/images/app',
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
							outputPath: 'fonts/',
						},
					},
				],
			},
		],
	},
	resolve: {
		extensions: ['.js', '.jsx', '.json', '.scss', '.css'],
		modules: [path.resolve(__dirname, 'src'), 'node_modules'],
	},
	plugins: [
		new FixStyleOnlyEntriesPlugin(),
		new webpack.BannerPlugin('WP Ever Accounting v' + pkg.version),
		new webpack.DefinePlugin({
			'process.env': {NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development')},
			EACCOUNTING_VERSION: "'" + pkg.version + "'",
		}),
		new CustomTemplatedPathPlugin({
			modulename(outputPath, data) {
				const entryName = get(data, ['chunk', 'name']);
				if (entryName) {
					return entryName.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
				}
				return outputPath;
			},
		}),
		new WordPressExternalDependenciesPlugin(),
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
				path: path.join(__dirname, 'dist'),
			},
		}),
		new DuplicatePackageCheckerPlugin(),
		new MiniCssExtractPlugin({
			filename: './dist/[name]/style.css',
		}),
		new CopyWebpackPlugin(
			eAccountingPackages.map(packageName => ({
				from: `./packages/${packageName}/build-style/*.css`,
				to: `./dist/${packageName}/`,
				flatten: true,
				transform: content => content,
			}))
		),
	],
	watchOptions: {
		ignored: [/node_modules/],
	},
	performance: {
		hints: false,
	},
	watch: true,
};

if (webpackConfig.mode !== 'production') {
	webpackConfig.devtool = process.env.SOURCEMAP || 'source-map';
}

module.exports = webpackConfig;
