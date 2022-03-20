/**
 * External dependencies
 */
import { useEffect } from 'react';

/**
 * Internal dependencies
 */
import { KeyCodes } from '../utils/keyCodes';

const useOnEscapeKeyDown = ( isListening, onEscapeKeyDown ) => {
	useEffect( () => {
		const handleKeyDown = ( event ) => {
			if ( event.keyCode === KeyCodes.ESCAPE ) {
				onEscapeKeyDown();
			}
		};

		if ( isListening ) {
			document.addEventListener( 'keydown', handleKeyDown );
		}
		return () => {
			document.removeEventListener( 'keydown', handleKeyDown );
		};
	}, [ isListening, onEscapeKeyDown ] );
};

export default useOnEscapeKeyDown;
