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
import { Button, Icon } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { useSelect } from '@wordpress/data';
/**
 * External dependencies
 */
import { CORE_STORE_NAME } from '@eaccounting/data';

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

const EntitySelect = ( args ) => {
	const {
		// eslint-disable-next-line no-unused-vars
		type,
		creatable,
		modal,
		...props
	} = {
		...getAutocompleter( args.type ),
		...args,
	};
	const [ query, setQuery ] = useState( {} );
	const [ isModalOpen, setModalStatus ] = useState( false );
	const ref = useRef();

	const onClick = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
		setModalStatus( ( open ) => ! open );
	};

	const After = () => {
		const style = { cursor: 'pointer' };
		return <Icon icon="plus" style={ style } onClick={ onClick } />;
	};

	const Button = () => {
		return <Button className={ 'change-data' }>Add New</Button>;
	};

	const handleCreate = async ( item ) => {
		await ref.current.select.select.setValue( item );
		setModalStatus( ( open ) => ! open );
	};

	const options = useSelect( ( select ) =>
		select( 'ea/core' ).getEntityRecords( 'customers', query )
	);
	const isLoading = useSelect( ( select ) =>
		select( 'ea/core' ).isResolving( 'getEntityRecords', [
			'customers',
			query,
		] )
	);

	const onInputChange = ( search ) => {
		setQuery( { search } );
		return search;
	};
	console.log( query );
	return (
		<>
			<SelectControl
				setRef={ ref }
				isLoading={ isLoading }
				options={ options }
				button={ <Button /> }
				getOptionLabel={ ( customer ) => customer && customer.name }
				getOptionValue={ ( customer ) => customer && customer.id }
				onInputChange={ onInputChange }
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
