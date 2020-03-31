/**
 * External dependencies
 */
import { Component, Fragment } from 'react';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import moment from 'moment';
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import DatePicker from '../date-picker';
import TextControl from '../text-control';
import classnames from 'classnames';
import { SVG, Path } from '@wordpress/components';

export default class DateRangeControl extends Component {
	static propTypes = {
		date: PropTypes.string,
		className: PropTypes.string,
		onChange: PropTypes.func,
	};

	static defaultProps = {
		startDate: undefined,
		endDate: undefined,
	};

	constructor(props) {
		super(props);
		this.state = {
			statDate: undefined,
			endDate: undefined,
		};
	}

	makeRange = (start, end, format = 'DD MMM YYYY', sep = '-') => {
		const startDate = (start && start.format(format)) || undefined;
		const endDate = (end && end.format(format)) || undefined;
		return (startDate && endDate && `${startDate}${sep}${endDate}`) || '';
	};

	onChange = (event, picker) => {
		this.setState({
			start: picker.startDate,
			end: picker.endDate,
		});

		this.props.onChange && this.props.onChange(this.makeRange(picker.startDate, picker.endDate, 'YYYY-MM-DD', '_'));
	};

	onCancel = () => {
		this.props.onChange && this.props.onChange(undefined, undefined);
	};

	render() {
		const { className } = this.props;
		const range = {
			'All Time': [null, null],
			Today: [moment(), moment()],
			Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [
				moment()
					.subtract(1, 'month')
					.startOf('month'),
				moment()
					.subtract(1, 'month')
					.endOf('month'),
			],
		};
		const classes = classnames('ea-date-filter', className);
		const { start, end } = this.state;
		return (
			<Fragment>
				<DatePicker
					ranges={range}
					startDate={start}
					endDate={end}
					onApply={this.onChange}
					onCancel={this.onCancel}
					autoUpdateInput={false}
				>
					<TextControl
						placeholder={__('Select Date')}
						className={classes}
						autoComplete="off"
						value={this.makeRange(start, end)}
						onChange={() => {}}
					/>
				</DatePicker>
			</Fragment>
		);
	}
}
