import {useState} from "@wordpress/element";
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';

/**
 * SearchBox component used for list table.
 *
 * @param props
 * @returns {*}
 * @constructor
 */
function SearchBox(props) {
	const {isDisabled = false, onSearch} = props;
	const [search, setSearch] = useState('');

	const handleSubmit = (ev) => {
		ev && ev.preventDefault();
		onSearch && onSearch(search);
	}

	const handleChange = (ev) => {
		setSearch(ev.target.value);
		if (ev.target.value === '') {
			onSearch('');
		}
	}

	return (
		<form onSubmit={handleSubmit} className="search-box">
			<input type="search" name="s" value={search} onChange={handleChange}/>
			<input type="submit" className="button" value={__('Search')} disabled={isDisabled || !search}/>
		</form>
	)
}

SearchBox.propTypes = {
	onSearch: PropTypes.func,
	isDisabled: PropTypes.string,
}

SearchBox.defaultProps = {
	onSearch: () => {
	},
	isDisabled: false
}

export default SearchBox;
