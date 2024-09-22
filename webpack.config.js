const RemoveEmptyScript = require('webpack-remove-empty-scripts');
const DependencyHandler = require('@wordpress/dependency-extraction-webpack-plugin');
const defaults = require('@wordpress/scripts/config/webpack.config');
const CopyPlugin = require('copy-webpack-plugin');
const path = require('path');
const glob = require('glob');

const PACKAGE_NAMESPACE = '@eac/';

module.exports = [
	{
		...defaults,
		entry: {
			...defaults.entry(),

			// 3rd party libraries.
			'js/chartjs': './.assets/vendors/js/chartjs.js',
			'js/select2': './.assets/vendors/js/select2.js',
			'js/inputmask': './.assets/vendors/js/inputmask.js',
			'js/tiptip': './.assets/vendors/js/tiptip.js',
			'js/blockui': './.assets/vendors/js/blockui.js',
			'css/jquery-ui': './.assets/css/jquery-ui.scss',

			// Core plugins.
			'js/eac-api': './.assets/js/eac-api.js',
			'js/eac-form': './.assets/js/eac-form.js',
			'js/eac-modal': './.assets/js/eac-modal.js',
			'js/eac-money': './.assets/js/eac-money.js',

			// Admin scripts.
			'js/admin': './.assets/js/admin.js',
			'js/admin-accounts': './.assets/js/admin-accounts.js',
			'js/admin-currencies': './.assets/js/admin-currencies.js',
			'js/admin-customers': './.assets/js/admin-customers.js',
			'js/admin-dashboard': './.assets/js/admin-dashboard.js',
			'js/admin-expenses': './.assets/js/admin-expenses.js',
			'js/admin-invoices': './.assets/js/admin-invoices.js',
			'js/admin-payments': './.assets/js/admin-payments.js',
			'js/admin-settings': './.assets/js/admin-settings.js',
			'js/admin-transfers': './.assets/js/admin-transfers.js',
			'js/admin-vendors': './.assets/js/admin-vendors.js',
			'js/admin-reports': './.assets/js/admin-reports.js',
			'css/admin': './.assets/css/admin.scss',

			// Frontend scripts.
			'js/frontend': './.assets/js/frontend.js',
		},
		output: {
			...defaults.output,
			filename: '[name].js',
			path: __dirname + '/assets/',
			chunkFilename: 'chunks/[chunkhash].js',
			uniqueName: '__eac_webpackJsonp',
			library: {
				name: '[name]',
				type: 'window',
			},
		},
		resolve: {
			...defaults.resolve,
			modules: [
				'node_modules',
				'.assets/packages',
			],
			alias: {
				...defaults.resolve.alias,
				'@js': path.resolve(__dirname, '.assets/js'),
				'@eac': path.resolve(__dirname, '.assets/packages'),
			}
		},
		module: {
			rules: [...defaults.module.rules].filter(Boolean),
		},
		plugins: [
			...defaults.plugins,
			new RemoveEmptyScript(
				{
					stage: RemoveEmptyScript.STAGE_AFTER_PROCESS_PLUGINS,
					remove: /\.(js)$/,
				}
			),
		],
	},
	// Packages.
	{
		...defaults,
		entry: glob.sync('./.assets/packages/*/src/index.js').reduce((memo, file) => {
			const module = file.replace('.assets/packages/', '').replace('/src/index.js', '');
			return {
				...memo,
				[`${module}`]: {
					import: path.resolve(__dirname, file),
					library: {
						name: [
							'eac',
							module.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase())
						],
						type: 'window',
					}
				}
			};
		}, {}),
		output: {
			...defaults.output,
			path: path.resolve(__dirname, 'assets/packages'),
		},
		plugins: [
			...defaults.plugins.filter((plugin) => !['DependencyExtractionWebpackPlugin'].includes(plugin.constructor.name)),
			new DependencyHandler({
				injectPolyfill: false,
				requestToExternal(request) {
					if (request.startsWith(PACKAGE_NAMESPACE)) {
						return [
							'eac',
							request.substring(PACKAGE_NAMESPACE.length).replace(/-([a-z])/g, (_, letter) => letter.toUpperCase()),
						];
					}
				},
				requestToHandle(request) {
					if (request.startsWith(PACKAGE_NAMESPACE)) {
						return `eac-${request.substring(PACKAGE_NAMESPACE.length).replace(/-([a-z])/g, (_, letter) => letter.toUpperCase())}`;
					}
				},
			}),
		],
	}
];
