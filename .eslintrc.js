const eslintConfig = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	env: {
		browser: true,
		es6: true,
	},
	rules: {
		'import/no-unresolved': [
			2,
			{
				ignore: [ '^@wordpress/' ],
			},
		],
		'import/no-extraneous-dependencies': 0,
		'no-console': 0,
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
		eaccounting_i18n: true,
	},
};

module.exports = eslintConfig;
