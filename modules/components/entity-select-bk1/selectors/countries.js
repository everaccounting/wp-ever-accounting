/**
 * External dependencies
 */
import { getSiteData } from '@eaccounting/data';
const countries = getSiteData( 'countries' );
export default {
	options: Object.keys( countries ).map( ( ISO ) => ( {
		label: countries[ ISO ],
		value: ISO,
	} ) ),
};
