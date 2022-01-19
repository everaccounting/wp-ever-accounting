const config = require('@byteever/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
	...config,
	entry: {
		admin: './assets/css/admin.scss',
		public: './assets/css/public.scss',
		release: './assets/css/release.scss',
		setup: './assets/css/setup.scss',
		'jquery-ui': './assets/css/jquery-ui/jquery-ui.css',
	},
	plugins: [
		...config.plugins,
		// Copy vendor files to ensure 3rd party plugins relying on a script
		// handle to exist continue to be enqueued.
		new CopyWebpackPlugin({
			patterns: [
				// Styles.
				// Scripts.
				{
					from: 'assets/js/admin-legacy',
					to: 'js',
				},
				{
					from: 'assets/js/vendor',
					to: 'js',
				},
				{
					from: './node_modules/chart.js/dist/Chart.min.js',
					to: 'js/chart.bundle.js',
				},
				{
					from: './node_modules/moment/min/moment.min.js',
					to: 'js/moment.js',
				},
				{
					from: './node_modules/select2/dist/js/select2.full.min.js',
					to: 'js/select2.full.js',
				},
				{
					from:
						'./node_modules/inputmask/dist/jquery.inputmask.min.js',
					to: 'js/jquery.inputmask.js',
				}
			],
		}),

	].filter(Boolean),
};
