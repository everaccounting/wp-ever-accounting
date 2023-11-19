export const sanitizePrice = ( price, currency ) => {
	if ( ! price ) {
		return '';
	}
	const { decimalSeparator, thousandSeparator } = currency;
	const sanitizedPrice = price
		.replace( new RegExp( `\\${ thousandSeparator }`, 'g' ), '' )
		.replace( new RegExp( `\\${ decimalSeparator }`, 'g' ), '.' );
	return sanitizedPrice;
};
