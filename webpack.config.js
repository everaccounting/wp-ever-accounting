const config = require('@byteever/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
module.exports = {
	...config,
	plugins: [
		...config.plugins,
		// Copy vendor files to ensure 3rd party plugins relying on a script
		// handle to exist continue to be enqueued.
		new CopyWebpackPlugin({
			patterns: [
				{
					from: './assets/lib/jquery-block-ui/jquery.blockUI.js',
					to: 'js/jquery.blockUI.js',
				},
				{
					from: './assets/lib/jquery-tiptip/jquery.tipTip.min.js',
					to: 'js/jquery.tipTip.min.js',
				},
				{
					from: './assets/lib/print-this/printThis.js',
					to: 'js/printThis.js',
				},
				{
					from: './node_modules/chart.js/dist/Chart.min.js',
					to: 'js/chart.bundle.js',
				},
				{
					from: './node_modules/select2/dist/js/select2.full.min.js',
					to: 'js/select2.full.js',
				},
				{
					from:
						'./node_modules/inputmask/dist/jquery.inputmask.min.js',
					to: 'js/jquery.inputmask.js',
				},
			],
		}),
	],
};
