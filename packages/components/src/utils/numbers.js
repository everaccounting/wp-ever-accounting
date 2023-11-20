export function numberFormat( value, decimals = 0, decimalSeparator = '.', thousandSeparator = ',' ) {
	const number = typeof value === 'string' ? parseFloat( value ) : value;

	const str = number.toFixed( decimals ).toString().split( '.' );

	const parts = [];

	for ( let i = str[ 0 ].length; i > 0; i -= 3 ) {
		parts.unshift( str[ 0 ].substring( Math.max( 0, i - 3 ), i ) );
	}

	str[ 0 ] = parts.join( thousandSeparator );

	return str.join( decimalSeparator );
}
