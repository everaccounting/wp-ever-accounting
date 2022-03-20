var path = require('path');
var webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader')
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const CustomTemplatedPathPlugin = require( './custom-templated-path-webpack-plugin.js' );

const isProduction = process.env.NODE_ENV === 'production';
const mode = isProduction ? 'production' : 'development';


module.exports = {
	devtool: isProduction ? false : 'source-map',
	mode,
	entry: {
		app: './assets/js/admin/index.js',
		components: './assets/js/components/index.js',
	},
	output: {
		clean: true,
		path: path.resolve(__dirname, './assets/dist'),
		filename: 'js/[name].js',
		chunkFilename: 'chunks/[name].js',
		publicPath: path.resolve( __dirname, 'assets/dist' ),
		library: [ 'eaccounting', '[camelName]' ],
	},
	externals: {
		vue: 'Vue',
		lodash: 'lodash',
		jquery: 'jQuery',
		$: 'jQuery',
	},
	resolve: {
		alias: {
			vue: "vue/dist/vue.esm-bundler.js",
			'@eaccounting': path.resolve( __dirname, './assets/js' ),
			'admin': path.resolve('./assets/js/admin/'),
			'frontend': path.resolve('./assets/js/frontend/'),
		},
		extensions: ['.js', '.vue', '.json'],
		modules: [
			path.resolve('./node_modules'),
		]
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /(node_modules)/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env'],
					},
				},
			},
			{
				test: /\.vue$/,
				use: {
					loader: 'vue-loader',
				},
			},
		],
	},
	plugins: [
		new VueLoaderPlugin(),

		new CustomTemplatedPathPlugin( {
			// eslint-disable-next-line no-shadow
			camelName( path, data ) {
				return data.chunk.name.replace(
					/-([a-z])/g,
					( match, letter ) => letter.toUpperCase()
				);
			},
		} ),

		// Copy static assets to the `dist` folder.
		new CopyWebpackPlugin( {
			patterns: [
				{
					from: './node_modules/vue/dist/vue.global.js',
					to: 'js/vue.js',
					noErrorOnMissing: false,
				},
			],
		} ),
	].filter( Boolean ),
};
