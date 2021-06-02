/**
 * External dependencies
 */
import 'core-js/features/object/assign';
import 'core-js/features/array/from';
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { Dropdown, DatePicker as WpDatePicker } from '@wordpress/components';
import { partial } from 'lodash';
import { TAB } from '@wordpress/keycodes';
import moment from 'moment';
import PropTypes from 'prop-types';
import { toMoment } from '../lib';

/**
 * Internal dependencies
 */
import TextControl from '../text-control';
import './style.scss';
import classnames from "classnames";

class DatePicker extends Component {
	constructor( props ) {
		super( props );

		this.onDateChange = this.onDateChange.bind( this );
		this.onInputChange = this.onInputChange.bind( this );
	}

	handleKeyDown( isOpen, onToggle, { keyCode } ) {
		if ( TAB === keyCode && isOpen ) {
			onToggle();
		}
	}

	handleFocus( isOpen, onToggle ) {
		if ( ! isOpen ) {
			onToggle();
		}
	}

	onDateChange( onToggle, dateString ) {
		const { onChange, dateFormat } = this.props;
		const date = moment( dateString );
		onChange( dateString ? date.format( dateFormat ) : '' );
		onToggle();
	}

	onInputChange( value ) {
		const { dateFormat } = this.props;
		const date = toMoment( dateFormat, value );
		this.props.onChange( date ? date.format( dateFormat ) : null );
	}

	render() {
		const {
			disabled,
			value,
			isInvalidDate,
			dateFormat,
			className,
			label
		} = this.props;

		const classes = classnames(
			'ea-date-picker',
			className
		);

		const momentDate = toMoment( dateFormat, value );
		const date = momentDate ? momentDate.format( dateFormat ) : ''
		return (
			<Dropdown
				position="bottom center"
				className={classes}
				focusOnMount={ false }
				renderToggle={ ( { isOpen, onToggle } ) => (
					<TextControl
						disabled={ disabled }
						value={ date }
						onChange={ this.onInputChange }
						label={ label }
						onFocus={ partial(
							this.handleFocus,
							isOpen,
							onToggle
						) }
						onKeyDown={ partial(
							this.handleKeyDown,
							isOpen,
							onToggle
						) }
					/>
				) }
				renderContent={ ( { onToggle } ) => (
					<div className="ea-calendar__react-dates is-core-datepicker">
						<WpDatePicker
							currentDate={ momentDate }
							className='ea-calendar'
							onChange={ partial(
								this.onDateChange,
								onToggle
							) }
							isInvalidDate={ isInvalidDate }
						/>
					</div>
				) }
			/>
		);
	}
}

DatePicker.propTypes = {
	/**
	 * Whether the input is disabled.
	 */
	disabled: PropTypes.bool,
	/**
	 * The date in human-readable format. Displayed in the text input.
	 */
	value: PropTypes.string,
	/**
	 * A function called upon selection of a date or input change.
	 */
	onChange: PropTypes.func.isRequired,
	/**
	 * The date format in moment.js-style tokens.
	 */
	dateFormat: PropTypes.string.isRequired,
	/**
	 * A function to determine if a day on the calendar is not valid
	 */
	isInvalidDate: PropTypes.func,
};

export default DatePicker;
