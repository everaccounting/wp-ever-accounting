import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import moment from 'moment';
import PropTypes from 'prop-types';
import {DateRange, TextControl} from "@eaccounting/components";
import classnames from 'classnames';

import './style.scss';

export default class DateFilter extends Component {
	static propTypes = {
		startDate: PropTypes.object,
		endDate: PropTypes.object,
		value: PropTypes.string,
		className: PropTypes.string,
		onChange: PropTypes.func,
	};

	static defaultProps = {
		startDate: moment().subtract(29, 'days'),
		endDate: moment(),
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},
	};

	constructor(props) {
		super(props);
	}

	onChange = (event, picker) => {
		const start = picker.startDate;
		const end = picker.endDate;
		// this.setState({input: start.format('d MMM Y') + '-' + end.format('d MMM Y')});
		this.props.onChange && this.props.onChange(start, end);
	};


	render() {
		const {startDate, endDate, ranges, className} = this.props;
		let local = {
			"format": "DD-MM-YYYY",
			"sundayFirst": false
		};

		let classes = classnames('ea-date-filter', className);
		return (
			<Fragment>
				<DateRange
					local={local}
					ranges={ranges}
					startDate={endDate}
					endDate={startDate}
					onApply={this.onChange}
					autoUpdateInput={true}
					className={classes}
				>
					{this.props.children && this.props.children}
				</DateRange>

			</Fragment>
		)
	}
}

