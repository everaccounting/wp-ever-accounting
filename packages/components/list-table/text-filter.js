import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {TextControl} from '@wordpress/components'


class TextFilter extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const {
			filter,
			onFilterChange,
			className
		} = this.props;

		const {input} = filter;

		return (
			<TextControl
				{...input}
				className={className}
				isClearable={true}
				onChange={onFilterChange}
			/>
		)
	}
}

TextFilter.propTypes = {
	/**
	 * The activeFilter handed down by AdvancedFilters.
	 */
	filter: PropTypes.shape({
		key: PropTypes.string,
		value: PropTypes.string,
	}).isRequired,
	/**
	 * Function to be called on update.
	 */
	onFilterChange: PropTypes.func.isRequired,
};

export default TextFilter;
