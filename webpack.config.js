const path                              = require( 'path' );
const chalk                             = require( 'chalk' );
const webpack                           = require( 'webpack' );
const pkg                               = require( './package.json' );
const { get }                           = require( 'lodash' );
const TerserPlugin                      = require( 'terser-webpack-plugin' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const ProgressBarPlugin                 = require( 'progress-bar-webpack-plugin' );
const FixStyleOnlyEntriesPlugin         = require( 'webpack-fix-style-only-entries' );
const CustomTemplatedPathPlugin         = require( '@wordpress/custom-templated-path-webpack-plugin' );
const DuplicatePackageCheckerPlugin     = require( 'duplicate-package-checker-webpack-plugin' );
const WebpackRTLPlugin 					= require( 'webpack-rtl-plugin' );
const MomentTimezoneDataPlugin 			= require( 'moment-timezone-data-webpack-plugin' );
const postcssPresetEnv     				= require( 'postcss-preset-env' );
const postcssFocus         				= require( 'postcss-focus' );
const postcssReporter      				= require( 'postcss-reporter' );
const MiniCssExtractPlugin 				= require( 'mini-css-extract-plugin' );
const NODE_ENV 							= process.env.NODE_ENV || 'development';
const suffix 							= NODE_ENV === 'production' ? '.min' : '';


const externals   = [];
const entryPoints = {};
const packages 	  = ['components', 'data'];
packages.forEach( (name) => {
    externals[`@eaccounting/${name}`] = {
    	this: [
			'eaccounting',
			name.replace( /-([a-z])/g, ( match, letter ) =>letter.toUpperCase() ),
		]
    };
    entryPoints[name] = `./client/${name}`;
});

const config = {
	mode: NODE_ENV,
	entry: {
		invoice: './client/invoice',
		...entryPoints,
	},
	output: {
		filename: `[name].js`,
		path: path.resolve( __dirname, 'assets/dist'),
		library: ['eaccounting', '[modulename]'],
		libraryTarget: 'this',
		jsonpFunction: '__eaccounting_webpackJsonp',
	},
	externals,
	resolve: {
		extensions: ['.js', '.jsx', '.json', '.scss', '.css'],
		alias:{'@eaccounting': path.resolve(__dirname, 'client')},
		modules: [path.resolve(__dirname, 'client'),  'node_modules'],
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
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					{
						loader: 'postcss-loader',
						options: {
							config: {
								path: 'postcss.config.js',
							},
						},
					},
					{
						loader: 'sass-loader',
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
			format:
				chalk.blue('Build core script') +
				' [:bar] ' +
				chalk.green(':percent') +
				' :msg (:elapsed seconds)',
		}),
		new DependencyExtractionWebpackPlugin({
			injectPolyfill: true,
			requestToExternal: (request) => {
				if ( externals[ request ] ) {
					return externals[ request ]["this"];
				}
			},
			requestToHandle: (request) => {
				if ( externals[ request ] ) {
					return request.replace('@eaccounting/', 'ea-');
				}
			}
		}),
		new WebpackRTLPlugin( {
			minify: {
				safe: true,
			},
		} ),
		new webpack.BannerPlugin('WP Ever Accounting v' + pkg.version),
		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: JSON.stringify(
					process.env.NODE_ENV || 'development'
				),
			},
			EACCOUNTING_VERSION: "'" + pkg.version + "'",
		}),
		new FixStyleOnlyEntriesPlugin(),
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
		new MomentTimezoneDataPlugin( {
			startYear: 2000, // This strips out timezone data before the year 2000 to make a smaller file.
		} ),
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
				path: path.join(__dirname),
			},
		}),
		new MiniCssExtractPlugin({
			filename: '[name].css',
			chunkFilename: '[id].style.css',
			rtlEnabled: true,
		}),
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
	optimization: {
		minimize: NODE_ENV !== 'development',
		minimizer: [ new TerserPlugin() ],
		splitChunks: {
			name: false,
		},
	},
	watchOptions: {
		ignored: [/node_modules/],
	},
	performance: {
		hints: false,
	},
	watch: true,
};

if (NODE_ENV !== 'development') {
	config.plugins.push(
		new webpack.LoaderOptionsPlugin({minimize: true})
	);
	config.module.rules.push({
		test: /\.js$/,
		loader: 'webpack-remove-debug',
		exclude: /node_modules/,
	});
}

if (config.mode !== 'production') {
	config.devtool = process.env.SOURCEMAP || 'inline-source-map';
}
module.exports = config;
