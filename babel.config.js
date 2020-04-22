module.exports = {
	presets: [
		[
			'@wordpress/babel-preset-default',
			{
				targets: {
					node: 'current',
				},
			},
		],
	],
	plugins: ['@babel/plugin-proposal-class-properties'],
	env: {
		production: {
			plugins: [
				'transform-react-remove-prop-types',
				'@babel/plugin-transform-react-inline-elements',
				// '@babel/plugin-transform-react-constant-elements',
				'@wordpress/babel-plugin-makepot',
			],
		},
	},
};
