import { Component } from '@wordpress/element';
import PropTypes from 'prop-types';
import { BaseControl } from '@wordpress/components';
import classnames from 'classnames';
import MaskedInput from 'react-text-mask';
import createNumberMask from 'text-mask-addons/dist/createNumberMask';

export default class PriceControl extends Component {

	onChange = (e) => {
		this.props.onChange && this.props.onChange(e.target.value);
	};

	render() {
		const { label, code = 'USD', help, className, before, after, required, value, ...props } = this.props;
		const classes = classnames('ea-form-group', 'ea-price-field', className, {
			required: !!required,
		});

		const currency = eAccountingi10n.data.currencies[code];

		const suffix = currency && 'before' !== currency.position ? currency.symbol : '';
		const prefix = currency && 'before' === currency.position ? currency.symbol : '';

		const maskOptions = {
			prefix:prefix,
			suffix:suffix,
			allowDecimal: !! currency && currency.precision,
			decimalSymbol: currency && currency.decimalSeparator,
			decimalLimit: currency && currency.precision,
			thousandsSeparatorSymbol: currency && currency.thousandSeparator,
		};

		const currencyMask = createNumberMask(maskOptions);
		const placeholder = currency && currency.symbol && `${currency.symbol} 0.00`;
		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && <span className="ea-input-group__before">{before}</span>}

					<MaskedInput
						required={required}
						placeholder={placeholder}
						className="components-text-control__input ea-input-group__input"
						mask={currencyMask}
						value={value && value || ""}
						onChange={this.onChange}
						inputMode="numeric"
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
