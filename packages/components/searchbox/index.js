import { useState } from '@wordpress/element';
import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';

/**
 * SearchBox component used for list table.
 *
 * @param props
 * @return {*}
 * @class
 */
function SearchBox( props ) {
	const { isDisabled = false, onSearch } = props;
	const [ search, setSearch ] = useState( '' );

	const handleSubmit = ( ev ) => {
		ev && ev.preventDefault();
		onSearch && onSearch( search );
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

SearchBox.propTypes = {
	onSearch: PropTypes.func,
	isDisabled: PropTypes.bool,
};

SearchBox.defaultProps = {
	onSearch: () => {},
	isDisabled: false,
};

export default SearchBox;