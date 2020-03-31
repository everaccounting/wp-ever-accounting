module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended', 'prettier' ],
	globals: {
		eAccountingi18n: true,
	},
	rules: {
		'@wordpress/dependency-group': 'error',
		'valid-jsdoc': 'off',
		radix: 'error',
		yoda: [ 'error', 'never' ],
	},
};
