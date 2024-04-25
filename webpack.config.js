const defaultConfig            = require( '@wordpress/scripts/config/webpack.config' );
const CopyWebpackPlugin        = require( 'copy-webpack-plugin' );
const path                     = require( 'path' );
const RemoveEmptyScriptsPlugin= require( 'webpack-remove-empty-scripts' );
module.exports                 = [
	{
		...defaultConfig,
		entry: {
			...defaultConfig.entry(),
			//3rd party libraries
			'js/select-woo': './node_modules/select-woo/dist/js/selectWoo.js',
			'css/select-woo': './node_modules/select-woo/dist/css/selectWoo.css',
			'css/jquery-ui': './node_modules/jquery-ui/themes/base/all.css',
			'js/tipTip': './src/libraries/tipTip/tipTip.js',

			'js/eac-core': './src/js/common/eac-core.js',
			'js/eac-admin': './src/js/admin/admin.js',
			'css/eac-admin': './src/css/admin/admin.scss',
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

			new RemoveEmptyScriptsPlugin(
				{
					stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
					remove: /\.(js)$/,
				}
			),
		],
	},
	];
