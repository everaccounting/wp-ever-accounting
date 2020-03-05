import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import moment from 'moment';
import PropTypes from 'prop-types';
import DatePicker from '../date-picker';
import TextControl from '../text-control';
import classnames from 'classnames';

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
	}

	onChange = (event, picker) => {
		const start = picker.startDate.format('YYYY-MM-DD') || undefined;
		const end = picker.endDate.format('YYYY-MM-DD') || undefined;
		let date = start && end && `${start}_${end}`;
		this.props.onChange && this.props.onChange(date);
	};

	onCancel = () => {
		this.props.onChange && this.props.onChange(undefined, undefined);
	};

	render() {
		const { date, className } = this.props;
		const range = {
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
		let classes = classnames('ea-date-filter', className);

		let dates, startDate, endDate;
		dates = date.split('_', 2);
		startDate = dates[0] || undefined;
		endDate = dates[1] || undefined;
		let start = startDate !== undefined ? moment(startDate) : undefined;
		let end = endDate !== undefined ? moment(endDate) : undefined;
		let date_range = '';
		if (start && end) {
			date_range = start.format('D MMM Y');
			date_range += ' - ' + end.format('D MMM Y');
		}

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
						value={date_range}
						onChange={() => {}}
					/>
				</DatePicker>
			</Fragment>
		);
	}
}
