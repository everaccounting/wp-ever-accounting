module.exports = function(api) {
	api.cache(true);
	return {
		presets: [
			[
				'@babel/preset-env',
				{
					modules: false,
				},
			],
			'@babel/preset-react',
		],
		plugins: [
			'@babel/plugin-proposal-object-rest-spread',
			'@babel/plugin-syntax-dynamic-import',
			'@babel/plugin-syntax-import-meta',
			'@babel/plugin-proposal-class-properties',
			[
				'@babel/plugin-proposal-decorators',
				{
					legacy: true,
				},
			],
		],
		env: {
			production: {
				plugins: [
					'transform-react-remove-prop-types',
					'@babel/plugin-transform-react-inline-elements',
					'@babel/plugin-transform-react-constant-elements',
					'@wordpress/babel-plugin-makepot',
				],
			},
		},
	};
};