/**
 * External dependencies
 */
import { getIdFromQuery } from '@eaccounting/navigation';
/**
 * Internal dependencies
 */
import Customer from './customer';
import Customers from './customers';

export default function ( props ) {
	const id = getIdFromQuery();
	if ( !! id ) {
		return <Customer { ...props } />;
	}

	return <Customers { ...props } />;
}
