module.exports = {
	presets: ['@wordpress/babel-preset-default'],
	plugins: [ '@babel/plugin-proposal-class-properties' ],
	env: {
		production: {
			plugins: [
				'transform-react-remove-prop-types',
				'@wordpress/babel-plugin-makepot',
			],
		},
	},
};
