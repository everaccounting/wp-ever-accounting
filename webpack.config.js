const {BundleAnalyzerPlugin} = require('webpack-bundle-analyzer');
const DependencyExtractionWebpackPlugin = require('@wordpress/dependency-extraction-webpack-plugin');
const path = require('path');

const NODE_ENV = process.env.NODE_ENV || 'development';
const externals = [];

module.exports = {
	mode: NODE_ENV,
	devtool: NODE_ENV === 'development' ? 'source-map' : false,
	entry: {
		eaccounting: './client',
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
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
			{
				test: /\.svg$/,
				use: ['@svgr/webpack', 'url-loader'],
			},
		],
	},
	plugins: [
		new BundleAnalyzerPlugin(),
		new ProgressBarPlugin( {
			format: chalk.blue( 'Build core script' ) +
				' [:bar] ' + chalk.green( ':percent' ) +
				' :msg (:elapsed seconds)',
		} ),
		new DependencyExtractionWebpackPlugin({injectPolyfill: true})
	],
	stats: {
		children: false,
	},
	watchOptions: {
		ignored: [/node_modules/],
	},
	performance: {
		hints: false,
	},
	watch: true
};
