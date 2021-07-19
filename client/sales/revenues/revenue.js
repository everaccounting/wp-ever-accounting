/**
 * External dependencies
 */
import { getIdFromQuery } from '@eaccounting/navigation';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { getSelectors } from '@eaccounting/data';
// eslint-disable-next-line no-unused-vars
export default function Revenue( props ) {
	const id = getIdFromQuery();
	const payment = useSelect( ( select ) =>
		getSelectors( { select, name: 'payments', id } )
	);

	console.log( payment );
	return (
		<>
			<h1>Revenue</h1>
			Single Page
		</>
	);
}
