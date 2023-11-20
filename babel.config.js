module.exports = function ( api ) {
	api.cache( true );

	return {
		presets: [ '@wordpress/babel-preset-default' ],
		sourceType: 'unambiguous',
		plugins: [ '@babel/plugin-transform-runtime' ],
		ignore: [ 'packages/**/node_modules' ],
		env: {
			production: {},
		},
	};
};
