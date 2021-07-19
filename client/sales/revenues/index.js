/**
 * External dependencies
 */
import { getIdFromQuery } from '@eaccounting/navigation';
/**
 * Internal dependencies
 */
import Revenue from './revenue';
import Revenues from './revenues';

export default function ( props ) {
	const { action = '' } = props.query;
	const id = getIdFromQuery();
	if ( !! id || action === 'add' ) {
		return <Revenue { ...props } />;
	}

	return <Revenues { ...props } />;
}
