/**
 * External dependencies
 */
import { debounce } from 'lodash';

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { FormTokenField, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

function Autocomplete( { tokens, onChange, help, ...props } ) {
	const [ suggestions, setSuggestions ] = useState( [] );
	const [ validValues, setValidValues ] = useState( {} );
	const [ loading, setLoading ] = useState( false );

	/**
	 * Get a list of labels for input values.
	 *
	 * @param {Array} values Array of values (ids, etc.).
	 * @return {Array} array of valid labels corresponding to the values.
	 */
	const getLabelsForValues = ( values ) => {
		return values.reduce(
			( accumulator, value ) => ( validValues[ value ] ? [ ...accumulator, validValues[ value ] ] : accumulator ),
			[]
		);
	};

	/**
	 * Get a list of values for input labels.
	 *
	 * @param {Array} labels Array of labels from the tokens.
	 * @return {Array} Array of valid values corresponding to the labels.
	 */
	const getValuesForLabels = ( labels ) => {
		return labels.map( ( label ) => Object.keys( validValues ).find( ( key ) => validValues[ key ] === label ) );
	};

	/**
	 * When a token is selected, we need to convert the string label into a recognized value suitable for saving as an attribute.
	 *
	 * @param {Array} tokenStrings An array of token label strings.
	 */
	const handleOnChange = ( tokenStrings ) => {
		if ( onChange ) {
			onChange( getValuesForLabels( tokenStrings ) );
		}
	};

	/**
	 * To populate the tokens, we need to convert the values into a human-readable label.
	 *
	 * @return {Array} An array of token label strings.
	 */
	const getTokens = () => {
		return getLabelsForValues( tokens );
	};

	const debouncedUpdateSuggestions = debounce( ( input ) => {

	}

	return (
		<div className="autocomplete-tokenfield">
			<FormTokenField
				{ ...props }
				value={ getTokens() }
				suggestions={ suggestions }
				validValues={ validValues }
				onChange={ ( tokens ) => handleOnChange( tokens ) }
				onInputChange={ ( input ) => {
			/>
			{ loading && <Spinner /> }
			{ help && <p className="autocomplete-tokenfield__help">{ help }</p> }
		</div>
	);
}

export default Autocomplete;
