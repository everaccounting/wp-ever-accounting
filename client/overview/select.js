/**
 * External dependencies
 */
import Select from 'react-select';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * onChange from Redux Form Field has to be called explicity.
 */
function singleChangeHandler( func ) {
	return function handleSingleChange( value ) {
		func( value ? value.value : '' );
	};
}

/**
 * onBlur from Redux Form Field has to be called explicity.
 */
function multiChangeHandler( func ) {
	return function handleMultiHandler( values ) {
		func( values.map( ( value ) => value.value ) );
	};
}

/**
 * For single select, Redux Form keeps the value as a string, while React Select
 * wants the value in the form { value: "grape", label: "Grape" }
 *
 * * For multi select, Redux Form keeps the value as array of strings, while React Select
 * wants the array of values in the form [{ value: "grape", label: "Grape" }]
 */
function transformValue( value, options = [], multi ) {
	if ( multi && typeof value === 'string' ) return [];

	const filteredOptions = options.filter( ( option ) => {
		return multi
			? value.indexOf( option.value ) !== -1
			: option.value === value;
	} );

	return multi ? filteredOptions : filteredOptions[ 0 ];
}

export default function SelectControl( props ) {
	const [ query, setQuery ] = useState( {} );

	const options = useSelect( ( select ) =>
		select( 'ea/core' ).getEntityRecords( 'customers', query )
	);
	const isLoading = useSelect( ( select ) =>
		select( 'ea/core' ).isResolving( 'getEntityRecords', [
			'customers',
			query,
		] )
	);

	return (
		<Select
			isLoading={ isLoading }
			onInputChange={ ( search ) => setQuery( { search } ) }
			getOptionLabel={ ( customer ) => customer && customer.name }
			getOptionValue={ ( customer ) => customer && customer.id }
			options={ options }
		/>
	);
}
