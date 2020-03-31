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
import {FORMAT_SITE_DATE} from "@eaccounting/data";
export default class DateControl extends Component {
	static propTypes = {
		renderFormat: PropTypes.string,
		returnFormat: PropTypes.string,
	};

	static defaultTypes = {
		renderFormat: FORMAT_SITE_DATE,
		returnFormat: FORMAT_SITE_DATE,
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
		const startDate = date !== undefined ? moment(date, this.props.returnFormat) : undefined;
		const inputVal = startDate !== undefined ? startDate.format(this.props.renderFormat) : '';
		return (
			<Fragment>
				<DatePicker
					showDropdowns
					singleDatePicker
					startDate={startDate}
					onApply={this.onChange}
					containerClass="ea-date-field-container">
					<TextControl value={inputVal} onChange={noop} className={classes} {...restProps} />
				</DatePicker>
			</Fragment>
		);
	}
}
