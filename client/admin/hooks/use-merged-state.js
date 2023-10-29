/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';

export function useMergedState( defaultValue ) {
	const [ value, setValue ] = useState( defaultValue );

	useEffect( () => {
		setValue( defaultValue );
	}, [ defaultValue ] );
	return [ value, setValue ];
}

export default useMergedState;
