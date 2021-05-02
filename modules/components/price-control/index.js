/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { BaseControl } from '@wordpress/components';
import classnames from 'classnames';
import NumberFormat from 'react-number-format';
import './style.scss';
import {useEntity, useSettings} from "@eaccounting/data";

export default function PriceControl(props) {
	const onChange = price => {
		props.onChange && props.onChange(price.value);
	};
	const {default_currency = 'USD' } = useSettings()
	const { label, code = default_currency, help, className, before, after, required, value, ...restProps } = props;
	const {entity:currency} = useEntity({name:'currencies', id:code});
	const classes = classnames('ea-form-group', 'ea-price-field', className, {
		required: !!required,
	});

	const suffix = currency && currency.position !== 'before' ? currency.symbol : '';
	const prefix = currency && currency.position === 'before' ? currency.symbol : '';
	const placeholder = currency && currency.symbol && `${currency.symbol}0.00`;

	return (
		<BaseControl label={label} help={help} className={classes}>
			<div className="ea-input-group">
				{before && <span className="ea-input-group__before">{before}</span>}
				<NumberFormat
					suffix={suffix}
					prefix={prefix}
					required={required}
					placeholder={placeholder}
					className="components-text-control__input ea-input-group__input"
					thousandsGroupStyle="thousand"
					decimalSeparator={currency && currency.decimal_separator}
					thousandSeparator={currency && currency.thousand_separator}
					value={(value && value) || ''}
					onValueChange={onChange}
					{...restProps}
				/>
				{after && <span className="ea-input-group__after">{after}</span>}
			</div>
		</BaseControl>
	);
}

PriceControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.any,
	code: PropTypes.string,
	className: PropTypes.string,
	onChange: PropTypes.func,
	required: PropTypes.bool,
	before: PropTypes.node,
	after: PropTypes.node,
};
