const eslintConfig = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	env: {
		browser: true,
		es6: true,
	},
	rules: {
		'@wordpress/dependency-group': 'warn',
		'@wordpress/i18n-translator-comments': 'warn',
		'@wordpress/valid-sprintf': 'warn',
		'import/no-extraneous-dependencies': [
			'warn',
			{ devDependencies: true, optionalDependencies: false, peerDependencies: true },
		],
		'import/no-unresolved': [ 2, { ignore: [ '^@wordpress/', '^lodash-es', '^react', '^react-dom' ] } ],
		'no-console': 0,
		camelcase: 0,
	},
	globals: {
		ajaxurl: true,
		document: true,
		jQuery: true,
		lodash: true,
		module: true,
		process: true,
		window: true,
		eac_vars: true,
	},
};

module.exports = eslintConfig;
