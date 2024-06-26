const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
module.exports = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			'css/admin': './ui/legacy/css/admin.scss',
			'css/public': './ui/legacy/css/public.scss',
			'css/release': './ui/legacy/css/release.scss',
			'css/setup': './ui/legacy/css/setup.scss',
			'css/jquery-ui': './ui/legacy/css/jquery-ui/jquery-ui.scss',
		},
		output: {
			...defaultConfig.output,
			filename: '[name].js',
			path: __dirname + '/assets/',
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
			// Copy images to the build folder.
			new CopyWebpackPlugin({
				patterns: [
					{
						from: 'ui/legacy/js/admin-legacy',
						to: 'js',
					},
					{
						from: 'ui/legacy/js/plugins',
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
					},
					{
						from: path.resolve(__dirname, 'ui/legacy/images'),
						to: path.resolve(__dirname, 'assets/images'),
					}
				]
			}),

			new RemoveEmptyScriptsPlugin({
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
				remove: /\.(js)$/,
			}),
		],
	},
];
