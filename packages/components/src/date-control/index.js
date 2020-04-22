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
import { isEmpty } from 'lodash';

/**
 * Internal dependencies
 */
import DatePicker from '../date-picker';
import TextControl from '../text-control';
import { noop } from 'lodash';
import { FORMAT_SITE_DATE } from '@eaccounting/data';

export default class DateControl extends Component {
	static propTypes = {
		renderFormat: PropTypes.string,
		returnformat: PropTypes.string,
	};

	static defaultProps = {
		returnformat: 'YYYY-MM-DD',
	};

	constructor(props) {
		super(props);
	}

	onChange = (event, picker) => {
		const { returnformat = FORMAT_SITE_DATE } = this.props;
		const value = picker.startDate.format(returnformat) || undefined;
		this.props.onChange && this.props.onChange(value);
	};

	render() {
		const { onChange, value, containerClass, className, renderFormat = FORMAT_SITE_DATE, ...restProps } = this.props;
		const classes = classnames('ea-date-field', className);

		const startDate = !isEmpty(value) ? moment(new Date(value)) : undefined;
		const inputVal = !isEmpty(startDate) ? startDate.format(renderFormat) : undefined;

		return (
			<Fragment>
				<DatePicker
					showDropdowns
					singleDatePicker
					startDate={startDate}
					onApply={this.onChange}
					containerClass={classnames('ea-date-field-container', containerClass)}
				>
					<TextControl value={inputVal} className={classes} onChange={noop} {...restProps} />
				</DatePicker>
			</Fragment>
		);
	}
}
