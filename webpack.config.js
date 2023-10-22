/**
 * External dependencies
 */
const glob = require( 'glob' );
const path = require( 'path' );

/**
 * Internal dependencies
 */
const baseConfig = require( './bin/webpack/base.config' );
const { dependencies } = require( './package.json' );
const PACKAGE_NAMESPACE = '@eac/';

module.exports = [
	// App config.
	baseConfig( {
		entry: glob.sync( './client/*/index.js' ).reduce( ( memo, filepath ) => {
			const name = filepath.replace( 'client/', '' ).replace( '/index.js', '' );
			return {
				...memo,
				[ name ]: path.resolve( __dirname, filepath ),
			};
		}, {} ),
		browserSync: true,
	} ),
	// Packages config.
	baseConfig( {
		entry: Object.keys( dependencies )
			.filter( ( dependency ) => dependency.startsWith( PACKAGE_NAMESPACE ) )
			.map( ( packageName ) => packageName.replace( PACKAGE_NAMESPACE, '' ) )
			.reduce( ( memo, packageName ) => {
				return {
					...memo,
					[ packageName ]: {
						import: `./packages/${ packageName }/src/index.js`,
						library: {
							name: [ 'eac', packageName.replace( /-([a-z])/g, ( _, letter ) => letter.toUpperCase() ) ],
							type: 'window',
						},
					},
				};
			}, {} ),
	} ),
];
