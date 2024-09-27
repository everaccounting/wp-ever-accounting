const defaults = require('./.bin/webpack');
const glob = require("glob");
const path = require("path");

module.exports = [
	// Core scripts.
	{
		...defaults,
		entry: {
			...defaults.entry(),

			// 3rd party libraries.
			// 'js/chartjs': './.assets/js/vendor/chartjs.js',
			// 'js/select2': './.assets/js/vendor/select2.js',
			// 'js/inputmask': './.assets/js/vendor/inputmask.js',
			// 'js/tiptip': './.assets/js/vendor/tiptip.js',
			// 'js/blockui': './.assets/js/vendor/blockui.js',
			// 'css/jquery-ui': './.assets/css/vendor/jquery-ui.scss',
			//
			// // Core plugins.
			// 'js/form': './.assets/js/vendor/form.js',
			// 'js/modal': './.assets/js/vendor/modal.js',
			// 'js/visibility': './.assets/js/vendor/visibility.js',
			//
			//
			// // Admin scripts.
			// 'js/admin': './.assets/js/admin/admin.js',
			// 'js/admin-sales': './.assets/js/admin/sales.js',
			// 'js/admin-settings': './.assets/js/admin/settings.js',
			// 'css/admin': './.assets/css/admin/admin.scss',
			// 'css/admin-settings': './.assets/css/admin/settings.scss',
		},
	},
	//Package scripts.
	{
		...defaults,
		entry: glob.sync('./.assets/packages/*/src/index.js').reduce((memo, file) => {
			const module = file.replace('.assets/packages/', '').replace('/src/index.js', '');
			console.log(module);
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
		}
	}
]
