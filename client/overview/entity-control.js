/**
 * External dependencies
 */
import { castArray } from 'lodash';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../../modules/components/select-control';

export default function EntityControl( props ) {
	const { entityName, entity_id, baseQuery = {}, ...rest } = props;
	const [ query, setQuery ] = useState( baseQuery );
	const [ selected, setSelected ] = useState( [] );
	const entity_ids = entity_id && castArray( entity_id );
	const selected_entities = useSelect(
		( select ) => {
			return (
				!! entity_ids &&
				select( 'ea/core' ).getEntityRecords( entityName, {
					include: entity_ids,
				} )
			);
		},
		[ entity_ids ]
	);

	const options = useSelect(
		( select ) =>
			entityName &&
			select( 'ea/core' ).getEntityRecords( entityName, query ),
		[ query ]
	);

	const isLoading = useSelect(
		( select ) =>
			entityName &&
			select( 'ea/core' ).isResolving( 'getEntityRecords', [
				entityName,
				query,
			] ),
		[ query ]
	);

	useEffect( () => {
		handleChange( selected_entities );
	}, [ selected_entities ] );

	const handleChange = ( val ) => {
		setSelected( val );
		props.onChange( val );
	};
	const onInputChange = ( search, { action } ) => {
		if ( action === 'input-change' ) {
			setQuery( ( query ) => ( { ...query, search } ) );
		}
	};
	console.log( props );
	return (
		<>
			<SelectControl
				value={ selected }
				options={ options }
				onInputChange={ onInputChange }
				isLoading={ isLoading }
				onChange={ handleChange }
				{ ...rest }
			/>
		</>
	);
}
