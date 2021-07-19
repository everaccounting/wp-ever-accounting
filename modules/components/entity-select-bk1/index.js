/**
 * Internal dependencies
 */
import {
	customers,
	countries,
	vendors,
	currencies,
	codes,
	incomeCategories,
	expenseCategories,
	itemCategories,
	accounts,
	items,
} from './selectors';
import SelectControl from '../select-control';
/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import {
	cloneElement,
	useEffect,
	useRef,
	useState,
	withState,
} from '@wordpress/element';
import { isObject, find } from 'lodash';
import { Button, Icon } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { useSelect } from '@wordpress/data';
/**
 * External dependencies
 */
import { CORE_STORE_NAME } from '@eaccounting/data';
import { __ } from '@wordpress/i18n';

const getAutocompleter = ( type ) => {
	switch ( type ) {
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
		case 'countries':
			return countries;
		case 'codes':
			return codes;
		default:
			return {};
	}
};

const normalizedValue = ( input, options, valueType, isMulti ) => {
	if ( valueType === 'string' ) {
		isMulti
			? options.filter( ( option ) => option.value === input.value )
			: options.find( ( option ) => option.value === input.value );
	}

	if ( valueType === 'object' ) {
		isMulti
			? options.filter( ( option ) => option === input.value )
			: options.find( ( option ) => option === input.value );
	}
};

const EntitySelect = ( args ) => {
	const {
		// eslint-disable-next-line no-unused-vars
		type,
		entityName,
		baseQuery = {},
		//selected = [],
		creatable,
		modal,
		isMulti = false,
		...props
	} = {
		...getAutocompleter( args.type ),
		...args,
	};
	const [ selected, setSelected ] = useState( [] );
	const [ query, setQuery ] = useState( baseQuery );
	const [ isModalOpen, setModalStatus ] = useState( false );
	const ref = useRef();

	const onClick = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
		console.log( ref );
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

	const items = useSelect(
		( select ) =>
			entityName &&
			select( CORE_STORE_NAME ).getEntityRecords( entityName, query )
	);

	const isLoading = useSelect(
		( select ) =>
			entityName &&
			select( CORE_STORE_NAME ).isResolving( 'getEntityRecords', [
				entityName,
				query,
			] )
	);

	const onInputChange = ( search, { action } ) => {
		if ( action === 'input-change' ) {
			setQuery( ( query ) => ( { ...query, search } ) );
		}
	};

	const handleChange = ( val ) => {
		setSelected( val );
	};

	const options =
		( props.options || [] ).concat( selected ).concat( items || [] ) || [];
	return (
		<>
			<SelectControl
				setRef={ ref }
				options={ options }
				isMulti={ isMulti }
				{ ...( !! creatable && { button: <AddButton /> } ) }
				{ ...( !! entityName && { isLoading } ) }
				{ ...( !! entityName && { onInputChange } ) }
				{ ...props }
				onChange={ handleChange }
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
};

EntitySelect.propTypes = {};

export default EntitySelect;
