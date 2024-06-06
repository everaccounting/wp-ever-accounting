const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
module.exports = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			//3rd party libraries
			'js/chartjs': './node_modules/chart.js/dist/chart.js',
			'js/jquery-plugins': [
				'./node_modules/select-woo/dist/js/selectWoo.js',
				'./src/libraries/tipTip/tipTip.js',
				'./src/libraries/mask/mask.js',
				'./src/libraries/inputmask/inputmask.js',
				'./src/libraries/inputmask/inputmask.binding.js',
				'./node_modules/micromodal/dist/micromodal.js',
				'./node_modules/select2/dist/js/select2.full.js',
			],
			'css/jquery-plugins': [
				'./node_modules/select-woo/dist/css/selectWoo.css',
				'./node_modules/jquery-ui/themes/base/datepicker.css',
				'./node_modules/jquery-ui/themes/base/tooltip.css',
			],


			'js/eac-admin': './src/js/admin/admin.js',
			'js/eac-settings': './src/js/admin/settings.js',
			'css/eac-admin': './src/css/admin/admin.scss',
			'css/eac-settings': './src/css/admin/settings.scss',
		},
		output: {
			...defaultConfig.output,
			filename: '[name].js',
			path: __dirname + '/assets/',
			chunkFilename: 'chunks/[chunkhash].js',
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
			],
		},
		plugins: [
			...defaultConfig.plugins,
			new RemoveEmptyScriptsPlugin(
				{
					stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
					remove: /\.(js)$/,
				}
			),
		],
	},
];
