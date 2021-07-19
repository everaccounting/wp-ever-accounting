/**
 * WordPress dependencies
 */
import { Fragment } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { sprintf, _x } from '@wordpress/i18n';
/**
 * External dependencies
 */
import classnames from 'classnames';
import { partial, get, isArray, find, isEmpty } from 'lodash';
import interpolateComponents from 'interpolate-components';
/**
 * Internal dependencies
 */
import TextControl from '../text-control';
import { textContent } from './utils';
import PropTypes from 'prop-types';

function NumberFilter( props ) {
	const { className, config, filter, onFilterChange, currency = {} } = props;
	const { title, mixedString, rules = [] } = config;
	const { symbol: currencySymbol, symbolPosition } = currency;
	const { rule = '' } = filter;

	const getBetweenString = () => {
		return _x(
			'{{rangeStart /}}{{span}} and {{/span}}{{rangeEnd /}}',
			'Numerical range inputs arranged on a single line'
		);
	};

	const getScreenReaderText = () => {
		const rule = find( rules, { value: filter.rule } ) || {};
		const [ rangeStart, rangeEnd ] = isArray( filter.value )
			? filter.value
			: [ filter.value ];

		// Return nothing if we're missing input(s)
		if ( ! rangeStart || ( rule.value === 'between' && ! rangeEnd ) ) {
			return '';
		}
		const inputType = get( config, [ 'input', 'type' ], 'number' );

		if ( inputType === 'currency' ) {
			// const { formatAmount } = CurrencyFactory( currency );
			// rangeStart = formatAmount( rangeStart );
			// rangeEnd = formatAmount( rangeEnd );
		}

		let filterStr = rangeStart;

		if ( rule.value === 'between' ) {
			filterStr = interpolateComponents( {
				mixedString: getBetweenString(),
				components: {
					rangeStart: <Fragment>{ rangeStart }</Fragment>,
					rangeEnd: <Fragment>{ rangeEnd }</Fragment>,
					span: <Fragment />,
				},
			} );
		}

		return textContent(
			interpolateComponents( {
				mixedString,
				components: {
					filter: <Fragment>{ filterStr }</Fragment>,
					rule: <Fragment>{ rule.label }</Fragment>,
					title: <Fragment />,
				},
			} )
		);
	};

	const getFilterInputs = () => {
		if ( rule === 'between' ) {
			return getRangeInput();
		}

		const inputType = get( config, [ 'input', 'type' ], 'number' );

		const [ rangeStart, rangeEnd ] = isArray( filter.value )
			? filter.value
			: [ filter.value ];
		if ( Boolean( rangeEnd ) ) {
			// If there's a value for rangeEnd, we've just changed from "between"
			// to "less than" or "more than" and need to transition the value
			onFilterChange( 'value', rangeStart || rangeEnd );
		}

		let labelFormat = '';

		if ( filter.rule === 'lessthan' ) {
			/* eslint-disable-next-line max-len */
			/* translators: Sentence fragment, "maximum amount" refers to a numeric value the field must be less than. Screenshot for context: https://cloudup.com/cmv5CLyMPNQ */
			// eslint-disable-next-line @wordpress/i18n-translator-comments
			labelFormat = _x(
				'%(field)s maximum amount',
				'maximum value input',
				'woocommerce-admin'
			);
		} else {
			/* eslint-disable-next-line max-len */
			/* translators: Sentence fragment, "minimum amount" refers to a numeric value the field must be more than. Screenshot for context: https://cloudup.com/cmv5CLyMPNQ */
			// eslint-disable-next-line @wordpress/i18n-translator-comments
			labelFormat = _x(
				'%(field)s minimum amount',
				'minimum value input',
				'woocommerce-admin'
			);
		}

		return getFormControl( {
			type: inputType,
			value: rangeStart || rangeEnd,
			label: sprintf( labelFormat, {
				field: get( config, [ 'labels', 'add' ] ),
			} ),
			onChange: partial( onFilterChange, 'value' ),
			currencySymbol,
			symbolPosition,
		} );
	};

	const getRangeInput = () => {
		const inputType = get( config, [ 'input', 'type' ], 'number' );
		const [ rangeStart, rangeEnd ] = isArray( filter.value )
			? filter.value
			: [ filter.value ];

		const rangeStartOnChange = ( newRangeStart ) => {
			onFilterChange( 'value', [ newRangeStart, rangeEnd ] );
		};

		const rangeEndOnChange = ( newRangeEnd ) => {
			onFilterChange( 'value', [ rangeStart, newRangeEnd ] );
		};

		return interpolateComponents( {
			mixedString: '{{rangeStart /}}{{span}} and {{/span}}{{rangeEnd /}}',
			components: {
				rangeStart: getFormControl( {
					type: inputType,
					value: rangeStart || '',
					label: '',
					onChange: rangeStartOnChange,
					currencySymbol,
					symbolPosition,
				} ),
				rangeEnd: getFormControl( {
					type: inputType,
					value: rangeEnd || '',
					label: '',
					onChange: rangeEndOnChange,
					currencySymbol,
					symbolPosition,
				} ),
				span: <span className="separator" />,
			},
		} );
	};

	const getFormControl = ( {
		type,
		value,
		label,
		onChange,
		currencySymbol,
		symbolPosition,
	} ) => {
		if ( type === 'currency' ) {
			return symbolPosition.indexOf( 'right' ) === 0 ? (
				<TextControl
					suffix={
						<span
							dangerouslySetInnerHTML={ {
								__html: currencySymbol,
							} }
						/>
					}
					className="ea-advanced-filters__input"
					type="number"
					value={ value || '' }
					aria-label={ label }
					onChange={ onChange }
				/>
			) : (
				<TextControl
					prefix={
						<span
							dangerouslySetInnerHTML={ {
								__html: currencySymbol,
							} }
						/>
					}
					className="ea-advanced-filters__input"
					type="number"
					value={ value || '' }
					aria-label={ label }
					onChange={ onChange }
				/>
			);
		}

		return (
			<TextControl
				className="ea-advanced-filters__input"
				type="number"
				value={ value || '' }
				aria-label={ label }
				onChange={ onChange }
			/>
		);
	};

	const children = interpolateComponents( {
		mixedString,
		components: {
			title: (
				<span
					className={ classnames(
						className,
						'ea-advanced-filters__label'
					) }
				/>
			),
			rule: (
				<div
					className={ classnames(
						className,
						'ea-advanced-filters__rule',
						{
							'display--none': isEmpty( rules ),
						}
					) }
				>
					<SelectControl
						options={ rules }
						value={ rule }
						onChange={ partial( onFilterChange, 'rule' ) }
						aria-label={ rule }
					/>
				</div>
			),
			filter: (
				<div
					className={ classnames(
						className,
						'ea-advanced-filters__input-range',
						{
							'is-between': rule === 'between',
						}
					) }
				>
					{ getFilterInputs() }
				</div>
			),
		},
	} );

	const screenReaderText = getScreenReaderText();

	return (
		<fieldset className="ea-advanced-filters__line-item" tabIndex="0">
			<legend className="screen-reader-text">{ title }</legend>
			<div className="ea-advanced-filters__fieldset">{ children }</div>
			{ screenReaderText && (
				<span className="screen-reader-text">{ screenReaderText }</span>
			) }
		</fieldset>
	);
}

NumberFilter.propTypes = {
	// The configuration object for the single filter to be rendered.
	config: PropTypes.shape( {
		labels: PropTypes.shape( {
			rule: PropTypes.string,
			title: PropTypes.string,
			filter: PropTypes.string,
		} ),
		rules: PropTypes.arrayOf( PropTypes.object ),
		input: PropTypes.object,
	} ).isRequired,
	// The activeFilter handed down by AdvancedFilters.
	filter: PropTypes.shape( {
		key: PropTypes.string,
		rule: PropTypes.string,
		value: PropTypes.string,
	} ).isRequired,
	// Function to be called on update.
	onFilterChange: PropTypes.func.isRequired,
};

export default NumberFilter;
