/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';

/**
 * SearchBox component used for list table.
 *
 * @param {Object} props properties
 * @return {Object} search box
 */
function TableSearch( props ) {
	const { isDisabled = false, onSearch } = props;
	const [ search, setSearch ] = useState( '' );

	const handleSubmit = ( ev ) => {
		ev.preventDefault();
		onSearch( search );
	};

	const handleChange = ( ev ) => {
		setSearch( ev.target.value );
		if ( ev.target.value === '' ) {
			onSearch( '' );
		}
	};

	return (
		<p className="search-box">
			<input
				type="search"
				name="s"
				value={ search }
				onChange={ handleChange }
				disabled={ !! isDisabled }
			/>
			<input
				type="submit"
				className="button"
				value={ __( 'Search' ) }
				onClick={ handleSubmit }
				disabled={ isDisabled || ! search }
			/>
		</p>
	);
}

TableSearch.propTypes = {
	onSearch: PropTypes.func,
	isDisabled: PropTypes.bool,
};

TableSearch.defaultProps = {
	onSearch: () => {},
	isDisabled: false,
};

export default TableSearch;
