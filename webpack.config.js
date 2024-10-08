/**
 * External dependencies
 */
const glob = require('glob');
const CopyWebpackPlugin = require('copy-webpack-plugin');
/**
 * Internal dependencies
 */
const {config, NAMESPACE} = require('./.bin/webpack.config');
const {dependencies} = require('./package.json');
const path = require('path');

module.exports = [
	{
		...config,
		entry: {
			...config.entry(),

			// 3rd party libraries.
			'js/chartjs': './node_modules/chart.js/dist/chart.js',
			'js/select2': './.assets/js/vendor/select2.js',
			'js/inputmask': './.assets/js/vendor/inputmask.js',
			'js/tiptip': './.assets/js/vendor/tiptip.js',
			'js/blockui': './.assets/js/vendor/blockui.js',
			'css/jquery-ui': './.assets/css/vendor/jquery-ui.scss',

			// Core plugins.
			'js/form': './.assets/js/admin/form.js',
			'js/modal': './.assets/js/admin/modal.js',
			'js/api': './.assets/js/admin/api.js',

			// Admin scripts.
			'js/admin': './.assets/js/admin/admin.js',
			'css/admin': './.assets/css/admin/admin.scss',

			// Client scripts.
			// ...glob.sync('./client/*/*/index.js').reduce((memo, file) => {
			// 	const [type, name] = new RegExp('client/(.*)/(.*)/index.js')
			// 		.exec(file)
			// 		.slice(1);
			// 	return {
			// 		...memo,
			// 		[`client/${type}-${name}`]: path.resolve(__dirname, file),
			// 	};
			// }, {}),
		},
		plugins: [
			...config.plugins,
			// new CopyWebpackPlugin({
			// 	patterns: [
			// 		{
			// 			from: './node_modules/chart.js/dist/Chart.min.js',
			// 			to: 'js/chart.bundle.js',
			// 		}
			// 	]
			// }),
		],
	},
	//Package scripts.
	{
		...config,
		entry: Object.keys(dependencies)
			.filter((dependency) => dependency.startsWith(NAMESPACE))
			.map((packageName) => packageName.replace(NAMESPACE, ''))
			.reduce((memo, packageName) => {
				const name = packageName.replace(/-([a-z])/g, (_, letter) =>
					letter.toUpperCase()
				);
				return {
					...memo,
					[packageName]: {
						import: `./packages/${packageName}/src/index.js`,
						library: {
							name: ['eac', name],
							type: 'window',
						},
					},
				};
			}, {}),
		output: {
			...config.output,
			path: path.resolve(__dirname, 'assets/client'),
		},
	},
];
