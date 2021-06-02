/**
 * WordPress dependencies
 */
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

export default function DateControl({ onChange, value, className, autoApply = true , renderFormat = 'YYYY-MM-DD', returnFormat = 'YYYY-MM-DD', containerClass, ...props}){
	const classes = classnames('ea-date-field', className);
	const startDate = !isEmpty(value) ? moment(new Date(value)) : undefined;
	const inputVal = !isEmpty(startDate) ? startDate.format(renderFormat) : undefined;

	const handleChange = (event, picker) => {
		const value = picker.startDate.format(returnFormat) || undefined;
		onChange && onChange(value);
	};

	return(
		<DatePicker
			showDropdowns
			autoApply={autoApply}
			singleDatePicker
			startDate={startDate}
			onApply={handleChange}
			containerClass={classnames('ea-date-field-container', containerClass)}
		>
			<TextControl value={inputVal} className={classes} onChange={noop} {...props} />
		</DatePicker>
	)
}

DateControl.propTypes = {
	label: PropTypes.string,
	OnChange: PropTypes.func,
	value: PropTypes.string,
	renderFormat: PropTypes.string,
	returnFormat: PropTypes.string,
};
