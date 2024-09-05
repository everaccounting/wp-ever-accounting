const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const webpack = require( 'webpack' );
const path = require('path');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const PACKAGE_NAMESPACE = '@eac/';

module.exports = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			'js/chartjs': './node_modules/chart.js/dist/chart.js',
			'js/select2': [
				'./node_modules/select-woo/dist/js/selectWoo.js',
				'./node_modules/select2/dist/js/select2.full.js',
			],
			'js/inputmask': [
				'./.assets/libraries/inputmask/inputmask.js',
				'./.assets/libraries/inputmask/inputmask.binding.js',
			],
			'js/blockui': './.assets/libraries/blockui/blockUI.js',
			'js/tiptip': './.assets/libraries/tipTip/tipTip.js',
			'js/eac-admin': './.assets/js/admin/admin.js',
			'js/eac-settings': './.assets/js/admin/settings.js',
			'js/eac-invoices': './.assets/js/admin/invoices.js',
			'css/jquery-ui': [
				'./node_modules/jquery-ui/themes/base/theme.css',
				'./node_modules/jquery-ui/themes/base/datepicker.css',
				'./node_modules/jquery-ui/themes/base/tooltip.css',
			],
			'css/select2': './node_modules/select-woo/dist/css/selectWoo.css',
			'css/eac-admin': './.assets/css/admin/admin.scss',
			'css/eac-settings': './.assets/css/admin/settings.scss',
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
	// Packages.
	{
		...defaultConfig,
		entry: Object.keys(dependencies)
			.filter((dependency) => dependency.startsWith(PACKAGE_NAMESPACE))
			.map((packageName) => packageName.replace(PACKAGE_NAMESPACE, ''))
			.reduce((memo, packageName) => {
				return {
					...memo,
					[packageName]: {
						import: `./packages/${packageName}/src/index.js`,
						library: {
							name: [
								'eac',
								packageName.replace(/-([a-z])/g, (_, letter) =>
									letter.toUpperCase()
								),
							],
							type: 'window',
						},
					},
				};
			}, {}),
		output: {
			...baseConfig.output,
			path: path.resolve(__dirname, 'assets/packages'),
		},
	}
];
