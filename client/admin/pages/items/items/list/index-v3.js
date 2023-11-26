/**
 * External dependencies
 */
import { SectionHeader, Button } from '@eac/components';
import { useCurrencies } from '@eac/data';
import { AddCategory } from '@eac/editor';
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

function List() {
	const [ showModal, setShowModal ] = useState( false );
	const getCurrency = useCurrencies();
	return (
		<>
			<SectionHeader title={ __( 'List', 'wp-ever-accounting' ) } />
			<Button onClick={ () => setShowModal( true ) }> Add Category </Button>
			{ showModal && (
				<AddCategory
					values={ {
						name: 'Category 1',
						description: 'This is a description.',
						type: 'income',
					} }
					//showType={ false }
					modalProps={ {
						style: {
							minWidth: '50%',
						},
					} }
					onClose={ () => setShowModal( false ) }
					beforeSubmit={ ( values ) => ( { ...values, type: 'item' } ) }
					onSuccess={ ( data ) => {
						console.log( data );
						setShowModal( false );
					} }
					onError={ ( error ) => {
						console.log( 'error', error );
					} }
					// onSubmit={ async ( values ) => {
					// 	// return a promise.
					// 	return new Promise( ( resolve ) => {
					// 		setTimeout( () => {
					// 			console.log( values );
					// 			resolve( true );
					// 		}, 2000 );
					// 	} );
					// } }
				/>
			) }

			{ console.log( getCurrency( 'USD' ) ) }
		</>
	);
}

export default List;
