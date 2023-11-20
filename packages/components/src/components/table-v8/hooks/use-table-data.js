/**
 * WordPress dependencies
 */
import { useEffect, useRef } from '@wordpress/element';

function useTableData( data ) {
	const previous = useRef( [] );

	useEffect( () => {
		if ( data && data !== previous.current ) {
			previous.current = data;
		}
	}, [ data ] );

	return {
		data: previous.current,
	};
}

export default useTableData;
