/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Fragment, useState } from '@wordpress/element';
import { SelectControl, Spinner } from '@wordpress/components';
import { find, isEmpty, partial } from 'lodash';
import PropTypes from 'prop-types';
import interpolateComponents from 'interpolate-components';
import classnames from 'classnames';
import { getDefaultOptionValue } from '@eaccounting/navigation';

/**
 * Internal dependencies
 */
import { textContent } from './utils';

function SelectFilter(props) {
	const { className, config, filter, onFilterChange } = props;
	const { title, mixedString, rules = [], input = { options: [] } } = config;
	const [options, setOptions] = useState(input.options);

	if (!options && input.getOptions) {
		input
			.getOptions()
			.then((options) => setOptions(options))
			.then((returnedOptions) => {
				if (!filter.value) {
					const value = getDefaultOptionValue(
						config,
						returnedOptions
					);
					onFilterChange('value', value);
				}
			});
	}

	const getScreenReaderText = (filter, config) => {
		if (filter.value === '') {
			return '';
		}

		const rule = find(rules, { value: filter.rule }) || {};
		const value = find(config.input.options, { value: filter.value }) || {};
		return textContent(
			interpolateComponents({
				mixedString,
				components: {
					filter: <Fragment>{value.label}</Fragment>,
					rule: <Fragment>{rule.label}</Fragment>,
					title: <Fragment />,
				},
			})
		);
	};

	const { rule, value } = filter;
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
			filter: options ? (
				<div
					className={classnames(
						className,
						'ea-advanced-filters__input'
					)}
				>
					<SelectControl
						options={options}
						value={value}
						onChange={partial(onFilterChange, 'value')}
						aria-label={title}
					/>
				</div>
			) : (
				<Spinner />
			),
		},
	});

	const screenReaderText = getScreenReaderText(filter, config);

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

SelectFilter.propTypes = {
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

export default SelectFilter;
