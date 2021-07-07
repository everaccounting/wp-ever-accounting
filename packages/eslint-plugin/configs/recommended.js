module.exports = {
    extends: [
        'plugin:react-hooks/recommended',
        require.resolve( './custom.js' ),
        'plugin:@wordpress/eslint-plugin/recommended',
    ],
	parser: '@typescript-eslint/parser',
    globals: {
        ajaxurl: true,
        document: true,
        jQuery: true,
        lodash: true,
        module: true,
        process: true,
        window: true,
    },
    plugins: [ '@wordpress' ],
    rules: {
        radix: 'error',
        yoda: [ 'error', 'never' ],
        'react/react-in-jsx-scope': 0,
        'react/prop-types': 0,
        'react/jsx-props-no-spreading': 0,
        'import/no-unresolved': [2, { ignore: ['^@wordpress/', '^@eaccounting/'] }],
        '@wordpress/dependency-group': 1,
    },
};
