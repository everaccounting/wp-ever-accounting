const RemoveEmptyScript = require('webpack-remove-empty-scripts');
const DependencyHandler = require('@wordpress/dependency-extraction-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const defaults = require('@wordpress/scripts/config/webpack.config');
const glob = require('glob');
const path = require('path');

const PACKAGE_NAMESPACE = '@eac/';

module.exports = [
	{
		...defaults,
		entry: {
			...defaults.entry(),
			'js/chartjs': './node_modules/chart.js/dist/chart.js',
			'js/select2': [
				'./node_modules/select-woo/dist/js/selectWoo.js',
				'./node_modules/select2/dist/js/select2.full.js',
			],
			'js/inputmask': [
				'./.assets/libraries/inputmask/inputmask.js',
				'./.assets/libraries/inputmask/inputmask.binding.js',
			],
			'js/tiptip': './.assets/libraries/tiptip/tiptip.js',
			'js/blockui': './.assets/libraries/blockui/blockUI.js',
			'js/eac-modal': './.assets/js/admin/modal.js',
			'js/eac-form': './.assets/js/admin/form.js',
			'js/eac-admin': './.assets/js/admin/admin.js',
			'js/eac-invoice': './.assets/js/admin/invoice.js',
			'js/eac-bill-form': './.assets/js/admin/bill-form.js',
			'js/eac-settings': './.assets/js/admin/settings.js',
			'css/jquery-ui': [
				'./node_modules/jquery-ui/themes/base/theme.css',
				'./node_modules/jquery-ui/themes/base/datepicker.css',
				'./node_modules/jquery-ui/themes/base/tooltip.css',
			],
			'css/eac-admin': './.assets/css/admin/admin.scss',
			'css/eac-settings': './.assets/css/admin/settings.scss',
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
				'@css': path.resolve(__dirname, '.assets/css'),
				'@eac': path.resolve(__dirname, '.assets/packages'),
			}
		},
		module: {
			rules: [...defaults.module.rules].filter(Boolean),
		},
		plugins: [
			...defaults.plugins.filter((plugin) => !['DependencyExtractionWebpackPlugin'].includes(plugin.constructor.name)),

			// Extracts dependencies from the source code.
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
				// requestToHandle(request) {
				// 	if (request.startsWith(PACKAGE_NAMESPACE)) {
				// 		return `eac-${request.substring(PACKAGE_NAMESPACE.length).replace(/-([a-z])/g, (_, letter) => letter.toUpperCase())}`;
				// 	}
				// },
			}),

			// copy vue js file from node_modules to assets folder.
			new CopyPlugin({
				patterns: [
					{from: 'node_modules/accounting/accounting.js', to: 'js/accounting.js'},
				],
			}),

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
	}
];
