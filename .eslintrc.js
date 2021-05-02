module.exports = {
	extends: ['plugin:@byteever/eslint-plugin/recommended'],
	rules: {
		'import/no-unresolved': [
			2,
			{ ignore: ['^@wordpress/', '^@eaccounting/'] },
		],
		'no-shadow': 0,
	},
};
