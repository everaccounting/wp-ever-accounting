/**
 * External dependencies
 */
import { getSiteData } from '@eaccounting/data';
const codes = getSiteData( 'codes' );
export default {
	options: Object.values( codes ),
	getOptionLabel: ( code ) =>
		code && `${ code.name } (${ code.code } ${ code.symbol } )`,
	getOptionValue: ( code ) => code && code.code,
};
