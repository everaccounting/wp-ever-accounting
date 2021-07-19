/**
 * External dependencies
 */
import { getIdFromQuery } from '@eaccounting/navigation';

/**
 * Internal dependencies
 */
import Payments from './payments';

export default function ( props ) {
	const id = getIdFromQuery();
	if ( !! id ) {
		return null;
	}

	return <Payments { ...props } />;
}
