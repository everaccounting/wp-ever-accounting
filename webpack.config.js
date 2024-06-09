const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
module.exports = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			'js/chartjs': './node_modules/chart.js/dist/chart.js',
			'js/eac-admin': [
				'./node_modules/select-woo/dist/js/selectWoo.js',
				//'./node_modules/micromodal/dist/micromodal.js',
				'./node_modules/select2/dist/js/select2.full.js',
				'./src/libraries/tipTip/tipTip.js',
				'./src/libraries/inputmask/inputmask.js',
				'./src/libraries/inputmask/inputmask.binding.js',
				'./src/js/common/eac.js',
				'./src/js/admin/admin.js'
			],
			'css/eac-admin': [
				'./node_modules/select-woo/dist/css/selectWoo.css',
				'./node_modules/jquery-ui/themes/base/datepicker.css',
				'./node_modules/jquery-ui/themes/base/tooltip.css',
				'./src/css/admin/admin.scss'
			],
			'js/eac-settings': './src/js/admin/settings.js',
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
