/**
 * External dependencies
 */
import { castArray, isEqual } from 'lodash';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { cloneElement, useEffect, useRef, useState } from '@wordpress/element';
/**
 * Internal dependencies
 */
import SelectControl from '../select-control';
import { CORE_STORE_NAME } from '@eaccounting/data';
import { Button, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	accounts,
	currencies,
	customers,
	expenseCategories,
	incomeCategories,
	itemCategories,
	items,
	vendors,
} from './entities';

const getEntityProps = ( entityName ) => {
	switch ( entityName ) {
		case 'customers':
			return customers;
		case 'vendors':
			return vendors;
		case 'accounts':
			return accounts;
		case 'items':
			return items;
		case 'incomeCategories':
			return incomeCategories;
		case 'expenseCategories':
			return expenseCategories;
		case 'itemCategories':
			return itemCategories;
		case 'currencies':
			return currencies;
		default:
			return {};
	}
};

export default function EntitySelect( args ) {
	const {
		entityName,
		entity_id,
		baseQuery = {},
		creatable,
		modal,
		onChange = ( x ) => x,
		...props
	} = {
		...args,
		...getEntityProps( args.entityName ),
	};
	const ref = useRef();
	const [ query, setQuery ] = useState( baseQuery );
	const [ selected, setSelected ] = useState( [] );
	const [ isModalOpen, setModalStatus ] = useState( false );
	const entity_ids = entity_id && castArray( entity_id );
	const selected_entities = useSelect(
		( select ) => {
			return (
				!! entity_ids &&
				select( CORE_STORE_NAME ).getEntityRecords( entityName, {
					include: entity_ids,
				} )
			);
		},
		[ entity_ids ]
	);

	const options = useSelect(
		( select ) =>
			entityName &&
			select( CORE_STORE_NAME ).getEntityRecords( entityName, query ),
		[ query ]
	);

	const isLoading = useSelect(
		( select ) =>
			entityName &&
			select( CORE_STORE_NAME ).isResolving( 'getEntityRecords', [
				entityName,
				query,
			] ),
		[ query ]
	);

	useEffect( () => {
		handleChange( selected_entities );
	}, [ selected_entities ] );

	const handleChange = ( val ) => {
		if ( ! isEqual( val, selected ) ) {
			setSelected( val );
			onChange( val );
		}
	};
	const onInputChange = ( search, { action } ) => {
		if ( action === 'input-change' ) {
			setQuery( ( query ) => ( { ...query, search } ) );
		}
	};

	const onClick = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
		ref.current.select.blur();
		setModalStatus( ( open ) => ! open );
	};

	const AddButton = () => {
		return (
			<Button
				className="ea-advance-select__add-new-entity"
				onClick={ onClick }
			>
				<Icon icon="plus" />
				{ __( 'Add New' ) }
			</Button>
		);
	};

	const handleCreate = async ( item ) => {
		await ref.current.select.setValue( item );
		setModalStatus( ( open ) => ! open );
	};

	return (
		<>
			<SelectControl
				setRef={ ref }
				value={ selected }
				options={ options }
				onInputChange={ onInputChange }
				isLoading={ isLoading }
				onChange={ handleChange }
				{ ...( !! creatable && { button: <AddButton /> } ) }
				{ ...props }
			/>

			{ !! creatable &&
				!! isModalOpen &&
				!! modal &&
				cloneElement( modal, {
					onClose: () => setModalStatus( ( open ) => ! open ),
					onSave: handleCreate,
				} ) }
		</>
	);
}
