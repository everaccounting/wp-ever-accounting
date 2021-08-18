/**
 * WordPress dependencies
 */
import { Fragment, useState } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { find, partial, isEmpty } from 'lodash';
import PropTypes from 'prop-types';
import interpolateComponents from 'interpolate-components';
import classnames from 'classnames';
import moment from 'moment';

/**
 * Internal dependencies
 */
import DatePicker from '../date-picker';
import { toMoment } from '../lib';
import { textContent } from './utils';

const dateStringFormat = __('MMM D, YYYY');
const dateFormat = __('MM/DD/YYYY');
export const isoDateFormat = 'YYYY-MM-DD';

function DateFilter(props) {
	const { className, config, filter, onFilterChange } = props;
	const { title, mixedString, rules = [] } = config;
	const { rule } = filter;
	const [isoAfter, isoBefore] = Array.isArray(filter.value)
		? filter.value
		: [null, filter.value];
	const after = isoAfter ? toMoment(isoAfter) : null;
	const before = isoBefore ? toMoment(isoBefore) : null;
	const [state, setState] = useState({
		before,
		beforeError: null,
		after,
		afterError: null,
	});

	const getBetweenString = () =>
		_x(
			'{{after /}}{{span}} and {{/span}}{{before /}}',
			'Date range inputs arranged on a single line'
		);

	const onSingleDateChange = (value, date, error) => {
		setState({
			...state,
			before: date,
			beforeError: error,
		});
		if (date) {
			onFilterChange('value', date.format(isoDateFormat));
		}
	};

	const onRangeDateChange = (input, value, date, error) => {
		setState((state) => ({
			...state,
			[input]: date,
			[input + 'Error']: error,
		}));

		if (date) {
			const { before, after } = state;
			let nextAfter = null;
			let nextBefore = null;

			if (input === 'after') {
				nextAfter = date.format(isoDateFormat);
				nextBefore = before ? before.format(isoDateFormat) : null;
			}

			if (input === 'before') {
				nextAfter = after ? after.format(isoDateFormat) : null;
				nextBefore = date.format(isoDateFormat);
			}

			if (nextAfter && nextBefore) {
				onFilterChange('value', [nextAfter, nextBefore]);
			}
		}
	};

	const isFutureDate = (dateString) => {
		return moment().isBefore(moment(dateString), 'day');
	};

	const getFormControl = ({ date, error, onChange }) => {
		return (
			<DatePicker
				value={date}
				dateFormat={dateFormat}
				error={error}
				isInvalidDate={isFutureDate}
				onChange={onChange}
			/>
		);
	};

	const getRangeInput = () => {
		const { before, beforeError, after, afterError } = state;
		return interpolateComponents({
			mixedString: getBetweenString(),
			components: {
				after: getFormControl({
					date: after,
					error: afterError,
					onChange: partial(onRangeDateChange, 'after'),
				}),
				before: getFormControl({
					date: before,
					error: beforeError,
					onChange: partial(onRangeDateChange, 'before'),
				}),
				span: <span className="separator" />,
			},
		});
	};

	const getFilterInputs = () => {
		const { before, beforeError } = state;
		if (filter.rule === 'between') {
			return getRangeInput();
		}

		return getFormControl({
			date: before,
			error: beforeError,
			onChange: onSingleDateChange,
			// onChange: ( val ) => console.log( val ),
		});
	};

	const getScreenReaderText = () => {
		const rule = find(config.rules, { value: filter.rule }) || {};

		const { before, after } = state;

		// Return nothing if we're missing input(s)
		if (!before || (rule.value === 'between' && !after)) {
			return '';
		}

		let filterStr = before.format(dateStringFormat);

		if (rule.value === 'between') {
			filterStr = interpolateComponents({
				mixedString: getBetweenString(),
				components: {
					after: (
						<Fragment>{after.format(dateStringFormat)}</Fragment>
					),
					before: (
						<Fragment>{before.format(dateStringFormat)}</Fragment>
					),
					span: <Fragment />,
				},
			});
		}

		return textContent(
			interpolateComponents({
				mixedString: title,
				components: {
					filter: <Fragment>{filterStr}</Fragment>,
					rule: <Fragment>{rule.label}</Fragment>,
					title: <Fragment />,
				},
			})
		);
	};

	const screenReaderText = getScreenReaderText(filter, config);

	const children = interpolateComponents({
		mixedString,
		components: {
			title: (
				<span
					className={classnames(
						className,
						'ea-advanced-filters__label'
					)}
				/>
			),
			rule: (
				<div
					className={classnames(
						className,
						'ea-advanced-filters__rule',
						{
							'display--none': isEmpty(rules),
						}
					)}
				>
					<SelectControl
						options={rules}
						value={rule}
						onChange={partial(onFilterChange, 'rule')}
						aria-label={rule}
					/>
				</div>
			),
			filter: (
				<div
					className={classnames(
						className,
						'ea-advanced-filters__input-range',
						{
							'is-between': rule === 'between',
						}
					)}
				>
					{getFilterInputs()}
				</div>
			),
		},
	});

	return (
		<>
			<fieldset className="ea-advanced-filters__line-item" tabIndex="0">
				<legend className="screen-reader-text">{title}</legend>
				<div className="ea-advanced-filters__fieldset">{children}</div>
				{screenReaderText && (
					<span className="screen-reader-text">
						{screenReaderText}
					</span>
				)}
			</fieldset>
		</>
	);
}

DateFilter.propTypes = {
	// The configuration object for the single filter to be rendered.
	config: PropTypes.shape({
		labels: PropTypes.shape({
			rule: PropTypes.string,
			title: PropTypes.string,
			filter: PropTypes.string,
		}),
		rules: PropTypes.arrayOf(PropTypes.object),
		input: PropTypes.object,
	}).isRequired,
	// The activeFilter handed down by AdvancedFilters.
	filter: PropTypes.shape({
		key: PropTypes.string,
		rule: PropTypes.string,
		value: PropTypes.string,
	}).isRequired,
	// Function to be called on update.
	onFilterChange: PropTypes.func.isRequired,
};

export default DateFilter;
