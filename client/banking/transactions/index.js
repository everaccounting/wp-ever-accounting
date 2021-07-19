/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
/**
 * External dependencies
 */
import { getItems } from '@eaccounting/data';
import { getTableQuery } from '@eaccounting/navigation';

export default function () {
	const query = getTableQuery();
	const { items, total, isRequesting } = useSelect( ( select ) =>
		getItems( { select, name: 'transactions', query } )
	);
	console.log( items, total, isRequesting );
	return (
		<>
			<h1>Transactions</h1>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit,
			sed.
		</>
	);
}
