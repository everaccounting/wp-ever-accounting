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
	plugins: [
		'@babel/plugin-proposal-class-properties',
		'@wordpress/babel-plugin-makepot',
	]
};
