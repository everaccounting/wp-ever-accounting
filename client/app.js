/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { getItemSchema } from '../modules/data';

export default function () {
	const route = useSelect( ( select ) =>
		getItemSchema( { select, resourceName: 'accounts' } )
	);
	console.log( route );
	return <>APP fdsfsa hello</>;
}
