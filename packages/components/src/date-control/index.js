/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import moment from 'moment';
/**
 * Internal dependencies
 */
import DatePicker from '../date-picker';
import TextControl from '../text-control';
import { noop } from 'lodash';

export default class DateControl extends Component {
	static propTypes = {
		format: PropTypes.string,
	};

	constructor(props) {
		super(props);
	}

	onChange = (event, picker) => {
		const value = picker.startDate.format('YYYY-MM-DD') || undefined;
		this.props.onChange && this.props.onChange(value);
	};

	render() {
		const { onChange, value, className, ...restProps } = this.props;
		const classes = classnames('ea-date-field', className);
		const date = value || undefined;
		const startDate = date !== undefined ? moment(date, 'YYYY-MM-DD') : undefined;
		const inputVal = startDate !== undefined ? startDate.format('YYYY-MM-DD') : '';
		return (
			<Fragment>
				<DatePicker
					showDropdowns
					singleDatePicker
					startDate={startDate}
					onApply={this.onChange}
					containerClass="ea-date-field-container"
				>
					<TextControl value={inputVal} onChange={noop} className={classes} {...restProps} />
				</DatePicker>
			</Fragment>
		);
	}
}
