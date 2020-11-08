import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import SelectControl from '../select-control'

class SelectFilter extends Component {
	constructor({props}) {
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
			<SelectControl.Customer
				{...input}
				className={className}
				isClearable={true}
				onChange={onFilterChange}
			/>
		)
	}
}


SelectFilter.propTypes = {
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

export default SelectFilter;
