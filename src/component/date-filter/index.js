import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import moment from 'moment';
import PropTypes from 'prop-types';
import {DateRange, TextControl} from "@eaccounting/components";

export default class DateFilter extends Component {
	static propTypes = {
		startDate: PropTypes.object,
		endDate: PropTypes.object,
		ranges: PropTypes.object,
		value: PropTypes.string,
	};

	static defaultProps = {
		startDate:  moment(),
		endDate: moment(),
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
		},
		value: '',
	};

	constructor(props) {
		super(props);
		this.state = {
			input: ''
		}
	}

	onChange = (event, picker) => {
		const start = picker.startDate;
		const end = picker.endDate;
		this.setState({input: start.format('d MMM Y') + '-' + end.format('d MMM Y')});
	};


	render() {
		const {startDate, endDate, ranges} = this.props;
		let local = {
			"format": "DD-MM-YYYY",
			"sundayFirst": false
		};

		return (
			<Fragment>
				<DateRange
					local={local}
					ranges={ranges}
					startDate={endDate}
					endDate={startDate}
					onApply={this.onChange}
					autoUpdateInput={true}
				>
					<TextControl autoComplete='off' placeholder={__('Date Search')} value={this.state.input} onChange={()=>{}}/>
				</DateRange>

			</Fragment>
		)
	}
}

