module.exports = {
	root: true,
	parser: 'babel-eslint',
	env: {
		browser: true,
		node: true,
	},
	globals: {
		asyncRequire: true,
		PROJECT_NAME: true,
	},
	rules: {
		camelcase: 0, // REST API objects include underscores
		'max-len': 0,
		'no-unused-expressions': 0, // Allows Chai `expect` expressions
	},
};
