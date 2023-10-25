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
	const [ query, setQuery ] = useState( {} );
	const items = useSelect(
		( select ) => {
			const {getRecords, } = select( ENTITIES_STORE_NAME );
			return select( ENTITIES_STORE_NAME ).getRecords( 'item', query );
		},
		[ query ]
	);
	const deleteRecord = useDispatch( ENTITIES_STORE_NAME ).deleteRecord;
	// const { user } = useUser();

	const deleteItem = ( id ) => {
		deleteRecord( 'item', id );
	};

	const currentUser = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecord( 'root', 'user', 1 );
	}, [] );

	console.log( currentUser );

	return (
		<div className="eac-admin-app">
			<h1>WP Ever Accounting</h1>
			<input
				type="text"
				value={ query?.paged || 1 }
				onChange={ ( event ) => {
					const paged = parseInt( event.target.value );
					setQuery( { ...query, paged } );
				} }
			/>
			{ items && (
				<ul>
					{ items.map( ( item ) => {
						return (
							<li key={ item.id } onClick={ () => deleteItem( item.id ) }>
								{ item.name }
							</li>
						);
					} ) }
				</ul>
			) }
		</div>
	);
}

export default App;
