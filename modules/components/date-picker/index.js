/**
 * External dependencies
 */
// import 'core-js/features/object/assign';
// import 'core-js/features/array/from';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import {
	Dropdown,
	DatePicker as WpDatePicker,
	Popover,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { partial } from 'lodash';
// eslint-disable-next-line import/no-extraneous-dependencies
import { TAB } from '@wordpress/keycodes';
import moment from 'moment';
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import { toMoment } from '../lib';
import TextControl from '../text-control';
import './style.scss';

const dateValidationMessages = {
	invalid: __( 'Invalid date' ),
	future: __( 'Select a date in the past' ),
	startAfterEnd: __( 'Start date must be before end date' ),
	endBeforeStart: __( 'Start date must be before end date' ),
};

class DatePicker extends Component {
	constructor( props ) {
		super( props );
		console.log( props );
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
		const date = !! dateString && moment( dateString );
		const value =
			!! dateString && !! date
				? date.format( this.props.dateFormat )
				: '';
		this.props.onChange( value, date, null );
		onToggle();
	}

	onInputChange( value ) {
		const date = toMoment( value );
		const error = !! date ? null : dateValidationMessages.invalid;
		this.props.onChange( value, date, error );
	}

	render() {
		const {
			className,
			label,
			value,
			disabled,
			dateFormat,
			error,
			isInvalidDate,
		} = this.props;

		const classes = classnames( 'ea-date-picker', className );

		const date = !! value && toMoment( value );
		const text = date ? date.format( dateFormat ) : '';
		return (
			<Dropdown
				position="bottom center"
				className={ classes }
				focusOnMount={ false }
				renderToggle={ ( { isOpen, onToggle } ) => (
					<>
						<TextControl
							disabled={ disabled }
							value={ text }
							onChange={ this.onInputChange }
							placeholder={ dateFormat.toLowerCase() }
							label={ label }
							error={ error }
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
						{ error && (
							<Popover
								className="woocommerce-calendar__input-error"
								focusOnMount={ false }
								position="bottom center"
							>
								{ error }
							</Popover>
						) }
					</>
				) }
				renderContent={ ( { onToggle } ) => (
					<div className="ea-calendar__react-dates is-core-datepicker">
						<WpDatePicker
							currentDate={ date }
							className="ea-calendar"
							onChange={ partial( this.onDateChange, onToggle ) }
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
	text: PropTypes.string,
	/**
	 * A string error message, shown to the user.
	 */
	error: PropTypes.string,
	/**
	 * A moment date object representing the selected date. `null` for no selection.
	 */
	value: PropTypes.any,
	/**
	 * A function called upon selection of a date or input change.
	 */
	onChange: PropTypes.func,
	/**
	 * A function called upon selection of a date or input change.
	 */
	onUpdate: PropTypes.func,
	/**
	 * The date format in moment.js-style tokens.
	 */
	dateFormat: PropTypes.string.isRequired,
	/**
	 * A function to determine if a day on the calendar is not valid
	 */
	isInvalidDate: PropTypes.func,
};

DatePicker.defaultProps = {
	dateFormat: 'YYYY-MM-DD',
	onChange: ( x ) => x,
};

export default DatePicker;
