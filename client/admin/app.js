/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
/**
 * External dependencies
 */
import { ENTITIES_STORE_NAME, useUser } from '@eac/data';

/**
 * Internal dependencies
 */
import './style.scss';

export function App() {
	// const currentUser = useSelect( ( select ) => {
	// 	console.log( select( 'core' ) );
	// 	// return select( 'core' ).getEntityRecord( 'root', 'user', 1 );
	// }, [] );

	// console.log( currentUser );

	return (
		<div className="eac-admin-app">
			<h1>WP Ever Accounting</h1>
		</div>
	);
}

export default App;
