const eslintConfig = {
	extends: ['plugin:@wordpress/eslint-plugin/recommended', 'prettier'],
	env: {
		browser: true,
		es6: true,
	},
	globals: {
		ajaxurl: true,
		document: true,
		jQuery: true,
		lodash: true,
		module: true,
		process: true,
		window: true,
		eaccounting: true,
		wp: 'readonly',
	},
};

module.exports = eslintConfig;
