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
import { getCurrencyConfig } from '@eaccounting/data';
import NumberFormat from 'react-number-format';
export default class PriceControl extends Component {
	onChange = price => {
		this.props.onChange && this.props.onChange(price.value);
	};

	render() {
		const { label, code = 'USD', help, className, before, after, required, value, ...props } = this.props;
		const classes = classnames('ea-form-group', 'ea-price-field', className, {
			required: !!required,
		});

		const currency = getCurrencyConfig(code);
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
						decimal_separator={currency && currency.decimal_separator}
						thousand_separator={currency && currency.thousand_separator}
						value={(value && value) || ''}
						onValueChange={this.onChange}
						//onChange={this.onChange}
					/>
					{after && <span className="ea-input-group__after">{after}</span>}
				</div>
			</BaseControl>
		);
	}
}

PriceControl.defaultProps = {
	code: 'USD',
};

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
